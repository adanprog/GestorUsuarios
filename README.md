# Gestor de Direcciones - Sistema de Gestión de Direcciones y Usuarios

![Version](https://img.shields.io/badge/version-1.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-8.2.12-purple)
![MySQL](https://img.shields.io/badge/MySQL-10.4-orange)

## 📋 Descripción

**Gestor de Direcciones** es una aplicación web completa para la gestión de direcciones, usuarios y roles. Permite administradores gestionar el sistema de forma centralizada, mientras que usuarios normales pueden manejar sus propias direcciones de forma segura.

## ✨ Características Principales

### 🔐 Seguridad
- Sistema de autenticación con email y contraseña
- Confirmación de contraseña al crear usuarios (2 campos)
- Toggle visual para mostrar/ocultar contraseña
- Validación mínima de 6 caracteres
- Control de sesiones
- Roles y permisos por usuario

### 👥 Gestión de Usuarios
- Crear, editar, activar/desactivar usuarios
- Asignación de roles
- Historial de fechas de registro
- Visualización de estado activo/inactivo
- Solo administradores pueden gestionar usuarios

### 📍 Gestión de Propiedades
- Crear, editar, eliminar direcciones
- Información del propietario separada del creador:
  - Nombre del propietario
  - Teléfono del propietario (clickeable para llamar)
  - Email del propietario (clickeable para enviar correo)
- Búsqueda en tiempo real por múltiples criterios:
  - Nombre de propiedad
  - Nombre del propietario
  - Calle
  - Ciudad
  - Provincia
  - Email del propietario
  - Teléfono del propietario
- Códigos postales automáticos

### 🛡️ Control de Acceso
- Panel de administración exclusivo para admins
- Vistas personalizadas por rol
- Estadísticas diferentes según tipo de usuario
- Permisos granulares por rol

### 🎨 Interfaz
- Diseño responsive con Bootstrap 5
- Modo claro y modo oscuro
- Gradientes de color para mejor visualización
- Iconos de Bootstrap Icons
- Tarjetas con bordes visibles en modo oscuro

## 🚀 Instalación y Configuración

### Requisitos Previos
- PHP 8.2+
- MySQL 5.7+ o MariaDB 10.4+
- Servidor web (Apache, Nginx, etc.)
- XAMPP (recomendado para desarrollo)

### Pasos de Instalación

#### 1. Clonar o descargar el proyecto
```bash
cd c:\xampp\htdocs\
# Descarga o clona el proyecto en la carpeta gestor_direcciones
```

#### 2. Configurar la base de datos
Edita `config.php` con tus datos:
```php
$host = "localhost";
$port = 3307; // o el puerto de tu MySQL
$db_name = "gestor_direcciones";
$user = "root";
$password = ""; // contraseña de tu MySQL
```

#### 3. Crear las tablas de la base de datos
Abre en tu navegador:
```
http://localhost/phpmyadmin/
```
- Crea una base de datos llamada `gestor_direcciones`
- Importa el archivo `bbdd/gestor_direcciones.sql`

O ejecuta desde terminal:
```bash
php scripts/create_database_tables.php
```

#### 4. Crear usuario administrador
Accede a tu base de datos y ejecuta:
```sql
INSERT INTO users (nombre, email, password, role, activo, fecha_creacion) 
VALUES ('Administrador', 'admin@email.com', SHA2('contraseña123', 256), 'administrador', 1, NOW());
```

#### 5. Acceder a la aplicación
```
http://localhost:3307/gestor_direcciones/
```
**Email**: admin@email.com  
**Contraseña**: contraseña123

## 📁 Estructura del Proyecto

```
gestor_direcciones/
├── index.php                          # Página de login
├── logout.php                         # Cierre de sesión
├── config.php                         # Configuración de base de datos
├── README.md                          # Este archivo
├── DOCUMENTACION_PROYECTO.md          # Documentación técnica
│
├── clases/                            # Clases PHP
│   ├── Database.php                   # Conexión a BD
│   ├── CPGenerico.php                 # Clase base
│   ├── CPUser.php                     # Gestión de usuarios
│   ├── CPRole.php                     # Gestión de roles
│   ├── CPDir.php                      # Gestión de direcciones
│   └── CPDesplegableDir.php           # Códigos postales
│
├── templates/
│   ├── frontend/                      # Páginas visibles
│   │   ├── head.php                   # Encabezado común
│   │   ├── sidebar.php                # Menú lateral
│   │   ├── footer.php                 # Pie de página
│   │   ├── home.php                   # Panel principal
│   │   ├── login.php                  # Formulario de login
│   │   ├── usuarios.php               # Gestión de usuarios
│   │   ├── roles.php                  # Gestión de roles
│   │   ├── direcciones.php            # Gestión de direcciones
│   │   ├── edituser.php               # Editar perfil
│   │   └── profile.php                # Ver perfil
│   │
│   └── backend/                       # Lógica de procesamiento
│       ├── login_back.php             # Procesar login
│       ├── procesar_usuarios.php      # CRUD usuarios
│       ├── procesar_roles.php         # CRUD roles
│       ├── procesar_direcciones.php   # CRUD direcciones
│       ├── message_helper.php         # Mensajes
│       └── debug.php                  # Depuración
│
├── bootstrap/                         # CSS de Bootstrap 5
├── archivos/                          # Caché y datos
│   └── codigos_postales_cache.json    # Códigos postales
│
├── bbdd/                              # Base de datos
│   └── gestor_direcciones.sql         # Script SQL
│
└── scripts/
    └── create_database_tables.php     # Crear tablas
```

## 👥 Roles y Permisos

### Administrador
- ✅ Ver y gestionar todos los usuarios
- ✅ Ver y gestionar todos los roles
- ✅ Ver todas las direcciones del sistema
- ✅ Acceso completo a todas las secciones
- ✅ Panel con estadísticas del sistema

### Empleado
- ✅ Ver y gestionar sus propias direcciones
- ✅ Ver su perfil
- ✅ Actualizar su información
- ❌ No puede ver usuarios del sistema
- ❌ No puede gestionar roles

### Observador
- ✅ Ver listado de direcciones
- ✅ Ver información de usuarios
- ❌ No puede editar ni crear
- ❌ Solo lectura

## 🔄 Flujo de Uso

### Para Administradores
1. Inicia sesión con credenciales de admin
2. Ve el panel con estadísticas del sistema
3. Accede a:
   - **Usuarios**: Crear, editar, activar/desactivar usuarios
   - **Roles**: Crear roles personalizados y asignar permisos
   - **Direcciones**: Ver todas las propiedades del sistema
4. Usa accesos rápidos para navegar

### Para Usuarios Normales
1. Inicia sesión con tu email
2. Ve tu panel personalizado con:
   - Bienvenida con tu nombre
   - Tus estadísticas personales
   - Acceso rápido a tus direcciones
3. En **Mis Direcciones**:
   - Crea nuevas propiedades
   - Edita tus propiedades
   - Busca rápidamente por nombre, ubicación, etc.
4. En **Mi Perfil**:
   - Ve tu información
   - Ve tus permisos

## 🔍 Búsqueda de Propiedades

La barra de búsqueda permite filtrar direcciones en tiempo real por:
- 📝 Nombre de la propiedad
- 👤 Nombre del propietario
- 🚗 Calle
- 🏙️ Ciudad
- 📍 Provincia
- 📧 Email del propietario
- 📞 Teléfono del propietario

## 🎨 Temas y Personalización

### Modo Oscuro
Haz clic en "Modo noche" en la esquina superior derecha para cambiar a tema oscuro.

### Colores por Defecto
- **Morado** (#667eea - #764ba2): Usuarios
- **Rosa** (#f093fb - #f5576c): Roles
- **Cian** (#4facfe - #00f2fe): Direcciones

## 🛠️ Desarrollo

### Archivos Clave para Modificar

#### Para agregar campos a usuarios:
- `clases/CPUser.php` - Añade propiedades y métodos
- `templates/frontend/usuarios.php` - Añade campos en formularios
- `templates/backend/procesar_usuarios.php` - Procesa los datos

#### Para agregar campos a direcciones:
- `clases/CPDir.php` - Añade propiedades y métodos
- `templates/frontend/direcciones.php` - Formularios
- `templates/backend/procesar_direcciones.php` - Lógica

#### Para crear nuevos roles:
- `clases/CPRole.php` - Define la clase
- `templates/frontend/roles.php` - Interfaz
- `templates/backend/procesar_roles.php` - Procesamiento

## 🐛 Resolución de Problemas

### No puedo conectar a la base de datos
- Verifica que MySQL esté corriendo
- Revisa los datos en `config.php`
- Comprueba que la base de datos existe

### El login no funciona
- Verifica que el usuario existe en la tabla `users`
- Asegúrate que la contraseña es correcta
- Revisa la sesión está habilitada en PHP

### No veo las direcciones
- Verifica que tienes permisos para ver direcciones
- Comprueba que la tabla `direcciones` tiene datos
- Revisa tu rol en la tabla `users`

### Modo oscuro no funciona
- Limpia la caché del navegador
- Verifica que JavaScript está habilitado
- Revisa la consola del navegador para errores


---

**Última actualización**: 20 de Abril de 2026  
**Versión**: 1.0  
**Estado**: En producción
