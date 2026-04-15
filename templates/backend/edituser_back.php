<?php
/**
 * BACKEND: Cargar usuario para editar
 * ----------------------------------
 * Toma el ID enviado por la URL y recupera los datos del usuario.
 */
$id = $_GET['id'] ?? null;
require_once('procesar_usuarios.php'); // Lógica de botones (guardar/borrar)
require_once dirname(__DIR__, 2) . '/clases/CPUser.php';

if ($id) {
    $usuario = CPUser::buscarPorId($id);
    if (!$usuario) {
        header('Location: ../frontend/usuarios.php');
        exit;
    }
}
