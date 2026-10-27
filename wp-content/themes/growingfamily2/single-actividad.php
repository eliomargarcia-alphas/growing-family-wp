<?php
/**
 * Actividad para hacer juntos (capa 1), wireframe «plantilla-actividad». Bloques 0 a 9: migas, título con la edad y
 * «me gusta», ficha y ficha ampliada (panel lateral; franja en móvil), portada, pasos con su imagen y «Para ti»,
 * imprimible a cambio del correo, qué trabaja y por qué, puente, autoría y enlaces.
 *
 * Los datos los da el plugin gwf-actividades (gwf_act_*). Reutiliza el diseño de las guías (gwf-guias.css).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gwf_id        = get_the_ID();
	$gwf_meta      = function ( $name ) use ( $gwf_id ) {
		return gwf_act_meta( $gwf_id, $name );
	};
	$gwf_ages      = gwf_act_ages_label( $gwf_id );
	$gwf_trabaja   = gwf_act_terms( $gwf_id, 'trabaja' );
	$gwf_trabaja_t = implode( ' y ', array_map( 'mb_strtolower', wp_list_pluck( array_slice( $gwf_trabaja, 0, 2 ), 'name' ) ) );
	$gwf_minutes   = (int) $gwf_meta( 'gwf_act_minutos' );
	$gwf_steps     = gwf_act_steps( $gwf_id );
	$gwf_materials = gwf_act_materials( $gwf_id );
	$gwf_has_print = (bool) gwf_act_print_file( $gwf_id );
	$gwf_source    = gwf_act_source( $gwf_id );
	$gwf_reviewer  = gwf_act_reviewer( $gwf_id );
	$gwf_updated   = gwf_act_updated_time( $gwf_id );
	$gwf_pilar     = gwf_act_pillar( $gwf_id );
	$gwf_siblings  = gwf_act_siblings( $gwf_id );
	$gwf_edad      = gwf_act_terms( $gwf_id, 'edad' );
	$gwf_date_html = gwf_g_date_html( $gwf_updated );
	$gwf_status    = isset( $_GET['imprimible'] ) ? sanitize_key( wp_unslash( $_GET['imprimible'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	$gwf_facts = array_filter(
		array(
			$gwf_ages ? 'Edad · ' . $gwf_ages : '',
			$gwf_trabaja ? 'Qué trabaja · ' . ucfirst( $gwf_trabaja_t ) : '',
			$gwf_minutes ? $gwf_minutes . ' minutos' : '',
		)
	);
	$gwf_reviewer_html = $gwf_reviewer ? ( $gwf_reviewer['ficha'] ? '<a href="' . esc_url( $gwf_reviewer['ficha'] ) . '">' . esc_html( $gwf_reviewer['nombre'] ) . '</a>' : esc_html( $gwf_reviewer['nombre'] ) ) : '';

	/** Ficha ampliada: materiales, participantes y lugar (panel lateral y desplegable de móvil). */
	$gwf_more = function () use ( $gwf_materials, $gwf_meta ) {
		if ( $gwf_materials ) {
			echo '<p class="gwf-a-more-title">Materiales (de casa)</p><ul class="gwf-a-materials">';
			foreach ( $gwf_materials as $gwf_m ) {
				echo '<li>' . esc_html( $gwf_m ) . '</li>';
			}
			echo '</ul>';
		}
		echo '<ul class="gwf-g-facts">';
		if ( $gwf_meta( 'gwf_act_participantes' ) ) {
			echo '<li>Participantes · ' . esc_html( $gwf_meta( 'gwf_act_participantes' ) ) . '</li>';
		}
		if ( $gwf_meta( 'gwf_act_lugar' ) ) {
			echo '<li>Lugar · ' . esc_html( $gwf_meta( 'gwf_act_lugar' ) ) . '</li>';
		}
		echo '</ul>';
	};
	?>

