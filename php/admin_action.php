<?php
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION['es_admin']) {
    header("location: ../inicio.html?error=acceso_denegado");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserva_id = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (!$reserva_id || empty($action)) {
        header("location: ../admin.php?error=datos_invalidos");
        exit;
    }

    $nuevo_estado = '';
    if ($action === 'cancelar') {
        $nuevo_estado = 'cancelada';
    } 

    if ($nuevo_estado) {
        $sql = "UPDATE reservas SET estado = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $reserva_id);
            if (mysqli_stmt_execute($stmt)) {
                header("location: ../admin.php?success=reserva_actualizada");
            } else {
                header("location: ../admin.php?error=error_bd_update");
            }
            mysqli_stmt_close($stmt);
        }
    }
}
mysqli_close($link);
?>