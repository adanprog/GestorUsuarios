<?php
/**
 * VISTA LOGIN
 * ----------
 * Muestra el formulario de inicio de sesión.
 * Al pulsar el botón, envía los datos a login_back.php para validarlos.
 */
error_reporting(E_ALL ^ E_NOTICE);
require_once("templates/backend/login_back.php");
?>

<main class="form-signin m-auto" style="max-width: 330px;">

        <form method="post" class="card shadow p-4">

                <h1 class="h3 mb-3 fw-normal">Iniciar sesión</h1>

                <div class="form-floating">
                        <input
                        type="email"
                        class="form-control"
                        id="floatingInput"
                        placeholder="name@example.com"
                        name="email"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required
                        >
                        <label for="floatingInput">Correo</label>
                </div>

                <div class="form-floating">
                        <input
                        type="password"
                        class="form-control"
                        id="floatingPassword"
                        placeholder="Password"
                        name="password"
                        >
                        <label for="floatingPassword">Contraseña</label>
                </div>

                <!-- Muestra mensajes de error o éxito que vienen de login_back.php -->
                <?php cp_render_alert_message($message ?? null, $messageType ?? null); ?>

                <div class="form-check text-start my-3">
                        <input
                        class="form-check-input"
                        type="checkbox"
                        value="remember-me"
                        id="checkDefault"
                        >
                        <label class="form-check-label" for="checkDefault">
                        Recordarme
                        </label>
                </div>

                <button class="btn btn-primary w-100 py-2" type="submit" name="login">
                        Iniciar sesión
                </button>

        </form>

</main>
