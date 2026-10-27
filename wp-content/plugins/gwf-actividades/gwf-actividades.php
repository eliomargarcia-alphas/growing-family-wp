<?php
/**
 * Plugin Name: GWF Actividades
 * Description: Actividades para hacer juntos (capa 1): tipo de contenido, ficha, pasos, imprimible a cambio del correo (lista de Mailchimp), listados por edad y por lo que trabajan.
 * Version: 0.1.0
 * Author: Elio
 * Text Domain: gwf-actividades
 *
 * Estrategia SEO y GEO v1.5 («Actividad para hacer juntos») y wireframe «plantilla-actividad».
 * Independiente de gwf-guias: si está activo, comparte con él la taxonomía Edad y enlaza al pilar y a la ficha del revisor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GWF_ACT_VERSION', '0.1.0' );
define( 'GWF_ACT_DIR', plugin_dir_path( __FILE__ ) );

require GWF_ACT_DIR . 'includes/types.php';
require GWF_ACT_DIR . 'includes/fields.php';
require GWF_ACT_DIR . 'includes/data.php';
require GWF_ACT_DIR . 'includes/listings.php';
require GWF_ACT_DIR . 'includes/print.php';
require GWF_ACT_DIR . 'includes/schema.php';

function gwf_act_activate() {
	gwf_act_register_taxonomies();
	gwf_act_register_type();
	gwf_act_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gwf_act_activate' );

function gwf_act_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gwf_act_deactivate' );

/** Al cambiar de versión: términos iniciales y reglas de URL. */
function gwf_act_maybe_upgrade() {
	if ( get_option( 'gwf_act_version' ) !== GWF_ACT_VERSION ) {
		gwf_act_seed_terms();
		update_option( 'gwf_act_version', GWF_ACT_VERSION );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'gwf_act_maybe_upgrade', 20 );
