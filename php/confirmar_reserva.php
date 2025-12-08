<?php
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../inicio.html?error=no_logueado");
    exit;
}

define('ALL_INCLUSIVE_DAILY_COST', 100.00);

$link = $GLOBALS['link']; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["id"];

    $fecha_entrada = filter_input(INPUT_POST, 'fecha_entrada', FILTER_SANITIZE_STRING);
    $fecha_salida = filter_input(INPUT_POST, 'fecha_salida', FILTER_SANITIZE_STRING);
    $habitacion_id = filter_input(INPUT_POST, 'habitacion_id', FILTER_VALIDATE_INT);
    $paquete_tipo = filter_input(INPUT_POST, 'paquete_tipo', FILTER_SANITIZE_STRING);

    $extra_ids_json = filter_input(INPUT_POST, 'extra_ids_json', FILTER_SANITIZE_STRING);
    $extra_ids = json_decode($extra_ids_json, true) ?? [];

    if (!$fecha_entrada || !$fecha_salida || !$habitacion_id || !$paquete_tipo) {
        header("location: ../reservas.php?error=datos_faltantes");
        exit;
    }

    $date_in = new DateTime($fecha_entrada);
    $date_out = new DateTime($fecha_salida);
    $interval = $date_in->diff($date_out);
    $noches = $interval->days > 0 ? $interval->days : 1;

    $sql_check_availability = "
        SELECT COUNT(id) AS count_reservas
        FROM reservas
        WHERE habitacion_id = ?
        AND estado != 'cancelada'
        AND NOT (fecha_salida <= ? OR fecha_entrada >= ?)
    ";

    if ($stmt_check = mysqli_prepare($link, $sql_check_availability)) {
        mysqli_stmt_bind_param($stmt_check, "iss", $habitacion_id, $fecha_entrada, $fecha_salida);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_bind_result($stmt_check, $count_reservas);
        mysqli_stmt_fetch($stmt_check);
        mysqli_stmt_close($stmt_check);

        if ($count_reservas > 0) {
            mysqli_rollback($link); 
            header("location: ../reservas.php?error=no_disponible");
            exit;
        }
    }

    $precio_noche = 0;
    $habitacion_nombre = "";
    $sql_room = "SELECT nombre, precio_noche FROM habitaciones WHERE id = ?";
    if ($stmt_room = mysqli_prepare($link, $sql_room)) {
        mysqli_stmt_bind_param($stmt_room, "i", $habitacion_id);
        mysqli_stmt_execute($stmt_room);
        mysqli_stmt_bind_result($stmt_room, $habitacion_nombre, $precio_noche_db);
        if (mysqli_stmt_fetch($stmt_room)) {
            $precio_noche = $precio_noche_db;
        }
        mysqli_stmt_close($stmt_room);
    }

    if ($precio_noche === 0) {
        header("location: ../reservas.php?error=habitacion_no_valida");
        exit;
    }

    $room_subtotal = $precio_noche * $noches;
    $package_cost = 0;
    $paquete_nombre = "Estándar";
    $extras_detalle = "";

    if ($paquete_tipo === 'custom' && !empty($extra_ids)) {
        $paquete_nombre = "Personalizado";

        $in_query = implode(',', array_map('intval', $extra_ids));

        $sql_extras = "SELECT nombre, precio FROM extras WHERE id IN ($in_query)";
        $result_extras = mysqli_query($link, $sql_extras);

        if ($result_extras) {
            $extras_detalle_array = [];
            while ($row = mysqli_fetch_assoc($result_extras)) {
                $package_cost += $row['precio'];
                $extras_detalle_array[] = $row['nombre'] . ' ($' . $row['precio'] . ')';
            }
            $extras_detalle = implode(", ", $extras_detalle_array);
        }

    } elseif ($paquete_tipo === 'all_inclusive') {
        $paquete_nombre = "Todo incluido";
        if ($habitacion_id != 3) {
            $package_cost = ALL_INCLUSIVE_DAILY_COST * $noches;
            $extras_detalle = "Paquete Todo Incluido por " . $noches . " noches.";
        } else {
            $extras_detalle = "Servicio incluido en la Suite Todo Incluido.";
        }
    }

    $subtotal = $room_subtotal + $package_cost;
    $iva = $subtotal * IVA_RATE;
    $precio_total = $subtotal + $iva;
    $estado = 'confirmada';

    $fecha_reserva = date("Y-m-d H:i:s");

    mysqli_begin_transaction($link);
    $reserva_ok = false;
    $extras_ok = true; 

    try {
        $sql_insert_reserva = "
            INSERT INTO reservas (
                usuario_id,
                habitacion_id,
                fecha_reserva,
                fecha_entrada,
                fecha_salida,
                noches,
                paquete_nombre,
                extras_detalle,
                subtotal,
                iva,
                precio_total,
                estado
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

        $stmt_reserva = mysqli_prepare($link, $sql_insert_reserva);

        $tipos = "iissssssddds";  //Estos son los tipos de datos de lo que se va a enviar a la base de datos

        mysqli_stmt_bind_param($stmt_reserva, $tipos,
            $usuario_id,
            $habitacion_id,
            $fecha_reserva, 
            $fecha_entrada,
            $fecha_salida,
            $noches,
            $paquete_nombre,
            $extras_detalle,
            $subtotal,
            $iva,
            $precio_total,
            $estado
        );

        if (mysqli_stmt_execute($stmt_reserva)) {
            $reserva_id = mysqli_insert_id($link);
            $reserva_ok = true;
            mysqli_stmt_close($stmt_reserva);

            if ($paquete_tipo === 'custom' && !empty($extra_ids) && $reserva_id) {

                $sql_insert_extras = "INSERT INTO reserva_extras (reserva_id, extra_id) VALUES (?, ?)";
                $stmt_extras = mysqli_prepare($link, $sql_insert_extras);

                if ($stmt_extras) {
                    foreach ($extra_ids as $extra_id) {
                        mysqli_stmt_bind_param($stmt_extras, "ii", $reserva_id, $extra_id);
                        if (!mysqli_stmt_execute($stmt_extras)) {
                            $extras_ok = false;
                            break;
                        }
                    }
                    mysqli_stmt_close($stmt_extras);
                } else {
                    $extras_ok = false; 
                }
            }
        }

        if ($reserva_ok && $extras_ok) {
            mysqli_commit($link);
            header("location: ../calificaciones.php?success=reserva_confirmada");
        } else {
            mysqli_rollback($link);
            header("location: ../reservas.php?error=error_al_guardar_extras");
        }

    } catch (Exception $e) {
        mysqli_rollback($link);
        header("location: ../reservas.php?error=error_inesperado");
    }
}
mysqli_close($link);
?>