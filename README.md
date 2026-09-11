# Guía de estudio — Proyecto Inscripción a Cursos
### Rui Luis Valdez Arevalo

Esta guía explica **qué hace cada línea**, tanto de PHP como de HTML,
de los 9 archivos del proyecto. Úsala para repasar antes de tu
exposición — si te preguntan "¿qué hace esta línea?", aquí está la
respuesta.

---

## 1. conexion.php

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "desarrolloweb";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
```

**No tiene HTML** — es un archivo puramente lógico, nunca se visita
directamente en el navegador.

| Línea | Qué hace |
|---|---|
| `$host`, `$user`, `$pass`, `$db` | Variables con los datos de acceso a MySQL |
| `mysqli_connect(...)` | Intenta conectar. Devuelve un objeto de conexión, o `false` si falla |
| `if (!$conexion)` | Si falló la conexión (`$conexion` es `false`), entra aquí |
| `die(...)` | Imprime el mensaje y **detiene todo el script** inmediatamente |
| `mysqli_connect_error()` | Devuelve el motivo exacto del fallo (útil para depurar) |
| `mysqli_set_charset(..., "utf8mb4")` | Configura la codificación para que acentos y ñ se guarden bien |

**Por qué existe como archivo separado:** para no repetir esta lógica
en cada archivo — solo se hace `require "conexion.php";` y ya
tenemos `$conexion` lista para usar.

---

## 2. login.php

### Parte PHP (arriba del todo)

```php
<?php
session_start();
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
```

| Línea | Qué hace |
|---|---|
| `session_start()` | Activa el sistema de sesiones de PHP. **Debe ir siempre primero**, antes de cualquier salida HTML |
| `$_SESSION['error'] ?? null` | Si existe un mensaje de error guardado en sesión, lo toma; si no existe, usa `null` en vez de dar error |
| `unset($_SESSION['error'])` | Borra ese mensaje de la sesión — para que no se quede mostrando siempre, solo la primera vez |

### Parte HTML

```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
```
Importa el framework Bootstrap desde un CDN (servidor externo), para
tener estilos ya hechos (botones, formularios, alertas) sin escribir
CSS propio.

```html
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
```
- `<?php if ($error): ?> ... <?php endif; ?>` es la sintaxis
  alternativa de PHP para mezclarlo con HTML (en vez de `{ }`, se
  usa `:` y `endif;`). Más fácil de leer dentro de HTML.
- Si `$error` tiene un valor (no es `null`), muestra una caja roja
  de Bootstrap (`alert alert-danger`) con el mensaje.
- `<?= $error ?>` es lo mismo que `<?php echo $error; ?>`, pero corto.

```html
<form method="POST" action="procesar_login.php" class="needs-validation" novalidate>
    <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
        <div class="invalid-feedback">Ingresa tu usuario.</div>
    </div>
    ...
    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
</form>
```

| Atributo/clase | Qué hace |
|---|---|
| `method="POST"` | Los datos viajan ocultos en el cuerpo de la petición, no en la URL |
| `action="procesar_login.php"` | A dónde se envían los datos al hacer submit |
| `class="needs-validation"` + `novalidate` | Le dice al navegador "no valides tú solo", para que Bootstrap + JS controlen la validación visual |
| `name="usuario"` | El nombre con el que ese campo llegará a `$_POST` (`$_POST['usuario']`) |
| `class="form-control"` | Estilo de Bootstrap para inputs |
| `required` | El campo no puede quedar vacío |
| `class="invalid-feedback"` | Texto que Bootstrap muestra en rojo si el campo es inválido y el formulario ya se intentó enviar |
| `class="mb-3"` | Margen inferior (utilidad de espaciado de Bootstrap) |
| `w-100` | Ancho 100% (el botón ocupa todo el ancho disponible) |

### Parte JavaScript

```javascript
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
```
| Línea | Qué hace |
|---|---|
| `querySelectorAll('.needs-validation')` | Busca todos los formularios con esa clase |
| `addEventListener('submit', ...)` | Ejecuta código cuando el formulario intenta enviarse |
| `form.checkValidity()` | Pregunta al navegador: "¿todos los campos `required` están llenos correctamente?" |
| `event.preventDefault()` | Si NO es válido, cancela el envío (el formulario no llega a PHP) |
| `classList.add('was-validated')` | Le agrega esta clase al formulario, lo que hace que Bootstrap **muestre visualmente** los mensajes `.invalid-feedback` en rojo |

---

## 3. procesar_login.php

**No tiene HTML** — solo procesa datos y redirige. Nunca se ve
directamente en el navegador (aparece y desaparece en microsegundos).

```php
<?php
session_start();
require "conexion.php";

$usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
$contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
```
| Línea | Qué hace |
|---|---|
| `$_POST['usuario']` | Captura el dato que llegó del formulario de `login.php` |
| `mysqli_real_escape_string()` | "Limpia" el texto para que no pueda alterar la sintaxis SQL (previene inyección SQL) |

```php
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) === 1) {
```
| Línea | Qué hace |
|---|---|
| `SELECT * FROM usuarios WHERE ...` | Busca una fila donde coincidan usuario Y contraseña exactamente |
| `mysqli_query(...)` | Ejecuta la consulta, devuelve un objeto resultado |
| `mysqli_num_rows($resultado)` | Cuenta cuántas filas trajo la consulta |
| `=== 1` | Compara tipo y valor exactos (más seguro que `==`) — si es exactamente 1 fila, el login es válido |

```php
    $fila = mysqli_fetch_assoc($resultado);
    $_SESSION['usuario_id'] = $fila['id'];
    $_SESSION['usuario_nombre'] = $fila['usuario'];
    header("Location: index.php");
} else {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: login.php");
}
exit;
```
| Línea | Qué hace |
|---|---|
| `mysqli_fetch_assoc($resultado)` | Convierte la fila encontrada en un array asociativo: `$fila['id']`, `$fila['usuario']` |
| `$_SESSION['usuario_id'] = ...` | Guarda el ID del usuario en la sesión — esto "marca" al navegador como logueado |
| `header("Location: index.php")` | Redirige al navegador a otra página (redirección del servidor) |
| `exit;` | Detiene la ejecución del script — obligatorio después de un `header()` de redirección |

---

## 4. index.php

### Parte PHP — protección de sesión

```php
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
```
| Línea | Qué hace |
|---|---|
| `isset($_SESSION['usuario_id'])` | Revisa si esa variable de sesión existe |
| `!isset(...)` | "Si NO existe" |
| Si no existe | Significa que nadie inició sesión → redirige a `login.php` de inmediato |

Este bloque de 4 líneas se repite igual en `procesar.php`, `editar.php`,
`actualizar.php`, `eliminar.php` — es el "guardia" de seguridad de
cada operación.

### Parte PHP — traer los datos

```php
require "conexion.php";
$sql = "SELECT * FROM inscripciones ORDER BY id DESC";
$resultado = mysqli_query($conexion, $sql);
```
`ORDER BY id DESC` ordena del más reciente al más antiguo (orden
descendente por ID).

### Parte HTML — encabezado

```html
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Inscripción a Cursos</h1>
    <div>
        <span class="me-2">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
    </div>
</div>
```
| Clase/línea | Qué hace |
|---|---|
| `d-flex` | Convierte el div en un contenedor flexbox (elementos en fila) |
| `justify-content-between` | Separa los elementos hijos a los extremos (título a la izquierda, botón a la derecha) |
| `align-items-center` | Centra verticalmente los elementos |
| `htmlspecialchars($_SESSION['usuario_nombre'])` | Muestra el nombre del usuario logueado, protegido contra XSS |
| `btn-outline-danger btn-sm` | Botón rojo con solo borde (outline), tamaño pequeño |

### Parte HTML — formulario CREATE

```html
<form method="POST" action="procesar.php" class="row g-2 needs-validation" novalidate>
    <div class="col-md-3">
        <input type="text" name="nombre_estudiante" class="form-control" placeholder="Nombre del estudiante" required>
    </div>
    ...
