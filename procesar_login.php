<?php
session_start();
require "conexion.php";

$usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
$contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) === 1) {
    $fila = mysqli_fetch_assoc($resultado);
    $_SESSION['usuario_id'] = $fila['id'];
    $_SESSION['usuario_nombre'] = $fila['usuario'];
    header("Location: index.php");
} else {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: login.php");
}
exit;