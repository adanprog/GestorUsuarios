<?php
/**
 * PROCESAR ROLES (Backend)
 * ------------------------
 * Este archivo se encarga de recibir las peticiones que vienen del
 * formulario de la página de Roles y actuar según el botón que se pulse.
 */

// Mostrar errores de PHP, excepto los avisos pequeños (notices).
error_reporting(E_ALL ^ E_NOTICE);

// Si no hay sesión iniciada, la iniciamos para poder usar los datos de usuario.
if (session_status() === PHP_SESSION_NONE) session_start();

// SEGURIDAD: Solo una persona logueada como administrador puede usar esta página.
// Si no cumple esa condición, lo enviamos de vuelta al inicio.
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Importamos funciones y clases necesarias.
require_once __DIR__ . '/message_helper.php'; // Funciones para mostrar mensajes al usuario.
require_once __DIR__ . '/../helpers.php';      // Funciones auxiliares para limpiar datos.
require_once dirname(__DIR__, 2) . '/clases/CPRole.php'; // Clase para manejar roles.
require_once dirname(__DIR__, 2) . '/clases/CPUser.php'; // Clase para manejar usuarios.

// Inicializamos variables que usaremos más adelante.
$message = null;      // Mensaje que se mostrará al usuario (éxito o error).
$messageType = null;  // Tipo de mensaje para el estilo (success, danger, etc.).

// Buscamos al usuario actual, el que está en sesión.
$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
// Comprobamos si ese usuario es administrador.
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;

/**
 * Obtener permisos desde el formulario.
 * Devuelve un arreglo con verdadero / falso según los checkboxes seleccionados.
 */
function obtenerPermisosDelPost(): array {
    return [
        'view_direcciones' => isset($_POST['perm_view_direcciones']),
        'view_creator' => isset($_POST['perm_view_creator']),
        'add_direcciones' => isset($_POST['perm_add_direcciones']),
        'edit_direcciones' => isset($_POST['perm_edit']),
        'delete_direcciones' => isset($_POST['perm_delete']),
    ];
}

/**
 * Si se ha pulsado el botón de "añadir rol", recogemos los datos del formulario
 * y llamamos a la función que crea el nuevo rol en la base de datos.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_role'])) {
    $message = CPRole::agregar(
        cp_post_string('name'),
        cp_post_string('description'),
        isset($_POST['active']) ? 1 : 0,
        obtenerPermisosDelPost()
    );
    $messageType = cp_alert_type_from_message($message);
}

/**
 * Si se ha pulsado el botón de "editar rol", recogemos los datos y actualizamos el rol.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_role'])) {
    $message = CPRole::actualizar(
        cp_post_string('role_id'),
        cp_post_string('name'),
        cp_post_string('description'),
        isset($_POST['active']) ? 1 : 0,
        obtenerPermisosDelPost()
    );
    $messageType = cp_alert_type_from_message($message);
}

/**
 * Si se ha pulsado el botón de "borrar rol", borramos el rol indicado.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_role'])) {
    $message = CPRole::borrar(cp_post_string('role_id'));
    $messageType = cp_alert_type_from_message($message);
}

/**
 * Si se ha pulsado el botón de "asignar rol a usuario", actualizamos el rol
 * del usuario solamente si quien hace la acción es administrador.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user_role'])) {
    if ($isAdminUser) {
        $message = CPUser::actualizarRolePorId(
            cp_post_string('user_id'),
            cp_post_string('role_name')
        );
    } else {
        $message = 'No tienes permiso para cambiar roles.';
    }
    $messageType = cp_alert_type_from_message($message);
}

// Cargamos el encabezado HTML común para la página.
require_once('head.php');
