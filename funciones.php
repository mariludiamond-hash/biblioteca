<?php
declare(strict_types=1);

/**
 * Funciones principales del sistema de biblioteca.
 *
 * Este archivo concentra la lógica de negocio y las operaciones
 * de acceso a datos relacionadas con libros, usuarios y préstamos.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/exceptions.php';

/**
 * Escapa texto para salida HTML segura.
 *
 * @param string|null $texto Texto a escapar.
 * @return string Texto escapado para HTML.
 */
function e(?string $texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Registra un mensaje de error en el archivo de log.
 *
 * @param string $mensaje Mensaje de error a registrar.
 * @return void
 */
function registrar_error(string $mensaje): void
{
    error_log(
        date('[Y-m-d H:i:s] ') . $mensaje . PHP_EOL,
        3,
        ERROR_LOG_FILE
    );
}

/**
 * Valida el formato de un correo electrónico.
 *
 * @param string $email Correo a validar.
 * @return bool Retorna true si el correo es válido.
 */
function validar_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Registra un nuevo libro en la base de datos.
 *
 * @param string $isbn ISBN único del libro.
 * @param string $titulo Título del libro.
 * @param string $autor Autor del libro.
 * @param int $copias Cantidad de copias disponibles.
 * @return string Mensaje de resultado de la operación.
 */
function registrar_libro(string $isbn, string $titulo, string $autor, int $copias): string
{
    global $conexion;

    $isbn = trim($isbn);
    $titulo = trim($titulo);
    $autor = trim($autor);

    if ($isbn === '' || $titulo === '' || $autor === '') {
        return 'Error: todos los campos del libro son obligatorios.';
    }

    if ($copias < 0) {
        return 'Error: las copias disponibles no pueden ser negativas.';
    }

    try {
        $sql = 'INSERT INTO libros (isbn, titulo, autor, estado, copias_disponibles)
                VALUES (:isbn, :titulo, :autor, :estado, :copias)';

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':isbn' => $isbn,
            ':titulo' => $titulo,
            ':autor' => $autor,
            ':estado' => $copias > 0 ? 'disponible' : 'prestado',
            ':copias' => $copias,
        ]);

        return 'Libro registrado correctamente.';
    } catch (PDOException $e) {
        registrar_error('Error al registrar libro: ' . $e->getMessage());
        return 'Error: no se pudo registrar el libro. Verifica que el ISBN no esté duplicado.';
    }
}

/**
 * Busca libros por coincidencia en ISBN, título o autor.
 *
 * @param string $termino Término de búsqueda.
 * @return array<int, array<string, mixed>> Lista de libros encontrados.
 */
function buscar_libros(string $termino): array
{
    global $conexion;

    $termino = trim($termino);

    try {
        $sql = 'SELECT *
                FROM libros
                WHERE isbn LIKE :termino
                   OR titulo LIKE :termino
                   OR autor LIKE :termino
                ORDER BY id DESC';

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':termino' => '%' . $termino . '%',
        ]);

        /** @var array<int, array<string, mixed>> $libros */
        $libros = $stmt->fetchAll();
        return $libros;
    } catch (PDOException $e) {
        registrar_error('Error al buscar libros: ' . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene la información de un libro por su identificador.
 *
 * @param int $libro_id Identificador del libro.
 * @return array<string, mixed>|null Datos del libro o null si no existe.
 */
function obtener_libro(int $libro_id): ?array
{
    global $conexion;

    try {
        $sql = 'SELECT * FROM libros WHERE id = :id';
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id' => $libro_id,
        ]);

        $libro = $stmt->fetch();
        return $libro !== false ? $libro : null;
    } catch (PDOException $e) {
        registrar_error('Error al obtener libro: ' . $e->getMessage());
        return null;
    }
}

/**
 * Obtiene el catálogo completo de libros.
 *
 * @return array<int, array<string, mixed>> Lista de libros.
 */
function obtener_todos_libros(): array
{
    global $conexion;

    try {
        $sql = 'SELECT * FROM libros ORDER BY id DESC';
        $stmt = $conexion->query($sql);

        /** @var array<int, array<string, mixed>> $libros */
        $libros = $stmt->fetchAll();
        return $libros;
    } catch (PDOException $e) {
        registrar_error('Error al obtener libros: ' . $e->getMessage());
        return [];
    }
}

