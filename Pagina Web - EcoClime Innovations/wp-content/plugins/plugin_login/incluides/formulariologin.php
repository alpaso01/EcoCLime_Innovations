<?php
function formulario_inicio_sesion_registro() {
    ob_start();
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['usuario_externo'])) { ?>
        <div class="formulario-sesion">
            <h2>Iniciar sesión</h2>
            <form method="POST">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login_externo">Iniciar sesión</button>
            </form>
            <h3>¿No tienes cuenta? Regístrate aquí:</h3>
            <form method="POST">
                <label for="reg_username">Usuario:</label>
                <input type="text" id="reg_username" name="reg_username" required>
                <label for="reg_email">Correo electrónico:</label>
                <input type="email" id="reg_email" name="reg_email" required>
                <label for="reg_password">Contraseña:</label>
                <input type="password" id="reg_password" name="reg_password" required>
                <button type="submit" name="register_externo">Registrarse</button>
            </form>
        </div>
    <?php } else {
        echo '<p>Hola, ' . $_SESSION['usuario_externo'] . ' | <a href="?logout_externo=1">Cerrar sesión</a></p>';
    }
    return ob_get_clean();
}
add_shortcode('formulario_inicio_sesion_registro', 'formulario_inicio_sesion_registro');
?>