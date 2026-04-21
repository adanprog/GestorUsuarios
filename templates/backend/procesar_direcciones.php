<?php
/**
 * PROCESAR DIRECCIONES (Backend)
 * ----------------------------
 * Este archivo procesa las acciones de la página de direcciones:
 * crear, editar o borrar una dirección.
 */
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) session_start();

// Seguridad: solo usuarios logueados pueden usar esta página.
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . '/message_helper.php';
require_once dirname(__DIR__, 2) . '/clases/CPDir.php';
require_once dirname(__DIR__, 2) . '/clases/CPUser.php';

$message = null;
$messageType = null;

// 1. Guardar una nueva dirección.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dir'])) {
    $usuarioActual = CPUser::buscarPorEmail($_SESSION['email']);
    if (!$usuarioActual || !$usuarioActual->canAddDirecciones()) {
        $message = "No tienes permiso para añadir direcciones con tu rol actual.";
        $messageType = cp_alert_type_from_message($message);
    } else {
        $userId = $usuarioActual ? $usuarioActual->getId() : null;
        $dir = new CPDir(
            null,
            trim($_POST['nombre'] ?? ''),
            trim($_POST['descripcion'] ?? ''),
            trim($_POST['ubicacion'] ?? ''),
            trim($_POST['calle'] ?? ''),
            trim($_POST['numero'] ?? ''),
            '',
            trim($_POST['puerta'] ?? ''),
            '',
            trim($_POST['codigoPostal'] ?? ''),
            trim($_POST['ciudad'] ?? ''),
            trim($_POST['provincia'] ?? ''),
            $_SESSION['email'], // Usuario que creó la dirección.
            '',
            'propietario',
            $userId,
            trim($_POST['nombrePropietario'] ?? ''),
            trim($_POST['telefonoPropietario'] ?? ''),
            trim($_POST['emailPropietario'] ?? '')
        );

        if ($dir->guardar()) {
            $dir->sincronizarConDisco();
            $message = "¡Nueva dirección guardada y carpeta creada!";
            $messageType = cp_alert_type_from_message($message);
        }
    }
}

// 2. Editar una dirección existente.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_dir'])) {
    $usuarioActual = CPUser::buscarPorEmail($_SESSION['email']);
    if (!$usuarioActual || !$usuarioActual->canEditDirecciones()) {
        $message = "No tienes permiso para editar direcciones con tu rol actual.";
    } else {
        $dir = CPDir::buscarPorId(trim($_POST['id'] ?? ''));
        if (!$dir) {
            $message = "No se encontró la dirección para editar.";
        } elseif ($usuarioActual->getRoleNormalized() !== 'administrador' && $dir->getEmail() !== $usuarioActual->getEmail()) {
            $message = "No tienes permiso para editar esta dirección.";
        } else {
            $updatedDir = new CPDir(
                $dir->getId(),
                trim($_POST['nombre'] ?? ''),
                $dir->getDescripcion(),
                $dir->getUbicacion(),
                trim($_POST['calle'] ?? ''),
                trim($_POST['numero'] ?? ''),
                $dir->getPiso(),
                trim($_POST['puerta'] ?? ''),
                $dir->getEscalera(),
                trim($_POST['codigoPostal'] ?? ''),
                trim($_POST['ciudad'] ?? ''),
                trim($_POST['provincia'] ?? ''),
                $dir->getEmail(),
                '',
                $dir->getRole(),
                $dir->getUserId(),
                trim($_POST['nombrePropietario'] ?? ''),
                trim($_POST['telefonoPropietario'] ?? ''),
                trim($_POST['emailPropietario'] ?? '')
            );

            if ($updatedDir->guardar()) {
                $message = "Dirección actualizada correctamente.";
            } else {
                $message = "Error al guardar los cambios de la dirección.";
            }
        }
    }
    $messageType = cp_alert_type_from_message($message);
}

// 3. Borrar una dirección.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_dir'])) {
    $usuarioActual = CPUser::buscarPorEmail($_SESSION['email']);
    if (!$usuarioActual || !$usuarioActual->canDeleteDirecciones()) {
        $message = "No tienes permiso para eliminar direcciones con tu rol actual.";
    } else {
        if (CPDir::borrarPorId($_POST['id'])) {
            $message = "La dirección ha sido eliminada con éxito.";
        } else {
            $message = "No se encontró la dirección para eliminar.";
        }
    }
    $messageType = cp_alert_type_from_message($message);
}
?>

