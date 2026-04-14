<?php
declare(strict_types=1);

/**
 * Archivo de excepciones personalizadas del sistema de biblioteca.
 *
 * Estas clases permiten diferenciar errores de negocio
 * de errores técnicos o de validación general.
 */

/**
 * Se lanza cuando un libro no existe en la base de datos.
 */
class LibroNoEncontradoError extends Exception
{
}

/**
 * Se lanza cuando un usuario suspendido intenta realizar un préstamo.
 */
class UsuarioSuspendidoError extends Exception
{
}

/**
 * Se lanza cuando no hay copias disponibles de un libro.
 */
class SinStockError extends Exception
{
}
?>