</form>
```
| Clase | Qué hace |
|---|---|
| `row` | Bootstrap: convierte el contenedor en una fila del sistema de grid |
| `g-2` | Espaciado (gap) entre columnas |
| `col-md-3` | La columna ocupa 3 de 12 espacios del grid en pantallas medianas o más grandes (es decir, 1/4 del ancho) |
| `placeholder="..."` | Texto gris de ejemplo dentro del input, desaparece al escribir |

### Parte HTML/PHP — la tabla (lo más importante para explicar)

```html
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
    <tr>
        <td><?= htmlspecialchars($fila['nombre_estudiante']) ?></td>
        ...
        <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
        <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-outline-danger"
           onclick="return confirm('¿Eliminar esta inscripción?');">Eliminar</a>
    </tr>
<?php endwhile; ?>
```

**Esta es la parte que más te van a preguntar en la exposición:**

- `while ($fila = mysqli_fetch_assoc($resultado))`:
  - Cada vuelta del ciclo, `mysqli_fetch_assoc()` **avanza** al
    siguiente registro del resultado y lo entrega como array.
  - La misma línea sirve como **asignación** (guarda en `$fila`) y
    **condición** (el `while` evalúa si ese valor es verdadero).
  - Cuando ya no hay más filas, `mysqli_fetch_assoc()` devuelve
    `false`, y el `while` se detiene automáticamente.
- `<?= $fila['id'] ?>` dentro del `href` — imprime el ID directamente
  en la URL del link, generando algo como `editar.php?id=5`.
- `onclick="return confirm('...')"` — es **JavaScript**, no PHP:
  - `confirm()` abre un cuadro de diálogo nativo del navegador con
    "Aceptar" / "Cancelar".
  - Si el usuario presiona "Cancelar", `confirm()` devuelve `false`.
  - Como el `onclick` tiene `return` antes, ese `false` **cancela la
    navegación** — el navegador nunca llega a visitar `eliminar.php`.

---

## 5. procesar.php

**No tiene HTML.**

```php
$telefono = $_POST['telefono'];

if (!is_numeric($telefono)) {
    die("El teléfono debe contener solo números.");
}
```
| Línea | Qué hace |
|---|---|
| `is_numeric($telefono)` | Revisa si el valor podría interpretarse como número (acepta enteros, decimales) |
| `!is_numeric(...)` | Si NO es numérico |
| `die(...)` | Detiene todo con un mensaje — nunca llega a insertarse en la base |

**Por qué esta validación es la que realmente importa:** la
validación de Bootstrap/JS del formulario se puede saltar
fácilmente (deshabilitando JavaScript, o enviando la petición
directamente). Esta validación en PHP ocurre **en el servidor**, y
es la única que no se puede evadir desde el navegador.

```php
$sql = "INSERT INTO inscripciones (nombre_estudiante, correo, telefono, curso, fecha_inscripcion)
        VALUES ('$nombre_estudiante', '$correo', '$telefono', '$curso', '$fecha_inscripcion')";

mysqli_query($conexion, $sql);
header("Location: index.php");
exit;
```
`INSERT INTO tabla (columnas) VALUES (valores)` — sintaxis SQL
estándar para crear un nuevo registro. Este es el **Create** del CRUD.

---

## 6. editar.php

### Parte PHP

```php
$id = $_GET['id'];
$sql = "SELECT * FROM inscripciones WHERE id = '$id'";
$resultado = mysqli_query($conexion, $sql);
$fila = mysqli_fetch_assoc($resultado);
```
| Línea | Qué hace |
|---|---|
| `$_GET['id']` | Captura el dato que viene **en la URL** (después del `?`), a diferencia de `$_POST` que viene oculto en el formulario |
| `WHERE id = '$id'` | Filtra para traer solo ese registro específico |
| `mysqli_fetch_assoc($resultado)` (fuera de un `while`) | Se llama una sola vez porque sabemos que solo hay **una** fila con ese ID (es llave primaria, único) |

### Parte HTML

```html
<input type="hidden" name="id" value="<?= $fila['id'] ?>">
<input type="text" name="nombre_estudiante" class="form-control"
       value="<?= htmlspecialchars($fila['nombre_estudiante']) ?>" required>
