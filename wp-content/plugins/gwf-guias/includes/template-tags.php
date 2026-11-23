<?php
/**
 * Datos que usan las plantillas del tema (single-guia.php y, más adelante, single-desarrollo.php).
 * La presentación vive en el tema; aquí solo se reúnen los datos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Títulos de las secciones de la guía de problema: el editor puede cambiarlos (campos gwf_titulo_*); vacíos, se usa el de
 * por defecto. «toc» es la versión corta del índice, que solo se usa mientras el título sea el de por defecto.
 */
const GWF_GUIAS_SECTION_TITLES = array(
	'lo-que-intentaste' => array( 'field' => 'gwf_titulo_intentaste', 'default' => 'Lo que ya intentaste', 'toc' => 'Lo que ya intentaste' ),
	'por-que-pasa'      => array( 'field' => 'gwf_titulo_por_que', 'default' => 'Por qué pasa a esta edad', 'toc' => 'Por qué pasa a esta edad' ),
	'que-hacer'         => array( 'field' => 'gwf_titulo_que_hacer', 'default' => 'Qué hacer esta noche', 'toc' => 'Qué hacer esta noche' ),
	'cuando-consultar'  => array( 'field' => 'gwf_titulo_consultar', 'default' => 'Cuándo consultar con un profesional', 'toc' => 'Cuándo consultar' ),
	'que-evitar'        => array( 'field' => 'gwf_titulo_evitar', 'default' => 'Qué conviene evitar', 'toc' => 'Qué conviene evitar' ),
	'desacuerdo'        => array( 'field' => 'gwf_titulo_desacuerdo', 'default' => '¿Y si en casa cada adulto responde distinto?', 'toc' => 'Si cada adulto responde distinto' ),
	'cuento'            => array( 'field' => 'gwf_titulo_cuento', 'default' => 'Un cuento para esta noche', 'toc' => 'Un cuento para esta noche' ),
);

/** Título propio de una sección, o '' si está vacío o es igual al de por defecto. */
function gwf_guias_custom_title( $post_id, $section ) {
	$def   = GWF_GUIAS_SECTION_TITLES[ $section ] ?? null;
	$saved = $def ? trim( (string) get_post_meta( $post_id, $def['field'], true ) ) : '';
	return ( $def && $saved !== $def['default'] ) ? $saved : '';
}

/** Título de una sección tal como se muestra en la guía. */
function gwf_guias_section_title( $post_id, $section ) {
	$custom = gwf_guias_custom_title( $post_id, $section );
	return '' !== $custom ? $custom : ( GWF_GUIAS_SECTION_TITLES[ $section ]['default'] ?? '' );
}

/** Texto de la sección en el índice: el título propio si lo hay; si no, la versión corta. */
function gwf_guias_toc_label( $post_id, $section ) {
	$custom = gwf_guias_custom_title( $post_id, $section );
	return '' !== $custom ? $custom : ( GWF_GUIAS_SECTION_TITLES[ $section ]['toc'] ?? '' );
}

// Los campos de la persona revisora (y de cualquier ficha del equipo) están en includes/people.php.

/**
 * Revisor de una guía, listo para pintar.
 *
 * @return array{id:int,nombre:string,cargo:string,foto:string,ficha:string}|null
 */
function gwf_guias_reviewer( $post_id ) {
	$user_id = (int) get_post_meta( $post_id, 'gwf_revisado_por', true );
	$user    = $user_id ? get_userdata( $user_id ) : false;
	if ( ! $user ) {
		return null;
	}
	$foto_id = (int) get_user_meta( $user_id, 'gwf_foto', true );
	return array(
		'id'        => $user_id,
		'nombre'    => $user->display_name,
		'cargo'     => (string) get_user_meta( $user_id, 'gwf_cargo', true ),
		'foto'      => $foto_id ? (string) wp_get_attachment_image_url( $foto_id, 'thumbnail' ) : '',
		'formacion' => (string) get_user_meta( $user_id, 'gwf_formacion', true ),
		'ficha'     => gwf_guias_person_url( $user_id ), // Su página en /equipo/ si la ficha es pública.
	);
}

/** Fecha de revisión (la que se muestra como «Actualizado el»), como timestamp, o null. */
function gwf_guias_review_time( $post_id ) {
	$ymd = (string) get_post_meta( $post_id, 'gwf_fecha_revision', true );
	return preg_match( '/^\d{8}$/', $ymd ) ? strtotime( $ymd ) : null;
}

/** Minutos de lectura del texto del editor y de los bloques (200 palabras por minuto, mínimo 1). */
function gwf_guias_reading_time( $post_id ) {
	$text = get_post_field( 'post_content', $post_id );
	foreach ( array( 'gwf_respuesta', 'gwf_ya_intentaste', 'gwf_cuando_consultar', 'gwf_pasos', 'gwf_evitar', 'gwf_desacuerdo', 'gwf_definicion', 'gwf_hitos', 'gwf_curriculo' ) as $name ) {
		$text .= ' ' . get_post_meta( $post_id, $name, true );
	}
	return max( 1, (int) ceil( str_word_count( wp_strip_all_tags( $text ) ) / 200 ) );
}

