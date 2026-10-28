<?php
/**
 * Piezas de presentación compartidas por single-guia.php y single-desarrollo.php.
 * Los datos los da el plugin gwf-guias; aquí solo se arma el HTML (todo escapado).
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** «🕒 N min de lectura» con el reloj en SVG (color del texto: var(--g-blue) en el CSS). */
function gwf_g_read_html( $minutes, $label = 'min de lectura' ) {
	return '<span class="gwf-g-read"><svg class="gwf-g-clock" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
		. '<circle cx="12" cy="12" r="9.5" fill="none" stroke="currentColor" stroke-width="2"/>'
		. '<path d="M12 6.5V12l3.5 3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
		. (int) $minutes . ' ' . esc_html( $label ) . '</span>';
}

/** Texto con icono SVG delante (participantes o lugar), con el mismo estilo que el reloj. */
function gwf_g_icon_html( $icon, $text ) {
	$paths = array(
		'participantes' => '<circle cx="9" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="17" cy="9" r="2.4" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16.5 14.1c2.6.3 4.5 2.6 4.5 5.4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'lugar'         => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" fill="none" stroke="currentColor" stroke-width="2"/>',
	);
	return '<span class="gwf-g-read gwf-g-icon"><svg class="gwf-g-clock" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
		. ( $paths[ $icon ] ?? '' ) . '</svg>' . esc_html( $text ) . '</span>';
}

/** Imagen destacada de una entrada para las tarjetas (vacío si no tiene). */
function gwf_g_card_image( $post_id ) {
	if ( ! $post_id || ! has_post_thumbnail( $post_id ) ) {
		return '';
	}
	return '<span class="gwf-g-related-img">'
		. get_the_post_thumbnail( $post_id, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '', 'sizes' => '(min-width: 1025px) 280px, 100vw' ) )
		. '</span>';
}

/** Tarjeta de «Relacionados»: imagen destacada (si la hay), tipo y título. $title/$url opcionales. */
function gwf_g_card( $post_id, $label, $title = null, $url = null ) {
	return '<li><a href="' . esc_url( $url ? $url : get_permalink( $post_id ) ) . '">'
		. gwf_g_card_image( $post_id )
		. '<span class="gwf-g-related-text"><span class="gwf-g-related-type">' . esc_html( $label ) . '</span>'
		. esc_html( null !== $title ? $title : get_the_title( $post_id ) ) . '</span></a></li>';
}

/** Tarjeta fija de «El método», con la imagen destacada de su página si la tiene. */
function gwf_g_method_card() {
	$page = get_page_by_path( 'acerca-de/el-metodo' );
	return gwf_g_card( $page ? $page->ID : 0, 'El método', '10 minutos cada noche', home_url( '/acerca-de/el-metodo/' ) );
}

/** «Nombre, cargo», enlazado a su ficha si la tiene. */
function gwf_g_reviewer_html( $reviewer ) {
	if ( ! $reviewer ) {
		return '';
	}
	$label = esc_html( $reviewer['nombre'] . ( $reviewer['cargo'] ? ', ' . $reviewer['cargo'] : '' ) );
	return $reviewer['ficha'] ? '<a href="' . esc_url( $reviewer['ficha'] ) . '">' . $label . '</a>' : $label;
}

/** Autor de la guía o el pilar: el equipo editorial o, si firma un redactor con ficha pública, su nombre enlazado a ella. */
function gwf_g_author_html() {
	$name = esc_html( get_the_author() );
	$url  = gwf_guias_person_signs( get_the_author_meta( 'ID' ) ) ? gwf_guias_person_url( get_the_author_meta( 'ID' ) ) : '';
	return $url ? '<a href="' . esc_url( $url ) . '" rel="author">' . $name . '</a>' : $name;
}

