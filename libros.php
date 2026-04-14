<?php
declare(strict_types=1);

require_once "funciones.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isbn = trim($_POST["isbn"] ?? "");
    $titulo = trim($_POST["titulo"] ?? "");
    $autor = trim($_POST["autor"] ?? "");
    $copias = (int)($_POST["copias"] ?? 1);

    if ($isbn !== "" && $titulo !== "" && $autor !== "" && $copias >= 0) {
        $mensaje = registrar_libro($isbn, $titulo, $autor, $copias);
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}

$libros = obtener_todos_libros();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libros</title>
    <style>
        body { font-family: Arial; background: #f8f9fa; padding: 20px; }
        form, table { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        input, button { padding: 10px; margin: 5px; width: 95%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .mensaje { color: darkgreen; font-weight: bold; }
        .volver { display:inline-block; margin-top:10px; }
    </style>
</head>
<body>
    <h1>Gestión de Libros</h1>

    <?php if ($mensaje !== ""): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="isbn" placeholder="ISBN" required>
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="text" name="autor" placeholder="Autor" required>
        <input type="number" name="copias" placeholder="Copias disponibles" min="0" required>
        <button type="submit">Registrar libro</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>ISBN</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Estado</th>
            <th>Copias</th>
        </tr>
        <?php foreach ($libros as $libro): ?>
            <tr>
                <td><?= $libro["id"] ?></td>
                <td><?= htmlspecialchars($libro["isbn"]) ?></td>
                <td><?= htmlspecialchars($libro["titulo"]) ?></td>
                <td><?= htmlspecialchars($libro["autor"]) ?></td>
                <td><?= htmlspecialchars($libro["estado"]) ?></td>
                <td><?= $libro["copias_disponibles"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a class="volver" href="index.php">Volver al menú</a>
</body>
</html>