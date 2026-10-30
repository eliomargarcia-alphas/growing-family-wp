<?php
/**
 * Página del cuento (pantalla 1 del «Recorrido enfocado», wireframes cuento-enfocado-v1-pagina-*): migas, título,
 * selector Ana/Max, ficha, portada, «Así empieza» con la primera página, «Comenzar el cuento» y «¿Cómo contarlo?»,
 * botones rápidos, qué trabaja, autoría, puente, «me gusta» y enlaces.
 *
 * Todas las páginas del cuento van en el HTML (#leer, dentro de .gwf-cuento): con JS solo se ve la primera y el resto
 * se lee en el modo lectura (etapas siguientes); sin JS, «Comenzar el cuento» lleva a #leer y el cuento se ve seguido.
 * Datos: plugin gwf-cuentos (gwf_cue_*). Selector y modo lectura: assets/js/gwf-cuentos.js.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gwf_id       = get_the_ID();
	$gwf_prota    = gwf_cue_protagonist( $gwf_id );
	$gwf_other    = 'ana' === $gwf_prota ? 'max' : 'ana';
	$gwf_pages    = gwf_cue_pages( $gwf_id );
	$gwf_total    = count( $gwf_pages );
	$gwf_minutes  = gwf_cue_minutes( $gwf_id );
	$gwf_edad     = gwf_cue_terms( $gwf_id, 'edad' );
	$gwf_emocion  = gwf_cue_terms( $gwf_id, 'emocion' );
	$gwf_situ     = gwf_cue_terms( $gwf_id, 'situacion' );
	$gwf_alt_img  = (int) get_post_meta( $gwf_id, 'gwf_cue_ilustracion_alt', true );
	$gwf_texts    = gwf_cue_texts();
	$gwf_howto    = trim( (string) get_post_meta( $gwf_id, 'gwf_cue_como_contarlo', true ) );
	$gwf_howto    = $gwf_howto ? $gwf_howto : $gwf_texts['como_contarlo'];
	$gwf_trabaja  = trim( (string) get_post_meta( $gwf_id, 'gwf_cue_que_trabaja', true ) );
	$gwf_sources  = gwf_cue_sources( $gwf_id );
	$gwf_reviewer = gwf_cue_reviewer( $gwf_id );
	$gwf_date     = gwf_g_date_html( gwf_cue_updated_time( $gwf_id ) );
	$gwf_sibling  = gwf_cue_sibling( $gwf_id );
	$gwf_guia     = gwf_cue_linked( $gwf_id, 'gwf_cue_guia' );
	$gwf_pilar    = gwf_cue_linked( $gwf_id, 'gwf_cue_pilar' );
	$gwf_names    = array( 'ana' => 'Ana', 'max' => 'Max' );

	// Ficha: edad, emoción o situación, páginas y minutos (panel lateral; en móvil, franja bajo el título).
	$gwf_facts = array_filter(
		array(
			$gwf_edad ? esc_html( 'Edad · ' . $gwf_edad[0]->name ) : '',
			$gwf_emocion ? esc_html( 'Emoción · ' . $gwf_emocion[0]->name ) : '',
			$gwf_situ ? esc_html( 'Situación · ' . $gwf_situ[0]->name ) : '',
			esc_html( $gwf_total . ( 1 === $gwf_total ? ' página' : ' páginas' ) ),
			gwf_g_read_html( $gwf_minutes, 'min para contarlo' ),
		)
	);

	/** Selector «¿Quién protagoniza esta noche?» (panel lateral y franja de móvil). */
	$gwf_selector = function () use ( $gwf_prota, $gwf_names ) {
		echo '<div class="gwf-cue-selector" role="group" aria-label="Quién protagoniza el cuento">';
		foreach ( $gwf_names as $key => $name ) {
			echo '<button type="button" class="gwf-cue-prota" data-prota="' . esc_attr( $key ) . '" aria-pressed="' . ( $key === $gwf_prota ? 'true' : 'false' ) . '">' . esc_html( $name ) . '</button>';
		}
		echo '</div>';
	};
	$gwf_btn = function ( $icon, $label, $href, $class = '' ) {
		return '<a href="' . esc_url( $href ) . '" class="' . esc_attr( trim( 'gwf-cue-quick ' . $class ) ) . '">' . gwf_g_svg( $icon, 'gwf-g-btn-icon' ) . esc_html( $label ) . '</a>';
	};
	?>

