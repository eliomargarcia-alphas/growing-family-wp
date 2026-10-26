<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * En Growing Family es el listado del blog (/blog/, /blog/tema/x/, etiquetas y búsqueda). Usa las mismas piezas
 * que los listados de guías: cabecera (gwf-listado-cabecera), filtros (gwf-filtros) y tarjetas (gwf-tarjeta).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();

// Helper para calcular el tiempo de lectura dinámico
function gf_get_reading_time() {
    $word_count = str_word_count( strip_tags( get_the_content() ) );
    $reading_time = ceil( $word_count / 200 );
    return ( $reading_time < 1 ) ? 1 : $reading_time;
}

// Cabecera
if ( is_search() ) {
	$gwf_title = 'Resultados de la búsqueda para: ' . get_search_query();
	$gwf_intro = '';
} elseif ( is_home() ) {
	$gwf_title = 'Recursos para padres';
	$gwf_intro = 'Acompañándote en la etapa más importante de la vida de tus hijos con artículos basados en neurociencia y pedagogía.';
} else {
	$gwf_title = single_term_title( '', false );
	$gwf_intro = wp_strip_all_tags( term_description() );
}

// Filtros: los temas (categorías) con entradas publicadas
$gwf_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/blog/' );
$gwf_items    = array(
	array(
		'label'  => 'Todos',
		'url'    => $gwf_blog_url,
		'active' => is_home(),
	),
);
foreach ( get_categories( array( 'hide_empty' => true, 'exclude' => array( 41, 32, 6, 42 ) ) ) as $gwf_cat ) {
	$gwf_items[] = array(
		'label'  => $gwf_cat->name,
		'url'    => get_category_link( $gwf_cat ),
		'active' => is_category( $gwf_cat->term_id ),
	);
}
$gwf_groups = array( array( 'label' => 'Temas', 'items' => $gwf_items ) );

/** Tarjeta de la entrada actual del loop, con su tema principal (Yoast) sobre la foto. */
$gwf_blog_card = function () {
	$pid     = get_the_ID();
	$primary = function_exists( 'yoast_get_primary_term_id' ) ? (int) yoast_get_primary_term_id( 'category', $pid ) : 0;
	$badge   = $primary ? get_term( $primary, 'category' ) : null;
	if ( ! $badge || is_wp_error( $badge ) ) {
		$badge = null;
		foreach ( get_the_category( $pid ) as $cat ) {
			if ( 'blog' !== strtolower( $cat->name ) ) {
				$badge = $cat;
				break;
			}
		}
	}
	get_template_part(
		'template-parts/gwf-tarjeta',
		null,
		array(
			'badge'     => $badge,
			'badge_url' => $badge ? get_category_link( $badge ) : '',
			'summary'   => get_the_excerpt(),
			'minutes'   => gf_get_reading_time(),
		)
	);
};
?>

<main id="site-content" class="gwf-guia gwf-listado gwf-blog">
	<?php get_template_part( 'template-parts/gwf-listado-cabecera', null, array( 'title' => $gwf_title, 'intro' => $gwf_intro ) ); ?>

	<div class="container-fluid mt-5">
		<div class="row">

			<div class="d-none d-xl-block col-md-2 mb-5">
				<?php get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'side', 'groups' => $gwf_groups ) ); ?>
			</div>

			<div class="col-md-10 entradas-resultados" id="resultados">
				<?php get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'chips', 'groups' => $gwf_groups ) ); ?>

				<?php if ( is_home() ) { ?>
					<?php
					// Paso 1: Obtiene los IDs de los posts con likes, ordenados de mayor a menor.
					$popular_post_ids = (array) wp_ulike_get_popular_items_ids(array(
						'type'   => 'post',
						'status' => 'like',
						'period' => 'all'
					));

					// Paso 2: Obtiene los IDs de todos los posts, excluyendo los ya obtenidos.
					$all_posts_query = new WP_Query(array(
						'posts_per_page'   => -1, // Obtiene todos los posts
						'fields'           => 'ids', // Solo devuelve los IDs
						'post__not_in'     => $popular_post_ids,
						'category__not_in' => array(41, 32, 6, 42)
					));
					$other_post_ids = $all_posts_query->posts;

					// Paso 3: Populares primero, sin repetir, hasta 60.
					$ordered_ids = array_slice( array_values( array_unique( array_merge( $popular_post_ids, $other_post_ids ) ) ), 0, 60 );

					// Paso 4: Consulta principal con la lista ordenada.
					$my_query = new WP_Query(array(
						'post__in'         => $ordered_ids ? $ordered_ids : array( 0 ),
						'orderby'          => 'post__in',
						'posts_per_page'   => 60,
						'category__not_in' => array(41, 32, 6)
					));

					if ( $my_query->have_posts() ) {
					?>
						<div class="row">
							<?php
							while ( $my_query->have_posts() ) {
								$my_query->the_post();
								$gwf_blog_card();
							}
							?>
						</div>
					<?php
						wp_reset_postdata();
					}
					?>
				<?php } elseif ( have_posts() ) { ?>
					<div class="row">
						<?php
						while ( have_posts() ) {
							the_post();
							$gwf_blog_card();
						}
						?>
					</div>
					<?php
					$gwf_pages = paginate_links(
						array(
							'type'      => 'list',
							'prev_text' => '‹ Anteriores',
							'next_text' => 'Siguientes ›',
							'mid_size'  => 1,
						)
					);
					if ( $gwf_pages ) {
						echo '<nav class="gwf-l-pages" aria-label="Páginas">' . $gwf_pages . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de WordPress.
					}
					?>
				<?php } else { ?>
					<div class="container" style=" min-height: 50vh; ">
						<div class="col-xl-12 color-barium font-size-20">
							<h2 class="font-size-32 color-barium fw-semibold mb-4">Sin resultados</h2>
							<p class="font-size-24 color-barium fw-semibold">¿Desea intentar con otro término de búsqueda?</p>
							<?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

</main><!-- #site-content -->

<?php
get_footer();
