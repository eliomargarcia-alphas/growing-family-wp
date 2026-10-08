<?php
/**
 * Plugin Name: Tarjeta GWF Imagen + Texto
 * Description: Tarjeta con título, texto e imagen.
 * Version: 1.0.0
 * Author: Elio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

function gwf_tarjetas_imagen_texto_init() {
    // 1. Registrar (no encolar) el script: editor_script lo carga solo en el editor de bloques
    wp_register_script(
        'gwf-tarjetas-imagen-texto-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ) 
    );

    // 2. Registrar el bloque
    register_block_type( 'gwf-tarjetas/imagen-texto', array(
        'editor_script' => 'gwf-tarjetas-imagen-texto-script',
    ) );
}
add_action( 'init', 'gwf_tarjetas_imagen_texto_init' );
?>