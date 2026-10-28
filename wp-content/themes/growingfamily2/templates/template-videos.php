<?php
/**
 * Template Name: Vídeos
 * Template Post Type: page
 *
 * Vídeos del blog (/videos/): cabecera como la del listado del blog (migas, título e introducción = extracto de
 * la página) y, debajo, el contenido de la página con el feed de YouTube (plugin Feeds for YouTube Pro,
 * shortcode [youtube-feed feed=N]).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
<main id="site-content" class="gwf-guia gwf-listado gwf-videos">
	<?php
	get_template_part(
		'template-parts/gwf-listado-cabecera',
		null,
		array(
			'title' => get_the_title(),
			'intro' => has_excerpt() ? get_the_excerpt() : '',
		)
	);
	?>
	<div class="container gwf-videos-feed">
		<?php the_content(); ?>
	</div>
</main>
	<?php
endwhile;

get_footer();
