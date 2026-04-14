<?php
declare(strict_types=1);

/**
 * Módulo de gestión de libros.
 *
 * Permite registrar libros nuevos y visualizar el catálogo almacenado.
 */

require_once __DIR__ . '/funciones.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = trim((string)($_POST['isbn'] ?? ''));
    $titulo = trim((string)($_POST['titulo'] ?? ''));
    $autor = trim((string)($_POST['autor'] ?? ''));
    $copias = (int)($_POST['copias'] ?? 0);

    $mensaje = registrar_libro($isbn, $titulo, $autor, $copias);
}

$libros = obtener_todos_libros();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros</title>
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

        input, button {
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
            overflow-x: auto;
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
            <h1>Gestión de Libros</h1>

            <?php if ($mensaje !== ''): ?>
                <div class="mensaje"><?= e($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <input type="text" name="isbn" placeholder="ISBN" required>
                    <input type="text" name="titulo" placeholder="Título" required>
                    <input type="text" name="autor" placeholder="Autor" required>
                    <input type="number" name="copias" placeholder="Copias disponibles" min="0" required>
                </div>
                <div style="margin-top: 12px;">
                    <button type="submit">Registrar libro</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Listado de libros</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Estado</th>
                        <th>Copias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($libros) > 0): ?>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?= (int)$libro['id'] ?></td>
                                <td><?= e((string)$libro['isbn']) ?></td>
                                <td><?= e((string)$libro['titulo']) ?></td>
                                <td><?= e((string)$libro['autor']) ?></td>
                                <td><?= e((string)$libro['estado']) ?></td>
                                <td><?= (int)$libro['copias_disponibles'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No hay libros registrados.</td>
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