/**
 * Registra un nuevo usuario en la base de datos.
 *
 * @param string $nombre Nombre completo del usuario.
 * @param string $email Correo electrónico del usuario.
 * @param string $tipo Tipo de usuario: estudiante o profesor.
 * @return string Mensaje de resultado de la operación.
 */
function registrar_usuario(string $nombre, string $email, string $tipo): string
{
    global $conexion;

    $nombre = trim($nombre);
    $email = trim($email);
    $tipo = trim($tipo);

    if ($nombre === '' || $email === '' || $tipo === '') {
        return 'Error: todos los campos del usuario son obligatorios.';
    }

    if (!validar_email($email)) {
        return 'Error: el formato del correo no es válido.';
    }

    if (!in_array($tipo, ['estudiante', 'profesor'], true)) {
        return 'Error: el tipo de usuario no es válido.';
    }

    try {
        $sql = 'INSERT INTO usuarios (nombre, email, tipo, estado)
                VALUES (:nombre, :email, :tipo, :estado)';

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':tipo' => $tipo,
            ':estado' => 'activo',
        ]);

        return 'Usuario registrado correctamente.';
    } catch (PDOException $e) {
        registrar_error('Error al registrar usuario: ' . $e->getMessage());
        return 'Error: no se pudo registrar el usuario. Verifica que el correo no esté duplicado.';
    }
}

/**
 * Verifica si un usuario existe y se encuentra activo.
 *
 * @param int $usuario_id Identificador del usuario.
 * @return bool True si el usuario está activo.
 */
