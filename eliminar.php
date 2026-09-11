<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "conexion.php";

$id = mysqli_real_escape_string($conexion, $_GET['id']);

$sql = "DELETE FROM inscripciones WHERE id = '$id'";
mysqli_query($conexion, $sql);

header("Location: index.php");
exit;