<?php
declare(strict_types=1);

/**
 * Módulo de gestión de usuarios.
 *
 * Permite registrar usuarios y consultar el listado actual.
 */

require_once __DIR__ . '/funciones.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $tipo = trim((string)($_POST['tipo'] ?? ''));

    $mensaje = registrar_usuario($nombre, $email, $tipo);
}

$usuarios = obtener_todos_usuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .contenedor {
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        h1, h2 {
            color: #1f2937;
        }

        .mensaje {
            padding: 12px;
            border-radius: 8px;
            background: #e0f2fe;
            color: #075985;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        input, select, button {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background: #2563eb;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1d4ed8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .acciones {
            margin-top: 18px;
        }

        .acciones a {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="card">
            <h1>Gestión de Usuarios</h1>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                    <input type="email" name="email" placeholder="Correo electrónico" required>
                    <select name="tipo" required>
                        <option value="">Selecciona el tipo de usuario</option>
                        <option value="estudiante">Estudiante</option>
                        <option value="profesor">Profesor</option>
                    </select>
                </div>
                <div style="margin-top: 12px;">
                    <button type="submit">Registrar usuario</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Listado de usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= (int)$usuario['id'] ?></td>
                                <td><?= e((string)$usuario['nombre']) ?></td>
                                <td><?= e((string)$usuario['email']) ?></td>
                                <td><?= e((string)$usuario['tipo']) ?></td>
                                <td><?= e((string)$usuario['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="acciones">
                <a href="index.php">Volver al menú principal</a>
            </div>
        </div>
    </div>
</body>
</html>