<?php
/**
 * Guía de problema (capa 2). Bloques 0 a 13 en el orden de la Estrategia v1.6 y los wireframes
 * «plantilla-guia-v2». Todo el texto sale en el HTML del servidor; assets/js/gwf-guias.js solo maneja
 * el índice, la barra fija de móvil y la carga del vídeo al pulsar.
 *
 * Los datos los da el plugin gwf-guias (gwf_guias_items, gwf_guias_reviewer, …).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gwf_id        = get_the_ID();
	$gwf_meta      = function ( $name ) use ( $gwf_id ) {
		return trim( (string) get_post_meta( $gwf_id, $name, true ) );
	};
	$gwf_reviewer  = gwf_guias_reviewer( $gwf_id );
	$gwf_reviewed  = gwf_guias_review_time( $gwf_id );
	$gwf_ages      = gwf_guias_ages_label( $gwf_id );
	$gwf_temas     = get_the_terms( $gwf_id, 'tema' );
	$gwf_tema      = ( $gwf_temas && ! is_wp_error( $gwf_temas ) ) ? $gwf_temas[0] : null;
	$gwf_minutes   = gwf_guias_reading_time( $gwf_id );
	$gwf_pasos     = gwf_guias_items( $gwf_id, 'gwf_pasos' );
	$gwf_evitar    = gwf_guias_items( $gwf_id, 'gwf_evitar' );
	$gwf_fuentes   = gwf_guias_items( $gwf_id, 'gwf_fuentes' );
	$gwf_video_id  = gwf_guias_youtube_id( $gwf_meta( 'gwf_video_url' ) );
	$gwf_pilar     = (int) $gwf_meta( 'gwf_pilar' );
	$gwf_pilar     = ( $gwf_pilar && 'publish' === get_post_status( $gwf_pilar ) ) ? $gwf_pilar : 0;
	$gwf_siblings  = gwf_guias_siblings( $gwf_id );
	$gwf_notes     = gwf_guias_notes( $gwf_id );
	$gwf_has_body  = '' !== trim( wp_strip_all_tags( get_the_content() ) );

	// Piezas compartidas con el pilar (inc/gwf-guias-view.php).
	$gwf_read_html     = gwf_g_read_html( $gwf_minutes );
	$gwf_reviewer_html = gwf_g_reviewer_html( $gwf_reviewer );
	$gwf_date_html     = gwf_g_date_html( $gwf_reviewed );

	// Secciones presentes, en orden: alimentan el índice lateral, el desplegable de móvil y la barra fija.
	$gwf_toc = array_filter(
		array(
			'respuesta'        => $gwf_meta( 'gwf_respuesta' ) ? 'Respuesta directa' : '',
			'lo-que-intentaste' => $gwf_meta( 'gwf_ya_intentaste' ) ? 'Lo que ya intentaste' : '',
			'cuando-consultar' => $gwf_meta( 'gwf_cuando_consultar' ) ? 'Cuándo consultar' : '',
			'por-que-pasa'     => $gwf_has_body ? 'Por qué pasa a esta edad' : '',
			'que-hacer'        => $gwf_pasos ? 'Qué hacer esta noche' : '',
			'que-evitar'       => $gwf_evitar ? 'Qué conviene evitar' : '',
			'desacuerdo'       => $gwf_meta( 'gwf_desacuerdo' ) ? 'Si no están de acuerdo' : '',
			'video'            => $gwf_video_id ? 'Vídeo' : '',
		)
	);
	$gwf_toc_html = gwf_g_toc_html( $gwf_toc );
	?>

<main id="site-content" class="gwf-guia">
	<div class="container">

		<?php // 0. Migas de pan (Yoast: Inicio › Guías › guía). ?>
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<div class="gwf-g-layout">
			<?php // div y no article: las reglas antiguas del blog (.single article h2/p, con !important) no deben aplicarse a la guía. ?>
			<div <?php post_class( 'gwf-g-main' ); ?> id="post-<?php the_ID(); ?>">

				<header class="gwf-g-header">
					<?php // 1. Pregunta de la familia con la edad + línea de autoría. ?>
					<?php the_title( '<h1 class="gwf-g-title">', '</h1>' ); ?>
					<p class="gwf-g-byline">
						<?php echo esc_html( get_the_author() ); ?>
						<?php if ( $gwf_reviewer_html ) : ?> · Revisado por <?php echo $gwf_reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado arriba. ?><?php endif; ?>
						<?php if ( $gwf_date_html ) : ?> · Actualizado el <?php echo $gwf_date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php endif; ?>
						<span class="gwf-g-byline-read">· <?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</p>
					<?php // Franja compacta con la ficha (solo móvil). ?>
					<p class="gwf-g-strip">
						<?php echo esc_html( implode( ' · ', array_filter( array( $gwf_ages, $gwf_tema ? $gwf_tema->name : '' ) ) ) ); ?>
						· <?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>
				</header>

				<?php if ( $gwf_meta( 'gwf_respuesta' ) ) : // 2. Respuesta directa. ?>
					<section id="respuesta" class="gwf-g-block gwf-g-respuesta" aria-label="Respuesta directa">
						<p><?php echo esc_html( $gwf_meta( 'gwf_respuesta' ) ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_meta( 'gwf_ya_intentaste' ) ) : // 3. Reconocer lo que ya intentaste. ?>
					<section id="lo-que-intentaste" class="gwf-g-block">
						<h2>Lo que ya intentaste</h2>
						<?php echo wp_kses_post( wpautop( $gwf_meta( 'gwf_ya_intentaste' ) ) ); ?>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_meta( 'gwf_cuando_consultar' ) ) : // 4. Cuándo consultar: arriba, en la primera pantalla en móvil. ?>
					<section id="cuando-consultar" class="gwf-g-block gwf-g-consultar">
						<h2>Cuándo consultar con un profesional</h2>
						<?php echo wp_kses_post( wpautop( $gwf_meta( 'gwf_cuando_consultar' ) ) ); ?>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_toc_html ) : // Índice desplegable (solo móvil), después de «Cuándo consultar». ?>
					<details class="gwf-g-toc-mobile" id="indice">
						<summary>En esta guía</summary>
						<ol><?php echo $gwf_toc_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado al construirlo. ?></ol>
					</details>
				<?php endif; ?>

				<?php if ( $gwf_has_body ) : // 5. Por qué pasa a esta edad: editor, con H2 que son preguntas de la familia. ?>
					<section id="por-que-pasa" class="gwf-g-block gwf-g-body">
						<p class="gwf-g-kicker">Por qué pasa a esta edad</p>
						<?php the_content(); ?>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_pasos ) : // 6. Qué hacer esta noche. ?>
					<section id="que-hacer" class="gwf-g-block">
						<h2>Qué hacer esta noche</h2>
						<ol class="gwf-g-steps">
							<?php foreach ( $gwf_pasos as $gwf_paso ) : ?>
								<li>
									<?php if ( $gwf_paso['titulo'] ) : ?><strong><?php echo esc_html( $gwf_paso['titulo'] ); ?></strong><?php endif; ?>
									<?php if ( $gwf_paso['texto'] ) : ?><span><?php echo esc_html( $gwf_paso['texto'] ); ?></span><?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_evitar ) : // 7. Qué conviene evitar. ?>
					<section id="que-evitar" class="gwf-g-block">
						<h2>Qué conviene evitar</h2>
						<ul class="gwf-g-avoid">
							<?php foreach ( $gwf_evitar as $gwf_item ) : ?>
								<li><?php echo esc_html( $gwf_item['texto'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_meta( 'gwf_desacuerdo' ) ) : // 8. Madre y padre en desacuerdo (opcional). ?>
					<section id="desacuerdo" class="gwf-g-block gwf-g-optional">
						<h2>Cuando la madre y el padre no están de acuerdo</h2>
						<?php echo wp_kses_post( wpautop( $gwf_meta( 'gwf_desacuerdo' ) ) ); ?>
					</section>
				<?php endif; ?>

				<?php // 9. Un cuento para esta noche: se añade cuando exista el tipo Cuento (plugin gwf-cuentos). ?>

			</div>

			<aside class="gwf-g-side" aria-label="Sobre esta guía">
				<div class="gwf-g-side-inner">
					<div class="gwf-g-card">
						<p class="gwf-g-card-title">Ficha</p>
						<ul class="gwf-g-facts">
							<?php if ( $gwf_ages ) : ?><li>Edad · <?php echo esc_html( $gwf_ages ); ?></li><?php endif; ?>
							<?php if ( $gwf_tema ) : ?><li>Tema · <?php echo esc_html( $gwf_tema->name ); ?></li><?php endif; ?>
							<li><?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
						</ul>
					</div>

					<?php gwf_g_reviewer_card( $gwf_reviewer ); ?>

					<?php if ( $gwf_toc_html ) : ?>
						<nav class="gwf-g-card gwf-g-toc" aria-label="En esta guía">
							<p class="gwf-g-card-title">En esta guía</p>
							<ol><?php echo $gwf_toc_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></ol>
						</nav>
					<?php endif; ?>
				</div>
			</aside>
		</div>

		<div class="gwf-g-bottom">
			<?php if ( $gwf_video_id ) : // 10. Vídeo a todo el ancho: miniatura, y YouTube solo al pulsar. ?>
				<section id="video" class="gwf-g-block gwf-g-video-block">
					<h2 class="screen-reader-text">Vídeo de la guía</h2>
					<?php $gwf_video_title = $gwf_meta( 'gwf_video_titulo' ) ? $gwf_meta( 'gwf_video_titulo' ) : get_the_title(); ?>
					<a class="gwf-g-video" href="<?php echo esc_url( 'https://www.youtube.com/watch?v=' . $gwf_video_id ); ?>" data-yt="<?php echo esc_attr( $gwf_video_id ); ?>">
						<img src="<?php echo esc_url( 'https://i.ytimg.com/vi/' . $gwf_video_id . '/hqdefault.jpg' ); ?>" alt="" width="480" height="360" loading="lazy" decoding="async">
						<span class="gwf-g-play" aria-hidden="true"></span>
						<span class="gwf-g-video-caption">«<?php echo esc_html( $gwf_video_title ); ?>»<?php echo $gwf_reviewer ? ' · ' . esc_html( $gwf_reviewer['nombre'] . ( $gwf_reviewer['cargo'] ? ', ' . $gwf_reviewer['cargo'] : '' ) ) : ''; ?></span>
						<span class="screen-reader-text">Reproducir el vídeo</span>
					</a>
				</section>
			<?php endif; ?>

			<?php gwf_g_bridge( 'guia' ); // 11. Sostenerlo en el tiempo: puente al método (texto global). ?>

			<?php gwf_g_credits( get_the_author(), $gwf_reviewer_html, $gwf_date_html, $gwf_fuentes ); // 12. Autoría, revisión, fuentes y fecha. ?>

			<?php gwf_g_like(); ?>

			<?php // 13. Relacionados: pilar, guías hermanas, nota de padre a padre y método. ?>
			<nav class="gwf-g-block gwf-g-related" aria-label="Relacionados">
				<h2>Relacionados</h2>
				<ul>
					<?php
					// phpcs:disable WordPress.Security.EscapeOutput -- gwf_g_card escapa cada valor.
					if ( $gwf_pilar ) {
						echo gwf_g_card( $gwf_pilar, 'Pilar' );
					}
					foreach ( $gwf_siblings as $gwf_s ) {
						echo gwf_g_card( $gwf_s, 'Guía' );
					}
					foreach ( array_slice( $gwf_notes, 0, 1 ) as $gwf_n ) {
						echo gwf_g_card( $gwf_n, 'Nota de padre a padre' );
					}
					// phpcs:enable
					?>
					<?php echo gwf_g_method_card(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</ul>
			</nav>
		</div>
	</div>

	<?php // Barra fija de móvil: índice y acceso directo a «Cuándo consultar». ?>
	<div class="gwf-g-bar" aria-label="Accesos de la guía">
		<?php if ( $gwf_toc_html ) : ?><a href="#indice" class="gwf-g-bar-toc">Índice de la guía</a><?php endif; ?>
		<?php if ( isset( $gwf_toc['cuando-consultar'] ) ) : ?><a href="#cuando-consultar">Cuándo consultar</a><?php endif; ?>
	</div>
</main>

	<?php
endwhile;

get_footer();
