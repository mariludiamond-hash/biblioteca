<?php
declare(strict_types=1);

$host = "localhost";
$dbname = "biblioteca_db";
$username = "root";
$password = "";

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, "errors.log");
    die("Error de conexión con la base de datos.");
}
?>