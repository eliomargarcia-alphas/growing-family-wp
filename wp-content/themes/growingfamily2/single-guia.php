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

	// Tiempo de lectura con icono de reloj (SVG en línea, color del texto: var(--g-blue) en el CSS).
	$gwf_read_html = '<span class="gwf-g-read"><svg class="gwf-g-clock" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
		. '<circle cx="12" cy="12" r="9.5" fill="none" stroke="currentColor" stroke-width="2"/>'
		. '<path d="M12 6.5V12l3.5 3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
		. (int) $gwf_minutes . ' min de lectura</span>';

	// Tarjeta de «Relacionados»: imagen destacada (si la hay), tipo y título.
	$gwf_card = function ( $post_id, $label ) {
		$img = has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '', 'sizes' => '(min-width: 1025px) 280px, 100vw' ) )
			: '';
		return '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '">'
			. ( $img ? '<span class="gwf-g-related-img">' . $img . '</span>' : '' )
			. '<span class="gwf-g-related-text"><span class="gwf-g-related-type">' . esc_html( $label ) . '</span>' . esc_html( get_the_title( $post_id ) ) . '</span></a></li>';
	};

	$gwf_reviewer_html = '';
	if ( $gwf_reviewer ) {
		$gwf_label         = esc_html( $gwf_reviewer['nombre'] . ( $gwf_reviewer['cargo'] ? ', ' . $gwf_reviewer['cargo'] : '' ) );
		$gwf_reviewer_html = $gwf_reviewer['ficha'] ? '<a href="' . esc_url( $gwf_reviewer['ficha'] ) . '">' . $gwf_label . '</a>' : $gwf_label;
	}
	$gwf_date_html = $gwf_reviewed ? '<time datetime="' . esc_attr( gmdate( 'Y-m-d', $gwf_reviewed ) ) . '">' . esc_html( date_i18n( 'j M Y', $gwf_reviewed ) ) . '</time>' : '';

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
	$gwf_toc_html = '';
	foreach ( $gwf_toc as $gwf_anchor => $gwf_text ) {
		$gwf_toc_html .= '<li><a href="#' . esc_attr( $gwf_anchor ) . '">' . esc_html( $gwf_text ) . '</a></li>';
	}
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
						· <?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba. ?>
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

					<?php if ( $gwf_reviewer ) : ?>
						<div class="gwf-g-card">
							<p class="gwf-g-card-title">Revisado por</p>
							<div class="gwf-g-reviewer">
								<?php if ( $gwf_reviewer['foto'] ) : ?>
									<img src="<?php echo esc_url( $gwf_reviewer['foto'] ); ?>" alt="" width="48" height="48" loading="lazy" decoding="async">
								<?php else : ?>
									<span class="gwf-g-avatar" aria-hidden="true"><?php echo esc_html( mb_substr( preg_replace( '/[^\p{L}]/u', '', preg_replace( '/\[[^\]]*\]/', '', $gwf_reviewer['nombre'] ) ), 0, 1 ) ); ?></span>
								<?php endif; ?>
								<div>
									<p class="gwf-g-reviewer-name"><?php echo esc_html( $gwf_reviewer['nombre'] ); ?></p>
									<p class="gwf-g-reviewer-role">
										<?php echo esc_html( $gwf_reviewer['cargo'] ); ?>
										<?php if ( $gwf_reviewer['ficha'] ) : ?> · <a href="<?php echo esc_url( $gwf_reviewer['ficha'] ); ?>">Ver ficha</a><?php endif; ?>
									</p>
								</div>
							</div>
						</div>
					<?php endif; ?>

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

			<?php // 11. Sostenerlo en el tiempo: puente al método (texto global). ?>
			<section class="gwf-g-block gwf-g-bridge">
				<h2>Sostenerlo en el tiempo</h2>
				<p><?php echo esc_html( gwf_guias_bridge_text( 'guia' ) ); ?></p>
				<a class="gwf-g-button" href="<?php echo esc_url( home_url( '/acerca-de/el-metodo/' ) ); ?>">Conoce el método</a>
			</section>

			<?php // 12. Autoría, revisión, fuentes y fecha. ?>
			<section id="fuentes" class="gwf-g-block gwf-g-credits">
				<h2>Autoría, revisión y fuentes</h2>
				<div class="gwf-g-credits-grid">
					<dl class="gwf-g-credits-who">
						<div><dt>Escrito por</dt><dd><?php echo esc_html( get_the_author() ); ?></dd></div>
						<?php if ( $gwf_reviewer_html ) : ?><div><dt>Revisado por</dt><dd><?php echo $gwf_reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></dd></div><?php endif; ?>
						<?php if ( $gwf_date_html ) : ?><div><dt>Actualizado</dt><dd><?php echo $gwf_date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></dd></div><?php endif; ?>
					</dl>
					<?php if ( $gwf_fuentes ) : ?>
						<details class="gwf-g-sources" open>
							<summary>Fuentes (<?php echo count( $gwf_fuentes ); ?>)</summary>
							<ol>
								<?php foreach ( $gwf_fuentes as $gwf_f ) : ?>
									<li>
										<?php if ( $gwf_f['url'] ) : ?>
											<a href="<?php echo esc_url( $gwf_f['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $gwf_f['titulo'] ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $gwf_f['titulo'] ); ?>
										<?php endif; ?>
										<?php if ( $gwf_f['organismo'] ) : ?><span> · <?php echo esc_html( $gwf_f['organismo'] ); ?></span><?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ol>
						</details>
					<?php endif; ?>
				</div>
			</section>

			<?php if ( shortcode_exists( 'wp_ulike' ) ) : // Valoración: los «me gusta» ordenan los listados de guías. ?>
				<section class="gwf-g-block gwf-g-like">
					<h2>¿Te ha gustado este contenido?</h2>
					<?php echo do_shortcode( '[wp_ulike]' ); ?>
				</section>
			<?php endif; ?>

			<?php // 13. Relacionados: pilar, guías hermanas, nota de padre a padre y método. ?>
			<nav class="gwf-g-block gwf-g-related" aria-label="Relacionados">
				<h2>Relacionados</h2>
				<ul>
					<?php
					// phpcs:disable WordPress.Security.EscapeOutput -- $gwf_card escapa cada valor.
					if ( $gwf_pilar ) {
						echo $gwf_card( $gwf_pilar, 'Pilar' );
					}
					foreach ( $gwf_siblings as $gwf_s ) {
						echo $gwf_card( $gwf_s, 'Guía' );
					}
					foreach ( array_slice( $gwf_notes, 0, 1 ) as $gwf_n ) {
						echo $gwf_card( $gwf_n, 'Nota de padre a padre' );
					}
					// phpcs:enable
					?>
					<?php
					// El método: la imagen destacada de su página, como el resto de tarjetas (sin imagen, solo texto).
					$gwf_metodo     = get_page_by_path( 'acerca-de/el-metodo' );
					$gwf_metodo_img = ( $gwf_metodo && has_post_thumbnail( $gwf_metodo ) )
						? get_the_post_thumbnail( $gwf_metodo, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '', 'sizes' => '(min-width: 1025px) 280px, 100vw' ) )
						: '';
					?>
					<li><a href="<?php echo esc_url( home_url( '/acerca-de/el-metodo/' ) ); ?>">
						<?php if ( $gwf_metodo_img ) : ?><span class="gwf-g-related-img"><?php echo $gwf_metodo_img; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de WordPress. ?></span><?php endif; ?>
						<span class="gwf-g-related-text"><span class="gwf-g-related-type">El método</span>10 minutos cada noche</span>
					</a></li>
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
