<?php
declare(strict_types=1);

/**
 * Configuración general de la conexión a la base de datos.
 *
 * Este archivo crea una conexión PDO reutilizable para todo el sistema.
 * También configura el modo de errores por excepciones y el charset UTF-8.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Host del servidor de base de datos.
 */
$host = 'localhost';

/**
 * Nombre de la base de datos.
 */
$dbname = 'biblioteca_db';

/**
 * Usuario de MySQL.
 */
$username = 'root';

/**
 * Contraseña de MySQL.
 */
$password = '';

/**
 * Ruta del archivo de errores.
 */
define('ERROR_LOG_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'errors.log');

try {
    $conexion = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log(
        date('[Y-m-d H:i:s] ') . 'Error de conexión: ' . $e->getMessage() . PHP_EOL,
        3,
        ERROR_LOG_FILE
    );

    die('Error de conexión con la base de datos.');
}
?>