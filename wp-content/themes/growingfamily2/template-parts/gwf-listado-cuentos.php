<?php
/**
 * Listado de cuentos (plugin gwf-cuentos): /cuentos/, /cuentos/emocion/<término>/, /cuentos/situacion/<término>/,
 * /cuentos/para-contar/<término>/ y /cuentos/edad/<edad>/. Mismas piezas que los listados de guías, actividades y blog:
 * cabecera, filtros (Emoción, Situación y Edad) y tarjetas.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_h    = gwf_cue_listing_heading();
$gwf_term = $gwf_h['term'];

$gwf_groups = array();
foreach ( array( array( 'emocion', 'Emoción', 'Todas' ), array( 'situacion', 'Situación', 'Todas' ), array( 'edad', 'Edad', 'Todas' ) ) as list( $gwf_tax, $gwf_label, $gwf_all ) ) {
	$gwf_terms = gwf_cue_filter_terms( $gwf_tax );
	if ( ! $gwf_terms ) {
		continue;
	}
	$gwf_items = array(
		array(
			'label'  => $gwf_all,
			'url'    => gwf_cue_filter_url(),
			'active' => ! $gwf_term || $gwf_term->taxonomy !== $gwf_tax,
		),
	);
	foreach ( $gwf_terms as $gwf_t ) {
		$gwf_items[] = array(
			'label'  => $gwf_t->name,
			'url'    => gwf_cue_filter_url( $gwf_t ),
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
			<?php if ( $gwf_groups ) : ?>
				<div class="d-none d-xl-block col-md-2 mb-5">
					<?php get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'side', 'groups' => $gwf_groups ) ); ?>
				</div>
			<?php endif; ?>

			<div class="<?php echo $gwf_groups ? 'col-md-10' : 'col-md-12'; ?> entradas-resultados" id="resultados">
				<?php
				if ( $gwf_groups ) {
					get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'chips', 'groups' => $gwf_groups ) );
				}
				?>

				<?php if ( have_posts() ) : ?>
					<div class="row">
						<?php
						while ( have_posts() ) :
							the_post();
							$gwf_pid   = get_the_ID();
							// Etiqueta sobre la foto: la emoción; si no tiene, la situación.
							$gwf_badge = gwf_cue_terms( $gwf_pid, 'emocion' );
							$gwf_badge = $gwf_badge ? $gwf_badge : gwf_cue_terms( $gwf_pid, 'situacion' );
							$gwf_badge = $gwf_badge ? $gwf_badge[0] : null;
							get_template_part(
								'template-parts/gwf-tarjeta',
								null,
								array(
									'badge'      => $gwf_badge,
									'badge_url'  => $gwf_badge ? gwf_cue_filter_url( $gwf_badge ) : '',
									'summary'    => has_excerpt() ? get_the_excerpt() : gwf_cue_plain_variants( (string) get_post_meta( $gwf_pid, 'gwf_cue_asi_empieza', true ), $gwf_pid ),
									'minutes'    => gwf_cue_minutes( $gwf_pid ),
									'time_label' => 'min para contarlo',
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
						<p>Todavía no hay cuentos publicados aquí.</p>
						<a class="gwf-g-button" href="<?php echo esc_url( gwf_cue_filter_url() ); ?>">Ver todos</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>
