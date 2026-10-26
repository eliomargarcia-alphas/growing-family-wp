<?php
/**
 * Listado de guías de problema o de pilares (etapa 6). Lo usan archive-guia.php, archive-desarrollo.php,
 * taxonomy-tema.php, taxonomy-gran_tema.php y taxonomy-edad.php.
 *
 * Mismo esquema que el listado del blog (index.php): migas arriba, título centrado, filtros en una columna a la
 * izquierda y tarjetas .blog-card (tema sobre la foto, «me gusta» abajo sobre la foto, «Leer más»).
 * El orden (Destacado, «me gusta», fecha) y los 12 por página los pone el plugin gwf-guias (includes/listings.php).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_h      = gwf_guias_listing_heading();
$gwf_type   = $gwf_h['type'];
$gwf_term   = $gwf_h['term'];
$gwf_topic  = 'desarrollo' === $gwf_type ? 'gran_tema' : 'tema';
$gwf_topics = gwf_guias_filter_terms( $gwf_topic, $gwf_type );
$gwf_ages   = gwf_guias_filter_terms( 'edad', $gwf_type );
$gwf_groups = array_filter(
	array(
		array( 'label' => 'desarrollo' === $gwf_type ? 'Por tema' : 'Tema', 'all' => 'Todos', 'terms' => $gwf_topics, 'tax' => $gwf_topic ),
		array( 'label' => 'desarrollo' === $gwf_type ? 'Por edad' : 'Edad', 'all' => 'Todas', 'terms' => $gwf_ages, 'tax' => 'edad' ),
	),
	function ( $g ) {
		return (bool) $g['terms'];
	}
);

/** Enlace de filtro; el activo va marcado. */
$gwf_filter_link = function ( $label, $url, $active ) {
	return '<li><a href="' . esc_url( $url ) . '"' . ( $active ? ' class="is-active" aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a></li>';
};
/** ¿«Todos/Todas» activo en el grupo? (sin término, o el término es de otra taxonomía). */
$gwf_all_active = function ( $tax ) use ( $gwf_term ) {
	return ! $gwf_term || $gwf_term->taxonomy !== $tax;
};

/** Lista de filtros de un grupo (la misma para la columna izquierda y para los chips de móvil). */
$gwf_filter_list = function ( $group ) use ( $gwf_type, $gwf_term, $gwf_filter_link, $gwf_all_active ) {
	$html = $gwf_filter_link( $group['all'], gwf_guias_filter_url( $gwf_type ), $gwf_all_active( $group['tax'] ) );
	foreach ( $group['terms'] as $t ) {
		$html .= $gwf_filter_link( $t->name, gwf_guias_filter_url( $gwf_type, $t ), $gwf_term && $gwf_term->term_id === $t->term_id );
	}
	return $html;
};
?>

<main id="site-content" class="gwf-guia gwf-listado">
	<section class="gwf-l-head">
		<div class="container">
			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-l-migas" aria-label="Migas de pan">', '</nav>' ); } ?>
			<h1 class="font-size-44 text-center mb-4 color-navy mt-5"><?php echo esc_html( $gwf_h['title'] ); ?></h1>
			<p class="font-size-16 text-center text-black mb-0 gwf-l-intro"><?php echo esc_html( $gwf_h['intro'] ); ?></p>
		</div>
	</section>

	<div class="container-fluid mt-5">
		<div class="row">

			<?php if ( $gwf_groups ) : // Filtros en una columna a la izquierda (escritorio), como «Categorías» en el blog. ?>
				<div class="d-none d-xl-block col-md-2 mb-5">
					<nav class="gwf-l-side" aria-label="Filtrar">
						<?php foreach ( $gwf_groups as $gwf_group ) : ?>
							<p class="gwf-l-side-title"><?php echo esc_html( $gwf_group['label'] ); ?></p>
							<ul><?php echo $gwf_filter_list( $gwf_group ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en las funciones. ?></ul>
						<?php endforeach; ?>
					</nav>
				</div>
			<?php endif; ?>

			<div class="<?php echo $gwf_groups ? 'col-md-10' : 'col-md-12'; ?> entradas-resultados" id="resultados">

				<?php if ( $gwf_groups ) : // En móvil y tableta la columna se oculta: filtros como chips. ?>
					<nav class="gwf-l-filters d-xl-none" aria-label="Filtrar">
						<?php foreach ( $gwf_groups as $gwf_group ) : ?>
							<div class="gwf-l-filter">
								<p class="gwf-l-filter-label"><?php echo esc_html( $gwf_group['label'] ); ?></p>
								<ul><?php echo $gwf_filter_list( $gwf_group ); // phpcs:ignore WordPress.Security.EscapeOutput ?></ul>
							</div>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>

				<?php if ( have_posts() ) : ?>
					<div class="row">
						<?php
						while ( have_posts() ) :
							the_post();
							$gwf_pid = get_the_ID();

							// Etiqueta sobre la foto: el tema principal de Yoast o el primero; en pilares sin gran tema, la edad.
							$gwf_primary = function_exists( 'yoast_get_primary_term_id' ) ? (int) yoast_get_primary_term_id( $gwf_topic, $gwf_pid ) : 0;
							$gwf_badge   = $gwf_primary ? get_term( $gwf_primary, $gwf_topic ) : null;
							if ( ! $gwf_badge || is_wp_error( $gwf_badge ) ) {
								$gwf_list  = get_the_terms( $gwf_pid, $gwf_topic );
								$gwf_badge = ( $gwf_list && ! is_wp_error( $gwf_list ) ) ? $gwf_list[0] : null;
							}
							if ( ! $gwf_badge ) {
								$gwf_list  = get_the_terms( $gwf_pid, 'edad' );
								$gwf_badge = ( $gwf_list && ! is_wp_error( $gwf_list ) ) ? $gwf_list[0] : null;
							}

							$gwf_summary = get_post_meta( $gwf_pid, 'desarrollo' === $gwf_type ? 'gwf_definicion' : 'gwf_respuesta', true );
							$gwf_summary = $gwf_summary ? $gwf_summary : get_the_excerpt();
							?>
							<div class="col-xl-4 article mb-5 resultado">
								<article class="blog-card">
									<div class="blog-image-wrapper">
										<?php if ( $gwf_badge ) : ?>
											<p class="cat font-size-12 text-uppercase">
												<a href="<?php echo esc_url( gwf_guias_filter_url( $gwf_type, $gwf_badge ) ); ?>" class="text-white font-size-12 text-decoration-none bg-navy px-3 py-2 border-radius-24 d-inline-block"><?php echo esc_html( $gwf_badge->name ); ?></a>
											</p>
										<?php endif; ?>
										<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
											<?php if ( has_post_thumbnail() ) : ?>
												<?php the_post_thumbnail( 'medium_large', array( 'class' => 'blog-image', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1200px) 30vw, 100vw' ) ); ?>
											<?php else : ?>
												<img src="<?php echo esc_url( content_url( '/uploads/2026/08/article-1-opt.webp' ) ); ?>" alt="" class="blog-image" width="374" height="200" loading="lazy" decoding="async">
											<?php endif; ?>
										</a>
										<?php echo do_shortcode( '[wp_ulike]' ); ?>
									</div>
									<div class="blog-card-body">
										<h2 class="blog-card-title"><a href="<?php the_permalink(); ?>" class="gwf-l-title-link"><?php the_title(); ?></a></h2>
										<?php if ( $gwf_summary ) : ?><p class="gwf-l-summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $gwf_summary ), 24, '…' ) ); ?></p><?php endif; ?>
										<div class="blog-meta"><?php echo gwf_g_read_html( gwf_guias_reading_time( $gwf_pid ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										<a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">Leer más<span class="screen-reader-text">: <?php the_title(); ?></span></a>
									</div>
								</article>
							</div>
						<?php endwhile; ?>
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
				<?php else : ?>
					<div class="gwf-g-block gwf-l-empty">
						<p>Todavía no hay <?php echo 'desarrollo' === $gwf_type ? 'guías de desarrollo' : 'guías'; ?> publicadas aquí.</p>
						<a class="gwf-g-button" href="<?php echo esc_url( gwf_guias_filter_url( $gwf_type ) ); ?>">Ver todas</a>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</main>
