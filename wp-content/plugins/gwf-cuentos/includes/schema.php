<?php
/**
 * SEO del cuento (etapa 9). Yoast: title «<título> | Growing Family» (máx. 60) y descripción desde el extracto o
 * «Así empieza», salvo valores propios en Yoast. Una sola URL: #leer, #pagina-n y ?con=max no cambian el canonical
 * (Yoast ya usa el enlace permanente).
 * Datos estructurados sobre el grafo de Yoast: Article con autoría (el equipo editorial, o el redactor si firma con
 * ficha), fechas, imagen, Ana y Max como personajes y tiempo para contarlo; reviewedBy y lastReviewed en el WebPage
 * si requiere firma. Los «me gusta» nunca se marcan como valoración.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gwf_cue_single_title( $title ) {
	if ( ! is_singular( 'cuento' ) || get_post_meta( get_queried_object_id(), '_yoast_wpseo_title', true ) ) {
		return $title;
	}
	$name = wp_strip_all_tags( get_the_title( get_queried_object_id() ) );
	return mb_strlen( $name . ' | Growing Family' ) <= 60 ? $name . ' | Growing Family' : $name;
}
add_filter( 'wpseo_title', 'gwf_cue_single_title' );

/** Resumen del cuento: el extracto o, si no hay, «Así empieza» (con la variante del protagonista por defecto). */
function gwf_cue_summary( $post_id ) {
	$text = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : gwf_cue_plain_variants( (string) get_post_meta( $post_id, 'gwf_cue_asi_empieza', true ), $post_id );
	return trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
}

function gwf_cue_single_description( $desc ) {
	$post_id = is_singular( 'cuento' ) ? get_queried_object_id() : 0;
	if ( ! $post_id || get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ) {
		return $desc;
	}
	$text = gwf_cue_summary( $post_id );
	return $text ? mb_strimwidth( $text, 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_cue_single_description' );
add_filter( 'wpseo_opengraph_desc', 'gwf_cue_single_description' );

function gwf_cue_schema_graph( $graph, $context ) {
	if ( ! is_singular( 'cuento' ) || ! is_array( $graph ) ) {
		return $graph;
	}
	$post_id   = get_queried_object_id();
	$canonical = isset( $context->canonical ) && $context->canonical ? $context->canonical : get_permalink( $post_id );
	$org       = array( '@id' => home_url( '/#organization' ) );
	$updated   = gmdate( 'c', gwf_cue_updated_time( $post_id ) );
	$reviewer  = gwf_cue_reviewer( $post_id );
	$has_image = false;
	foreach ( $graph as $piece ) {
		$has_image = $has_image || ( $piece['@id'] ?? '' ) === $canonical . '#primaryimage';
	}

	$committee = function_exists( 'gwf_g_committee_url' ) ? gwf_g_committee_url() : '';
	$article   = array(
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
			'url'                => $committee ? $committee : home_url( '/' ),
			'parentOrganization' => $org,
		),
		'publisher'        => $org,
		'inLanguage'       => str_replace( '_', '-', get_locale() ),
		// Ana y Max: el mismo cuento se lee con cualquiera de los dos (selector de la página).
		'character'        => array(
			array( '@type' => 'Person', 'name' => 'Ana' ),
			array( '@type' => 'Person', 'name' => 'Max' ),
		),
		'timeRequired'     => 'PT' . gwf_cue_minutes( $post_id ) . 'M',
	);
	if ( $has_image ) {
		$article['image'] = array( '@id' => $canonical . '#primaryimage' );
	}
	$summary = gwf_cue_summary( $post_id );
	if ( $summary ) {
		$article['description'] = mb_strimwidth( $summary, 0, 300, '…' );
	}
	$section = array_merge( gwf_cue_terms( $post_id, 'emocion' ), gwf_cue_terms( $post_id, 'situacion' ) );
	if ( $section ) {
		$article['articleSection'] = wp_list_pluck( $section, 'name' );
	}

	$people    = array();
	$author_id = (int) get_post_field( 'post_author', $post_id );
	if ( function_exists( 'gwf_guias_person_signs' ) && gwf_guias_person_signs( $author_id ) ) {
		$article['author'] = array( '@id' => gwf_guias_person_schema_id( $author_id ) ); // Redactor único: firma la persona.
		$people[]          = $author_id;
	}
	$can_people = function_exists( 'gwf_guias_person_piece' );
	foreach ( $graph as $i => $piece ) {
		if ( in_array( 'WebPage', (array) ( $piece['@type'] ?? array() ), true ) ) {
			$graph[ $i ]['mainEntity']   = array( '@id' => $article['@id'] );
			$graph[ $i ]['dateModified'] = $updated;
			if ( $reviewer && $can_people ) {
				$graph[ $i ]['reviewedBy']   = array( '@id' => gwf_guias_person_schema_id( $reviewer['id'] ) );
				$graph[ $i ]['lastReviewed'] = gmdate( 'Y-m-d', gwf_cue_updated_time( $post_id ) );
				$people[]                    = $reviewer['id'];
			}
		}
	}
	$graph[] = $article;
	if ( $can_people ) {
		foreach ( array_unique( $people ) as $person_id ) {
			$graph[] = gwf_guias_person_piece( $person_id );
		}
	}
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_cue_schema_graph', 10, 2 );
