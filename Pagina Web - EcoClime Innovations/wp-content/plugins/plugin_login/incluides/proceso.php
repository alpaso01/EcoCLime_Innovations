<?php
function procesar_inicio_sesion_registro() {
    if (!isset($_SESSION)) {
        session_start();
    }

    $conexion = conectar_db_externa();

    // Procesar inicio de sesión
    if (isset($_POST['login_externo'])) {
        $username = $conexion->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        $sql = "SELECT * FROM usuarios WHERE username = '$username'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_externo'] = $usuario['username'];
                wp_redirect(home_url());
                exit;
            } else {
                echo '<p style="color:red;">Contraseña incorrecta.</p>';
            }
        } else {
            echo '<p style="color:red;">El usuario no existe.</p>';
        }
    }

    // Procesar registro
    if (isset($_POST['register_externo'])) {
        $username = $conexion->real_escape_string($_POST['reg_username']);
        $email = $conexion->real_escape_string($_POST['reg_email']);
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conexion->query($sql) === TRUE) {
            echo '<p>Cuenta creada con éxito. Ahora puedes iniciar sesión.</p>';
        } else {
            echo '<p style="color:red;">Error al registrar el usuario.</p>';
        }
    }

    // Cerrar sesión
    if (isset($_GET['logout_externo'])) {
        session_destroy();
        wp_redirect(home_url());
        exit;
    }
}
?>