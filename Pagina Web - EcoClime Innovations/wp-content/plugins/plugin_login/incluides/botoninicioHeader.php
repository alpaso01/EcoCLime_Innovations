<?php
function agregar_boton_inicio_sesion() {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION['usuario_externo'])) {
        echo '<button class="saludo-usuario">Hola, ' . esc_html($_SESSION['usuario_externo']) . '</button>';
        echo '<div class="opciones-desplegables">
                  <a href="?logout_externo=1">Salir</a>
              </div>';
    } else {
        echo '<button class="login-boton"><a href="' . home_url('/iniciar-sesion') . '">Iniciar Sesión</a></button>';
    }
}
add_shortcode('boton_inicio_sesion', 'agregar_boton_inicio_sesion');
?>