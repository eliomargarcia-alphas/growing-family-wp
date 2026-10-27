<?php
/**
 * Listados de guías (etapa 6): /guias/, /guias/tema/<tema>/, /guias/edad/<edad>/, /desarrollo/,
 * /desarrollo/tema/<gran-tema>/ y /desarrollo/edad/<edad>/.
 *
 * Orden (plan): Destacado primero, luego «me gusta» de WP ULike y, con los mismos, lo más reciente.
 * 12 por página. Los listados de un término con menos de 3 guías llevan noindex.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_GUIAS_PER_PAGE = 12;

/** Campo «Destacado» (fija arriba una guía o un pilar en su listado). */
function gwf_guias_register_featured_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_guia_destacado',
			'title'    => 'Listado',
			'position' => 'side',
			'location' => gwf_guias_location( GWF_GUIAS_TYPES ),
			'fields'   => array(
				gwf_guias_field( 'gwf_destacado', 'Destacado', 'true_false', array( 'ui' => 1, 'instructions' => 'Aparece primero en su listado.' ) ),
			),
		)
	);
}
add_action( 'acf/init', 'gwf_guias_register_featured_field' );

/** URLs de filtro por edad dentro de cada sección: /guias/edad/4-anos/ y /desarrollo/edad/4-anos/. */
function gwf_guias_listing_rewrites() {
	foreach ( array( 'guias' => 'guia', 'desarrollo' => 'desarrollo' ) as $base => $type ) {
		add_rewrite_rule( "^{$base}/edad/([^/]+)/page/([0-9]+)/?$", 'index.php?post_type=' . $type . '&edad=$matches[1]&paged=$matches[2]', 'top' );
		add_rewrite_rule( "^{$base}/edad/([^/]+)/?$", 'index.php?post_type=' . $type . '&edad=$matches[1]', 'top' );
	}
}
add_action( 'init', 'gwf_guias_listing_rewrites' );

/**
 * Tipo de guía del listado actual ('guia', 'desarrollo') o '' si no es un listado de guías.
 * /edad/<edad>/ a secas se trata como listado de guías de problema.
 */
function gwf_guias_listing_type( $query = null ) {
	$query = $query ? $query : $GLOBALS['wp_query'];
	if ( ! $query instanceof WP_Query ) {
		return '';
	}
	$pt = $query->get( 'post_type' );
	if ( is_string( $pt ) && in_array( $pt, GWF_GUIAS_TYPES, true ) && ( $query->is_post_type_archive() || $query->is_tax() ) ) {
		return $pt;
	}
	if ( is_string( $pt ) && '' !== $pt ) {
		return ''; // Listado de otro tipo, p. ej. /actividades/edad/<edad>/ (plugin gwf-actividades).
	}
	if ( $query->is_tax( 'tema' ) ) {
		return 'guia';
	}
	if ( $query->is_tax( 'gran_tema' ) ) {
		return 'desarrollo';
	}
	if ( $query->is_tax( 'edad' ) ) {
		return 'guia';
	}
	return '';
}

/** Consulta principal de los listados: tipo correcto y 12 por página. */
function gwf_guias_listing_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$type = gwf_guias_listing_type( $query );
	if ( ! $type ) {
		return;
	}
	$query->set( 'post_type', $type ); // Gran tema también está en las guías: /desarrollo/tema/ solo lista pilares.
	$query->set( 'posts_per_page', GWF_GUIAS_PER_PAGE );
	$query->set( 'gwf_guias_listing', 1 );
}
add_action( 'pre_get_posts', 'gwf_guias_listing_query' );

/** Orden: Destacado, «me gusta» (tabla de WP ULike) y fecha. */
function gwf_guias_listing_order( $clauses, $query ) {
	if ( ! $query->get( 'gwf_guias_listing' ) ) {
		return $clauses;
	}
	global $wpdb;
	$ulike = $wpdb->prefix . 'ulike_meta';
	$has_ulike = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $ulike ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} gwf_dest ON (gwf_dest.post_id = {$wpdb->posts}.ID AND gwf_dest.meta_key = 'gwf_destacado')";
	$order = array( "COALESCE(gwf_dest.meta_value + 0, 0) DESC" );
	if ( $has_ulike ) {
		$clauses['join'] .= " LEFT JOIN {$ulike} gwf_likes ON (gwf_likes.item_id = {$wpdb->posts}.ID AND gwf_likes.meta_group = 'post' AND gwf_likes.meta_key = 'count_distinct_like')";
		$order[] = 'COALESCE(gwf_likes.meta_value + 0, 0) DESC';
	}
	$order[]            = "{$wpdb->posts}.post_date DESC";
	$clauses['orderby'] = implode( ', ', $order );
	$clauses['groupby'] = "{$wpdb->posts}.ID";
	return $clauses;
}
add_filter( 'posts_clauses', 'gwf_guias_listing_order', 10, 2 );

