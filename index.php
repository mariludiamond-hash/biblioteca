<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Biblioteca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            text-align: center;
            padding: 50px;
        }
        .contenedor {
            background: white;
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        a {
            display: block;
            margin: 15px 0;
            padding: 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Sistema de Biblioteca</h1>
        <a href="libros.php">Gestión de Libros</a>
        <a href="usuarios.php">Gestión de Usuarios</a>
        <a href="prestamos.php">Gestión de Préstamos</a>
    </div>
</body>
</html>