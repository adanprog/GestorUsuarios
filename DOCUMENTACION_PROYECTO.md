# Documentación del Proyecto "Gestor Personal"

Esta documentación está hecha para que una persona sin conocimientos de programación pueda comprender cómo funciona el proyecto.

---

## 1. ¿Qué es este proyecto?

Es una aplicación web en PHP que sirve para gestionar:

- Usuarios y sus roles (permisos dentro del sistema).
- Direcciones físicas o propiedades.
- Inicios de sesión y seguridad básica.

Se usa desde un navegador y se guarda información en una base de datos MySQL.

---

## 2. Archivos principales

### `index.php`

- Es la primera página que ve el usuario.
- Muestra el formulario de inicio de sesión.
- Si el usuario ya está conectado, lo envía al panel principal.

### `logout.php`

- Cierra la sesión del usuario.
- El sistema deja de recordar quién está conectado.

### `config.php`

- Contiene los datos que el programa usa para conectarse a la base de datos.
- Aquí están: servidor, puerto, nombre de la base, usuario y contraseña.

### `scripts/create_database_tables.php`

- Crea las tablas necesarias en la base de datos.
- Se usa una sola vez cuando se instala el proyecto por primera vez.

---

## 3. Carpetas importantes

### `clases/`

Contiene las "clases" del proyecto. Una clase es como una plantilla para crear objetos con datos y comportamientos.

- `Database.php`: conecta con la base de datos.
- `CPGenerico.php`: es la base para otras clases. Contiene funciones comunes para guardar y leer datos.
- `CPUser.php`: gestiona los usuarios, su login y permisos.
- `CPRole.php`: gestiona los roles del sistema.
- `CPDir.php`: gestiona las direcciones y propiedades.
- `CPDesplegableDir.php`: ayuda a leer códigos postales de un archivo de Excel y guardarlos en un caché JSON.

### `templates/frontend/`

Contiene las páginas visibles por el usuario.

- `head.php`: inicio común de todas las páginas, incluye los estilos y comprueba si hay sesión.
- `sidebar.php`: menú lateral con las opciones.
- `home.php`: página de bienvenida después de iniciar sesión.
- `login.php`: formulario de acceso.
- `usuarios.php`: página para manejar usuarios (solo administradores).
- `roles.php`: página para manejar los roles y permisos.
- `direcciones.php`: página para manejar direcciones y propiedades.
- `edituser.php`: página para editar datos de usuario.
- `footer.php`: pie de página común.

### `templates/backend/`

Contiene la lógica que se ejecuta cuando el usuario pulsa botones.

- `login_back.php`: comprueba si el login es correcto.
- `procesar_usuarios.php`: añade, edita, activa/desactiva o borra usuarios.
- `procesar_roles.php`: añade, edita, borra roles y asigna roles a usuarios.
- `procesar_direcciones.php`: guarda, edita o borra direcciones.
- `message_helper.php`: genera mensajes bonitos de éxito o error.
- `sidebar_backend.php`: prepara los datos para mostrar el menú.
- `error_message.php`: muestra mensajes de error cuando hay un problema.
- `scripts.php`: incluye código JavaScript necesario para algunas páginas.
- `debug.php`: ayuda a mostrar información para depurar si algo falla.

### `archivos/`

- `codigos_postales_cache.json`: archivo que guarda de forma rápida la lista de códigos postales.
- `codigos_postales_municipios.xlsx`: origen de los códigos postales.

---

## 4. Cómo funciona el acceso

1. El usuario abre `index.php`.
2. Escribe su correo y contraseña.
3. El sistema busca ese correo en la base de datos.
4. Comprueba si la contraseña coincide.
5. Si todo está bien, guarda datos en la sesión y redirige a `home.php`.

Si no está bien, muestra un mensaje de error.

---

## 5. Qué hace cada página

### Página `home.php`

