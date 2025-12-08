<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, nombre, password, es_admin FROM usuarios WHERE email = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nombre, $hashed_password, $es_admin);
            mysqli_stmt_fetch($stmt);

            if (password_verify($password, $hashed_password)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["nombre"] = $nombre;
                $_SESSION["es_admin"] = $es_admin; 
            
                if ($es_admin) {
                    header("location: ../admin.php"); 
                } else {
                    header("location: ../index.php"); 
                }
            } else {
                header("location: ../inicio.html?error=credenciales_invalidas");
            }
        } else {
            header("location: ../inicio.html?error=credenciales_invalidas");
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($link);
?>