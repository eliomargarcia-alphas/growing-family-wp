<?php
/**
 * Ficha de una persona del equipo: /equipo/<persona>/ (plugin gwf-guias, includes/people.php).
 * Solo llegan aquí las fichas públicas; el resto de archivos de autor redirige a la portada (inc/gwf-blog.php).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();
get_template_part( 'template-parts/gwf-persona' );
get_footer();