- Es la pantalla principal o "tablero".
- Da la bienvenida al usuario.
- Muestra el menú con las opciones permitidas.

### Página `direcciones.php`

- Aquí se guardan y ven las direcciones o propiedades.
- El sistema muestra las direcciones que el usuario puede ver según su rol.
- Si el usuario tiene permiso, puede añadir, editar o borrar.
- Usa códigos postales con ayuda de un buscador automático.

### Página `usuarios.php`

- Solo pueden entrar los administradores.
- Permite crear nuevos usuarios.
- Permite activar o desactivar usuarios.
- Permite borrar usuarios.

### Página `roles.php`

- Solo pueden entrar los administradores.
- Permite crear roles nuevos con permisos.
- Permite editar roles ya existentes.
- Permite asignar un rol a cada usuario.

---

## 6. Roles y permisos

El proyecto distingue entre varios tipos de rol:

- `administrador`: puede ver y hacer todo.
- `empleado`: tiene menos permisos.
- `gerente` y `propietario`: se gestionan a través de roles personalizados.

Los permisos determinan si un usuario puede:

- ver todas las direcciones.
- ver quién creó una dirección.
- añadir direcciones.
- editar direcciones.
- eliminar direcciones.

El sistema también tiene una lógica que dice qué puede hacer cada rol.

---

## 7. Cómo se guardan los datos

Hay dos formas de guardar datos en este proyecto:

1. **Base de datos MySQL**
   - Usuarios
   - Roles
   - Direcciones

2. **Archivos de texto / caché**
   - `roles.txt`, `usuarios.txt`, `direcciones.txt` (en algunas partes del código)
   - `codigos_postales_cache.json` para guardar los códigos postales.

La base de datos es la fuente principal. Los archivos de texto se usan como soporte o para migraciones.

---

## 8. Flujo del sistema paso a paso

1. El usuario llega a `index.php`.
2. Si no está logueado, ve el formulario de login.
3. Si está logueado, ve el panel principal (`home.php`).
4. Desde el menú, elige:
   - Mis Direcciones
   - Usuarios (solo admin)
   - Roles (solo admin)

5. Al pulsar un botón en una página, se ejecuta un archivo de `templates/backend/`.
6. Ese archivo procesa los datos del formulario.
7. Si hay un error o éxito, se guarda un mensaje.
8. La misma página muestra ese mensaje de forma clara.

---

## 9. Notas importantes para no programadores

- "Sesión" es la forma en que el sistema recuerda quién está usando la aplicación.
- "Base de datos" es como una hoja de cálculo donde se guardan usuarios, roles y direcciones.
- "Clase" es un conjunto de código que sabe cómo manejar un tipo de dato.
- "Rol" es como una etiqueta que dice qué puede hacer una persona dentro del sistema.
- "Permiso" es una acción concreta que un usuario puede o no puede realizar.

---

## 10. Qué debes revisar si algo falla

- Comprueba que `config.php` tiene los datos correctos de la base de datos.
- Asegúrate de que la base de datos MySQL esté funcionando.
- Asegúrate de que la carpeta `archivos/` tenga permisos de lectura y escritura.
- Si el login no funciona, revisa que el usuario esté en la tabla `users`.

---

## 11. Cambios recomendados

Si quieres mejorar el proyecto, estas son buenas ideas:

- Añadir validación más estricta de campos en los formularios.
- Usar siempre la base de datos y eliminar el uso de archivos de texto antiguos.
- Limpiar los roles duplicados y asegurar que solo existen los que se usan.
- Añadir más comentarios dentro del código para facilitar mantenimiento.

---

## 12. Resumen rápido

Este proyecto es un pequeño gestor web para:

- iniciar sesión,
- controlar usuarios,
- configurar roles,
- y guardar direcciones.

La parte visible se encuentra en `templates/frontend/` y la lógica que procesa acciones en `templates/backend/`.

¡Con esta guía podrás entender qué hace cada archivo y cómo funciona el sistema general!