function verificar_usuario_activo(int $usuario_id): bool
{
    global $conexion;

    try {
        $sql = 'SELECT estado FROM usuarios WHERE id = :id';
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id' => $usuario_id,
        ]);

        $usuario = $stmt->fetch();
        return $usuario !== false && $usuario['estado'] === 'activo';
    } catch (PDOException $e) {
        registrar_error('Error al verificar usuario: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene el listado completo de usuarios.
 *
 * @return array<int, array<string, mixed>> Lista de usuarios.
 */
function obtener_todos_usuarios(): array
{
    global $conexion;

    try {
        $sql = 'SELECT * FROM usuarios ORDER BY id DESC';
        $stmt = $conexion->query($sql);

        /** @var array<int, array<string, mixed>> $usuarios */
        $usuarios = $stmt->fetchAll();
        return $usuarios;
    } catch (PDOException $e) {
        registrar_error('Error al obtener usuarios: ' . $e->getMessage());
        return [];
    }
}

/**
 * Realiza un préstamo validando usuario activo, existencia del libro
 * y disponibilidad de copias.
 *
 * @param int $libro_id Identificador del libro.
 * @param int $usuario_id Identificador del usuario.
 * @return string Mensaje de resultado de la operación.
 */
function realizar_prestamo(int $libro_id, int $usuario_id): string
{
    global $conexion;

    if ($libro_id <= 0 || $usuario_id <= 0) {
        return 'Error: selecciona un libro y un usuario válidos.';
    }

    try {
        $conexion->beginTransaction();

        if (!verificar_usuario_activo($usuario_id)) {
            throw new UsuarioSuspendidoError('El usuario está suspendido o no existe.');
        }

        $libro = obtener_libro($libro_id);

        if ($libro === null) {
            throw new LibroNoEncontradoError('El libro no existe.');
        }

        if ((int)$libro['copias_disponibles'] <= 0) {
            throw new SinStockError('No hay copias disponibles de este libro.');
        }

        $sqlPrestamo = 'INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado)
                        VALUES (:libro_id, :usuario_id, CURDATE(), :estado)';
        $stmtPrestamo = $conexion->prepare($sqlPrestamo);
        $stmtPrestamo->execute([
            ':libro_id' => $libro_id,
            ':usuario_id' => $usuario_id,
            ':estado' => 'activo',
        ]);

        $sqlActualizarCopias = 'UPDATE libros
                                SET copias_disponibles = copias_disponibles - 1
                                WHERE id = :id';
        $stmtActualizarCopias = $conexion->prepare($sqlActualizarCopias);
        $stmtActualizarCopias->execute([
            ':id' => $libro_id,
        ]);

        $sqlConsultar = 'SELECT copias_disponibles FROM libros WHERE id = :id';
        $stmtConsultar = $conexion->prepare($sqlConsultar);
        $stmtConsultar->execute([
            ':id' => $libro_id,
        ]);

        $libroActualizado = $stmtConsultar->fetch();

        if ($libroActualizado === false) {
            throw new LibroNoEncontradoError('No fue posible actualizar el estado del libro.');
        }

        $nuevoEstado = ((int)$libroActualizado['copias_disponibles'] > 0) ? 'disponible' : 'prestado';

        $sqlActualizarEstado = 'UPDATE libros SET estado = :estado WHERE id = :id';
        $stmtActualizarEstado = $conexion->prepare($sqlActualizarEstado);
        $stmtActualizarEstado->execute([
            ':estado' => $nuevoEstado,
            ':id' => $libro_id,
        ]);

        $conexion->commit();
        return 'Préstamo realizado correctamente.';
    } catch (LibroNoEncontradoError | UsuarioSuspendidoError | SinStockError $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        registrar_error('Error de negocio en préstamo: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    } catch (Throwable $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        registrar_error('Error técnico al realizar préstamo: ' . $e->getMessage());
        return 'Error interno del sistema al procesar el préstamo.';
    }
}

/**
 * Registra la devolución de un libro y actualiza su disponibilidad.
 *
 * @param int $prestamo_id Identificador del préstamo.
 * @return string Mensaje de resultado de la operación.
 */
function devolver_libro(int $prestamo_id): string
{
    global $conexion;

    if ($prestamo_id <= 0) {
        return 'Error: el identificador del préstamo no es válido.';
    }

    try {
        $conexion->beginTransaction();

        $sqlPrestamo = 'SELECT * FROM prestamos WHERE id = :id AND estado = :estado';
        $stmtPrestamo = $conexion->prepare($sqlPrestamo);
        $stmtPrestamo->execute([
            ':id' => $prestamo_id,
            ':estado' => 'activo',
        ]);

        $prestamo = $stmtPrestamo->fetch();

        if ($prestamo === false) {
            throw new Exception('El préstamo no existe o ya fue devuelto.');
        }

        $sqlActualizarPrestamo = 'UPDATE prestamos
                                  SET fecha_devolucion = CURDATE(), estado = :estado
                                  WHERE id = :id';
        $stmtActualizarPrestamo = $conexion->prepare($sqlActualizarPrestamo);
        $stmtActualizarPrestamo->execute([
            ':estado' => 'devuelto',
            ':id' => $prestamo_id,
        ]);

        $sqlActualizarLibro = 'UPDATE libros
                               SET copias_disponibles = copias_disponibles + 1,
                                   estado = :estado
                               WHERE id = :libro_id';
        $stmtActualizarLibro = $conexion->prepare($sqlActualizarLibro);
        $stmtActualizarLibro->execute([
            ':estado' => 'disponible',
            ':libro_id' => (int)$prestamo['libro_id'],
        ]);

        $conexion->commit();
        return 'Libro devuelto correctamente.';
    } catch (Throwable $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        registrar_error('Error al devolver libro: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
}

/**
 * Obtiene el historial completo de préstamos con datos del libro y usuario.
 *
 * @return array<int, array<string, mixed>> Lista de préstamos.
 */
function obtener_prestamos(): array
{
    global $conexion;

    try {
        $sql = 'SELECT
                    p.id,
                    l.titulo,
                    u.nombre,
                    p.fecha_prestamo,
                    p.fecha_devolucion,
                    p.estado
                FROM prestamos p
                INNER JOIN libros l ON p.libro_id = l.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.id DESC';

        $stmt = $conexion->query($sql);

        /** @var array<int, array<string, mixed>> $prestamos */
        $prestamos = $stmt->fetchAll();
        return $prestamos;
    } catch (PDOException $e) {
        registrar_error('Error al obtener préstamos: ' . $e->getMessage());
        return [];
    }
}
?>