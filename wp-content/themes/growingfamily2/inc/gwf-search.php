<?php
/**
 * Búsqueda del sitio (search.php) en cuentos, guías, pilares, actividades, blog y páginas, con filtro por tipo
 * (?tipo=cuentos|guias|actividades|blog|paginas) y las tarjetas de los listados.
 *
 * Además del título, el extracto y el texto, cada palabra se busca en los campos de guías, pilares, actividades, cuentos
 * y comité (GWF_SEARCH_META_KEYS), en la descripción y frase clave de Yoast y en los nombres de temas, edades,
 * emociones, situaciones, «qué trabaja», categorías y etiquetas. Las páginas hechas con plantilla PHP solo tienen en la
 * base de datos su título y su descripción de Yoast. Quedan fuera la portada, la página del blog, lo marcado «noindex»,
 * las páginas legales (privacidad y términos), la del comité y la de la política editorial (GWF_SEARCH_HIDDEN_PAGES).
 *
 * Ajax Search Lite sustituía los resultados de la búsqueda principal por los suyos (vacíos): aquí se le quita esa
 * sustitución solo en la búsqueda principal; el plugin sigue instalado.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

const GWF_SEARCH_PER_PAGE = 12;
const GWF_SEARCH_HIDDEN_CATS = array( 41, 32, 6, 42 ); // Las mismas categorías que el blog no muestra.

/** Páginas que no salen en la búsqueda (por su dirección, iguales en staging y producción): legales, comité y política editorial. */
const GWF_SEARCH_HIDDEN_PAGES = array( 'poltica-de-privacidad', 'politica-de-privacidad', 'politica-de-privacidad-growing-family-app', 'politica-de-privacidad-growing-family-adminapp', 'terminos-y-condiciones', 'comite-editorial', 'politica-editorial' );

/** Campos de texto en los que también se busca. */
const GWF_SEARCH_META_KEYS = array(
	// Guías de problema y pilares.
	'gwf_respuesta', 'gwf_ya_intentaste', 'gwf_cuando_consultar', 'gwf_pasos', 'gwf_evitar', 'gwf_desacuerdo', 'gwf_video_titulo',
	'gwf_definicion', 'gwf_hitos', 'gwf_curriculo',
	// Actividades.
	'gwf_act_materiales', 'gwf_act_participantes', 'gwf_act_lugar', 'gwf_act_pasos', 'gwf_act_imprimible_titulo',
	'gwf_act_imprimible_texto', 'gwf_act_porque_titulo', 'gwf_act_porque',
	// Cuentos.
	'gwf_cue_asi_empieza', 'gwf_cue_como_contarlo', 'gwf_cue_que_trabaja',
	// Página del comité editorial.
	'gwf_comite_compromiso', 'gwf_comite_pasos', 'gwf_comite_ia', 'gwf_comite_fuentes', 'gwf_comite_no_hacemos', 'gwf_comite_errores',
	// Yoast (todas, y lo único con texto en las páginas de plantilla PHP).
	'_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw',
);

/** Taxonomías cuyos nombres de término también se buscan. */
const GWF_SEARCH_TAXONOMIES = array( 'category', 'post_tag', 'tema', 'gran_tema', 'edad', 'emocion', 'situacion', 'para_contar', 'trabaja' );

/** Tipos de la búsqueda: filtro → tipos de contenido y nombres (solo los que existen: los plugins pueden no estar activos). */
function gwf_search_types() {
	$types = array(
		'cuentos'     => array( 'label' => 'Cuentos', 'types' => array( 'cuento' ) ),
		'guias'       => array( 'label' => 'Guías', 'types' => array( 'guia', 'desarrollo' ) ),
		'actividades' => array( 'label' => 'Actividades', 'types' => array( 'actividad' ) ),
		'blog'        => array( 'label' => 'Blog', 'types' => array( 'post' ) ),
		'paginas'     => array( 'label' => 'Páginas', 'types' => array( 'page' ) ),
	);
	foreach ( $types as $key => $type ) {
		$types[ $key ]['types'] = array_values( array_filter( $type['types'], 'post_type_exists' ) );
		if ( ! $types[ $key ]['types'] ) {
			unset( $types[ $key ] );
		}
	}
	return $types;
}

