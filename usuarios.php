<?php
declare(strict_types=1);

require_once "funciones.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "estudiante");

    if ($nombre !== "" && $email !== "" && $tipo !== "") {
        $mensaje = registrar_usuario($nombre, $email, $tipo);
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

$usuarios = obtener_todos_usuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <style>
        body { font-family: Arial; background: #f8f9fa; padding: 20px; }
        form, table { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        input, select, button { padding: 10px; margin: 5px; width: 95%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .mensaje { color: darkgreen; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <?php if ($mensaje !== ""): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <select name="tipo" required>
            <option value="estudiante">Estudiante</option>
            <option value="profesor">Profesor</option>
        </select>
        <button type="submit">Registrar usuario</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario["id"] ?></td>
                <td><?= htmlspecialchars($usuario["nombre"]) ?></td>
                <td><?= htmlspecialchars($usuario["email"]) ?></td>
                <td><?= htmlspecialchars($usuario["tipo"]) ?></td>
                <td><?= htmlspecialchars($usuario["estado"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="index.php">Volver al menú</a>
</body>
</html>