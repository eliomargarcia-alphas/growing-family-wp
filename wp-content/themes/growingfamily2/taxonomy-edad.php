<?php
/**
 * Listado por edad: de guías (plugin gwf-guias, template-parts/gwf-listado.php) o de actividades (gwf-actividades).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();
// /actividades/edad/<edad>/ es el listado de actividades; el resto, el de guías.
get_template_part( 'actividad' === get_query_var( 'post_type' ) ? 'template-parts/gwf-listado-actividades' : 'template-parts/gwf-listado' );
get_footer();
