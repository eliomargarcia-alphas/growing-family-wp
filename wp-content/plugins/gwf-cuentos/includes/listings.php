<?php
/**
 * Listados (etapa 8): /cuentos/, /cuentos/emocion/<término>/, /cuentos/situacion/<término>/, /cuentos/para-contar/<término>/
 * y /cuentos/edad/<edad>/ (misma lógica que los de gwf-guias y gwf-actividades). Orden: Destacado, «me gusta» de WP ULike
 * y fecha; 12 por página; noindex con menos de 3.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_CUE_PER_PAGE = 12;

/** /cuentos/edad/5-anos/ (la Edad es compartida: su enlace propio es /edad/x/). */
function gwf_cue_listing_rewrites() {
	add_rewrite_rule( '^cuentos/edad/([^/]+)/page/([0-9]+)/?$', 'index.php?post_type=cuento&edad=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^cuentos/edad/([^/]+)/?$', 'index.php?post_type=cuento&edad=$matches[1]', 'top' );
}
add_action( 'init', 'gwf_cue_listing_rewrites' );

/** ¿Es un listado de cuentos? */
function gwf_cue_is_listing( $query = null ) {
	$query = $query ? $query : $GLOBALS['wp_query'];
	if ( ! $query instanceof WP_Query ) {
		return false;
	}
	return ( 'cuento' === $query->get( 'post_type' ) && ( $query->is_post_type_archive() || $query->is_tax() ) ) || $query->is_tax( array( 'emocion', 'situacion', 'para_contar' ) );
}

function gwf_cue_listing_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! gwf_cue_is_listing( $query ) ) {
		return;
	}
	$query->set( 'post_type', 'cuento' );
	$query->set( 'posts_per_page', GWF_CUE_PER_PAGE );
	$query->set( 'gwf_cue_listing', 1 );
}
add_action( 'pre_get_posts', 'gwf_cue_listing_query' );

function gwf_cue_listing_order( $clauses, $query ) {
	if ( ! $query->get( 'gwf_cue_listing' ) ) {
		return $clauses;
	}
	global $wpdb;
	$ulike     = $wpdb->prefix . 'ulike_meta';
	$has_ulike = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $ulike ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} gwf_cue_dest ON (gwf_cue_dest.post_id = {$wpdb->posts}.ID AND gwf_cue_dest.meta_key = 'gwf_cue_destacado')";
	$order            = array( 'COALESCE(gwf_cue_dest.meta_value + 0, 0) DESC' );
	if ( $has_ulike ) {
		$clauses['join'] .= " LEFT JOIN {$ulike} gwf_cue_likes ON (gwf_cue_likes.item_id = {$wpdb->posts}.ID AND gwf_cue_likes.meta_group = 'post' AND gwf_cue_likes.meta_key = 'count_distinct_like')";
		$order[]          = 'COALESCE(gwf_cue_likes.meta_value + 0, 0) DESC';
	}
	$order[]            = "{$wpdb->posts}.post_date DESC";
	$clauses['orderby'] = implode( ', ', $order );
	$clauses['groupby'] = "{$wpdb->posts}.ID";
	return $clauses;
}
add_filter( 'posts_clauses', 'gwf_cue_listing_order', 10, 2 );

/** Cabecera del listado: título e introducción (la descripción del término, si la tiene). */
function gwf_cue_listing_heading() {
	$term  = get_queried_object();
	$term  = $term instanceof WP_Term ? $term : null;
	$title = 'Cuentos para hablar de emociones';
	$intro = 'Cuentos cortos para leer con tu hija o tu hijo antes de dormir y hablar de lo que siente, con Ana o con Max.';
	if ( $term ) {
		$name   = mb_strtolower( $term->name );
		$titles = array(
			'edad'        => 'Cuentos para niños de ' . $term->name,
			'emocion'     => 'Cuentos sobre ' . $name,
			'situacion'   => 'Cuentos para ' . $name,
			'para_contar' => 'Cuentos para contar ' . $name,
		);
		$title = $titles[ $term->taxonomy ] ?? $title;
		$intro = $term->description ? $term->description : $intro;
	}
	return array( 'term' => $term, 'title' => $title, 'intro' => $intro );
}

/** Enlace a un filtro (Emoción y Situación por su taxonomía; la edad, dentro de /cuentos/). */
function gwf_cue_filter_url( $term = null ) {
	if ( ! $term ) {
		return home_url( '/cuentos/' );
	}
	return 'edad' === $term->taxonomy ? home_url( '/cuentos/edad/' . $term->slug . '/' ) : get_term_link( $term );
}

/** Términos con algún cuento publicado. */
function gwf_cue_filter_terms( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$terms,
			function ( $t ) use ( $taxonomy ) {
				return (bool) get_posts( array( 'post_type' => 'cuento', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => $taxonomy, 'terms' => $t->term_id ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			}
		)
	);
}

/** Title, descripción, canonical y robots de los listados en Yoast. */
function gwf_cue_listing_title( $title ) {
	if ( ! gwf_cue_is_listing() ) {
		return $title;
	}
	$paged = (int) get_query_var( 'paged' );
	return gwf_cue_listing_heading()['title'] . ( $paged > 1 ? ' (página ' . $paged . ')' : '' ) . ' | Growing Family';
}
add_filter( 'wpseo_title', 'gwf_cue_listing_title', 15 );
add_filter( 'wpseo_opengraph_title', 'gwf_cue_listing_title', 15 );

function gwf_cue_listing_description( $desc ) {
	return gwf_cue_is_listing() ? mb_strimwidth( gwf_cue_listing_heading()['intro'], 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_cue_listing_description', 15 );
add_filter( 'wpseo_opengraph_desc', 'gwf_cue_listing_description', 15 );

function gwf_cue_listing_canonical( $url ) {
	$term = get_queried_object();
	if ( gwf_cue_is_listing() && $term instanceof WP_Term && 'edad' === $term->taxonomy ) {
		$paged = (int) get_query_var( 'paged' );
		return gwf_cue_filter_url( $term ) . ( $paged > 1 ? 'page/' . $paged . '/' : '' );
	}
	return $url;
}
add_filter( 'wpseo_canonical', 'gwf_cue_listing_canonical', 15 );

function gwf_cue_listing_robots( $robots ) {
	if ( ! gwf_cue_is_listing() ) {
		return $robots;
	}
	return $GLOBALS['wp_query']->found_posts < 3 ? 'noindex, follow' : $robots;
}
add_filter( 'wpseo_robots', 'gwf_cue_listing_robots', 25 );
