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
				<p class="gwf-cuentos-intro lead text-muted mx-auto" style="max-width: 500px;">
					<?php echo wp_kses_post( $gwf_h['intro'] ); ?>
				</p>
			</div>

			<!-- Buscador -->
			<div class="gwf-cuentos-search mx-auto mb-5">
				<div class="position-relative">
					<svg class="position-absolute start-3 top-50 translate-middle-y" width="18" height="18" fill="#666" viewBox="0 0 16 16">
						<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.003.004.009.01.017.02l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85c-.01-.01-.016-.016-.02-.017a.5.5 0 0 0 .006-.007z"/>
					</svg>
					<input type="text" class="form-control gwf-search-input" placeholder="¿Qué emoción o situación exploramos hoy?" aria-label="Buscar cuentos" id="gwf-cuentos-search" style="padding-left: 2.5rem; padding-right: 1rem;">
				</div>
			</div>

			<!-- Filtros como pills -->
			<?php if ( $gwf_groups ) : ?>
				<div class="gwf-cuentos-filters text-center">
					<?php foreach ( $gwf_groups as $gwf_group ) : ?>
						<div class="mb-3">
							<?php foreach ( $gwf_group['items'] as $gwf_item ) : ?>
								<a href="<?php echo esc_url( $gwf_item['url'] ); ?>" class="btn btn-sm me-2 mb-2 <?php echo $gwf_item['active'] ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill">
									<?php echo esc_html( $gwf_item['label'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
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
						<div class="col-12 col-md-6 col-lg-4">
							<article class="gwf-cuento-card h-100 bg-white rounded-4 border border-light p-4 position-relative overflow-hidden shadow-sm hover-shadow-lg transition-all">
								<!-- Efecto lomo de libro -->
								<div class="gwf-cuento-spine position-absolute start-0 top-0 bottom-0" style="width: 8px;"></div>

								<!-- Imagen del cuento -->
								<div class="position-relative rounded-3 overflow-hidden mb-4" style="aspect-ratio: 4/3;">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'large', array( 'class' => 'w-100 h-100 object-fit-cover transition-transform', 'alt' => '', 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php echo gwf_placeholder_img( 'w-100 h-100' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en la función. ?>
									<?php endif; ?>
									
									<!-- Overlay degradado -->
									<div class="position-absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,.5) 0%, rgba(0,0,0,.2) 50%, rgba(0,0,0,.1) 100%);"></div>

									<!-- Badge de emoción -->
									<?php if ( $gwf_badge ) : ?>
										<span class="badge bg-primary position-absolute top-3 start-3 small">
											<?php 
											// Emojis por emoción
											$gwf_emoji_map = array(
												'miedo'        => '🌙',
												'celos'        => '🧸',
												'enfado'       => '🎈',
												'ansiedad'     => '😟',
												'tristeza'     => '💙',
												'soledad'      => '🤝',
												'frustración'  => '💪',
											);
											$gwf_emoji = $gwf_emoji_map[ $gwf_badge->slug ] ?? '📚';
											echo esc_html( $gwf_emoji . ' ' . $gwf_badge->name );
											?>
										</span>
									<?php endif; ?>

									<!-- Badge de tiempo -->
									<span class="badge bg-dark position-absolute top-3 end-3 small">
										⏱️ <?php echo absint( $gwf_mins ); ?> min
									</span>

									<!-- Información de protagonistas -->
									<div class="position-absolute bottom-3 start-3 bg-white bg-opacity-90 px-3 py-1 rounded-pill small fw-semibold" style="backdrop-filter: blur(4px);">
										Para leer con: <span class="text-primary">Ana</span> • <span class="text-primary">Max</span>
									</div>
								</div>

								<!-- Contenido de la tarjeta -->
								<div class="d-flex flex-column flex-grow-1">
									<!-- Metadatos -->
									<div class="small text-muted mb-2">
										<?php if ( $gwf_age ) : ?>
											<span class="text-warning fw-medium">★ Edad <?php echo esc_html( $gwf_age ); ?></span>
											<span class="text-muted">•</span>
										<?php endif; ?>
										<span>Ilustrado</span>
									</div>

									<!-- Título -->
									<h3 class="gwf-cuento-title h5 fw-bold mb-2">
										<a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark hover-text-primary transition-colors">
											<?php the_title(); ?>
										</a>
									</h3>

									<!-- Descripción -->
									<?php if ( $gwf_summ ) : ?>
										<p class="small text-muted mb-3 flex-grow-1">
											<?php echo wp_kses_post( $gwf_summ ); ?>
										</p>
									<?php endif; ?>

									<!-- Aval pedagógico -->
									<div class="alert alert-light small mb-3 p-2 border-0">
										<span class="text-success me-1">✓</span>
										<span class="text-muted">Incluye pregunta de conexión para padres</span>
									</div>

									<!-- Botones -->
									<div class="d-flex gap-2 pt-3 border-top">
										<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm flex-grow-1 rounded-3">
											Abrir cuento
										</a>
										<button class="btn btn-light btn-sm rounded-3 text-dark" title="Tips antes de empezar">
											💡
										</button>
									</div>
								</div>
							</article>
						</div>
					<?php endwhile; ?>

					<!-- Tarjeta Próximamente (si existen cuentos no publicados) -->
					<div class="col-12 col-md-6 col-lg-4">
						<article class="gwf-cuento-card gwf-cuento-coming-soon h-100 bg-light rounded-4 border border-2 border-dashed p-5 position-relative d-flex flex-column justify-content-between" style="border-color: var(--g-blue-line, #c0d9f0);">
							<div>
								<div class="mb-3" style="font-size: 2rem;">
									🎈
								</div>
								<span class="badge bg-light text-primary small mb-2">
									Próximo Estreno
								</span>
								<h3 class="h5 fw-bold mb-3">
									El dragón que aprendió a soplar despacito
								</h3>
								<p class="small text-muted mb-0">
									Un cuento interactivo para aprender a respirar y canalizar la frustración. Edades 4 a 6 años.
								</p>
							</div>
							<div class="mt-4 pt-3 border-top">
								<button class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#gwf-cuentos-notify">
									🔔 Recibir aviso cuando esté listo
								</button>
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
