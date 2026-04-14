"""Excepciones personalizadas del sistema de biblioteca."""

from __future__ import annotations


class LibroNoEncontradoError(Exception):
    """Se lanza cuando el libro no existe."""


class UsuarioSuspendidoError(Exception):
    """Se lanza cuando el usuario está suspendido o no existe."""


class SinStockError(Exception):
    """Se lanza cuando el libro no tiene copias disponibles."""