<?php
/**
 * Contenido del cuento para la plantilla del tema (single-cuento.php, etapa 3):
 *
 * - Páginas: lo que hay entre dos «Salto de página» es una página del modo lectura. Todas van en el HTML del servidor.
 * - Variantes {Ana|Max} y {asustada|asustado}: el HTML lleva la del protagonista por defecto y las dos en data-*,
 *   para que el selector cambie todas a la vez sin recargar.
 * - Notas de lectura: la etiqueta «Pregunta para tu hija o hijo» y el icono del tono se añaden al pintar el párrafo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Palabras por minuto leyendo en voz alta a una niña o un niño (con pausas). */
const GWF_CUE_WPM = 90;

/** Protagonista por defecto del cuento: 'ana' o 'max'. */
function gwf_cue_protagonist( $post_id ) {
	return 'max' === get_post_meta( $post_id, 'gwf_cue_protagonista', true ) ? 'max' : 'ana';
}

/**
 * {Ana|Max} → la variante del protagonista. Con $html, envuelta en un span con las dos versiones
 * (el selector del tema cambia el texto); sin $html, solo el texto (title, descripciones, schema).
 */
function gwf_cue_variants( $text, $protagonist = 'ana', $html = true ) {
	$replace = function ( $segment, $wrap ) use ( $protagonist ) {
		return preg_replace_callback(
			'/\{([^{}|<>]*)\|([^{}|<>]*)\}/u',
			function ( $m ) use ( $protagonist, $wrap ) {
				$value = 'max' === $protagonist ? $m[2] : $m[1];
				if ( ! $wrap ) {
					return $value;
				}
				return '<span class="gwf-v" data-ana="' . esc_attr( $m[1] ) . '" data-max="' . esc_attr( $m[2] ) . '">' . esc_html( $value ) . '</span>';
			},
			$segment
		);
	};
	if ( ! $html ) {
		return $replace( (string) $text, false );
	}
	// Solo el texto visible lleva el span; dentro de las etiquetas (alt, title…) queda la variante por defecto.
	$parts = preg_split( '/(<[^>]*>)/', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	foreach ( $parts as $i => $part ) {
		$parts[ $i ] = $replace( $part, '' === $part || '<' !== $part[0] );
	}
	return implode( '', $parts );
}

/** Bloques del cuento agrupados por página (el «Salto de página» separa; los bloques vacíos no cuentan). */
function gwf_cue_page_blocks( $post_id ) {
	$pages   = array( array() );
	$current = 0;
	foreach ( parse_blocks( get_post_field( 'post_content', $post_id ) ) as $block ) {
		if ( 'core/nextpage' === $block['blockName'] ) {
			$pages[] = array();
			$current++;
			continue;
		}
		if ( null === $block['blockName'] && '' === trim( $block['innerHTML'] ) ) {
			continue;
		}
		$pages[ $current ][] = $block;
	}
	return array_values( array_filter( $pages ) );
}

/** HTML de cada página, con variantes, notas de lectura e imágenes con carga diferida. */
function gwf_cue_pages( $post_id ) {
	$protagonist = gwf_cue_protagonist( $post_id );
	$pages       = array();
	foreach ( gwf_cue_page_blocks( $post_id ) as $blocks ) {
		$html = '';
		foreach ( $blocks as $block ) {
			$html .= render_block( $block );
		}
		$html    = wp_filter_content_tags( do_shortcode( $html ) );
		$pages[] = gwf_cue_variants( $html, $protagonist );
	}
	return $pages;
}

function gwf_cue_page_count( $post_id ) {
	return count( gwf_cue_page_blocks( $post_id ) );
}

/** Minutos para contarlo: el campo, o el cálculo por palabras. */
function gwf_cue_minutes( $post_id ) {
	$minutes = (int) get_post_meta( $post_id, 'gwf_cue_minutos', true );
	if ( $minutes > 0 ) {
		return $minutes;
	}
	$text = gwf_cue_variants( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), gwf_cue_protagonist( $post_id ), false );
	return max( 1, (int) ceil( str_word_count( $text ) / GWF_CUE_WPM ) );
}

/** Fuentes «Título | Organismo | URL». */
function gwf_cue_sources( $post_id ) {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $post_id, 'gwf_cue_fuentes', true ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts  = array_map( 'trim', explode( '|', $line, 3 ) );
		$rows[] = array( 'titulo' => $parts[0], 'organismo' => $parts[1] ?? '', 'url' => $parts[2] ?? '' );
	}
	return $rows;
}

