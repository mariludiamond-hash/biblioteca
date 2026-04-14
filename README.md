# Sistema de Gestión de Biblioteca

Este sistema fue desarrollado para administrar de manera organizada el catálogo de libros, el registro de usuarios y el control de préstamos dentro de una biblioteca. Su propósito es ofrecer una solución funcional y estructurada que permita mantener la información centralizada, reducir errores operativos y aplicar reglas de negocio que garanticen la integridad de los datos. La solución implementa una arquitectura modular, conexión a base de datos relacional, validaciones, manejo de errores y una estructura clara de navegación, con el fin de facilitar su mantenimiento, comprensión y evaluación técnica.

El sistema permite registrar libros con su información bibliográfica básica, almacenar usuarios diferenciados por tipo, controlar si un usuario se encuentra activo o suspendido y administrar préstamos y devoluciones actualizando automáticamente la disponibilidad de ejemplares. La lógica aplicada evita operaciones inválidas, como prestar libros sin existencias o permitir préstamos a usuarios suspendidos, lo cual fortalece la consistencia del sistema y aporta evidencia de control interno.

## Tecnologías utilizadas

La solución fue construida en PHP con tipado estricto mediante `declare(strict_types=1);`, utilizando PDO para la conexión segura con la base de datos MySQL. La base de datos fue diseñada en phpMyAdmin y estructurada bajo un modelo relacional compuesto por las tablas `libros`, `usuarios` y `prestamos`. Git se utiliza como herramienta de control de versiones para mantener trazabilidad de cambios, mientras que XAMPP proporciona el entorno local de ejecución con Apache y MySQL.

## Estructura del proyecto

La organización del proyecto responde a la necesidad de mantener separados los archivos de configuración, la lógica de negocio y las interfaces del sistema. Esta estructura permite ubicar fácilmente cada componente y facilita futuras mejoras o correcciones.

