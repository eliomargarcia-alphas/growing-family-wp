<?php
/**
 * Plugin Name: Tarjeta GWF Azul
 * Description: Tarjeta azul.
 * Version: 1.0.0
 * Author: Elio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

function gwf_tarjetas_azul_init() {
    // 1. Registrar (no encolar) el script: editor_script lo carga solo en el editor de bloques
    wp_register_script(
        'gwf-tarjetas-azul-script',
        plugins_url( 'block.js', __FILE__ ),
        // Dependencias esenciales: wp-blocks, wp-element (para createElement) y wp-editor (para MediaUpload)
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' )
    );

    // 2. Registrar el bloque
    register_block_type( 'gwf-tarjetas/azul', array(
        'editor_script' => 'gwf-tarjetas-azul-script',
    ) );
}
add_action( 'init', 'gwf_tarjetas_azul_init' );