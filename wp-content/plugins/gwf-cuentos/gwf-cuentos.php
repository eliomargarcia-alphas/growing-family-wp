<?php
/**
 * Plugin Name: GWF Cuentos
 * Description: Cuentos con propósito (capa 1) con recorrido enfocado: tipo Cuento, taxonomías, campos, páginas del cuento y notas de lectura. 2.0: cada cuento es de Ana, de Max o de los dos con selector y variantes {Ana|Max}.
 * Version: 2.0.1
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

define( 'GWF_CUE_VERSION', '2.0.1' );
define( 'GWF_CUE_DIR', plugin_dir_path( __FILE__ ) );

require GWF_CUE_DIR . 'includes/types.php';
require GWF_CUE_DIR . 'includes/fields.php';
require GWF_CUE_DIR . 'includes/settings.php';
require GWF_CUE_DIR . 'includes/blocks.php';
require GWF_CUE_DIR . 'includes/content.php';
require GWF_CUE_DIR . 'includes/listings.php';
require GWF_CUE_DIR . 'includes/schema.php';
require GWF_CUE_DIR . 'includes/links.php';

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

/** Al cambiar de versión: términos iniciales, migraciones y reglas de URL. */
function gwf_cue_maybe_upgrade() {
	$installed = (string) get_option( 'gwf_cue_version' );
	if ( $installed !== GWF_CUE_VERSION ) {
		gwf_cue_migrate_selector();
		gwf_cue_seed_terms();
		update_option( 'gwf_cue_version', GWF_CUE_VERSION );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'gwf_cue_maybe_upgrade', 20 );

/**
 * 2.0: cada cuento es de un protagonista (Ana o Max) o, como en la 1.0, de los dos con selector.
 * Los cuentos que no tienen el campo «Se lee con Ana y con Max» vienen de la 1.0, donde todos tenían selector:
 * se marcan con selector para que se vean igual que antes. Es idempotente (solo toca los que no lo tienen).
 */
function gwf_cue_migrate_selector() {
	$ids = get_posts(
		array(
			'post_type'      => 'cuento',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array( array( 'key' => 'gwf_cue_selector', 'compare' => 'NOT EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	foreach ( $ids as $id ) {
		update_post_meta( $id, 'gwf_cue_selector', '1' );
		update_post_meta( $id, '_gwf_cue_selector', 'field_gwf_cue_selector' );
	}
}
