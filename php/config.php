<?php
define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', 'rockstay_db');

define('IVA_RATE', 0.16);

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: No se pudo conectar a la BD. " . mysqli_connect_error());
}

session_start();
?>