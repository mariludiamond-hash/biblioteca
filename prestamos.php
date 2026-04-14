<?php
declare(strict_types=1);

/**
 * Módulo de gestión de préstamos.
 *
 * Permite registrar préstamos, devolver libros y consultar el historial.
 */

require_once __DIR__ . '/funciones.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = trim((string)($_POST['accion'] ?? ''));

    if ($accion === 'prestar') {
        $libro_id = (int)($_POST['libro_id'] ?? 0);
        $usuario_id = (int)($_POST['usuario_id'] ?? 0);
        $mensaje = realizar_prestamo($libro_id, $usuario_id);
    }

    if ($accion === 'devolver') {
        $prestamo_id = (int)($_POST['prestamo_id'] ?? 0);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Préstamos</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .contenedor {
            max-width: 1200px;
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
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
        }

        select, input, button {
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
            <h1>Gestión de Préstamos</h1>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <h2>Registrar préstamo</h2>
            <form method="POST" action="">
                <input type="hidden" name="accion" value="prestar">

                <div class="form-grid">
                    <select name="libro_id" required>
                        <option value="">Selecciona un libro</option>
                        <?php foreach ($libros as $libro): ?>
                            <option value="<?= (int)$libro['id'] ?>">
                                <?= e((string)$libro['titulo']) ?> | Copias: <?= (int)$libro['copias_disponibles'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="usuario_id" required>
                        <option value="">Selecciona un usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= (int)$usuario['id'] ?>">
                                <?= e((string)$usuario['nombre']) ?> | Estado: <?= e((string)$usuario['estado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-top: 12px;">
                    <button type="submit">Realizar préstamo</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Registrar devolución</h2>
            <form method="POST" action="">
                <input type="hidden" name="accion" value="devolver">

                <div class="form-grid">
                    <input type="number" name="prestamo_id" placeholder="ID del préstamo" min="1" required>
                </div>

                <div style="margin-top: 12px;">
                    <button type="submit">Devolver libro</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Historial de préstamos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Libro</th>
                        <th>Usuario</th>
                        <th>Fecha préstamo</th>
                        <th>Fecha devolución</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($prestamos) > 0): ?>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr>
                                <td><?= (int)$prestamo['id'] ?></td>
                                <td><?= e((string)$prestamo['titulo']) ?></td>
                                <td><?= e((string)$prestamo['nombre']) ?></td>
                                <td><?= e((string)$prestamo['fecha_prestamo']) ?></td>
                                <td><?= e($prestamo['fecha_devolucion'] !== null ? (string)$prestamo['fecha_devolucion'] : 'Pendiente') ?></td>
                                <td><?= e((string)$prestamo['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No hay préstamos registrados.</td>
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