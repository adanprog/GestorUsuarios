<?php
/**
 * PROCESAR USUARIOS (Backend)
 * --------------------------
 * Este archivo procesa las acciones de la página de Usuarios:
 * crear, borrar, activar/desactivar y editar datos.
 */
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) session_start();

// Seguridad: solo administradores pueden usar estas funciones.
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . '/message_helper.php';
require_once __DIR__ . '/../helpers.php';
require_once dirname(__DIR__, 2) . '/clases/CPUser.php';

$message = null;      // Mensaje para mostrar en pantalla.
$messageType = null;  // Tipo de mensaje (success, danger, info).

$currentUser = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
$isAdminUser = $currentUser ? $currentUser->esAdministrador() : false;

// 1. Añadir usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $message = CPUser::agregar(
        cp_post_string('email'),
        cp_post_string('password'),
        cp_post_string('role')
    );
    $messageType = cp_alert_type_from_message($message);
}

// 2. Borrar usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $message = CPUser::borrar(cp_post_string('email'));
    $messageType = cp_alert_type_from_message($message);
}

// 3. Activar o desactivar usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_user_active'])) {
    $email = cp_post_string('email');
    $activo = cp_post_string('active') === '1' ? 1 : 0;
    $message = CPUser::actualizarActivoPorEmail($email, $activo);
    $messageType = cp_alert_type_from_message($message);
}

// 4. Editar datos de usuario.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $nuevoRol = $isAdminUser ? cp_post_string('role') : null;
    $u = CPUser::buscarPorEmail(cp_post_string('original_email'));
    if (!$u) {
        $message = 'Usuario no encontrado para editar.';
    } else {
        if (!$isAdminUser) {
            $nuevoRol = $u->getRole();
        }

        $message = CPUser::actualizar(
            cp_post_string('original_email'),
            cp_post_string('username'),
            cp_post_string('lastname'),
            cp_post_string('DNI'),
            cp_post_string('telefono'),
            cp_post_string('email'),
            cp_post_string('password'),
            $nuevoRol,
            cp_post_string('fechaNacimiento')
        );
    }

    if (isset($_POST['active'])) {
        $estadoActivo = cp_post_string('active') === '1' ? 1 : 0;
        $u = CPUser::buscarPorEmail(cp_post_string('email'));
        if ($u) {
            $u->setActivo($estadoActivo);
            $u->guardar();
        }
    }

    $messageType = cp_alert_type_from_message($message);
}

require_once('head.php');
?>
