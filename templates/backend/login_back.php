<?php
/**
 * LÓGICA DE LOGIN (Backend)
 * ------------------------
 * Este archivo comprueba si el usuario y la contraseña son correctos.
 */
error_reporting(E_ALL ^ E_NOTICE);
if (session_status() === PHP_SESSION_NONE) session_start();

$error_message = ''; // Aquí guardamos un mensaje cuando el login falla.

// Calculamos la ruta a la zona interna para redirigir después del login.
$baseRuta = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$redireccionHome = $baseRuta . '/templates/frontend/home.php';

// Si el usuario ya había iniciado sesión, lo enviamos directamente al panel.
if (isset($_SESSION['logged_in'])) {
    header('Location: ' . $redireccionHome);
    exit();
}

require_once __DIR__ . '/message_helper.php';

// Procesamos el formulario cuando el usuario pulsa el botón de iniciar sesión.
if (isset($_POST['login'])) {
    require_once dirname(__DIR__, 2) . '/clases/CPUser.php';
    
    $correo = trim($_POST['email']);
    $clave = trim($_POST['password']);

    // Buscamos el usuario por correo en la base de datos.
    $usuario = CPUser::buscarPorEmail($correo);
    
    if ($usuario && $usuario->verificarPassword($clave)) {
        if (!$usuario->isActivo()) {
            // El usuario existe pero su cuenta está desactivada.
            $error_message = 'Cuenta desactivada. Contacta con un administrador.';
            $message = $error_message;
            $messageType = 'danger';
        } else {
            // Login correcto: guardamos la información en la sesión.
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $usuario->getEmail();
            $_SESSION['role'] = $usuario->getRole();
            
            header('Location: ' . $redireccionHome);
            exit();
        }
    } else {
        // Si el correo o la contraseña son incorrectos, mostramos un error.
        $error_message = '¡Error! Correo o contraseña no válidos.';
        $message = $error_message;
        $messageType = 'danger';
    }
}
?>
