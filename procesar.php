// CREATE

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

$nombre_estudiante = mysqli_real_escape_string($conexion, $_POST['nombre_estudiante']);
$correo            = mysqli_real_escape_string($conexion, $_POST['correo']);
$telefono          = mysqli_real_escape_string($conexion, $telefono);
$curso             = mysqli_real_escape_string($conexion, $_POST['curso']);
$fecha_inscripcion = mysqli_real_escape_string($conexion, $_POST['fecha_inscripcion']);

$sql = "INSERT INTO inscripciones (nombre_estudiante, correo, telefono, curso, fecha_inscripcion)
        VALUES ('$nombre_estudiante', '$correo', '$telefono', '$curso', '$fecha_inscripcion')";

mysqli_query($conexion, $sql);

header("Location: index.php");
exit;