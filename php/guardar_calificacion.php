<?php
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../inicio.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reserva_id = filter_input(INPUT_POST, 'reserva_id', FILTER_VALIDATE_INT); 
    $calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_INT);
    $comentario = filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_STRING); 

    if (!$reserva_id || $calificacion < 1 || $calificacion > 5) {
        header("location: ../calificaciones.php?error=datos_invalidos");
        exit;
    }

    $sql = "INSERT INTO calificaciones (reserva_id, calificacion, comentario) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), comentario = VALUES(comentario)";
            
    if ($stmt = mysqli_prepare($link, $sql)) {
        $comentario = $comentario ?: 'Sin comentarios.'; 

        mysqli_stmt_bind_param($stmt, "iis", $reserva_id, $calificacion, $comentario);

        if (mysqli_stmt_execute($stmt)) {
            header("location: ../calificaciones.php?success=calificacion_guardada");
        } else {
            header("location: ../calificaciones.php?error=error_al_guardar");
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($link);
?>