<main id="site-content" class="gwf-guia gwf-actividad" data-gwf-origen="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>" data-gwf-tipo="actividad">
	<div class="container">

		<?php // 0. Migas de pan (Yoast: Inicio › Actividades › actividad). ?>
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<div class="gwf-g-layout">
			<div <?php post_class( 'gwf-g-main' ); ?> id="post-<?php the_ID(); ?>">

				<header class="gwf-g-header">
					<?php // 1. Título con la edad, autoría, fecha y «me gusta» (10). ?>
					<?php the_title( '<h1 class="gwf-g-title">', '</h1>' ); ?>
					<div class="gwf-a-byline">
						<p class="gwf-g-byline">
							Equipo editorial Growing Family
							<?php if ( $gwf_reviewer_html ) : ?> · Revisado por <?php echo $gwf_reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado arriba. ?><?php endif; ?>
							· Actualizado el <?php echo $gwf_date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</p>
						<?php echo shortcode_exists( 'wp_ulike' ) ? do_shortcode( '[wp_ulike]' ) : ''; ?>
					</div>

					<?php // 2. Franja compacta con la ficha (solo móvil); 2a se despliega. ?>
					<div class="gwf-a-strip">
						<p><?php echo esc_html( implode( ' · ', array_filter( array( $gwf_ages, ucfirst( $gwf_trabaja_t ), $gwf_minutes ? $gwf_minutes . ' min' : '' ) ) ) ); ?></p>
						<details>
							<summary>Materiales, participantes y lugar</summary>
							<?php $gwf_more(); ?>
						</details>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : // Portada. ?>
					<figure class="gwf-a-cover"><?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'alt' => '' ) ); ?></figure>
				<?php endif; ?>

				<?php if ( $gwf_steps ) : // 4. La actividad completa, siempre visible, sin registro. ?>
					<section id="pasos" class="gwf-g-block">
						<h2>La actividad, paso a paso</h2>
						<ol class="gwf-a-steps">
							<?php foreach ( $gwf_steps as $gwf_i => $gwf_step ) : ?>
								<li class="<?php echo $gwf_step['imagen'] ? 'has-image' : ''; ?>">
									<span class="gwf-a-step-n" aria-hidden="true"><?php echo (int) $gwf_i + 1; ?></span>
									<div class="gwf-a-step-text">
										<h3><?php echo esc_html( $gwf_step['titulo'] ); ?></h3>
										<?php if ( $gwf_step['texto'] ) : ?><p><?php echo esc_html( $gwf_step['texto'] ); ?></p><?php endif; ?>
										<?php if ( $gwf_step['para_ti'] ) : // 4b. Para ti: consejo para la madre o el padre. ?>
											<p class="gwf-a-tip"><strong>Para ti</strong><?php echo esc_html( $gwf_step['para_ti'] ); ?></p>
										<?php endif; ?>
									</div>
									<?php if ( $gwf_step['imagen'] ) : // 4a. Imagen del paso (opcional). ?>
										<figure class="gwf-a-step-img"><?php echo wp_get_attachment_image( $gwf_step['imagen'], 'medium_large', false, array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1025px) 240px, 100vw' ) ); ?></figure>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_has_print ) : // 5. El imprimible, a cambio del correo. ?>
					<section id="imprimible" class="gwf-g-block gwf-a-print">
						<?php $gwf_preview = (int) $gwf_meta( 'gwf_act_imprimible_preview' ); ?>
						<?php if ( $gwf_preview ) : ?>
							<figure class="gwf-a-print-preview"><?php echo wp_get_attachment_image( $gwf_preview, 'medium', false, array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => 'Vista previa del imprimible' ) ); ?></figure>
						<?php endif; ?>
						<div class="gwf-a-print-body">
							<h2><?php echo esc_html( $gwf_meta( 'gwf_act_imprimible_titulo' ) ? $gwf_meta( 'gwf_act_imprimible_titulo' ) : 'Descarga el imprimible' ); ?></h2>
							<?php if ( 'ok' === $gwf_status ) : ?>
								<p class="gwf-a-print-msg" role="status">¡Listo! Te lo enviamos a tu correo. Si no lo ves en unos minutos, revisa la carpeta de spam o promociones.</p>
							<?php else : ?>
								<?php if ( $gwf_meta( 'gwf_act_imprimible_texto' ) ) : ?><p><?php echo esc_html( $gwf_meta( 'gwf_act_imprimible_texto' ) ); ?></p><?php endif; ?>
								<?php if ( 'correo' === $gwf_status || 'error' === $gwf_status ) : ?>
									<p class="gwf-a-print-msg is-error" role="alert"><?php echo 'correo' === $gwf_status ? 'Revisa tu correo: parece que no es válido.' : 'No pudimos enviarlo. Inténtalo de nuevo en unos minutos.'; ?></p>
								<?php endif; ?>
								<form class="gwf-a-print-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
									<input type="hidden" name="action" value="gwf_act_imprimible">
									<input type="hidden" name="actividad" value="<?php echo (int) $gwf_id; ?>">
									<label class="screen-reader-text" for="gwf-a-email">Tu correo</label>
									<input type="email" id="gwf-a-email" name="email" placeholder="Tu correo" required autocomplete="email">
									<label class="gwf-a-check"><input type="checkbox" name="compromiso" value="1"> <?php echo esc_html( GWF_ACT_COMMITMENT_TXT ); ?></label>
									<span class="gwf-a-hp" aria-hidden="true"><input type="text" name="web" tabindex="-1" autocomplete="off"></span>
									<button type="submit" class="gwf-g-button">Enviarme el imprimible</button>
									<p class="gwf-a-privacy">Usamos tu correo para enviarte el imprimible y avisarte de novedades de Growing Family. Puedes darte de baja cuando quieras. <a href="<?php echo esc_url( home_url( '/poltica-de-privacidad/' ) ); ?>">Política de privacidad</a>.</p>
								</form>
							<?php endif; ?>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_meta( 'gwf_act_porque' ) ) : // 6. Qué trabaja y por qué a esta edad, con su fuente. ?>
					<section id="que-trabaja" class="gwf-g-block">
						<p class="gwf-g-kicker">Qué trabaja y por qué a esta edad</p>
						<?php if ( $gwf_meta( 'gwf_act_porque_titulo' ) ) : ?><h2><?php echo esc_html( $gwf_meta( 'gwf_act_porque_titulo' ) ); ?></h2><?php endif; ?>
						<?php echo wp_kses_post( wpautop( $gwf_meta( 'gwf_act_porque' ) ) ); ?>
						<?php if ( $gwf_source ) : ?>
							<p class="gwf-a-source">Fuente:
								<?php if ( $gwf_source['url'] ) : ?><a href="<?php echo esc_url( $gwf_source['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $gwf_source['titulo'] ); ?> <span aria-hidden="true">↗</span></a><?php else : ?><?php echo esc_html( $gwf_source['titulo'] ); ?><?php endif; ?>
							</p>
						<?php endif; ?>
					</section>
				<?php endif; ?>
			</div>

			<aside class="gwf-g-side" aria-label="Sobre esta actividad">
				<div class="gwf-g-side-inner">
					<?php if ( $gwf_facts ) : // 2. Ficha. ?>
						<div class="gwf-g-card">
							<p class="gwf-g-card-title">Ficha</p>
							<ul class="gwf-g-facts"><?php foreach ( $gwf_facts as $gwf_f ) : ?><li><?php echo esc_html( $gwf_f ); ?></li><?php endforeach; ?></ul>
						</div>
					<?php endif; ?>
					<div class="gwf-g-card"><?php $gwf_more(); // 2a. Ficha ampliada. ?></div>
					<nav class="gwf-g-card gwf-a-quick" aria-label="Ir a"><?php // 3. Botones rápidos. ?>
						<a href="#pasos">Pasos</a>
						<?php if ( $gwf_has_print ) : ?><a href="#imprimible">Imprimible</a><?php endif; ?>
						<?php if ( $gwf_meta( 'gwf_act_porque' ) ) : ?><a href="#que-trabaja">Qué trabaja</a><?php endif; ?>
					</nav>
				</div>
			</aside>
		</div>

		<div class="gwf-g-bottom">
			<?php // 7. Puente al método. ?>
			<section class="gwf-g-block gwf-g-bridge">
				<h2>Sostenerlo en el tiempo</h2>
				<p>Esta actividad es un momento compartido; el ritual de cada noche es la forma de sostenerlo.</p>
				<a class="gwf-g-button" href="<?php echo esc_url( home_url( '/acerca-de/el-metodo/' ) ); ?>">Conoce el método</a>
			</section>

			<?php // 8. Autoría y fecha. ?>
			<section class="gwf-g-block gwf-a-credits">
				<p><strong>Escrito por</strong> Equipo editorial Growing Family
					<?php if ( $gwf_reviewer_html ) : ?> · <strong>Revisado por</strong> <?php echo $gwf_reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php endif; ?>
					· <strong>Actualizado</strong> <?php echo $gwf_date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</section>

			<?php // 9. Enlaces: actividades de la misma edad, el cuento relacionado (con gwf-cuentos) y el pilar de la edad. ?>
			<nav class="gwf-g-block gwf-g-related" aria-label="Enlaces">
				<h2>Para seguir</h2>
				<ul>
					<?php
					// phpcs:disable WordPress.Security.EscapeOutput -- gwf_g_card escapa cada valor.
					if ( $gwf_edad ) {
						echo gwf_g_card( 0, 'Actividades', 'Actividades para ' . $gwf_edad[0]->name, gwf_act_filter_url( $gwf_edad[0] ) );
					}
					foreach ( $gwf_siblings as $gwf_s ) {
						echo gwf_g_card( $gwf_s, 'Actividad' );
					}
					if ( $gwf_pilar ) {
						echo gwf_g_card( $gwf_pilar, 'Pilar' );
					}
					// phpcs:enable
					?>
				</ul>
			</nav>
		</div>
	</div>

	<?php // 3. Botones rápidos en una barra fija abajo (móvil). ?>
	<div class="gwf-g-bar gwf-a-bar" aria-label="Ir a">
		<a href="#pasos">Pasos</a>
		<?php if ( $gwf_has_print ) : ?><a href="#imprimible">Imprimible</a><?php endif; ?>
		<?php if ( $gwf_meta( 'gwf_act_porque' ) ) : ?><a href="#que-trabaja">Qué trabaja</a><?php endif; ?>
	</div>
</main>

	<?php
endwhile;

get_footer();
