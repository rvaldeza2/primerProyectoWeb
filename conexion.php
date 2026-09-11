<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "proyecto";
$port = 3307; 

$conexion = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexion) {
    die("Error de Conexión: " . mysqli_connect_error());
} else {
    echo "Conexión Exitosa!";
}
