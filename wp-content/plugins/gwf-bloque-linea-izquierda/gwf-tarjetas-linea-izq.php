<?php
/**
 * Plugin Name: Tarjeta GWF Linea Izquierda
 * Description: Tarjeta con linea izquierda.
 * Version: 1.0.0
 * Author: Elio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

function gwf_tarjetas_linea_izq_init() {
    // 1. Encolar el script de JavaScript
    wp_enqueue_script(
        'gwf-tarjetas-linea-izq-script',
        plugins_url( 'block.js', __FILE__ ),
        // Dependencias esenciales: wp-blocks, wp-element (para createElement) y wp-editor (para MediaUpload)
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ) 
    );

    // 2. Registrar el bloque
    register_block_type( 'gwf-tarjetas/linea-izq', array(
        'editor_script' => 'gwf-tarjetas-linea-izq-script',
    ) );
}
add_action( 'init', 'gwf_tarjetas_linea_izq_init' );