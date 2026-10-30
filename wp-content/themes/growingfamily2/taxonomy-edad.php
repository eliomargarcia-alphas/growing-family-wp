<?php
/**
 * Listado por edad: de guías (plugin gwf-guias, template-parts/gwf-listado.php), de actividades (gwf-actividades) o de cuentos (gwf-cuentos).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();
// /actividades/edad/<edad>/ y /cuentos/edad/<edad>/ son los listados de actividades y de cuentos; el resto, el de guías.
$gwf_parts = array(
	'actividad' => 'template-parts/gwf-listado-actividades',
	'cuento'    => 'template-parts/gwf-listado-cuentos',
);
get_template_part( $gwf_parts[ get_query_var( 'post_type' ) ] ?? 'template-parts/gwf-listado' );
get_footer();
