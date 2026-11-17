<?php
/**
 * Listado de cuentos rediseñado (plugin gwf-cuentos): /cuentos/, /cuentos/emocion/<término>/, /cuentos/situacion/<término>/,
 * /cuentos/para-contar/<término>/ y /cuentos/edad/<edad>/. 
 * 
 * Estructura mejorada:
 * - Cabecera con título, subtítulo y buscador
 * - Filtros como pills/chips
 * - Tarjetas estilo "libro" con lomo decorativo
 * - Sección "Próximamente" para cuentos no publicados
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

<main id="site-content" class="gwf-guia gwf-listado gwf-cuentos-listado">
	<!-- Cabecera con título, subtítulo y buscador -->
	<section class="gwf-cuentos-header py-5">
		<div class="container">
			<div class="text-center mb-4">
				<!-- Badge de introducción -->
				<div class="d-inline-block mb-3">
					<span class="badge badge-light-blue rounded-pill small">
						✨ Biblioteca para la Calma y las Emociones
					</span>
				</div>

				<!-- Título principal -->
				<h1 class="gwf-cuentos-title h2 fw-bold mb-3">
					<?php echo esc_html( $gwf_h['title'] ); ?>
				</h1>

				<!-- Subtítulo -->
				<p class="gwf-cuentos-intro lead text-muted text-center mx-auto">
					<?php echo wp_kses_post( $gwf_h['intro'] ); ?>
				</p>
			</div>

			<!-- Buscador: formulario GET sobre el archivo de cuentos (?s=) -->
			<div class="gwf-cuentos-search mb-5">
				<form role="search" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'cuento' ) ); ?>" class="position-relative">
					<svg class="gwf-search-icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"></circle><path d="M20 20l-4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path></svg>
					<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" class="form-control gwf-search-input" placeholder="¿Qué emoción o situación exploramos hoy? (ej. miedo, celos, enfado...)" aria-label="Buscar cuentos" id="gwf-cuentos-search">
				</form>
			</div>

						<!-- Filtros como pills: un solo "Todos" + términos de emoción, situación y edad -->
			<?php if ( $gwf_groups ) : ?>
				<div class="gwf-cuentos-filters">
					<a href="<?php echo esc_url( gwf_cue_filter_url() ); ?>" class="btn btn-sm rounded-pill gwf-filter-pill <?php echo $gwf_term ? 'btn-outline-secondary' : 'btn-primary gwf-filter-pill-all'; ?>">
						Todos
					</a>
					<?php foreach ( $gwf_groups as $gwf_group ) : ?>
						<?php foreach ( array_slice( $gwf_group['items'], 1 ) as $gwf_item ) : ?>
							<a href="<?php echo esc_url( $gwf_item['url'] ); ?>" class="btn btn-sm rounded-pill gwf-filter-pill <?php echo $gwf_item['active'] ? 'btn-primary' : 'btn-outline-secondary'; ?>">
								<?php echo esc_html( $gwf_item['label'] ); ?>
							</a>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Listado de tarjetas de cuentos -->
	<section class="gwf-cuentos-grid py-5">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="row g-4">
					<?php
					while ( have_posts() ) :
						the_post();
						$gwf_pid    = get_the_ID();
						$gwf_badge  = gwf_cue_terms( $gwf_pid, 'emocion' );
						$gwf_badge  = $gwf_badge ? $gwf_badge : gwf_cue_terms( $gwf_pid, 'situacion' );
						$gwf_badge  = $gwf_badge ? $gwf_badge[0] : null;
						$gwf_age    = gwf_cue_terms( $gwf_pid, 'edad' );
						$gwf_age    = $gwf_age ? $gwf_age[0]->name : '';
						$gwf_mins   = gwf_cue_minutes( $gwf_pid );
						$gwf_summ   = has_excerpt() ? get_the_excerpt() : gwf_cue_plain_variants( (string) get_post_meta( $gwf_pid, 'gwf_cue_asi_empieza', true ), $gwf_pid );
						$gwf_protas = get_post_meta( $gwf_pid, 'gwf_cue_protagonistas', true );
						?>
						<?php
						$gwf_emoji_map = array(
							'miedo'       => '🌙',
							'celos'       => '🧸',
							'enfado'      => '🎈',
							'ansiedad'    => '😟',
							'tristeza'    => '💙',
							'soledad'     => '🤝',
							'frustración' => '💪',
						);
						?>
						<div class="col-12 col-md-6 col-xl-4">
							<article class="gwf-cuento-card">
								<a class="gwf-cuento-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'large', array( 'class' => 'gwf-cuento-img', 'alt' => '', 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php echo gwf_placeholder_img( 'gwf-cuento-img' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en la función. ?>
									<?php endif; ?>
									<?php if ( $gwf_badge ) : ?>
										<span class="gwf-cuento-chip gwf-cuento-chip--emotion"><?php echo esc_html( ( $gwf_emoji_map[ $gwf_badge->slug ] ?? '📚' ) . ' ' . $gwf_badge->name ); ?></span>
									<?php endif; ?>
									<span class="gwf-cuento-chip gwf-cuento-chip--time">⏱ <?php echo absint( $gwf_mins ); ?> min</span>
									<span class="gwf-cuento-readwith">Para leer con: <strong>Ana</strong> • <strong>Max</strong></span>
								</a>

								<div class="gwf-cuento-body">
									<p class="gwf-cuento-meta">
										<?php if ( $gwf_age ) : ?>
											<span>★ Edad <?php echo esc_html( $gwf_age ); ?></span>
											<span class="gwf-cuento-dot">•</span>
										<?php endif; ?>
										<span><?php echo absint( count( gwf_cue_pages( $gwf_pid ) ) ); ?> páginas ilustradas</span>
									</p>

									<h3 class="gwf-cuento-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>

									<?php if ( $gwf_summ ) : ?>
										<p class="gwf-cuento-desc"><?php echo wp_kses_post( $gwf_summ ); ?></p>
									<?php endif; ?>

									<p class="gwf-cuento-aval"><span class="gwf-cuento-check">✓</span> Incluye pregunta de conexión guiada para padres</p>

									<div class="gwf-cuento-actions">
										<a href="<?php the_permalink(); ?>" class="gwf-cuento-btn">Abrir cuento ›</a>
									</div>
								</div>
							</article>
						</div>
					<?php endwhile; ?>

					<!-- Tarjeta Próximamente -->
					<div class="col-12 col-md-6 col-xl-4">
						<article class="gwf-cuento-card gwf-cuento-soon">
							<div class="gwf-soon-icon" aria-hidden="true">🎈</div>
							<span class="gwf-soon-badge">Próximo Estreno</span>
							<h3 class="gwf-cuento-title">El dragón que aprendió a soplar despacito</h3>
							<p class="gwf-cuento-desc">Un cuento interactivo para aprender a respirar y canalizar la frustración y los berrinches de los 4 a los 6 años.</p>
							<div class="gwf-soon-foot">
								<button type="button" class="gwf-soon-link" data-bs-toggle="modal" data-bs-target="#gwf-cuentos-notify">🔔 Recibir aviso cuando esté listo</button>
							</div>
						</article>
					</div>
				</div>

				<?php gwf_l_pagination(); // Navegación entre páginas (inc/gwf-guias-view.php). ?>
			<?php else : ?>
				<div class="alert alert-info text-center">
					<p class="mb-0">Todavía no hay cuentos publicados aquí.</p>
					<a class="btn btn-primary btn-sm mt-3" href="<?php echo esc_url( gwf_cue_filter_url() ); ?>">Ver todos</a>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- Puente al método (mismo bloque que en la página de cada cuento) -->
	<section class="py-5">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<?php gwf_g_bridge( gwf_cue_texts()['puente'] ); ?>
				</div>
			</div>
		</div>
	</section>
</main>

<!-- Modal de suscripción para próximos cuentos -->
<div class="modal fade" id="gwf-cuentos-notify" tabindex="-1" aria-labelledby="gwf-cuentos-notify-label" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content rounded-4 border-0">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title fw-bold" id="gwf-cuentos-notify-label">Recibe un aviso cuando esté listo</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p class="text-muted small mb-4">Te enviaremos un email cuando el nuevo cuento esté disponible.</p>
				<!-- Formulario Mailchimp -->
				<form id="gwf-cuentos-form" class="position-relative">
					<div class="mb-3">
						<input type="email" class="form-control rounded-3" placeholder="Tu correo electrónico" required>
					</div>
					<button type="submit" class="btn btn-primary w-100 rounded-3">
						Avisarme cuando esté listo
					</button>
					<small class="text-muted d-block mt-3 text-center">
						No compartimos tu email. <a href="#" class="text-primary text-decoration-none">Política de privacidad</a>.
					</small>
				</form>
			</div>
		</div>
	</div>
</div>