/** Filtro activo (?tipo=), o '' para todo. */
function gwf_search_current_type() {
	$type = isset( $_GET['tipo'] ) ? sanitize_key( wp_unslash( $_GET['tipo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- búsqueda pública.
	return isset( gwf_search_types()[ $type ] ) ? $type : '';
}

/** Todos los tipos que entran en la búsqueda. */
function gwf_search_all_post_types() {
	return array_merge( ...array_values( wp_list_pluck( gwf_search_types(), 'types' ) ) );
}

function gwf_search_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}
	$type = gwf_search_current_type();
	$query->set( 'post_type', $type ? gwf_search_types()[ $type ]['types'] : gwf_search_all_post_types() );
	$query->set( 'posts_per_page', GWF_SEARCH_PER_PAGE );
	$query->set( 'category__not_in', GWF_SEARCH_HIDDEN_CATS );
	$query->set( 'post__not_in', gwf_search_excluded_ids() );
	$query->set( 'gwf_search', 1 );

	// Sin la sustitución de resultados de Ajax Search Lite en la búsqueda principal.
	global $wp_filter;
	foreach ( array( 'posts_results', 'posts_request' ) as $hook ) {
		if ( empty( $wp_filter[ $hook ] ) ) {
			continue;
		}
		foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $callback ) {
				if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) && 'WD_ASL_SearchOverride_Filter' === get_class( $callback['function'][0] ) ) {
					remove_filter( $hook, $callback['function'], $priority );
				}
			}
		}
	}
}
add_action( 'pre_get_posts', 'gwf_search_query', 20 );

/** Lo que no sale en la búsqueda: portada, página del blog, contenido marcado «noindex» en Yoast y GWF_SEARCH_HIDDEN_PAGES. */
function gwf_search_excluded_ids() {
	static $ids = null;
	if ( null === $ids ) {
		global $wpdb;
		$ids = array_map( 'intval', $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_yoast_wpseo_meta-robots-noindex' AND meta_value = '1'" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$slugs = "'" . implode( "','", array_map( 'esc_sql', GWF_SEARCH_HIDDEN_PAGES ) ) . "'";
		$pages = array_map( 'intval', $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_name IN ({$slugs})" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- direcciones fijas escapadas.
		$ids   = array_values( array_filter( array_merge( $ids, $pages, array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ) ) ) ) );
	}
	return $ids;
}

/**
 * Condición de búsqueda: cada palabra (todas obligatorias, como en WordPress) en título, extracto, texto, los campos
 * de GWF_SEARCH_META_KEYS o los nombres de sus términos. Las colaciones de MySQL ignoran mayúsculas y tildes.
 */
function gwf_search_where( $search, $query ) {
	$terms = (array) $query->get( 'search_terms' );
	if ( ! $query->get( 'gwf_search' ) || ! $terms ) {
		return $search;
	}
	global $wpdb;
	$keys  = "'" . implode( "','", array_map( 'esc_sql', GWF_SEARCH_META_KEYS ) ) . "'";
	$taxes = "'" . implode( "','", array_map( 'esc_sql', GWF_SEARCH_TAXONOMIES ) ) . "'";
	$parts = array();
	foreach ( $terms as $term ) {
		$term = trim( (string) $term );
		if ( '' === $term || '-' === $term[0] ) {
			continue;
		}
		$like    = '%' . $wpdb->esc_like( $term ) . '%';
		$parts[] = $wpdb->prepare(
			"({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s"
			. " OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} gwf_sm WHERE gwf_sm.post_id = {$wpdb->posts}.ID AND gwf_sm.meta_key IN ({$keys}) AND gwf_sm.meta_value LIKE %s)" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- claves fijas escapadas arriba.
			. " OR EXISTS (SELECT 1 FROM {$wpdb->term_relationships} gwf_tr JOIN {$wpdb->term_taxonomy} gwf_tt ON gwf_tt.term_taxonomy_id = gwf_tr.term_taxonomy_id JOIN {$wpdb->terms} gwf_t ON gwf_t.term_id = gwf_tt.term_id WHERE gwf_tr.object_id = {$wpdb->posts}.ID AND gwf_tt.taxonomy IN ({$taxes}) AND gwf_t.name LIKE %s))",
			$like,
			$like,
			$like,
			$like,
			$like
		);
	}
	if ( ! $parts ) {
		return $search;
	}
	$where = ' AND (' . implode( ' AND ', $parts ) . ')';
	if ( ! is_user_logged_in() ) {
		$where .= " AND ({$wpdb->posts}.post_password = '')";
	}
	return $where;
}
add_filter( 'posts_search', 'gwf_search_where', 10, 2 );

