<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "conexion.php";

$telefono = $_POST['telefono'];
if (!is_numeric($telefono)) {
    die("El teléfono debe contener solo números.");
}

$id                = mysqli_real_escape_string($conexion, $_POST['id']);
$nombre_estudiante = mysqli_real_escape_string($conexion, $_POST['nombre_estudiante']);
$correo            = mysqli_real_escape_string($conexion, $_POST['correo']);
$telefono          = mysqli_real_escape_string($conexion, $telefono);
$curso             = mysqli_real_escape_string($conexion, $_POST['curso']);
$fecha_inscripcion = mysqli_real_escape_string($conexion, $_POST['fecha_inscripcion']);

$sql = "UPDATE inscripciones SET
            nombre_estudiante = '$nombre_estudiante',
            correo = '$correo',
            telefono = '$telefono',
            curso = '$curso',
            fecha_inscripcion = '$fecha_inscripcion'
        WHERE id = '$id'";

mysqli_query($conexion, $sql);

header("Location: index.php");
exit;