<?php
/**
 * Listado de guías de problema o de pilares (etapa 6). Lo usan archive-guia.php, archive-desarrollo.php,
 * taxonomy-tema.php, taxonomy-gran_tema.php y taxonomy-edad.php.
 *
 * Mismo esquema que el listado del blog (index.php), con las mismas piezas: cabecera (gwf-listado-cabecera),
 * filtros a la izquierda o en chips (gwf-filtros) y tarjetas .blog-card (gwf-tarjeta).
 * El orden (Destacado, «me gusta», fecha) y los 12 por página los pone el plugin gwf-guias (includes/listings.php).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_h     = gwf_guias_listing_heading();
$gwf_type  = $gwf_h['type'];
$gwf_term  = $gwf_h['term'];
$gwf_topic = 'desarrollo' === $gwf_type ? 'gran_tema' : 'tema';

// Grupos de filtros: «Todos/Todas» activo si no hay término de esa taxonomía.
$gwf_groups = array();
foreach (
	array(
		array( $gwf_topic, 'desarrollo' === $gwf_type ? 'Por tema' : 'Tema', 'Todos' ),
		array( 'edad', 'desarrollo' === $gwf_type ? 'Por edad' : 'Edad', 'Todas' ),
	) as list( $gwf_tax, $gwf_label, $gwf_all )
) {
	$gwf_terms = gwf_guias_filter_terms( $gwf_tax, $gwf_type );
	if ( ! $gwf_terms ) {
		continue;
	}
	$gwf_items = array(
		array(
			'label'  => $gwf_all,
			'url'    => gwf_guias_filter_url( $gwf_type ),
			'active' => ! $gwf_term || $gwf_term->taxonomy !== $gwf_tax,
		),
	);
	foreach ( $gwf_terms as $gwf_t ) {
		$gwf_items[] = array(
			'label'  => $gwf_t->name,
			'url'    => gwf_guias_filter_url( $gwf_type, $gwf_t ),
			'active' => $gwf_term && $gwf_term->term_id === $gwf_t->term_id,
		);
	}
	$gwf_groups[] = array( 'label' => $gwf_label, 'items' => $gwf_items );
}
?>

<main id="site-content" class="gwf-guia gwf-listado">
	<?php get_template_part( 'template-parts/gwf-listado-cabecera', null, array( 'title' => $gwf_h['title'], 'intro' => $gwf_h['intro'] ) ); ?>

	<div class="container-fluid mt-5">
		<div class="row">

			<?php if ( $gwf_groups ) : // Filtros en una columna a la izquierda (escritorio), como «Categorías» en el blog. ?>
				<div class="d-none d-xl-block col-md-2 mb-5">
					<?php get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'side', 'groups' => $gwf_groups ) ); ?>
				</div>
			<?php endif; ?>

			<div class="<?php echo $gwf_groups ? 'col-md-10' : 'col-md-12'; ?> entradas-resultados" id="resultados">

				<?php
				if ( $gwf_groups ) { // En móvil y tableta la columna se oculta: filtros como chips.
					get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'chips', 'groups' => $gwf_groups ) );
				}
				?>

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

							get_template_part(
								'template-parts/gwf-tarjeta',
								null,
								array(
									'badge'     => $gwf_badge,
									'badge_url' => $gwf_badge ? gwf_guias_filter_url( $gwf_type, $gwf_badge ) : '',
									'summary'   => $gwf_summary ? $gwf_summary : get_the_excerpt(),
									'minutes'   => gwf_guias_reading_time( $gwf_pid ),
								)
							);
						endwhile;
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