```text
biblioteca/
│
├── config.php
├── funciones.php
├── index.php
├── libros.php
├── usuarios.php
├── prestamos.php
├── errors.log
└── README.md
Descripción de archivos

config.php contiene la configuración de conexión a la base de datos mediante PDO. También establece el modo de errores por excepciones y define la ruta del archivo de registro de errores.

funciones.php concentra la lógica principal del sistema. En este archivo se implementan funciones para registrar libros, registrar usuarios, validar correos electrónicos, consultar libros, verificar el estado de los usuarios, realizar préstamos, devolver libros y obtener los listados generales.

index.php funciona como página principal del sistema y muestra el menú de acceso a los diferentes módulos.

libros.php permite registrar nuevos libros y visualizar el catálogo completo.

usuarios.php permite registrar usuarios y consultar su información básica.

prestamos.php permite registrar préstamos, procesar devoluciones y visualizar el historial de movimientos.

errors.log registra los errores detectados por el sistema, especialmente aquellos relacionados con base de datos o validaciones críticas.

Base de datos

La base de datos utilizada se llama biblioteca_db. Su estructura se compone de tres tablas principales. La tabla libros almacena el identificador del libro, ISBN único, título, autor, estado y copias disponibles. La tabla usuarios contiene el identificador del usuario, nombre, correo electrónico único, tipo y estado. La tabla prestamos relaciona usuarios con libros y almacena la fecha del préstamo, la fecha de devolución y el estado del movimiento.

La integridad de la información se garantiza mediante claves primarias, llaves foráneas y restricciones de unicidad en ISBN y correo electrónico. Esto evita duplicidades y asegura que cada préstamo esté asociado a registros válidos.

Reglas de negocio implementadas

El sistema incorpora controles que protegen la coherencia de las operaciones. No se permite registrar libros con ISBN duplicado ni usuarios con correos repetidos. Los correos electrónicos se validan antes de guardarse. Un usuario suspendido no puede realizar préstamos. Un libro sin copias disponibles no puede ser prestado. Cuando un préstamo se registra correctamente, el número de copias disponibles disminuye en una unidad y el estado del libro se actualiza. Cuando un libro es devuelto, el sistema incrementa nuevamente las copias disponibles y registra la fecha de devolución.

Estas validaciones permiten que el sistema no dependa únicamente de la intervención del usuario, sino que aplique controles automáticos que fortalecen la confiabilidad del proceso.

Tipado y documentación

El sistema fue desarrollado con tipado estricto en PHP para mejorar la claridad del código y reducir errores de manejo de tipos. Las funciones cuentan con tipos de entrada y salida definidos, lo que facilita su comprensión y mantenimiento. Además, cada archivo y cada función principal incluye documentación mediante bloques PHPDoc, describiendo su propósito, parámetros, retornos y comportamiento general.

Este enfoque mejora la legibilidad del proyecto y hace que su revisión técnica sea más sencilla, ya que el código puede analizarse con mayor precisión y menor ambigüedad.

Manejo de errores

La conexión a la base de datos y las operaciones críticas se encuentran protegidas mediante bloques try-catch. Cuando ocurre un problema, el sistema registra el evento en el archivo errors.log con marca de tiempo, lo que facilita el seguimiento y análisis de fallos. En operaciones sensibles como préstamos y devoluciones se usan transacciones para asegurar que los cambios en la base de datos se realicen de forma consistente. Si ocurre un error durante la operación, los cambios se revierten y el sistema evita estados parciales o inconsistentes.

Requisitos para ejecutar el sistema

Para utilizar el sistema es necesario tener instalado XAMPP o un entorno equivalente con Apache y MySQL. La base de datos biblioteca_db debe existir previamente en phpMyAdmin junto con sus tablas. La carpeta del proyecto debe colocarse dentro de htdocs con el nombre biblioteca.

La ruta recomendada es la siguiente:

C:\xampp\htdocs\biblioteca
Instrucciones de instalación y ejecución

Primero se debe iniciar Apache y MySQL desde el panel de control de XAMPP. Después, en phpMyAdmin, se crea la base de datos biblioteca_db y se ejecuta el script SQL correspondiente para generar las tablas libros, usuarios y prestamos. Una vez hecho esto, los archivos del sistema deben guardarse dentro de la carpeta biblioteca en htdocs.

Con la estructura lista, el sistema puede ejecutarse desde el navegador accediendo a la siguiente dirección:

http://localhost/biblioteca/

Si la conexión está correctamente configurada y la base de datos existe, el sistema mostrará el menú principal con acceso a los módulos disponibles.

Flujo general de uso

El uso del sistema inicia desde el menú principal, desde donde se puede acceder a la gestión de libros, usuarios o préstamos. En el módulo de libros se registran los ejemplares disponibles. En el módulo de usuarios se almacenan las personas autorizadas para interactuar con la biblioteca. En el módulo de préstamos se selecciona un libro y un usuario para registrar la operación, siempre que se cumplan las condiciones de disponibilidad y estado. Posteriormente, mediante el mismo módulo, es posible registrar la devolución de un libro usando el identificador del préstamo.

Control de versiones con Git

El proyecto se encuentra vinculado a un repositorio Git, lo que permite llevar un historial de cambios y respaldar el desarrollo. Esto facilita la trazabilidad del trabajo realizado, así como la identificación de mejoras, correcciones y avances en cada etapa de implementación.

Comandos básicos utilizados:

git init
git branch -M main
git add .
git commit -m "Sistema de biblioteca en PHP tipado y documentado"
git remote add origin https://github.com/mariludiamond-hash/biblioteca.git
git push -u origin main
Alcance actual del sistema

Actualmente el sistema cubre el registro de libros, usuarios y préstamos, además del control de devoluciones y la actualización del inventario disponible. Su alcance corresponde a una solución funcional de base que puede evolucionar hacia un sistema más completo incorporando búsquedas avanzadas, edición y eliminación de registros, autenticación de usuarios, reportes, historial detallado y panel administrativo con diseño visual más avanzado.

Conclusión

La solución desarrollada cumple con la necesidad de administrar información bibliográfica y operativa de manera ordenada, aplicando una lógica coherente y controles que reducen errores frecuentes. La estructura modular, el uso de base de datos relacional, el tipado estricto, la documentación interna y el manejo de errores convierten al sistema en una base sólida, comprensible y mantenible. Además de resolver el problema funcional, el proyecto demuestra una organización técnica que facilita su revisión, continuidad y mejora, lo que resulta valioso tanto en un entorno académico como en una evaluación más formal de calidad del software.