<main id="site-content" class="gwf-guia gwf-cuento-page" data-gwf-origen="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>" data-gwf-tipo="cuento" data-prota="<?php echo esc_attr( $gwf_prota ); ?>" data-prota-default="<?php echo esc_attr( $gwf_prota ); ?>">
	<div class="container">

		<?php // 0. Migas (Yoast: Inicio › Cuentos › cuento). ?>
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<div class="gwf-g-layout">
			<div <?php post_class( 'gwf-g-main' ); ?> id="post-<?php the_ID(); ?>">

				<header class="gwf-g-header">
					<?php // 1. Título y autoría. ?>
					<?php the_title( '<h1 class="gwf-g-title">', '</h1>' ); ?>
					<p class="gwf-g-byline">
						Escrito por <?php echo gwf_g_author_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						· Actualizado el <?php echo $gwf_date; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>

					<?php // 4 y 2. Franja compacta de móvil: selector y ficha. ?>
					<div class="gwf-cue-strip">
						<?php $gwf_selector(); ?>
						<p><?php echo esc_html( implode( ' · ', array_filter( array( $gwf_edad ? $gwf_edad[0]->name : '', $gwf_emocion ? $gwf_emocion[0]->name : '', $gwf_total . ' páginas' ) ) ) ); ?> · <?php echo gwf_g_read_html( $gwf_minutes, 'min' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					</div>
				</header>

				<?php // 3. Portada: la del protagonista por defecto y, si hay, la del otro (cambia con el selector). ?>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="gwf-a-cover gwf-cue-cover">
						<?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'alt' => '', 'class' => 'gwf-cue-for-' . $gwf_prota ) ); ?>
						<?php if ( $gwf_alt_img ) : ?>
							<?php echo wp_get_attachment_image( $gwf_alt_img, 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'class' => 'gwf-cue-for-' . $gwf_other ) ); ?>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<?php // 5. Así empieza: resumen y primera página; las demás páginas siguen en el HTML (#leer). ?>
				<section class="gwf-g-block gwf-cue-start" aria-label="Así empieza">
					<p class="gwf-g-kicker">Así empieza</p>
					<?php $gwf_resumen = trim( (string) get_post_meta( $gwf_id, 'gwf_cue_asi_empieza', true ) ); ?>
					<?php if ( $gwf_resumen ) : ?><p class="gwf-cue-summary"><?php echo wp_kses_post( gwf_cue_variants( esc_html( $gwf_resumen ), $gwf_prota ) ); ?></p><?php endif; ?>

					<div id="leer" class="gwf-cuento" data-total="<?php echo (int) $gwf_total; ?>">
						<?php foreach ( $gwf_pages as $gwf_i => $gwf_page ) : ?>
							<div class="gwf-cue-page" id="pagina-<?php echo (int) $gwf_i + 1; ?>" role="group" aria-label="<?php echo esc_attr( 'Página ' . ( $gwf_i + 1 ) . ' de ' . $gwf_total ); ?>">
								<?php echo $gwf_page; // phpcs:ignore WordPress.Security.EscapeOutput -- bloques renderizados por WordPress. ?>
							</div>
						<?php endforeach; ?>
					</div>
					<p class="gwf-cue-pageinfo">Página 1 de <?php echo (int) $gwf_total; ?></p>
				</section>

				<?php // 6. Comenzar el cuento y ¿Cómo contarlo? ?>
				<div class="gwf-cue-actions">
					<a href="#leer" class="gwf-g-button gwf-cue-start-btn" data-gwf-cue="comenzar"><?php echo gwf_g_svg( 'comenzar', 'gwf-cue-play' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Comenzar el cuento</a>
					<a href="#como-contarlo" class="gwf-cue-howto-link" data-gwf-cue="como-contarlo">¿Cómo contarlo?</a>
				</div>

				<?php
				// Pantalla 2 · Antes de empezar (wireframes cuento-enfocado-v2-antes-*). Un <dialog> nativo: con JS se abre al pulsar
				// «Comenzar el cuento» la primera vez y con «¿Cómo contarlo?»; sin JS, el enlace lo muestra en la página (:target).
				$gwf_space = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $gwf_texts['espacio'] ) ) );
				?>
				<dialog id="como-contarlo" class="gwf-cue-antes" aria-labelledby="gwf-cue-antes-title">
					<div class="gwf-cue-antes-head">
						<h2 id="gwf-cue-antes-title">Antes de empezar</h2>
						<button type="button" class="gwf-cue-close" data-gwf-cue="cerrar-antes" aria-label="Cerrar">
							<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
						</button>
					</div>
					<p class="gwf-cue-antes-sub">Un minuto para que el cuento funcione mejor esta noche.</p>
					<section class="gwf-cue-antes-card">
						<h3>Cómo contarlo</h3>
						<?php echo wp_kses_post( wpautop( $gwf_howto ) ); ?>
					</section>
					<?php if ( $gwf_space ) : ?>
						<section class="gwf-cue-antes-card">
							<h3>Recomendaciones del espacio</h3>
							<ul class="gwf-cue-space"><?php foreach ( $gwf_space as $gwf_s ) : ?><li><?php echo esc_html( $gwf_s ); ?></li><?php endforeach; ?></ul>
						</section>
					<?php endif; ?>
					<div class="gwf-cue-antes-foot">
						<p class="gwf-cue-antes-note">No volverás a ver esto. Puedes repasarlo cuando quieras con el botón «¿Cómo contarlo?».</p>
						<a href="#leer" class="gwf-g-button gwf-cue-start-btn" data-gwf-cue="empezar">Empezar <span aria-hidden="true">›</span></a>
					</div>
				</dialog>

				<?php
				// Pantalla 3 · Modo lectura (wireframes cuento-enfocado-v3-lectura-*): capa a pantalla completa sobre la misma URL.
				// Al abrirse, assets/js/gwf-cuentos.js mueve aquí el mismo #leer (no se duplica el cuento) y lo devuelve al cerrar.
				?>
				<dialog id="gwf-cue-reader" class="gwf-cue-reader" aria-label="<?php echo esc_attr( 'Cuento: ' . wp_strip_all_tags( get_the_title() ) ); ?>">
					<div class="gwf-cue-r-top">
						<button type="button" class="gwf-cue-close" data-gwf-cue="cerrar" aria-label="Cerrar el cuento">
							<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
						</button>
						<p class="gwf-cue-r-count" aria-hidden="true"></p>
						<div class="gwf-cue-r-tools">
							<button type="button" class="gwf-cue-close" data-gwf-cue="ayuda" aria-label="¿Cómo contarlo?"><?php echo gwf_g_svg( 'contar', 'gwf-cue-r-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						</div>
					</div>
					<div class="gwf-cue-r-stage"></div>
					<div class="gwf-cue-r-nav">
						<div class="gwf-cue-r-buttons">
							<button type="button" class="gwf-cue-r-prev" data-gwf-cue="anterior"><span aria-hidden="true">‹</span> Anterior</button>
							<button type="button" class="gwf-cue-r-next" data-gwf-cue="siguiente" autofocus>Siguiente <span aria-hidden="true">›</span></button>
						</div>
						<div class="gwf-cue-r-dots" aria-hidden="true"></div>
						<p class="gwf-cue-r-progress" aria-hidden="true"></p>
					</div>
					<p class="screen-reader-text gwf-cue-r-live" aria-live="polite"></p>
				</dialog>
			</div>

			<aside class="gwf-g-side" aria-label="Sobre este cuento">
				<div class="gwf-g-side-inner">
					<div class="gwf-g-card">
						<p class="gwf-g-card-title">¿Quién protagoniza esta noche?</p>
						<?php $gwf_selector(); ?>
					</div>
					<div class="gwf-g-card">
						<p class="gwf-g-card-title">Ficha</p>
						<ul class="gwf-g-facts"><?php foreach ( $gwf_facts as $gwf_f ) : ?><li><?php echo $gwf_f; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado al construir la ficha. ?></li><?php endforeach; ?></ul>
					</div>
					<?php gwf_g_reviewer_card( $gwf_reviewer ); ?>
					<nav class="gwf-g-card gwf-a-quick gwf-cue-quickbar" aria-label="Ir a"><?php // 7. Botones rápidos. ?>
						<?php
						// phpcs:disable WordPress.Security.EscapeOutput -- $gwf_btn escapa.
						echo $gwf_btn( 'comenzar', 'Comenzar el cuento', '#leer', 'is-primary' );
						echo $gwf_btn( 'contar', '¿Cómo contarlo?', '#como-contarlo' );
						if ( $gwf_trabaja ) {
							echo $gwf_btn( 'trabaja', 'Qué trabaja', '#que-trabaja' );
						}
						// phpcs:enable
						?>
					</nav>
				</div>
			</aside>
		</div>

		<div class="gwf-g-bottom">
			<?php if ( $gwf_trabaja ) : // 8. Qué trabaja y por qué, con sus fuentes. ?>
				<section id="que-trabaja" class="gwf-g-block">
					<p class="gwf-g-kicker">Qué trabaja y por qué</p>
					<?php echo wp_kses_post( wpautop( $gwf_trabaja ) ); ?>
					<?php if ( $gwf_sources ) : ?>
						<ul class="gwf-cue-sources">
							<?php foreach ( $gwf_sources as $gwf_src ) : ?>
								<li>
									<?php if ( $gwf_src['url'] ) : ?><a href="<?php echo esc_url( $gwf_src['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $gwf_src['titulo'] ); ?> <span aria-hidden="true">↗</span></a><?php else : ?><?php echo esc_html( $gwf_src['titulo'] ); ?><?php endif; ?>
									<?php if ( $gwf_src['organismo'] ) : ?><span> · <?php echo esc_html( $gwf_src['organismo'] ); ?></span><?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php // 10. Puente al método (texto global de Cuentos → Ajustes). ?>
			<section class="gwf-g-block gwf-g-bridge">
				<h2>Sostenerlo en el tiempo</h2>
				<p><?php echo esc_html( $gwf_texts['puente'] ); ?></p>
				<a class="gwf-g-button" href="<?php echo esc_url( home_url( '/acerca-de/el-metodo/' ) ); ?>">Conoce el método</a>
			</section>

			<?php gwf_g_like(); // 12. «¿Te ha gustado este contenido?», como en guías, actividades y entradas. ?>

			<?php if ( $gwf_sibling || $gwf_guia || $gwf_pilar ) : // 11. Enlaces: otro cuento, su guía y su pilar. ?>
				<nav class="gwf-g-block gwf-g-related" aria-label="Para seguir">
					<h2>Para seguir</h2>
					<ul>
						<?php
						// phpcs:disable WordPress.Security.EscapeOutput -- gwf_g_card escapa.
						if ( $gwf_sibling ) {
							echo gwf_g_card( $gwf_sibling, $gwf_emocion ? 'Cuento · ' . mb_strtolower( $gwf_emocion[0]->name ) : 'Cuento' );
						}
						if ( $gwf_guia ) {
							echo gwf_g_card( $gwf_guia, 'Guía' );
						}
						if ( $gwf_pilar ) {
							echo gwf_g_card( $gwf_pilar, 'Pilar' );
						}
						// phpcs:enable
						?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>

	<?php // 7. En móvil, los botones rápidos van en una barra fija abajo; «Qué trabaja» se alcanza bajando. ?>
	<div class="gwf-g-bar gwf-cue-bar" aria-label="Accesos del cuento">
		<?php
		// phpcs:disable WordPress.Security.EscapeOutput
		echo $gwf_btn( 'comenzar', 'Comenzar el cuento', '#leer', 'is-primary' );
		echo $gwf_btn( 'contar', '¿Cómo contarlo?', '#como-contarlo' );
		// phpcs:enable
		?>
	</div>
</main>

	<?php
endwhile;

get_footer();
