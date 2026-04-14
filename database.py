"""Lógica de acceso a datos y reglas de negocio del sistema."""

from __future__ import annotations

from datetime import date
from typing import Any

import mysql.connector
from mysql.connector import Error

from config import DB_CONFIG
from exceptions import LibroNoEncontradoError, SinStockError, UsuarioSuspendidoError


def conectar() -> mysql.connector.MySQLConnection:
    """Crea y devuelve una conexión activa a MySQL."""
    return mysql.connector.connect(**DB_CONFIG)


def validar_email(email: str) -> bool:
    """Valida de forma simple el formato del correo."""
    return "@" in email and "." in email and len(email.strip()) >= 5


def registrar_libro(isbn: str, titulo: str, autor: str, copias: int) -> str:
    """Registra un libro en la base de datos."""
    if not isbn.strip() or not titulo.strip() or not autor.strip():
        return "Error: todos los campos del libro son obligatorios."

    if copias < 0:
        return "Error: las copias no pueden ser negativas."

    estado = "disponible" if copias > 0 else "prestado"

    try:
        conexion = conectar()
        cursor = conexion.cursor()
        sql = """
            INSERT INTO libros (isbn, titulo, autor, estado, copias_disponibles)
            VALUES (%s, %s, %s, %s, %s)
        """
        cursor.execute(sql, (isbn.strip(), titulo.strip(), autor.strip(), estado, copias))
        conexion.commit()
        return "Libro registrado correctamente."
    except Error as exc:
        return f"Error al registrar libro: {exc}"
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def obtener_libros() -> list[tuple[Any, ...]]:
    """Obtiene el listado completo de libros."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        cursor.execute(
            """
            SELECT id, isbn, titulo, autor, estado, copias_disponibles
            FROM libros
            ORDER BY id DESC
            """
        )
        return cursor.fetchall()
    except Error:
        return []
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def registrar_usuario(nombre: str, email: str, tipo: str) -> str:
    """Registra un usuario en la base de datos."""
    if not nombre.strip() or not email.strip() or not tipo.strip():
        return "Error: todos los campos del usuario son obligatorios."

    if not validar_email(email):
        return "Error: el correo no tiene un formato válido."

    if tipo not in {"estudiante", "profesor"}:
        return "Error: el tipo de usuario no es válido."

    try:
        conexion = conectar()
        cursor = conexion.cursor()
        sql = """
            INSERT INTO usuarios (nombre, email, tipo, estado)
            VALUES (%s, %s, %s, 'activo')
        """
        cursor.execute(sql, (nombre.strip(), email.strip(), tipo))
        conexion.commit()
        return "Usuario registrado correctamente."
    except Error as exc:
        return f"Error al registrar usuario: {exc}"
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def obtener_usuarios() -> list[tuple[Any, ...]]:
    """Obtiene el listado completo de usuarios."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        cursor.execute(
            """
            SELECT id, nombre, email, tipo, estado
            FROM usuarios
            ORDER BY id DESC
            """
        )
        return cursor.fetchall()
    except Error:
        return []
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def verificar_usuario_activo(usuario_id: int) -> bool:
    """Verifica si el usuario existe y está activo."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        cursor.execute("SELECT estado FROM usuarios WHERE id = %s", (usuario_id,))
        fila = cursor.fetchone()
        return fila is not None and fila[0] == "activo"
    except Error:
        return False
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def obtener_libro(libro_id: int) -> tuple[Any, ...] | None:
    """Obtiene un libro por su identificador."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        cursor.execute(
            """
            SELECT id, isbn, titulo, autor, estado, copias_disponibles
            FROM libros
            WHERE id = %s
            """,
            (libro_id,),
        )
        return cursor.fetchone()
    except Error:
        return None
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def realizar_prestamo(libro_id: int, usuario_id: int) -> str:
    """Realiza un préstamo validando usuario activo y stock disponible."""
    try:
        if not verificar_usuario_activo(usuario_id):
            raise UsuarioSuspendidoError("El usuario está suspendido o no existe.")

        libro = obtener_libro(libro_id)
        if libro is None:
            raise LibroNoEncontradoError("El libro no existe.")

        copias_disponibles = int(libro[5])
        if copias_disponibles <= 0:
            raise SinStockError("No hay copias disponibles de este libro.")

        conexion = conectar()
        cursor = conexion.cursor()
        conexion.start_transaction()

        cursor.execute(
            """
            INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, estado)
            VALUES (%s, %s, %s, 'activo')
            """,
            (libro_id, usuario_id, date.today()),
        )

        nuevas_copias = copias_disponibles - 1
        nuevo_estado = "disponible" if nuevas_copias > 0 else "prestado"

        cursor.execute(
            """
            UPDATE libros
            SET copias_disponibles = %s, estado = %s
            WHERE id = %s
            """,
            (nuevas_copias, nuevo_estado, libro_id),
        )

        conexion.commit()
        return "Préstamo realizado correctamente."
    except (UsuarioSuspendidoError, LibroNoEncontradoError, SinStockError) as exc:
        return f"Error: {exc}"
    except Error as exc:
        if "conexion" in locals() and conexion.is_connected():
            conexion.rollback()
        return f"Error técnico: {exc}"
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def devolver_libro(prestamo_id: int) -> str:
    """Registra la devolución de un libro y actualiza su disponibilidad."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        conexion.start_transaction()

        cursor.execute(
            """
            SELECT libro_id, estado
            FROM prestamos
            WHERE id = %s
            """,
            (prestamo_id,),
        )
        prestamo = cursor.fetchone()

        if prestamo is None or prestamo[1] != "activo":
            return "Error: el préstamo no existe o ya fue devuelto."

        libro_id = int(prestamo[0])

        cursor.execute(
            """
            UPDATE prestamos
            SET fecha_devolucion = %s, estado = 'devuelto'
            WHERE id = %s
            """,
            (date.today(), prestamo_id),
        )

        cursor.execute(
            """
            SELECT copias_disponibles
            FROM libros
            WHERE id = %s
            """,
            (libro_id,),
        )
        fila_libro = cursor.fetchone()

        if fila_libro is None:
            raise LibroNoEncontradoError("No se encontró el libro asociado al préstamo.")

        nuevas_copias = int(fila_libro[0]) + 1

        cursor.execute(
            """
            UPDATE libros
            SET copias_disponibles = %s, estado = 'disponible'
            WHERE id = %s
            """,
            (nuevas_copias, libro_id),
        )

        conexion.commit()
        return "Libro devuelto correctamente."
    except LibroNoEncontradoError as exc:
        if "conexion" in locals() and conexion.is_connected():
            conexion.rollback()
        return f"Error: {exc}"
    except Error as exc:
        if "conexion" in locals() and conexion.is_connected():
            conexion.rollback()
        return f"Error técnico: {exc}"
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()


def obtener_prestamos() -> list[tuple[Any, ...]]:
    """Obtiene el historial de préstamos."""
    try:
        conexion = conectar()
        cursor = conexion.cursor()
        cursor.execute(
            """
            SELECT p.id, l.titulo, u.nombre, p.fecha_prestamo, p.fecha_devolucion, p.estado
            FROM prestamos p
            INNER JOIN libros l ON p.libro_id = l.id
            INNER JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.id DESC
            """
        )
        return cursor.fetchall()
    except Error:
        return []
    finally:
        if "cursor" in locals():
            cursor.close()
        if "conexion" in locals() and conexion.is_connected():
            conexion.close()