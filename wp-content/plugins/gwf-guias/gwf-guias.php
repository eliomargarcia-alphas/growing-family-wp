<?php
/**
 * Plugin Name: GWF Guías
 * Description: Guías de problema (capa 2) y guías de desarrollo (capa 3) de Growing Family: tipos de contenido, taxonomías, rol Revisor, campos y regla de publicación con revisión experta.
 * Version: 0.2.0
 * Author: Elio
 * Text Domain: gwf-guias
 *
 * Plan: «Plan de implementación de guías en WordPress — Growing Family» (etapas 1 y 2).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GWF_GUIAS_VERSION', '0.2.0' );
define( 'GWF_GUIAS_DIR', plugin_dir_path( __FILE__ ) );

/** Tipos de contenido que gestiona el plugin. */
const GWF_GUIAS_TYPES = array( 'guia', 'desarrollo' );

require GWF_GUIAS_DIR . 'includes/types.php';
require GWF_GUIAS_DIR . 'includes/fields.php';
require GWF_GUIAS_DIR . 'includes/items.php';
require GWF_GUIAS_DIR . 'includes/publish-rule.php';
require GWF_GUIAS_DIR . 'includes/template-tags.php';
require GWF_GUIAS_DIR . 'includes/schema.php';
require GWF_GUIAS_DIR . 'includes/listings.php';
require GWF_GUIAS_DIR . 'includes/seo.php';
require GWF_GUIAS_DIR . 'includes/admin.php';

/** Al activar: rol, permisos, términos iniciales y reglas de URL. */
function gwf_guias_activate() {
	gwf_guias_register_types();
	gwf_guias_register_taxonomies();
	gwf_guias_setup_roles();
	gwf_guias_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gwf_guias_activate' );

function gwf_guias_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gwf_guias_deactivate' );