/** Número de resultados por filtro (para los chips «Cuentos (3)»). */
function gwf_search_counts( $search ) {
	$counts = array();
	foreach ( gwf_search_types() as $key => $type ) {
		$q              = new WP_Query(
			array(
				's'                => $search,
				'post_type'        => $type['types'],
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'category__not_in' => GWF_SEARCH_HIDDEN_CATS,
				'post__not_in'     => gwf_search_excluded_ids(),
				'gwf_search'       => 1,
				'no_found_rows'    => false,
			)
		);
		$counts[ $key ] = (int) $q->found_posts;
	}
	return $counts;
}

/** Datos de la tarjeta del resultado actual del loop: etiqueta del tipo, resumen y minutos. */
function gwf_search_card_args( $post_id ) {
	$type   = get_post_type( $post_id );
	$labels = array(
		'cuento'     => array( 'Cuento', home_url( '/cuentos/' ) ),
		'guia'       => array( 'Guía', home_url( '/guias/' ) ),
		'desarrollo' => array( 'Guía de desarrollo', home_url( '/desarrollo/' ) ),
		'actividad'  => array( 'Actividad', home_url( '/actividades/' ) ),
		'post'       => array( 'Blog', function_exists( 'gwf_blog_url' ) ? gwf_blog_url() : home_url( '/blog/' ) ),
		'page'       => array( 'Página', '' ),
	);
	$label = $labels[ $type ] ?? array( '', '' );

	$summary = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
	$minutes = 0;
	$time    = 'min de lectura';
	if ( 'cuento' === $type && function_exists( 'gwf_cue_summary' ) ) {
		$summary = gwf_cue_summary( $post_id );
		$minutes = gwf_cue_minutes( $post_id );
		$time    = 'min para contarlo';
	} elseif ( 'actividad' === $type ) {
		$summary = $summary ? $summary : (string) get_post_meta( $post_id, 'gwf_act_porque', true );
		$minutes = (int) get_post_meta( $post_id, 'gwf_act_minutos', true );
		$time    = 'min de actividad';
	} elseif ( in_array( $type, array( 'guia', 'desarrollo' ), true ) ) {
		$summary = $summary ? $summary : (string) get_post_meta( $post_id, 'guia' === $type ? 'gwf_respuesta' : 'gwf_definicion', true );
		$minutes = function_exists( 'gwf_guias_reading_time' ) ? gwf_guias_reading_time( $post_id ) : 0;
	} elseif ( 'page' === $type ) {
		// Páginas: su descripción de Yoast (las de plantilla PHP no tienen texto en el editor); sin minutos.
		$summary = $summary ? $summary : (string) get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		$summary = $summary ? $summary : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ), 30, '…' );
	} else {
		$summary = $summary ? $summary : get_the_excerpt( $post_id );
		$minutes = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) ) / 200 ) );
	}

	return array(
		'badge'      => $label[0] ? (object) array( 'name' => $label[0] ) : null,
		'badge_url'  => $label[1],
		'summary'    => $summary,
		'minutes'    => $minutes,
		'time_label' => $time,
	);
}