/** Término filtrado en el listado (Tema, Gran tema o Edad), o null. */
function gwf_guias_listing_term() {
	$term = get_queried_object();
	return $term instanceof WP_Term ? $term : null;
}

/** Datos de cabecera del listado: título, introducción y descripción para buscadores. */
function gwf_guias_listing_heading() {
	$type = gwf_guias_listing_type();
	$term = gwf_guias_listing_term();
	if ( 'desarrollo' === $type ) {
		$title = 'Desarrollo de 4 a 6 años';
		$intro = 'Qué cambia en cada edad y en cada gran tema del desarrollo, con fuentes y revisión experta.';
	} else {
		$title = 'Guías para madres y padres';
		$intro = 'Qué hacer cuando no te hace caso, con los berrinches, el miedo a dormir solo o las pantallas: guías revisadas por especialistas.';
	}
	if ( $term ) {
		if ( 'edad' === $term->taxonomy ) {
			$title = ( 'desarrollo' === $type ? 'Desarrollo a los ' : 'Guías para ' ) . $term->name;
		} else {
			$title = ( 'desarrollo' === $type ? 'Desarrollo: ' : 'Guías: ' ) . $term->name;
		}
		$intro = $term->description ? $term->description : $intro;
	}
	return array( 'type' => $type, 'term' => $term, 'title' => $title, 'intro' => $intro );
}

/** Enlace a un filtro del listado (tema/gran tema por su taxonomía, edad dentro de la sección). */
function gwf_guias_filter_url( $type, $term = null ) {
	$base = 'desarrollo' === $type ? 'desarrollo' : 'guias';
	if ( ! $term ) {
		return home_url( "/{$base}/" );
	}
	if ( 'edad' === $term->taxonomy ) {
		return home_url( "/{$base}/edad/{$term->slug}/" );
	}
	return get_term_link( $term );
}

/** Términos con contenido publicado de un tipo (para los filtros del listado). */
function gwf_guias_filter_terms( $taxonomy, $type ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$terms,
			function ( $t ) use ( $taxonomy, $type ) {
				return (bool) get_posts(
					array(
						'post_type'      => $type,
						'post_status'    => 'publish',
						'posts_per_page' => 1,
						'fields'         => 'ids',
						'tax_query'      => array( array( 'taxonomy' => $taxonomy, 'terms' => $t->term_id ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
					)
				);
			}
		)
	);
}

/** Títulos y descripciones de los listados en Yoast (las plantillas por defecto están en inglés). */
function gwf_guias_listing_title( $title ) {
	if ( ! gwf_guias_listing_type() ) {
		return $title;
	}
	$h     = gwf_guias_listing_heading();
	$paged = (int) get_query_var( 'paged' );
	return $h['title'] . ( $paged > 1 ? ' (página ' . $paged . ')' : '' ) . ' | Growing Family';
}
add_filter( 'wpseo_title', 'gwf_guias_listing_title' );
add_filter( 'wpseo_opengraph_title', 'gwf_guias_listing_title' );

function gwf_guias_listing_description( $desc ) {
	if ( ! gwf_guias_listing_type() ) {
		return $desc;
	}
	return mb_strimwidth( gwf_guias_listing_heading()['intro'], 0, 155, '…' );
}
add_filter( 'wpseo_metadesc', 'gwf_guias_listing_description' );
add_filter( 'wpseo_opengraph_desc', 'gwf_guias_listing_description' );

/** Canonical de /guias/edad/<edad>/ (Yoast pondría el del archivo del tipo). */
function gwf_guias_listing_canonical( $url ) {
	$term = gwf_guias_listing_term();
	$type = gwf_guias_listing_type();
	if ( $type && $term && 'edad' === $term->taxonomy ) {
		$url   = gwf_guias_filter_url( $type, $term );
		$paged = (int) get_query_var( 'paged' );
		$url  .= $paged > 1 ? 'page/' . $paged . '/' : '';
	}
	return $url;
}
add_filter( 'wpseo_canonical', 'gwf_guias_listing_canonical' );

/** Listados de un término con menos de 3 guías: noindex (plan). /edad/<edad>/ a secas, siempre noindex. */
function gwf_guias_listing_robots( $robots ) {
	$type = gwf_guias_listing_type();
	if ( ! $type || ! gwf_guias_listing_term() ) {
		return $robots;
	}
	global $wp_query;
	// /edad/<edad>/ a secas (sin /guias/ ni /desarrollo/ delante) duplica /guias/edad/<edad>/: siempre noindex.
	$path      = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$bare_edad = is_tax( 'edad' ) && ! preg_match( '#^/(guias|desarrollo)/#', $path );
	return ( $wp_query->found_posts < 3 || $bare_edad ) ? 'noindex, follow' : $robots;
}
add_filter( 'wpseo_robots', 'gwf_guias_listing_robots', 20 );
