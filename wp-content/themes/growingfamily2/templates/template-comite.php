<?php
/**
 * Template Name: Comité editorial
 * Template Post Type: page
 *
 * Página del Comité editorial (wireframe «plantilla-comite»). Encabezado = título y texto de la página;
 * los miembros salen solos de los usuarios «Miembro del comité» (plugin gwf-guias, includes/committee.php)
 * y el resto de textos son campos de la página.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

$gwf_has_plugin = function_exists( 'gwf_guias_committee_members' );
$gwf_field      = function ( $name ) {
	return trim( (string) get_post_meta( get_the_ID(), $name, true ) );
};
$gwf_lines      = function ( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $text ) ) ) );
};
/** Página publicada (o null): para mostrar sus botones solo cuando existan. */
$gwf_page = function ( $page ) {
	$page = is_numeric( $page ) ? get_post( (int) $page ) : get_page_by_path( $page );
	return ( $page && 'publish' === $page->post_status ) ? $page : null;
};

/** Tarjeta de un miembro: foto, nombre, cargo y botón a su ficha (las del fundador van anchas, con su texto). */
$gwf_member_card = function ( $p, $wide = false ) {
	$initial = mb_substr( preg_replace( '/[^\p{L}]/u', '', preg_replace( '/\[[^\]]*\]/', '', $p['nombre'] ) ), 0, 1 );
	?>
	<article class="gwf-c-card<?php echo $wide ? ' gwf-c-card-wide' : ''; ?>" id="<?php echo esc_attr( $p['ancla'] ); ?>">
		<?php if ( $p['foto'] ) : ?>
			<img class="gwf-c-photo" src="<?php echo esc_url( $p['foto'] ); ?>" alt="<?php echo esc_attr( $p['nombre'] ); ?>" width="72" height="72" loading="lazy" decoding="async">
		<?php else : ?>
			<span class="gwf-c-photo gwf-p-initial" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
		<?php endif; ?>
		<div class="gwf-c-body">
			<h4 class="gwf-c-name"><?php echo esc_html( $p['nombre'] ); ?></h4>
			<?php if ( $p['cargo'] ) : ?><p class="gwf-c-role"><?php echo esc_html( $p['cargo'] ); ?></p><?php endif; ?>
			<?php if ( $wide && $p['experiencia'] ) : ?><div class="gwf-c-desc"><?php echo wp_kses_post( wpautop( $p['experiencia'] ) ); ?></div><?php endif; ?>
			<?php if ( $p['url'] ) : // Solo el botón de su ficha: rol, temas y enlaces están en ella. ?>
				<a href="<?php echo esc_url( $p['url'] ); ?>" class="gwf-p-button gwf-c-profile">Ver perfil<span class="screen-reader-text"> de <?php echo esc_html( $p['nombre'] ); ?></span></a>
			<?php endif; ?>
		</div>
	</article>
	<?php
};
?>