```
| Atributo | Qué hace |
|---|---|
| `type="hidden"` | Campo invisible para el usuario, pero **sí viaja** con el resto del formulario. Sirve para "arrastrar" el ID hasta `actualizar.php` |
| `value="<?= ... ?>"` | Precarga el campo con el dato actual guardado en la base — así el usuario ve la información existente antes de modificarla |

---

## 7. actualizar.php

```php
$sql = "UPDATE inscripciones SET
            nombre_estudiante = '$nombre_estudiante',
            correo = '$correo',
            telefono = '$telefono',
            curso = '$curso',
            fecha_inscripcion = '$fecha_inscripcion'
        WHERE id = '$id'";
```
**La parte más delicada de todo el proyecto:**
- `UPDATE tabla SET columna = valor WHERE condición` modifica solo
  las filas que cumplan la condición del `WHERE`.
- Si se **omitiera** el `WHERE id = '$id'`, esta consulta
  modificaría **todos** los registros de la tabla con esos mismos
  valores — un error grave y común en SQL.
- El `$id` viene del campo `hidden` que vimos en `editar.php`,
  asegurando que solo se actualice la fila correcta.

---

## 8. eliminar.php

```php
$id = mysqli_real_escape_string($conexion, $_GET['id']);
$sql = "DELETE FROM inscripciones WHERE id = '$id'";
mysqli_query($conexion, $sql);
```
Mismo patrón que `actualizar.php`: `DELETE FROM tabla WHERE
condición` — y de nuevo, el `WHERE` es indispensable, o se borraría
toda la tabla.

---

## 9. logout.php

```php
<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
```
| Línea | Qué hace |
|---|---|
| `session_destroy()` | Borra **toda** la información de la sesión activa (usuario_id, usuario_nombre, todo) |
| Después de esto | Si el usuario intenta ir a `index.php`, el `isset($_SESSION['usuario_id'])` falla y es enviado de vuelta al login |

---

## El patrón que se repite (memorízalo, es la idea central)

```
1. session_start()                       -> activa el sistema de sesiones
2. if (!isset($_SESSION['usuario_id']))  -> verifica que esté logueado
3. require "conexion.php"                -> obtiene $conexion
4. Limpiar datos recibidos                -> mysqli_real_escape_string(), is_numeric()
5. Construir y ejecutar el SQL            -> SELECT / INSERT / UPDATE / DELETE
6. header("Location: ...") + exit         -> redirige a otra página
```

## Preguntas que probablemente te hagan (y cómo responderlas)

**¿Por qué usas $_GET en unos casos y $_POST en otros?**
`$_GET` se usa cuando el dato viaja en la URL (como el `id` en los
links de Editar/Eliminar) — es visible y se puede compartir como
enlace. `$_POST` se usa para datos de formularios que no deben verse
en la URL (como usuario/contraseña), y que suelen modificar datos.

**¿Qué pasa si alguien entra directo a index.php sin loguearse?**
El `isset($_SESSION['usuario_id'])` lo detecta y lo redirige
automáticamente a `login.php` — nunca ve el contenido protegido.

**¿Por qué usas htmlspecialchars()?**
Para evitar que si alguien guardó código HTML/JavaScript en un
campo (como el nombre), ese código se ejecute al mostrarse en la
tabla — se muestra como texto plano en vez de ejecutarse (previene
XSS).

**¿Dónde se guarda la sesión, se puede ver con F12?**
No. El navegador solo guarda un identificador (cookie PHPSESSID).
El contenido real (usuario_id, usuario_nombre) vive en el servidor,
en un archivo dentro de C:\xampp\tmp\ — inaccesible para cualquiera
que no tenga acceso al servidor.