/** Notas de lectura: etiqueta de la pregunta e icono del tono, dentro del párrafo (el lector de pantalla los lee bien). */
function gwf_cue_render_note( $html, $block ) {
	if ( 'core/paragraph' !== $block['blockName'] || 'cuento' !== get_post_type() || empty( $block['attrs']['className'] ) ) {
		return $html;
	}
	$class = $block['attrs']['className'];
	if ( false !== strpos( $class, 'is-style-gwf-pregunta' ) ) {
		return preg_replace( '/^(\s*<p[^>]*>)/', '$1<span class="gwf-note-label">Pregunta para tu hija o hijo</span>', $html, 1 );
	}
	if ( preg_match( '/is-style-gwf-tono-([a-z]+)/', $class, $m ) && isset( GWF_CUE_TONES[ $m[1] ] ) ) {
		$icon = '<span class="gwf-tone gwf-tone-' . esc_attr( $m[1] ) . '" aria-hidden="true"></span><span class="screen-reader-text">' . esc_html( GWF_CUE_TONES[ $m[1] ][1] ) . ': </span>';
		return preg_replace( '/^(\s*<p[^>]*>)/', '$1' . $icon, $html, 1 );
	}
	return $html;
}
add_filter( 'render_block', 'gwf_cue_render_note', 10, 2 );

/** Fuera de la plantilla del cuento (feeds, extractos, plantilla por defecto), las variantes quedan como texto. */
function gwf_cue_content_variants( $content ) {
	return 'cuento' === get_post_type() ? gwf_cue_variants( $content, gwf_cue_protagonist( get_the_ID() ) ) : $content;
}
add_filter( 'the_content', 'gwf_cue_content_variants', 20 );

function gwf_cue_plain_variants( $text, $post = null ) {
	$post = get_post( $post );
	return ( $post && 'cuento' === $post->post_type ) ? gwf_cue_variants( $text, gwf_cue_protagonist( $post->ID ), false ) : $text;
}
add_filter( 'get_the_excerpt', 'gwf_cue_plain_variants', 20, 2 );
add_filter( 'the_title', 'gwf_cue_plain_variants', 20, 2 );

/** Términos de una taxonomía del cuento, el principal de Yoast primero. */
function gwf_cue_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$primary = function_exists( 'yoast_get_primary_term_id' ) ? (int) yoast_get_primary_term_id( $taxonomy, $post_id ) : 0;
	usort(
		$terms,
		function ( $a, $b ) use ( $primary ) {
			return ( $b->term_id === $primary ) <=> ( $a->term_id === $primary ) ?: strnatcmp( $a->slug, $b->slug );
		}
	);
	return $terms;
}

/** Otro cuento para seguir: de la misma emoción o, si no hay, de la misma edad. */
function gwf_cue_sibling( $post_id ) {
	foreach ( array( 'emocion', 'edad' ) as $taxonomy ) {
		$ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( ! $ids || is_wp_error( $ids ) ) {
			continue;
		}
		$found = get_posts( array( 'post_type' => 'cuento', 'post_status' => 'publish', 'posts_per_page' => 1, 'post__not_in' => array( $post_id ), 'fields' => 'ids', 'orderby' => 'rand', 'tax_query' => array( array( 'taxonomy' => $taxonomy, 'terms' => $ids ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( $found ) {
			return (int) $found[0];
		}
	}
	return 0;
}

/** Guía y pilar enlazados (solo si están publicados). */
function gwf_cue_linked( $post_id, $field ) {
	$id = (int) get_post_meta( $post_id, $field, true );
	return ( $id && 'publish' === get_post_status( $id ) ) ? $id : 0;
}

/** Revisor (solo si el cuento requiere firma), con el formato de la tarjeta «Revisado por» de las guías. */
function gwf_cue_reviewer( $post_id ) {
	if ( ! get_post_meta( $post_id, 'gwf_cue_requiere_firma', true ) ) {
		return null;
	}
	$user_id = (int) get_post_meta( $post_id, 'gwf_cue_revisado_por', true );
	$user    = $user_id ? get_userdata( $user_id ) : false;
	if ( ! $user ) {
		return null;
	}
	$foto_id = (int) get_user_meta( $user_id, 'gwf_foto', true );
	return array(
		'id'     => $user_id,
		'nombre' => $user->display_name,
		'cargo'  => (string) get_user_meta( $user_id, 'gwf_cargo', true ),
		'foto'   => $foto_id ? (string) wp_get_attachment_image_url( $foto_id, 'thumbnail' ) : '',
		'ficha'  => function_exists( 'gwf_guias_person_url' ) ? gwf_guias_person_url( $user_id ) : '',
	);
}

/** Fecha visible: la de revisión si la hay; si no, la de modificación. */
function gwf_cue_updated_time( $post_id ) {
	$ymd = (string) get_post_meta( $post_id, 'gwf_cue_fecha_revision', true );
	return preg_match( '/^\d{8}$/', $ymd ) ? strtotime( $ymd ) : (int) get_post_modified_time( 'U', false, $post_id );
}