/** «4, 5 y 6 años» a partir de los términos de Edad. */
function gwf_guias_ages_label( $post_id ) {
	$terms = get_the_terms( $post_id, 'edad' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	usort( $terms, function ( $a, $b ) { return strnatcmp( $a->slug, $b->slug ); } );
	$nums = array_map( function ( $t ) { return (int) $t->name; }, $terms );
	if ( count( $nums ) === 1 ) {
		return $terms[0]->name;
	}
	$last = array_pop( $nums );
	return implode( ', ', $nums ) . ' y ' . $last . ' años';
}

/** Id de un vídeo de YouTube a partir de su URL (watch, youtu.be, shorts o embed). */
function gwf_guias_youtube_id( $url ) {
	if ( preg_match( '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', (string) $url, $m ) ) {
		return $m[1];
	}
	return '';
}

/** Guías hermanas: las elegidas a mano o, si no hay, 3 del mismo tema (y edad, si las hay) publicadas. */
function gwf_guias_siblings( $post_id, $limit = 3 ) {
	$manual = array_filter( array_map( 'intval', (array) get_post_meta( $post_id, 'gwf_guias_relacionadas', true ) ) );
	if ( $manual ) {
		return array_slice( array_filter( $manual, function ( $id ) { return 'publish' === get_post_status( $id ); } ), 0, $limit );
	}
	$temas = wp_get_post_terms( $post_id, 'tema', array( 'fields' => 'ids' ) );
	if ( ! $temas || is_wp_error( $temas ) ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'guia',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => array( $post_id ),
			'fields'         => 'ids',
			'tax_query'      => array( array( 'taxonomy' => 'tema', 'terms' => $temas ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

/** Notas de padre a padre: las elegidas a mano o, si no hay, la más reciente de la categoría del blog con el mismo tema. */
function gwf_guias_notes( $post_id ) {
	$manual = array_filter( array_map( 'intval', (array) get_post_meta( $post_id, 'gwf_notas', true ) ) );
	$manual = array_filter( $manual, function ( $id ) { return 'publish' === get_post_status( $id ); } );
	if ( $manual ) {
		return array_values( $manual );
	}
	$slugs = wp_get_post_terms( $post_id, 'tema', array( 'fields' => 'slugs' ) );
	if ( ! $slugs || is_wp_error( $slugs ) ) {
		return array();
	}
	return get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'category_name' => implode( ',', $slugs ) ) );
}

/** Texto global del puente al método (bloque 11). Editable más adelante desde la página de ajustes de guías. */
function gwf_guias_bridge_text( $type = 'guia' ) {
	$defaults = array(
		'guia'       => 'Un ritual de 10 minutos cada noche ayuda a que esto no dependa de un buen día.',
		'desarrollo' => 'El método acompaña este desarrollo con un currículo en espiral de 10 meses, diez minutos cada noche.',
	);
	$saved = get_option( 'gwf_guias_puente_' . $type );
	return $saved ? $saved : $defaults[ $type ];
}

/**
 * Enlaces hacia abajo de un pilar (bloque 8): todas las guías de problema publicadas de su gran tema o, si es
 * un pilar por edad, de su edad. Se actualiza solo al publicar contenido nuevo. Con gwf-cuentos, también sus cuentos.
 *
 * @return array{label:string, guias:int[], cuentos:int[]}
 */
function gwf_guias_down_links( $pilar_id ) {
	$taxonomy = 'gran_tema';
	$terms    = wp_get_post_terms( $pilar_id, 'gran_tema' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		$taxonomy = 'edad';
		$terms    = wp_get_post_terms( $pilar_id, 'edad' );
	}
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array( 'label' => '', 'guias' => array(), 'cuentos' => array() );
	}
	$label = 'edad' === $taxonomy ? gwf_guias_ages_label( $pilar_id ) : implode( ', ', wp_list_pluck( $terms, 'name' ) );
	$cuentos = function_exists( 'gwf_cue_for_pillar' ) ? gwf_cue_for_pillar( $pilar_id, $taxonomy, $terms ) : array();
	return array( 'label' => $label, 'guias' => gwf_guias_pillar_guides( $pilar_id, $taxonomy, $terms ), 'cuentos' => $cuentos );
}

/**
 * Guías que lista un pilar: las de su gran tema (o edad) y, para cerrar el triángulo, las que lo eligieron como
 * pilar en «Relacionados» aunque no compartan el término. Ordenadas por título.
 */
function gwf_guias_pillar_guides( $pilar_id, $taxonomy = '', $terms = array() ) {
	$base    = array(
		'post_type'      => 'guia',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'fields'         => 'ids',
	);
	$by_term = $terms ? get_posts( $base + array( 'tax_query' => array( array( 'taxonomy' => $taxonomy, 'terms' => wp_list_pluck( $terms, 'term_id' ) ) ) ) ) : array(); // phpcs:ignore WordPress.DB.SlowDBQuery
	$by_meta = get_posts( $base + array( 'meta_key' => 'gwf_pilar', 'meta_value' => (string) $pilar_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$ids     = array_values( array_unique( array_merge( $by_term, $by_meta ) ) );
	usort( $ids, function ( $a, $b ) { return strnatcasecmp( get_the_title( $a ), get_the_title( $b ) ); } );
	return $ids;
}

/** Otros pilares publicados (para «Relacionados»): primero los de edad, luego los de tema. */
function gwf_guias_other_pillars( $pilar_id, $limit = 2 ) {
	return get_posts(
		array(
			'post_type'      => 'desarrollo',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => array( $pilar_id ),
			'fields'         => 'ids',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
}
