<?php
/**
 * Guía de desarrollo (pilar de la capa 3). Bloques de la Estrategia v1.6 (sección 7) y los wireframes
 * «plantilla-pilar-desarrollo»: definición citable, hitos con matices, qué puede hacer la familia, currículo,
 * puente, autoría y fuentes primarias, enlaces hacia abajo automáticos y relacionados.
 * Comparte CSS, JS y piezas con la guía de problema (inc/gwf-guias-view.php).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gwf_id       = get_the_ID();
	$gwf_meta     = function ( $name ) use ( $gwf_id ) {
		return trim( (string) get_post_meta( $gwf_id, $name, true ) );
	};
	$gwf_reviewer = gwf_guias_reviewer( $gwf_id );
	$gwf_ages     = gwf_guias_ages_label( $gwf_id );
	$gwf_temas    = get_the_terms( $gwf_id, 'gran_tema' );
	$gwf_tema     = ( $gwf_temas && ! is_wp_error( $gwf_temas ) ) ? $gwf_temas[0] : null;
	$gwf_minutes  = gwf_guias_reading_time( $gwf_id );
	$gwf_hitos    = gwf_guias_items( $gwf_id, 'gwf_hitos' );
	$gwf_fuentes  = gwf_guias_items( $gwf_id, 'gwf_fuentes' );
	$gwf_down     = gwf_guias_down_links( $gwf_id );
	$gwf_pillars  = gwf_guias_other_pillars( $gwf_id );
	$gwf_has_body = '' !== trim( wp_strip_all_tags( get_the_content() ) );

	$gwf_read_html     = gwf_g_read_html( $gwf_minutes );
	$gwf_reviewer_html = gwf_g_reviewer_html( $gwf_reviewer );
	$gwf_date_html     = gwf_g_date_html( gwf_guias_review_time( $gwf_id ) );
	$gwf_down_title    = $gwf_down['label'] ? 'Guías y cuentos de ' . ( $gwf_tema ? mb_strtolower( $gwf_down['label'] ) : $gwf_down['label'] ) : 'Guías y cuentos';

	$gwf_toc = array_filter(
		array(
			'definicion' => $gwf_meta( 'gwf_definicion' ) ? 'Definición' : '',
			'hitos'      => $gwf_hitos ? ( $gwf_tema ? 'Hitos' : 'Hitos de esta edad' ) : '',
			'familia'    => $gwf_has_body ? 'Qué puede hacer la familia' : '',
			'curriculo'  => $gwf_meta( 'gwf_curriculo' ) ? 'Cómo lo trabajamos' : '',
			'enlaces'    => ( $gwf_down['guias'] || $gwf_down['cuentos'] ) ? $gwf_down_title : '',
		)
	);
	$gwf_toc_html = gwf_g_toc_html( $gwf_toc );

	// La edad extraordinaria (portal): el pilar informa y la página persuade; se enlazan entre sí.
	$gwf_portal = get_page_by_path( 'acerca-de/la-edad-extraordinaria' );
	?>

<main id="site-content" class="gwf-guia gwf-pilar">
	<div class="container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<div class="gwf-g-layout">
			<?php // div y no article: las reglas antiguas del blog (.single article h2/p, con !important) no deben aplicarse. ?>
			<div <?php post_class( 'gwf-g-main' ); ?> id="post-<?php the_ID(); ?>">

				<header class="gwf-g-header">
					<?php the_title( '<h1 class="gwf-g-title">', '</h1>' ); ?>
					<p class="gwf-g-byline">
						<?php echo esc_html( get_the_author() ); ?>
						<?php if ( $gwf_reviewer_html ) : ?> · Revisado por <?php echo $gwf_reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en la función. ?><?php endif; ?>
						<?php if ( $gwf_date_html ) : ?> · Actualizado el <?php echo $gwf_date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php endif; ?>
						· <?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>
					<p class="gwf-g-strip">
						<?php echo esc_html( implode( ' · ', array_filter( array( $gwf_ages, $gwf_tema ? $gwf_tema->name : '' ) ) ) ); ?>
						· <?php echo $gwf_read_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>
				</header>

				<?php if ( $gwf_meta( 'gwf_definicion' ) ) : // 2. Definición, citable tal cual. ?>
					<section id="definicion" class="gwf-g-block gwf-g-respuesta" aria-label="Definición">
						<p><?php echo esc_html( $gwf_meta( 'gwf_definicion' ) ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_toc_html ) : ?>
					<details class="gwf-g-toc-mobile" id="indice">
						<summary>En esta guía</summary>
						<ol><?php echo $gwf_toc_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado al construirlo. ?></ol>
					</details>
				<?php endif; ?>

				<?php if ( $gwf_hitos ) : // 3. Hitos con matices: rangos y variabilidad, sin normas rígidas. ?>
					<section id="hitos" class="gwf-g-block">
						<h2><?php echo esc_html( $gwf_toc['hitos'] ); ?></h2>
						<ul class="gwf-g-milestones">
							<?php foreach ( $gwf_hitos as $gwf_h ) : ?>
								<li>
									<?php if ( $gwf_h['rango'] ) : ?><span class="gwf-g-range"><?php echo esc_html( $gwf_h['rango'] ); ?></span><?php endif; ?>
									<div>
										<strong><?php echo esc_html( $gwf_h['hito'] ); ?></strong>
										<?php if ( $gwf_h['variabilidad'] ) : ?><span><?php echo esc_html( $gwf_h['variabilidad'] ); ?></span><?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_has_body ) : // 4. Qué puede hacer la familia: editor, con H2 que son preguntas de la familia. ?>
					<section id="familia" class="gwf-g-block gwf-g-body">
						<p class="gwf-g-kicker">Qué puede hacer la familia</p>
						<?php the_content(); ?>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_meta( 'gwf_curriculo' ) ) : // 5. Cómo lo trabajamos en el currículo (sin adelantar la app). ?>
					<section id="curriculo" class="gwf-g-block">
						<h2>Cómo lo trabajamos en el currículo</h2>
						<?php echo wp_kses_post( wpautop( $gwf_meta( 'gwf_curriculo' ) ) ); ?>
					</section>
				<?php endif; ?>

			</div>

			<aside class="gwf-g-side" aria-label="Sobre esta guía">
				<div class="gwf-g-side-inner">
					<div class="gwf-g-card">
						<p class="gwf-g-card-title">Ficha</p>
						<ul class="gwf-g-facts">
							<?php if ( $gwf_ages ) : ?><li>Edad · <?php echo esc_html( $gwf_ages ); ?></li><?php endif; ?>
							<?php if ( $gwf_tema ) : ?><li>Gran tema · <?php echo esc_html( $gwf_tema->name ); ?></li><?php endif; ?>
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
			<?php gwf_g_bridge( 'desarrollo', 'Puente al método' ); // 6. ?>

			<?php gwf_g_credits( get_the_author(), $gwf_reviewer_html, $gwf_date_html, $gwf_fuentes, 'Fuentes primarias' ); // 7. ?>

			<?php gwf_g_like(); ?>

			<?php if ( $gwf_down['guias'] || $gwf_down['cuentos'] ) : // 8. Enlaces hacia abajo: todo lo de su edad o tema (automático). ?>
				<section id="enlaces" class="gwf-g-block gwf-g-down">
					<h2><?php echo esc_html( $gwf_down_title ); ?></h2>
					<div class="gwf-g-down-grid">
						<?php if ( $gwf_down['guias'] ) : ?>
							<div>
								<h3>Guías de problema · <?php echo esc_html( $gwf_down['label'] ); ?></h3>
								<ul>
									<?php foreach ( $gwf_down['guias'] as $gwf_g ) : ?>
										<li><a href="<?php echo esc_url( get_permalink( $gwf_g ) ); ?>"><?php echo esc_html( get_the_title( $gwf_g ) ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php // Cuentos · {edad}: cuando exista el tipo Cuento (plugin gwf-cuentos). ?>
					</div>
				</section>
			<?php endif; ?>

			<nav class="gwf-g-block gwf-g-related" aria-label="Relacionados">
				<h2>Relacionados</h2>
				<ul>
					<?php
					// phpcs:disable WordPress.Security.EscapeOutput -- gwf_g_card escapa cada valor.
					if ( $gwf_portal && 'publish' === get_post_status( $gwf_portal ) ) {
						echo gwf_g_card( $gwf_portal->ID, 'Growing Family', get_the_title( $gwf_portal ) );
					}
					foreach ( $gwf_pillars as $gwf_p ) {
						echo gwf_g_card( $gwf_p, 'Pilar' );
					}
					echo gwf_g_method_card();
					// phpcs:enable
					?>
				</ul>
			</nav>
		</div>
	</div>

	<div class="gwf-g-bar" aria-label="Accesos de la guía">
		<?php if ( $gwf_toc_html ) : ?><a href="#indice" class="gwf-g-bar-toc">Índice</a><?php endif; ?>
		<?php if ( isset( $gwf_toc['enlaces'] ) ) : ?><a href="#enlaces">Guías y cuentos</a><?php endif; ?>
	</div>
</main>

	<?php
endwhile;

get_footer();