<main id="site-content" class="gwf-guia gwf-comite">
	<div class="container gwf-p-wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( function_exists( 'yoast_breadcrumb' ) ) {
				yoast_breadcrumb( '<nav class="gwf-l-migas" aria-label="Migas de pan">', '</nav>' );
			}
			?>

			<?php // 1. Encabezado: definición citable de 2 o 3 frases (texto de la página). ?>
			<header class="gwf-c-head">
				<?php the_title( '<h1 class="gwf-p-name">', '</h1>' ); ?>
				<div class="gwf-c-intro"><?php the_content(); ?></div>
			</header>

			<?php if ( $gwf_field( 'gwf_comite_compromiso' ) ) : // 2. Nuestro compromiso. ?>
				<p class="gwf-c-promise"><?php echo esc_html( $gwf_field( 'gwf_comite_compromiso' ) ); ?></p>
			<?php endif; ?>

			<?php
			// 3. Miembros del comité, por área; el fundador aparte.
			$gwf_groups = $gwf_has_plugin ? gwf_guias_committee_members() : array();
			if ( $gwf_groups ) :
				?>
				<section class="gwf-g-block gwf-p-block gwf-c-members">
					<h2>Miembros del comité</h2>
					<?php foreach ( $gwf_groups as $gwf_area => $gwf_people ) : ?>
						<h3 class="gwf-c-area"><?php echo esc_html( GWF_GUIAS_COMMITTEE_AREAS[ $gwf_area ] ); ?></h3>
						<div class="gwf-c-grid<?php echo 'fundador' === $gwf_area ? ' gwf-c-grid-wide' : ''; ?>">
							<?php
							foreach ( $gwf_people as $gwf_person ) {
								$gwf_member_card( $gwf_person, 'fundador' === $gwf_area );
							}
							?>
						</div>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>

			<?php
			$gwf_steps = $gwf_lines( $gwf_field( 'gwf_comite_pasos' ) );
			if ( $gwf_steps ) : // 4. Cómo creamos el contenido (el último paso, resaltado).
				?>
				<section class="gwf-g-block gwf-p-block">
					<h2>Cómo creamos el contenido</h2>
					<ol class="gwf-c-steps">
						<?php foreach ( $gwf_steps as $gwf_i => $gwf_step ) : ?>
							<li<?php echo count( $gwf_steps ) - 1 === $gwf_i ? ' class="is-key"' : ''; ?>><span class="gwf-c-step-n"><?php echo (int) $gwf_i + 1; ?></span><?php echo esc_html( $gwf_step ); ?></li>
						<?php endforeach; ?>
					</ol>
				</section>
			<?php endif; ?>

			<div class="gwf-p-grid">
				<?php if ( $gwf_field( 'gwf_comite_ia' ) ) : // 5. ?>
					<section class="gwf-g-block gwf-p-block"><h2>Cómo usamos la IA</h2><?php echo wp_kses_post( wpautop( $gwf_field( 'gwf_comite_ia' ) ) ); ?></section>
				<?php endif; ?>
				<?php if ( $gwf_field( 'gwf_comite_fuentes' ) ) : // 6. ?>
					<section class="gwf-g-block gwf-p-block"><h2>Qué fuentes usamos</h2><?php echo wp_kses_post( wpautop( $gwf_field( 'gwf_comite_fuentes' ) ) ); ?></section>
				<?php endif; ?>
			</div>

			<div class="gwf-p-grid">
				<?php $gwf_no = $gwf_lines( $gwf_field( 'gwf_comite_no_hacemos' ) ); ?>
				<?php if ( $gwf_no ) : // 7. ?>
					<section class="gwf-g-block gwf-p-block">
						<h2>Lo que no hacemos</h2>
						<ul class="gwf-c-list"><?php foreach ( $gwf_no as $gwf_item ) : ?><li><?php echo esc_html( $gwf_item ); ?></li><?php endforeach; ?></ul>
					</section>
				<?php endif; ?>
				<?php if ( $gwf_field( 'gwf_comite_errores' ) ) : // 8. ?>
					<section class="gwf-g-block gwf-p-block">
						<h2>Cómo corregimos errores</h2>
						<?php echo wp_kses_post( wpautop( $gwf_field( 'gwf_comite_errores' ) ) ); ?>
						<a class="gwf-p-button" href="<?php echo esc_url( home_url( '/contacto-y-soporte/' ) ); ?>">Reportar un error</a>
					</section>
				<?php endif; ?>
			</div>

			<?php
			$gwf_reviewed = $gwf_has_plugin ? gwf_guias_committee_reviewed( 4 ) : array();
			if ( $gwf_reviewed ) : // 9. Contenido revisado por el comité (automático).
				?>
				<section class="gwf-g-block gwf-p-block">
					<h2>Contenido revisado por el comité</h2>
					<ul class="gwf-c-reviewed">
						<?php foreach ( $gwf_reviewed as $gwf_post ) : ?>
							<?php $gwf_rt = gwf_guias_review_time( $gwf_post->ID ); ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $gwf_post ) ); ?>">
									<?php echo gwf_g_card_image( $gwf_post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<span class="gwf-c-reviewed-title"><?php echo esc_html( get_the_title( $gwf_post ) ); ?></span>
									<span class="gwf-c-reviewed-meta"><?php echo esc_html( ( 'desarrollo' === $gwf_post->post_type ? 'Pilar' : 'Guía' ) . ( $gwf_rt ? ' · revisada el ' . date_i18n( 'j M Y', $gwf_rt ) : '' ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php
			// 10 y 11. Quién está detrás y política editorial (cada botón, solo si su página existe).
			$gwf_company      = $gwf_field( 'gwf_comite_empresa' );
			$gwf_company_page = $gwf_page( $gwf_field( 'gwf_comite_empresa_pagina' ) ? $gwf_field( 'gwf_comite_empresa_pagina' ) : 'acerca-de/empresa' );
			$gwf_policy       = $gwf_page( 'politica-editorial' );
			$gwf_method       = $gwf_page( 'acerca-de/el-metodo' );
			?>
			<footer class="gwf-c-foot">
				<?php if ( $gwf_company ) : ?>
					<div>
						<h2>Quién está detrás</h2>
						<p>Growing Family es un proyecto de <strong><?php echo esc_html( $gwf_company ); ?></strong>.</p>
						<p class="gwf-p-back">
							<?php if ( $gwf_company_page ) : ?><a class="gwf-p-button" href="<?php echo esc_url( get_permalink( $gwf_company_page ) ); ?>">Página de empresa</a><?php endif; ?>
							<a class="gwf-p-button" href="<?php echo esc_url( home_url( '/contacto-y-soporte/' ) ); ?>">Contacto</a>
						</p>
					</div>
				<?php endif; ?>
				<?php if ( $gwf_policy || $gwf_method ) : ?>
					<div>
						<h2>Política editorial y método</h2>
						<p>Lee cómo trabajamos en detalle.</p>
						<p class="gwf-p-back">
							<?php if ( $gwf_policy ) : ?><a class="gwf-p-button" href="<?php echo esc_url( get_permalink( $gwf_policy ) ); ?>">Política editorial completa</a><?php endif; ?>
							<?php if ( $gwf_method ) : ?><a class="gwf-p-button" href="<?php echo esc_url( get_permalink( $gwf_method ) ); ?>">Conoce el método</a><?php endif; ?>
						</p>
					</div>
				<?php endif; ?>
			</footer>
		<?php endwhile; ?>
	</div>
</main>

<?php
get_footer();
