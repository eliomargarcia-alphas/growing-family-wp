<?php
/**
 * Listado de guías de problema o de pilares (etapa 6). Lo usan archive-guia.php, archive-desarrollo.php,
 * taxonomy-tema.php, taxonomy-gran_tema.php y taxonomy-edad.php.
 *
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

/** Chip de filtro: activo si es el término del listado (o «Todas» sin término). */
$gwf_chip = function ( $label, $url, $active ) {
	return '<li><a href="' . esc_url( $url ) . '"' . ( $active ? ' class="is-active" aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a></li>';
};
?>

<main id="site-content" class="gwf-guia gwf-listado">
	<div class="container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<header class="gwf-l-header">
			<h1 class="gwf-g-title"><?php echo esc_html( $gwf_h['title'] ); ?></h1>
			<p class="gwf-l-intro"><?php echo esc_html( $gwf_h['intro'] ); ?></p>
		</header>

		<?php if ( $gwf_topics || $gwf_ages ) : ?>
			<nav class="gwf-l-filters" aria-label="Filtrar">
				<?php if ( $gwf_topics ) : ?>
					<div class="gwf-l-filter">
						<p class="gwf-l-filter-label"><?php echo 'desarrollo' === $gwf_type ? 'Por tema' : 'Tema'; ?></p>
						<ul>
							<?php
							// phpcs:disable WordPress.Security.EscapeOutput -- $gwf_chip escapa.
							echo $gwf_chip( 'Todos', gwf_guias_filter_url( $gwf_type ), ! $gwf_term || 'edad' === $gwf_term->taxonomy );
							foreach ( $gwf_topics as $gwf_t ) {
								echo $gwf_chip( $gwf_t->name, gwf_guias_filter_url( $gwf_type, $gwf_t ), $gwf_term && $gwf_term->term_id === $gwf_t->term_id );
							}
							// phpcs:enable
							?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if ( $gwf_ages ) : ?>
					<div class="gwf-l-filter">
						<p class="gwf-l-filter-label"><?php echo 'desarrollo' === $gwf_type ? 'Por edad' : 'Edad'; ?></p>
						<ul>
							<?php
							// phpcs:disable WordPress.Security.EscapeOutput
							echo $gwf_chip( 'Todas', gwf_guias_filter_url( $gwf_type ), ! $gwf_term || 'edad' !== $gwf_term->taxonomy );
							foreach ( $gwf_ages as $gwf_a ) {
								echo $gwf_chip( $gwf_a->name, gwf_guias_filter_url( $gwf_type, $gwf_a ), $gwf_term && $gwf_term->term_id === $gwf_a->term_id );
							}
							// phpcs:enable
							?>
						</ul>
					</div>
				<?php endif; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<ul class="gwf-l-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$gwf_pid     = get_the_ID();
					// Tema de la tarjeta: el principal de Yoast si está marcado; si no, hasta dos temas.
					$gwf_primary = function_exists( 'yoast_get_primary_term_id' ) ? (int) yoast_get_primary_term_id( $gwf_topic, $gwf_pid ) : 0;
					$gwf_terms   = get_the_terms( $gwf_pid, $gwf_topic );
					$gwf_terms   = ( $gwf_terms && ! is_wp_error( $gwf_terms ) ) ? $gwf_terms : array();
					if ( $gwf_primary && term_exists( $gwf_primary, $gwf_topic ) ) {
						$gwf_topic_n = get_term( $gwf_primary, $gwf_topic )->name;
					} else {
						$gwf_topic_n = implode( ' · ', wp_list_pluck( array_slice( $gwf_terms, 0, 2 ), 'name' ) );
					}
					$gwf_summary = get_post_meta( $gwf_pid, 'desarrollo' === $gwf_type ? 'gwf_definicion' : 'gwf_respuesta', true );
					$gwf_summary = $gwf_summary ? $gwf_summary : get_the_excerpt();
					?>
					<li class="gwf-l-card">
						<a href="<?php the_permalink(); ?>">
							<?php echo gwf_g_card_image( $gwf_pid ); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de WordPress. ?>
							<span class="gwf-l-body">
								<span class="gwf-l-meta"><?php echo esc_html( implode( ' · ', array_filter( array( $gwf_topic_n, gwf_guias_ages_label( $gwf_pid ) ) ) ) ); ?></span>
								<?php the_title( '<h2 class="gwf-l-title">', '</h2>' ); ?>
								<?php if ( $gwf_summary ) : ?><span class="gwf-l-summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $gwf_summary ), 24, '…' ) ); ?></span><?php endif; ?>
								<span class="gwf-l-foot"><?php echo gwf_g_read_html( gwf_guias_reading_time( $gwf_pid ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							</span>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

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
</main>
