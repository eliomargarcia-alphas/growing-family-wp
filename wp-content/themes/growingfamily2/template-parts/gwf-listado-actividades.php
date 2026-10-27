<?php
/**
 * Listado de actividades (plugin gwf-actividades): /actividades/, /actividades/edad/<edad>/ y /actividades/trabaja/<término>/.
 * Mismas piezas que los listados de guías y del blog: cabecera, filtros (Qué trabaja y Edad) y tarjetas.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_h    = gwf_act_listing_heading();
$gwf_term = $gwf_h['term'];

$gwf_groups = array();
foreach ( array( array( 'trabaja', 'Qué trabaja', 'Todas' ), array( 'edad', 'Edad', 'Todas' ) ) as list( $gwf_tax, $gwf_label, $gwf_all ) ) {
	$gwf_terms = gwf_act_filter_terms( $gwf_tax );
	if ( ! $gwf_terms ) {
		continue;
	}
	$gwf_items = array(
		array(
			'label'  => $gwf_all,
			'url'    => gwf_act_filter_url(),
			'active' => ! $gwf_term || $gwf_term->taxonomy !== $gwf_tax,
		),
	);
	foreach ( $gwf_terms as $gwf_t ) {
		$gwf_items[] = array(
			'label'  => $gwf_t->name,
			'url'    => gwf_act_filter_url( $gwf_t ),
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
							$gwf_badge = gwf_act_terms( $gwf_pid, 'trabaja' );
							$gwf_badge = $gwf_badge ? $gwf_badge[0] : ( gwf_act_terms( $gwf_pid, 'edad' ) ? gwf_act_terms( $gwf_pid, 'edad' )[0] : null );
							get_template_part(
								'template-parts/gwf-tarjeta',
								null,
								array(
									'badge'      => $gwf_badge,
									'badge_url'  => $gwf_badge ? gwf_act_filter_url( $gwf_badge ) : '',
									'summary'    => has_excerpt() ? get_the_excerpt() : gwf_act_meta( $gwf_pid, 'gwf_act_porque' ),
									'minutes'    => (int) gwf_act_meta( $gwf_pid, 'gwf_act_minutos' ),
									'time_label' => 'min de actividad',
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
						<p>Todavía no hay actividades publicadas aquí.</p>
						<a class="gwf-g-button" href="<?php echo esc_url( gwf_act_filter_url() ); ?>">Ver todas</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>
