<?php
function conectar_db_externa() {
    $host = 'localhost'; // O la IP de la BD externa
    $usuario = 'root';
    $contraseña = '';
    $dbname = 'paginaweb';

    $conexion = new mysqli($host, $usuario, $contraseña, $dbname);

    if ($conexion->connect_error) {
        die("Error de conexión a la BD externa: " . $conexion->connect_error);
    }
    return $conexion;
}
?>