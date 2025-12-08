<?php
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../inicio.html?error=requiere_login");
    exit;
}

$usuario_id = $_SESSION["id"];
$reservas = [];

$sql = "SELECT id, habitacion_id, fecha_entrada, fecha_salida, noches, paquete_nombre, precio_total, estado, fecha_reserva 
        FROM reservas 
        WHERE usuario_id = ? 
        ORDER BY fecha_reserva DESC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $row['fecha_reserva_fmt'] = date('d/m/Y', strtotime($row['fecha_reserva']));
        $row['fecha_entrada_fmt'] = date('d/m/Y', strtotime($row['fecha_entrada']));
        $row['precio_total_fmt'] = number_format($row['precio_total'], 2, '.', ',');
        $reservas[] = $row;
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>