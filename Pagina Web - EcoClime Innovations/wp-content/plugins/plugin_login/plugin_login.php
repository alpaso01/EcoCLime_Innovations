<?php
/*
Plugin Name: Plugin de Login Externo
Plugin URI: https://tusitio.com/plugin-login
Description: Un plugin para manejar el inicio de sesión y registro con una base de datos externa.
Version: 1.0
Author: Tu Nombre
Author URI: https://tusitio.com
License: GPL2
*/

// Evitar acceso directo a este archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Cargar los archivos necesarios
require_once plugin_dir_path( __FILE__ ) . 'includes/conexionbda.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/formulariologin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/botoninicioHeader.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/proceso.php';

// Acciones y filtros
add_action('init', 'procesar_inicio_sesion_registro');
add_shortcode('formulario_inicio_sesion_registro', 'formulario_inicio_sesion_registro');
add_action('wp_head', 'agregar_boton_inicio_sesion');
?>