<?php
/**
 * Datos estructurados de la actividad sobre el grafo de Yoast: Article con autoría y fechas (sin HowTo, como pide el
 * wireframe). Si la revisa alguien, reviewedBy y lastReviewed en el WebPage, con la misma persona que usa gwf-guias.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gwf_act_schema_graph( $graph, $context ) {
	if ( ! is_singular( 'actividad' ) || ! is_array( $graph ) ) {
		return $graph;
	}
	$post_id   = get_queried_object_id();
	$canonical = isset( $context->canonical ) && $context->canonical ? $context->canonical : get_permalink( $post_id );
	$org       = array( '@id' => home_url( '/#organization' ) );
	$updated   = gmdate( 'c', gwf_act_updated_time( $post_id ) );
	$reviewer  = gwf_act_reviewer( $post_id );
	$has_image = false;
	foreach ( $graph as $piece ) {
		$has_image = $has_image || ( $piece['@id'] ?? '' ) === $canonical . '#primaryimage';
	}

	$article = array(
		'@type'            => 'Article',
		'@id'              => $canonical . '#article',
		'isPartOf'         => array( '@id' => $canonical ),
		'mainEntityOfPage' => array( '@id' => $canonical ),
		'headline'         => mb_strimwidth( wp_strip_all_tags( get_the_title( $post_id ) ), 0, 110, '…' ),
		'datePublished'    => get_post_time( 'c', true, $post_id ),
		'dateModified'     => $updated,
		'author'           => array(
			'@type'              => 'Organization',
			'name'               => 'Equipo editorial Growing Family',
			'url'                => home_url( '/' ),
			'parentOrganization' => $org,
		),
		'publisher'        => $org,
		'inLanguage'       => str_replace( '_', '-', get_locale() ),
	);
	if ( $has_image ) {
		$article['image'] = array( '@id' => $canonical . '#primaryimage' );
	}
	$about = gwf_act_terms( $post_id, 'trabaja' );
	if ( $about ) {
		$article['articleSection'] = wp_list_pluck( $about, 'name' );
	}

	$person = ( $reviewer && function_exists( 'gwf_guias_person_piece' ) ) ? gwf_guias_person_piece( $reviewer['id'] ) : null;
	foreach ( $graph as $i => $piece ) {
		if ( in_array( 'WebPage', (array) ( $piece['@type'] ?? array() ), true ) ) {
			$graph[ $i ]['mainEntity']   = array( '@id' => $article['@id'] );
			$graph[ $i ]['dateModified'] = $updated;
			if ( $person ) {
				$graph[ $i ]['reviewedBy']   = array( '@id' => $person['@id'] );
				$graph[ $i ]['lastReviewed'] = gmdate( 'Y-m-d', gwf_act_updated_time( $post_id ) );
			}
		}
	}
	$graph[] = $article;
	if ( $person ) {
		$graph[] = $person;
	}
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_act_schema_graph', 10, 2 );
