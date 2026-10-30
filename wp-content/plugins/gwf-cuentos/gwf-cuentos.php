<?php
/**
 * Plugin Name: GWF Cuentos
 * Description: Cuentos con propósito (capa 1) con recorrido enfocado: tipo Cuento, taxonomías, campos, páginas del cuento, notas de lectura y variantes {Ana|Max}.
 * Version: 0.2.0
 * Author: Elio
 * Text Domain: gwf-cuentos
 *
 * Plan: «Plan de implementación de cuentos en WordPress — Recorrido enfocado» (29 oct 2026).
 * Sin ACF PRO por ahora: las páginas del cuento se separan con el bloque nativo «Salto de página», las notas de
 * lectura son estilos de bloque del párrafo y los textos globales viven en Cuentos → Ajustes (ver CLAUDE.md).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GWF_CUE_VERSION', '0.2.0' );
define( 'GWF_CUE_DIR', plugin_dir_path( __FILE__ ) );

require GWF_CUE_DIR . 'includes/types.php';
require GWF_CUE_DIR . 'includes/fields.php';
require GWF_CUE_DIR . 'includes/settings.php';
require GWF_CUE_DIR . 'includes/blocks.php';
require GWF_CUE_DIR . 'includes/content.php';
require GWF_CUE_DIR . 'includes/listings.php';

function gwf_cue_activate() {
	gwf_cue_register_taxonomies();
	gwf_cue_register_type();
	gwf_cue_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gwf_cue_activate' );

function gwf_cue_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gwf_cue_deactivate' );

/** Al cambiar de versión: términos iniciales y reglas de URL. */
function gwf_cue_maybe_upgrade() {
	if ( get_option( 'gwf_cue_version' ) !== GWF_CUE_VERSION ) {
		gwf_cue_seed_terms();
		update_option( 'gwf_cue_version', GWF_CUE_VERSION );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'gwf_cue_maybe_upgrade', 20 );
