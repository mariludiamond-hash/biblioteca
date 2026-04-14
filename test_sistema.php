<?php
declare(strict_types=1);

/**
 * Archivo de pruebas básicas del sistema de biblioteca.
 *
 * Estas pruebas permiten verificar manualmente la conexión,
 * el registro de datos y las reglas principales del módulo de préstamos.
 */

require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/exceptions.php';

echo "<h1>Pruebas del Sistema de Biblioteca</h1>";

echo "<h2>1. Validación de email</h2>";
$emailValido = validar_email('usuario@correo.com');
$emailInvalido = validar_email('usuario-correo.com');

echo $emailValido ? "Correo válido: OK<br>" : "Correo válido: ERROR<br>";
echo !$emailInvalido ? "Correo inválido detectado: OK<br>" : "Correo inválido detectado: ERROR<br>";

echo "<h2>2. Registro de libro</h2>";
echo registrar_libro('9781234567890', 'Ingeniería de Software', 'Roger Pressman', 3) . "<br>";

echo "<h2>3. Registro de usuario</h2>";
echo registrar_usuario('María López', 'maria@example.com', 'estudiante') . "<br>";

echo "<h2>4. Verificar usuario activo</h2>";
echo verificar_usuario_activo(1) ? "Usuario activo: OK<br>" : "Usuario activo: ERROR<br>";

echo "<h2>5. Realizar préstamo</h2>";
echo realizar_prestamo(1, 1) . "<br>";

echo "<h2>6. Historial de préstamos</h2>";
$prestamos = obtener_prestamos();

if (count($prestamos) > 0) {
    echo "Préstamos encontrados: " . count($prestamos) . "<br>";
} else {
    echo "No se encontraron préstamos.<br>";
}

echo "<h2>7. Devolver libro</h2>";
echo devolver_libro(1) . "<br>";
?>