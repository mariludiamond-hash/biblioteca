<?php
declare(strict_types=1);

require_once "funciones.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["accion"]) && $_POST["accion"] === "prestar") {
        $libro_id = (int)($_POST["libro_id"] ?? 0);
        $usuario_id = (int)($_POST["usuario_id"] ?? 0);
        $mensaje = realizar_prestamo($libro_id, $usuario_id);
    }

    if (isset($_POST["accion"]) && $_POST["accion"] === "devolver") {
        $prestamo_id = (int)($_POST["prestamo_id"] ?? 0);
        $mensaje = devolver_libro($prestamo_id);
    }
}

$libros = obtener_todos_libros();
$usuarios = obtener_todos_usuarios();
$prestamos = obtener_prestamos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos</title>
    <style>
        body { font-family: Arial; background: #f8f9fa; padding: 20px; }
        form, table { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        select, input, button { padding: 10px; margin: 5px; width: 95%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .mensaje { color: darkgreen; font-weight: bold; }
        h2 { margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Gestión de Préstamos</h1>

    <?php if ($mensaje !== ""): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <h2>Registrar préstamo</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="prestar">

        <select name="libro_id" required>
            <option value="">Selecciona un libro</option>
            <?php foreach ($libros as $libro): ?>
                <option value="<?= $libro['id'] ?>">
                    <?= htmlspecialchars($libro['titulo']) ?> - Copias: <?= $libro['copias_disponibles'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="usuario_id" required>
            <option value="">Selecciona un usuario</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>">
                    <?= htmlspecialchars($usuario['nombre']) ?> - <?= htmlspecialchars($usuario['estado']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Realizar préstamo</button>
    </form>

    <h2>Registrar devolución</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="devolver">
        <input type="number" name="prestamo_id" placeholder="ID del préstamo" required>
        <button type="submit">Devolver libro</button>
    </form>

    <h2>Historial de préstamos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Libro</th>
            <th>Usuario</th>
            <th>Fecha préstamo</th>
            <th>Fecha devolución</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($prestamos as $prestamo): ?>
            <tr>
                <td><?= $prestamo["id"] ?></td>
                <td><?= htmlspecialchars($prestamo["titulo"]) ?></td>
                <td><?= htmlspecialchars($prestamo["nombre"]) ?></td>
                <td><?= htmlspecialchars($prestamo["fecha_prestamo"]) ?></td>
                <td><?= htmlspecialchars($prestamo["fecha_devolucion"] ?? "Pendiente") ?></td>
                <td><?= htmlspecialchars($prestamo["estado"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="index.php">Volver al menú</a>
</body>
</html>