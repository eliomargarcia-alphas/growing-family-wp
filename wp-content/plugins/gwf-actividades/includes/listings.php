<?php
/**
 * Listados: /actividades/, /actividades/edad/<edad>/ y /actividades/trabaja/<término>/ (misma lógica que los de
 * gwf-guias). Orden: Destacado, «me gusta» de WP ULike y fecha; 12 por página; noindex con menos de 3.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_ACT_PER_PAGE = 12;

/** /actividades/edad/4-anos/. */
function gwf_act_listing_rewrites() {
	add_rewrite_rule( '^actividades/edad/([^/]+)/page/([0-9]+)/?$', 'index.php?post_type=actividad&edad=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^actividades/edad/([^/]+)/?$', 'index.php?post_type=actividad&edad=$matches[1]', 'top' );
}
add_action( 'init', 'gwf_act_listing_rewrites' );

/** ¿Es un listado de actividades? */
function gwf_act_is_listing( $query = null ) {
	$query = $query ? $query : $GLOBALS['wp_query'];
	if ( ! $query instanceof WP_Query ) {
		return false;
	}
	return ( 'actividad' === $query->get( 'post_type' ) && ( $query->is_post_type_archive() || $query->is_tax() ) ) || $query->is_tax( 'trabaja' );
}

function gwf_act_listing_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! gwf_act_is_listing( $query ) ) {
		return;
	}
	$query->set( 'post_type', 'actividad' );
	$query->set( 'posts_per_page', GWF_ACT_PER_PAGE );
	$query->set( 'gwf_act_listing', 1 );
}
add_action( 'pre_get_posts', 'gwf_act_listing_query' );

function gwf_act_listing_order( $clauses, $query ) {
	if ( ! $query->get( 'gwf_act_listing' ) ) {
		return $clauses;
	}
	global $wpdb;
	$ulike     = $wpdb->prefix . 'ulike_meta';
	$has_ulike = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $ulike ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} gwf_act_dest ON (gwf_act_dest.post_id = {$wpdb->posts}.ID AND gwf_act_dest.meta_key = 'gwf_act_destacado')";
	$order            = array( 'COALESCE(gwf_act_dest.meta_value + 0, 0) DESC' );
	if ( $has_ulike ) {
		$clauses['join'] .= " LEFT JOIN {$ulike} gwf_act_likes ON (gwf_act_likes.item_id = {$wpdb->posts}.ID AND gwf_act_likes.meta_group = 'post' AND gwf_act_likes.meta_key = 'count_distinct_like')";
		$order[]          = 'COALESCE(gwf_act_likes.meta_value + 0, 0) DESC';
	}
	$order[]            = "{$wpdb->posts}.post_date DESC";
	$clauses['orderby'] = implode( ', ', $order );
	$clauses['groupby'] = "{$wpdb->posts}.ID";
	return $clauses;
}
add_filter( 'posts_clauses', 'gwf_act_listing_order', 10, 2 );

/** Cabecera del listado: título e introducción (la descripción del término, si la tiene). */
function gwf_act_listing_heading() {
	$term  = get_queried_object();
	$term  = $term instanceof WP_Term ? $term : null;
	$title = 'Actividades para hacer juntos';
	$intro = 'Juegos y manualidades sin pantalla para hacer hoy con tu hija o tu hijo, con lo que tienes en casa.';
	if ( $term ) {
		$title = 'edad' === $term->taxonomy ? 'Actividades para hacer con niños de ' . $term->name : 'Actividades para trabajar ' . mb_strtolower( $term->name );
		$intro = $term->description ? $term->description : $intro;
	}
	return array( 'term' => $term, 'title' => $title, 'intro' => $intro );
}

/** Enlace a un filtro (Qué trabaja por su taxonomía; la edad, dentro de /actividades/). */
function gwf_act_filter_url( $term = null ) {
	if ( ! $term ) {
		return home_url( '/actividades/' );
	}
	return 'edad' === $term->taxonomy ? home_url( '/actividades/edad/' . $term->slug . '/' ) : get_term_link( $term );
}

/** Términos con alguna actividad publicada. */
function gwf_act_filter_terms( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return array_values(
		array_filter(
			$terms,
			function ( $t ) use ( $taxonomy ) {
				return (bool) get_posts( array( 'post_type' => 'actividad', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => $taxonomy, 'terms' => $t->term_id ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			}
		)
	);
}

/** Title, descripción, canonical y robots de los listados en Yoast. */
function gwf_act_listing_title( $title ) {
	if ( ! gwf_act_is_listing() ) {
		return $title;
	}
	$paged = (int) get_query_var( 'paged' );
	return gwf_act_listing_heading()['title'] . ( $paged > 1 ? ' (página ' . $paged . ')' : '' ) . ' | Growing Family';
}
add_filter( 'wpseo_title', 'gwf_act_listing_title', 15 );
add_filter( 'wpseo_opengraph_title', 'gwf_act_listing_title', 15 );

function gwf_act_listing_description( $desc ) {
	return gwf_act_is_listing() ? mb_strimwidth( gwf_act_listing_heading()['intro'], 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_act_listing_description', 15 );
add_filter( 'wpseo_opengraph_desc', 'gwf_act_listing_description', 15 );

function gwf_act_listing_canonical( $url ) {
	$term = get_queried_object();
	if ( gwf_act_is_listing() && $term instanceof WP_Term && 'edad' === $term->taxonomy ) {
		$paged = (int) get_query_var( 'paged' );
		return gwf_act_filter_url( $term ) . ( $paged > 1 ? 'page/' . $paged . '/' : '' );
	}
	return $url;
}
add_filter( 'wpseo_canonical', 'gwf_act_listing_canonical', 15 );

function gwf_act_listing_robots( $robots ) {
	if ( ! gwf_act_is_listing() || ! is_tax() ) {
		return $robots;
	}
	return $GLOBALS['wp_query']->found_posts < 3 ? 'noindex, follow' : $robots;
}
add_filter( 'wpseo_robots', 'gwf_act_listing_robots', 25 );

/** Title y descripción de cada actividad: «<título> | Growing Family» (máx. 60) y la respuesta a «por qué», salvo valores propios en Yoast. */
function gwf_act_single_title( $title ) {
	if ( ! is_singular( 'actividad' ) || get_post_meta( get_queried_object_id(), '_yoast_wpseo_title', true ) ) {
		return $title;
	}
	$name = wp_strip_all_tags( get_the_title( get_queried_object_id() ) );
	return mb_strlen( $name . ' | Growing Family' ) <= 60 ? $name . ' | Growing Family' : $name;
}
add_filter( 'wpseo_title', 'gwf_act_single_title' );

function gwf_act_single_description( $desc ) {
	$post_id = is_singular( 'actividad' ) ? get_queried_object_id() : 0;
	if ( ! $post_id || get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ) {
		return $desc;
	}
	$text = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : gwf_act_meta( $post_id, 'gwf_act_porque' );
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
	return $text ? mb_strimwidth( $text, 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_act_single_description' );
add_filter( 'wpseo_opengraph_desc', 'gwf_act_single_description' );
