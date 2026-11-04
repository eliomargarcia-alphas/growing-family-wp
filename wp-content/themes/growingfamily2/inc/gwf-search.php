<?php
/**
 * Búsqueda del sitio (search.php): la búsqueda normal de WordPress en cuentos, guías, pilares, actividades y blog,
 * con filtro por tipo (?tipo=cuentos|guias|actividades|blog) y las tarjetas de los listados.
 *
 * Ajax Search Lite sustituía los resultados de la búsqueda principal por los suyos (vacíos): aquí se le quita esa
 * sustitución solo en la búsqueda principal; el plugin sigue instalado.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

const GWF_SEARCH_PER_PAGE = 12;
const GWF_SEARCH_HIDDEN_CATS = array( 41, 32, 6, 42 ); // Las mismas categorías que el blog no muestra.

/** Tipos de la búsqueda: filtro → tipos de contenido y nombres (solo los que existen: los plugins pueden no estar activos). */
function gwf_search_types() {
	$types = array(
		'cuentos'     => array( 'label' => 'Cuentos', 'types' => array( 'cuento' ) ),
		'guias'       => array( 'label' => 'Guías', 'types' => array( 'guia', 'desarrollo' ) ),
		'actividades' => array( 'label' => 'Actividades', 'types' => array( 'actividad' ) ),
		'blog'        => array( 'label' => 'Blog', 'types' => array( 'post' ) ),
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
