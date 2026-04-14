"""Interfaz gráfica del sistema de gestión de biblioteca."""

from __future__ import annotations

import tkinter as tk
from tkinter import messagebox, ttk

from database import (
    devolver_libro,
    obtener_libros,
    obtener_prestamos,
    obtener_usuarios,
    realizar_prestamo,
    registrar_libro,
    registrar_usuario,
)


class BibliotecaApp(tk.Tk):
    """Ventana principal del sistema."""

    def __init__(self) -> None:
        super().__init__()
        self.title("Sistema de Gestión de Biblioteca")
        self.geometry("1100x650")
        self.configure(bg="#f4f6f9")

        self.notebook = ttk.Notebook(self)
        self.notebook.pack(fill="both", expand=True, padx=10, pady=10)

        self.tab_libros = ttk.Frame(self.notebook)
        self.tab_usuarios = ttk.Frame(self.notebook)
        self.tab_prestamos = ttk.Frame(self.notebook)

        self.notebook.add(self.tab_libros, text="Libros")
        self.notebook.add(self.tab_usuarios, text="Usuarios")
        self.notebook.add(self.tab_prestamos, text="Préstamos")

        self._crear_tab_libros()
        self._crear_tab_usuarios()
        self._crear_tab_prestamos()

        self.cargar_libros()
        self.cargar_usuarios()
        self.cargar_prestamos()

    def _crear_tab_libros(self) -> None:
        form = ttk.LabelFrame(self.tab_libros, text="Registrar libro")
        form.pack(fill="x", padx=10, pady=10)

        ttk.Label(form, text="ISBN").grid(row=0, column=0, padx=8, pady=8)
        self.entry_isbn = ttk.Entry(form, width=30)
        self.entry_isbn.grid(row=0, column=1, padx=8, pady=8)

        ttk.Label(form, text="Título").grid(row=0, column=2, padx=8, pady=8)
        self.entry_titulo = ttk.Entry(form, width=30)
        self.entry_titulo.grid(row=0, column=3, padx=8, pady=8)

        ttk.Label(form, text="Autor").grid(row=1, column=0, padx=8, pady=8)
        self.entry_autor = ttk.Entry(form, width=30)
        self.entry_autor.grid(row=1, column=1, padx=8, pady=8)

        ttk.Label(form, text="Copias").grid(row=1, column=2, padx=8, pady=8)
        self.entry_copias = ttk.Entry(form, width=30)
        self.entry_copias.grid(row=1, column=3, padx=8, pady=8)

        ttk.Button(form, text="Guardar libro", command=self.guardar_libro).grid(
            row=2, column=0, columnspan=4, pady=10
        )

        self.tree_libros = ttk.Treeview(
            self.tab_libros,
            columns=("id", "isbn", "titulo", "autor", "estado", "copias"),
            show="headings",
            height=18,
        )
        for col, txt, width in [
            ("id", "ID", 60),
            ("isbn", "ISBN", 130),
            ("titulo", "Título", 260),
            ("autor", "Autor", 220),
            ("estado", "Estado", 120),
            ("copias", "Copias", 100),
        ]:
            self.tree_libros.heading(col, text=txt)
            self.tree_libros.column(col, width=width)
        self.tree_libros.pack(fill="both", expand=True, padx=10, pady=10)

    def _crear_tab_usuarios(self) -> None:
        form = ttk.LabelFrame(self.tab_usuarios, text="Registrar usuario")
        form.pack(fill="x", padx=10, pady=10)

        ttk.Label(form, text="Nombre").grid(row=0, column=0, padx=8, pady=8)
        self.entry_nombre = ttk.Entry(form, width=30)
        self.entry_nombre.grid(row=0, column=1, padx=8, pady=8)

        ttk.Label(form, text="Correo").grid(row=0, column=2, padx=8, pady=8)
        self.entry_email = ttk.Entry(form, width=30)
        self.entry_email.grid(row=0, column=3, padx=8, pady=8)

        ttk.Label(form, text="Tipo").grid(row=1, column=0, padx=8, pady=8)
        self.combo_tipo = ttk.Combobox(form, values=["estudiante", "profesor"], state="readonly")
        self.combo_tipo.grid(row=1, column=1, padx=8, pady=8)
        self.combo_tipo.set("estudiante")

        ttk.Button(form, text="Guardar usuario", command=self.guardar_usuario).grid(
            row=2, column=0, columnspan=4, pady=10
        )

        self.tree_usuarios = ttk.Treeview(
            self.tab_usuarios,
            columns=("id", "nombre", "email", "tipo", "estado"),
            show="headings",
            height=18,
        )
        for col, txt, width in [
            ("id", "ID", 60),
            ("nombre", "Nombre", 250),
            ("email", "Correo", 280),
            ("tipo", "Tipo", 120),
            ("estado", "Estado", 120),
        ]:
            self.tree_usuarios.heading(col, text=txt)
            self.tree_usuarios.column(col, width=width)
        self.tree_usuarios.pack(fill="both", expand=True, padx=10, pady=10)

    def _crear_tab_prestamos(self) -> None:
        form = ttk.LabelFrame(self.tab_prestamos, text="Gestión de préstamos")
        form.pack(fill="x", padx=10, pady=10)

        ttk.Label(form, text="ID Libro").grid(row=0, column=0, padx=8, pady=8)
        self.entry_libro_id = ttk.Entry(form, width=20)
        self.entry_libro_id.grid(row=0, column=1, padx=8, pady=8)

        ttk.Label(form, text="ID Usuario").grid(row=0, column=2, padx=8, pady=8)
        self.entry_usuario_id = ttk.Entry(form, width=20)
        self.entry_usuario_id.grid(row=0, column=3, padx=8, pady=8)

        ttk.Button(form, text="Realizar préstamo", command=self.hacer_prestamo).grid(
            row=0, column=4, padx=8, pady=8
        )

        ttk.Label(form, text="ID Préstamo").grid(row=1, column=0, padx=8, pady=8)
        self.entry_prestamo_id = ttk.Entry(form, width=20)
        self.entry_prestamo_id.grid(row=1, column=1, padx=8, pady=8)

        ttk.Button(form, text="Registrar devolución", command=self.hacer_devolucion).grid(
            row=1, column=2, padx=8, pady=8
        )

        self.tree_prestamos = ttk.Treeview(
            self.tab_prestamos,
            columns=("id", "libro", "usuario", "fecha_p", "fecha_d", "estado"),
            show="headings",
            height=18,
        )
        for col, txt, width in [
            ("id", "ID", 60),
            ("libro", "Libro", 260),
            ("usuario", "Usuario", 220),
            ("fecha_p", "Fecha préstamo", 120),
            ("fecha_d", "Fecha devolución", 130),
            ("estado", "Estado", 120),
        ]:
            self.tree_prestamos.heading(col, text=txt)
            self.tree_prestamos.column(col, width=width)
        self.tree_prestamos.pack(fill="both", expand=True, padx=10, pady=10)

    def guardar_libro(self) -> None:
        """Guarda un libro desde la interfaz."""
        try:
            copias = int(self.entry_copias.get())
        except ValueError:
            messagebox.showerror("Error", "Las copias deben ser numéricas.")
            return

        mensaje = registrar_libro(
            self.entry_isbn.get(),
            self.entry_titulo.get(),
            self.entry_autor.get(),
            copias,
        )
        messagebox.showinfo("Resultado", mensaje)
        self.cargar_libros()
        self.entry_isbn.delete(0, tk.END)
        self.entry_titulo.delete(0, tk.END)
        self.entry_autor.delete(0, tk.END)
        self.entry_copias.delete(0, tk.END)

    def guardar_usuario(self) -> None:
        """Guarda un usuario desde la interfaz."""
        mensaje = registrar_usuario(
            self.entry_nombre.get(),
            self.entry_email.get(),
            self.combo_tipo.get(),
        )
        messagebox.showinfo("Resultado", mensaje)
        self.cargar_usuarios()
        self.entry_nombre.delete(0, tk.END)
        self.entry_email.delete(0, tk.END)

    def hacer_prestamo(self) -> None:
        """Procesa un préstamo desde la interfaz."""
        try:
            libro_id = int(self.entry_libro_id.get())
            usuario_id = int(self.entry_usuario_id.get())
        except ValueError:
            messagebox.showerror("Error", "Los identificadores deben ser numéricos.")
            return

        mensaje = realizar_prestamo(libro_id, usuario_id)
        messagebox.showinfo("Resultado", mensaje)
        self.cargar_libros()
        self.cargar_prestamos()

    def hacer_devolucion(self) -> None:
        """Procesa una devolución desde la interfaz."""
        try:
            prestamo_id = int(self.entry_prestamo_id.get())
        except ValueError:
            messagebox.showerror("Error", "El ID del préstamo debe ser numérico.")
            return

        mensaje = devolver_libro(prestamo_id)
        messagebox.showinfo("Resultado", mensaje)
        self.cargar_libros()
        self.cargar_prestamos()

    def cargar_libros(self) -> None:
        """Carga la tabla visual de libros."""
        for item in self.tree_libros.get_children():
            self.tree_libros.delete(item)

        for fila in obtener_libros():
            self.tree_libros.insert("", tk.END, values=fila)

    def cargar_usuarios(self) -> None:
        """Carga la tabla visual de usuarios."""
        for item in self.tree_usuarios.get_children():
            self.tree_usuarios.delete(item)

        for fila in obtener_usuarios():
            self.tree_usuarios.insert("", tk.END, values=fila)

    def cargar_prestamos(self) -> None:
        """Carga la tabla visual de préstamos."""
        for item in self.tree_prestamos.get_children():
            self.tree_prestamos.delete(item)

        for fila in obtener_prestamos():
            self.tree_prestamos.insert("", tk.END, values=fila)


if __name__ == "__main__":
    app = BibliotecaApp()
    app.mainloop()