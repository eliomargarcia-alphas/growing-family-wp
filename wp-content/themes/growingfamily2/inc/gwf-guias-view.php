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
function gwf_g_read_html( $minutes ) {
	return '<span class="gwf-g-read"><svg class="gwf-g-clock" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">'
		. '<circle cx="12" cy="12" r="9.5" fill="none" stroke="currentColor" stroke-width="2"/>'
		. '<path d="M12 6.5V12l3.5 3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
		. (int) $minutes . ' min de lectura</span>';
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
function gwf_g_credits( $author, $reviewer_html, $date_html, $fuentes, $sources_label = 'Fuentes' ) {
	?>
	<section id="fuentes" class="gwf-g-block gwf-g-credits">
		<h2>Autoría, revisión y fuentes</h2>
		<div class="gwf-g-credits-grid">
			<dl class="gwf-g-credits-who">
				<div><dt>Escrito por</dt><dd><?php echo esc_html( $author ); ?></dd></div>
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
