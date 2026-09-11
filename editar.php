<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "conexion.php";

$id = $_GET['id'];
$sql = "SELECT * FROM inscripciones WHERE id = '$id'";
$resultado = mysqli_query($conexion, $sql);
$fila = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar inscripción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Editar inscripción</h1>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="actualizar.php" class="row g-2 needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= $fila['id'] ?>">

                <div class="col-md-3">
                    <input type="text" name="nombre_estudiante" class="form-control" value="<?= htmlspecialchars($fila['nombre_estudiante']) ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($fila['correo']) ?>" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($fila['telefono']) ?>" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="curso" class="form-control" value="<?= htmlspecialchars($fila['curso']) ?>" required>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inscripcion" class="form-control" value="<?= htmlspecialchars($fila['fecha_inscripcion']) ?>" required>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
})();
</script>

</body>
</html>