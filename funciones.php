<?php
declare(strict_types=1);

require_once "config.php";

function validar_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function registrar_libro(string $isbn, string $titulo, string $autor, int $copias): string
{
    global $conexion;

    try {
        $sql = "INSERT INTO libros (isbn, titulo, autor, copias_disponibles) 
                VALUES (:isbn, :titulo, :autor, :copias)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':isbn' => $isbn,
            ':titulo' => $titulo,
            ':autor' => $autor,
            ':copias' => $copias
        ]);

        return "Libro registrado correctamente.";
    } catch (PDOException $e) {
        error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, "errors.log");
        return "Error: no se pudo registrar el libro. Verifica que el ISBN no esté duplicado.";
    }
}

function registrar_usuario(string $nombre, string $email, string $tipo): string
{
    global $conexion;

    if (!validar_email($email)) {
        return "Error: el formato del correo no es válido.";
    }

    try {
        $sql = "INSERT INTO usuarios (nombre, email, tipo) 
                VALUES (:nombre, :email, :tipo)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':tipo' => $tipo
        ]);

        return "Usuario registrado correctamente.";
    } catch (PDOException $e) {
        error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, "errors.log");
        return "Error: no se pudo registrar el usuario. Verifica que el email no esté duplicado.";
    }
}

function buscar_libros(string $termino): array
{
    global $conexion;

    $sql = "SELECT * FROM libros
            WHERE titulo LIKE :termino
               OR autor LIKE :termino
               OR isbn LIKE :termino
            ORDER BY id DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':termino' => "%$termino%"
    ]);

    return $stmt->fetchAll();
}

function verificar_usuario_activo(int $usuario_id): bool
{
    global $conexion;

    $sql = "SELECT estado FROM usuarios WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch();

    return $usuario && $usuario['estado'] === 'activo';
}

function obtener_libro(int $libro_id): ?array
{
    global $conexion;

    $sql = "SELECT * FROM libros WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $libro_id]);
    $libro = $stmt->fetch();

    return $libro ?: null;
}

function realizar_prestamo(int $libro_id, int $usuario_id): string
{
    global $conexion;

    try {
        $conexion->beginTransaction();

        if (!verificar_usuario_activo($usuario_id)) {
            throw new Exception("El usuario está suspendido o no existe.");
        }

        $libro = obtener_libro($libro_id);

        if (!$libro) {
            throw new Exception("El libro no existe.");
        }

        if ((int)$libro['copias_disponibles'] <= 0) {
            throw new Exception("No hay copias disponibles de este libro.");
        }

        $sqlPrestamo = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado)
                        VALUES (:libro_id, :usuario_id, CURDATE(), 'activo')";
        $stmtPrestamo = $conexion->prepare($sqlPrestamo);
        $stmtPrestamo->execute([
            ':libro_id' => $libro_id,
            ':usuario_id' => $usuario_id
        ]);

        $sqlActualizar = "UPDATE libros
                          SET copias_disponibles = copias_disponibles - 1,
                              estado = IF(copias_disponibles - 1 = 0, 'prestado', 'disponible')
                          WHERE id = :id";
        $stmtActualizar = $conexion->prepare($sqlActualizar);
        $stmtActualizar->execute([':id' => $libro_id]);

        $conexion->commit();
        return "Préstamo realizado correctamente.";
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, "errors.log");
        return "Error: " . $e->getMessage();
    }
}

function devolver_libro(int $prestamo_id): string
{
    global $conexion;

    try {
        $conexion->beginTransaction();

        $sql = "SELECT * FROM prestamos WHERE id = :id AND estado = 'activo'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $prestamo_id]);
        $prestamo = $stmt->fetch();

        if (!$prestamo) {
            throw new Exception("El préstamo no existe o ya fue devuelto.");
        }

        $sqlDevolver = "UPDATE prestamos
                        SET fecha_devolucion = CURDATE(),
                            estado = 'devuelto'
                        WHERE id = :id";
        $stmtDevolver = $conexion->prepare($sqlDevolver);
        $stmtDevolver->execute([':id' => $prestamo_id]);

        $sqlLibro = "UPDATE libros
                     SET copias_disponibles = copias_disponibles + 1,
                         estado = 'disponible'
                     WHERE id = :libro_id";
        $stmtLibro = $conexion->prepare($sqlLibro);
        $stmtLibro->execute([':libro_id' => $prestamo['libro_id']]);

        $conexion->commit();
        return "Libro devuelto correctamente.";
    } catch (Exception $e) {
        $conexion->rollBack();
        error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL, 3, "errors.log");
        return "Error: " . $e->getMessage();
    }
}

function obtener_todos_libros(): array
{
    global $conexion;
    $stmt = $conexion->query("SELECT * FROM libros ORDER BY id DESC");
    return $stmt->fetchAll();
}

function obtener_todos_usuarios(): array
{
    global $conexion;
    $stmt = $conexion->query("SELECT * FROM usuarios ORDER BY id DESC");
    return $stmt->fetchAll();
}

function obtener_prestamos(): array
{
    global $conexion;

    $sql = "SELECT p.id, l.titulo, u.nombre, p.fecha_prestamo, p.fecha_devolucion, p.estado
            FROM prestamos p
            INNER JOIN libros l ON p.libro_id = l.id
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.id DESC";
    $stmt = $conexion->query($sql);

    return $stmt->fetchAll();
}
?>