/** Icono SVG de una red social (ficha del equipo). Las que no tienen icono propio usan uno de enlace. */
function gwf_g_social_icon( $network ) {
	$paths = array(
		'linkedin'  => '<path fill="currentColor" d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.55V9h3.57v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/>',
		'instagram' => '<rect x="2.5" y="2.5" width="19" height="19" rx="5.5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4.3" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.6" cy="6.4" r="1.3" fill="currentColor"/>',
		'facebook'  => '<path fill="currentColor" d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.09 10.13 24v-8.44H7.08v-3.49h3.05V9.43c0-3.01 1.79-4.67 4.53-4.67 1.31 0 2.69.24 2.69.24v2.95h-1.51c-1.49 0-1.96.93-1.96 1.87v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.09 24 18.1 24 12.07z"/>',
		'youtube'   => '<path fill="currentColor" d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/>',
		'twitter'   => '<path fill="currentColor" d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.47l8.6-9.83L0 1.15h7.59l5.24 6.93 6.07-6.93zm-1.29 19.5h2.04L6.49 3.24H4.3l13.31 17.41z"/>',
	);
	$path = $paths[ $network ] ?? '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M10 14a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5M14 10a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/>';
	return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

/** <time> de la fecha de revisión. */
function gwf_g_date_html( $timestamp ) {
	return $timestamp ? '<time datetime="' . esc_attr( gmdate( 'Y-m-d', $timestamp ) ) . '">' . esc_html( date_i18n( 'j M Y', $timestamp ) ) . '</time>' : '';
}

/** Lista <li> del índice a partir de [ancla => texto]. */
function gwf_g_toc_html( $toc ) {
	$html = '';
	foreach ( $toc as $anchor => $text ) {
		$html .= '<li><a href="#' . esc_attr( $anchor ) . '">' . esc_html( $text ) . '</a></li>';
	}
	return $html;
}

/** Tarjeta «Revisado por» del panel lateral. */
function gwf_g_reviewer_card( $reviewer ) {
	if ( ! $reviewer ) {
		return;
	}
	$initial = mb_substr( preg_replace( '/[^\p{L}]/u', '', preg_replace( '/\[[^\]]*\]/', '', $reviewer['nombre'] ) ), 0, 1 );
	?>
	<div class="gwf-g-card">
		<p class="gwf-g-card-title">Revisado por</p>
		<div class="gwf-g-reviewer">
			<?php if ( $reviewer['foto'] ) : ?>
				<img src="<?php echo esc_url( $reviewer['foto'] ); ?>" alt="" width="48" height="48" loading="lazy" decoding="async">
			<?php else : ?>
				<span class="gwf-g-avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
			<?php endif; ?>
			<div>
				<p class="gwf-g-reviewer-name"><?php echo esc_html( $reviewer['nombre'] ); ?></p>
				<p class="gwf-g-reviewer-role">
					<?php echo esc_html( $reviewer['cargo'] ); ?>
					<?php if ( $reviewer['ficha'] ) : ?> · <a href="<?php echo esc_url( $reviewer['ficha'] ); ?>">Ver ficha</a><?php endif; ?>
				</p>
			</div>
		</div>
	</div>
	<?php
}

/** Bloque de autoría, revisión y fuentes. */
function gwf_g_credits( $author_html, $reviewer_html, $date_html, $fuentes, $sources_label = 'Fuentes' ) {
	?>
	<section id="fuentes" class="gwf-g-block gwf-g-credits">
		<h2>Autoría, revisión y fuentes</h2>
		<div class="gwf-g-credits-grid">
			<dl class="gwf-g-credits-who">
				<div><dt>Escrito por</dt><dd><?php echo $author_html; // phpcs:ignore WordPress.Security.EscapeOutput -- gwf_g_author_html escapa. ?></dd></div>
				<?php if ( $reviewer_html ) : ?><div><dt>Revisado por</dt><dd><?php echo $reviewer_html; // phpcs:ignore WordPress.Security.EscapeOutput -- gwf_g_reviewer_html escapa. ?></dd></div><?php endif; ?>
				<?php if ( $date_html ) : ?><div><dt>Actualizado</dt><dd><?php echo $date_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></dd></div><?php endif; ?>
			</dl>
			<?php if ( $fuentes ) : ?>
				<details class="gwf-g-sources" open>
					<summary><?php echo esc_html( $sources_label ); ?> (<?php echo count( $fuentes ); ?>)</summary>
					<ol>
						<?php foreach ( $fuentes as $f ) : ?>
							<li>
								<?php if ( $f['url'] ) : ?>
									<a href="<?php echo esc_url( $f['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $f['titulo'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $f['titulo'] ); ?>
								<?php endif; ?>
								<?php if ( $f['organismo'] ) : ?><span> · <?php echo esc_html( $f['organismo'] ); ?></span><?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</details>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/** «¿Te ha gustado este contenido?» con el botón de WP ULike (los «me gusta» ordenan los listados). */
function gwf_g_like() {
	if ( ! shortcode_exists( 'wp_ulike' ) ) {
		return;
	}
	?>
	<section class="gwf-g-block gwf-g-like">
		<h2>¿Te ha gustado este contenido?</h2>
		<?php echo do_shortcode( '[wp_ulike]' ); ?>
	</section>
	<?php
}

/** Puente al método (bloque fijo con texto global por tipo). */
function gwf_g_bridge( $type, $title = 'Sostenerlo en el tiempo' ) {
	?>
	<section class="gwf-g-block gwf-g-bridge">
		<h2><?php echo esc_html( $title ); ?></h2>
		<p><?php echo esc_html( gwf_guias_bridge_text( $type ) ); ?></p>
		<a class="gwf-g-button" href="<?php echo esc_url( home_url( '/acerca-de/el-metodo/' ) ); ?>">Conoce el método</a>
	</section>
	<?php
}
