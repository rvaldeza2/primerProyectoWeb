<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "conexion.php";

$sql = "SELECT * FROM inscripciones ORDER BY id DESC";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción a Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nueva inscripción</h5>

            <form method="POST" action="procesar.php" class="row g-2 needs-validation" novalidate>
                <div class="col-md-3">
                    <input type="text" name="nombre_estudiante" class="form-control" placeholder="Nombre del estudiante" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="correo" class="form-control" placeholder="Correo" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="curso" class="form-control" placeholder="Curso" required>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inscripcion" class="form-control" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Guardar inscripción</button>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped bg-white">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Curso</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['nombre_estudiante']) ?></td>
                    <td><?= htmlspecialchars($fila['correo']) ?></td>
                    <td><?= htmlspecialchars($fila['telefono']) ?></td>
                    <td><?= htmlspecialchars($fila['curso']) ?></td>
                    <td><?= htmlspecialchars($fila['fecha_inscripcion']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta inscripción?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

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