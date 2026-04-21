<?php
/**
 * BACKEND: Cargar perfil de usuario
 * --------------------------------
 * Maneja la carga del usuario actual y procesa el formulario de perfil.
 */
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . '/message_helper.php';
require_once __DIR__ . '/../helpers.php';
require_once dirname(__DIR__, 2) . '/clases/CPUser.php';

$usuario = CPUser::buscarPorEmail($_SESSION['email'] ?? '');
if (!$usuario) {
    header('Location: ../index.php');
    exit();
}

$message = null;
$messageType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $usuario->setUsername(cp_post_string('username'));
    $usuario->setApellidos(cp_post_string('lastname'));
    $usuario->setDNI(cp_post_string('DNI'));
    $usuario->setTelefono(cp_post_string('telefono'));
    $usuario->setFechaNacimiento(cp_post_string('fechaNacimiento'));
    $nuevoEmail = cp_post_string('email');

    if (!empty($nuevoEmail)) {
        if ($nuevoEmail !== $usuario->getEmail()) {
            $existing = CPUser::buscarPorEmail($nuevoEmail);
            if ($existing && $existing->getId() !== $usuario->getId()) {
                $message = 'El correo ya está en uso por otro usuario.';
                $messageType = 'danger';
            } else {
                $usuario->setEmail($nuevoEmail);
            }
        }
    }

    $uploadDir = dirname(__DIR__, 2) . '/archivos/avatars';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileInfo = $_FILES['avatar'];
        if ($fileInfo['error'] === UPLOAD_ERR_OK && in_array($fileInfo['type'], $allowedTypes, true)) {
            $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $usuario->getId() . '_' . time() . '.' . $extension;
            $destination = $uploadDir . '/' . $filename;

            if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
                $usuario->setAvatar('archivos/avatars/' . $filename);
            } else {
                $message = 'No se pudo subir la imagen de perfil.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP.';
            $messageType = 'danger';
        }
    }

    if ($messageType !== 'danger') {
        $success = $usuario->guardar();
        if ($success) {
            $message = 'Perfil actualizado correctamente.';
            $messageType = 'success';
            $_SESSION['email'] = $usuario->getEmail();
        } else {
            $message = 'Error al guardar los cambios del perfil.';
            $messageType = 'danger';
        }
    }
}
