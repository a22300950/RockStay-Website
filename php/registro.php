<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($nombre) || empty($email) || empty($password)) {
        header("location: ../inicio.html?error=campos_vacios");
        exit;
    }

    $sql_check = "SELECT id FROM usuarios WHERE email = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) == 1) {
            header("location: ../inicio.html?error=email_existente");
            exit;
        }
        mysqli_stmt_close($stmt_check);
    }

    $sql_insert = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";

    if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt_insert, "sss", $nombre, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt_insert)) {
            header("location: ../inicio.html?success=registro_exitoso");
        } else {
            header("location: ../inicio.html?error=error_bd");
        }
        mysqli_stmt_close($stmt_insert);
    }
}
mysqli_close($link);
?>