<?php
declare(strict_types=1);

/**
 * Página principal del sistema de biblioteca.
 *
 * Muestra el menú principal de navegación hacia los módulos de libros,
 * usuarios y préstamos.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
        }

        .contenedor {
            max-width: 500px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        h1 {
            margin-bottom: 25px;
            color: #1f2937;
        }

        a {
            display: block;
            margin: 12px 0;
            padding: 14px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        a:hover {
            background: #1d4ed8;
        }

        p {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Sistema de Gestión de Biblioteca</h1>
        <p>Selecciona el módulo que deseas administrar.</p>

        <a href="libros.php">Gestión de Libros</a>
        <a href="usuarios.php">Gestión de Usuarios</a>
        <a href="prestamos.php">Gestión de Préstamos</a>
    </div>
</body>
</html>