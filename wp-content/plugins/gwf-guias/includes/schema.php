<?php
/**
 * Datos estructurados de las guías, sobre el grafo de Yoast.
 *
 * Etapa 4: VideoObject de la guía de problema, coherente con lo visible (miniatura, título del vídeo y
 * quien firma). La etapa 7 añade aquí el Article con reviewedBy y lastReviewed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Segundos → duración ISO 8601 (PT3M5S). */
function gwf_guias_iso_duration( $seconds ) {
	$seconds = (int) $seconds;
	if ( $seconds <= 0 ) {
		return '';
	}
	$h = intdiv( $seconds, 3600 );
	$m = intdiv( $seconds % 3600, 60 );
	$s = $seconds % 60;
	return 'PT' . ( $h ? $h . 'H' : '' ) . ( $m ? $m . 'M' : '' ) . ( $s || ( ! $h && ! $m ) ? $s . 'S' : '' );
}

/** Fecha Ymd del campo → ISO 8601 con la zona horaria del sitio. */
function gwf_guias_iso_date( $ymd ) {
	if ( ! preg_match( '/^\d{8}$/', (string) $ymd ) ) {
		return '';
	}
	$date = date_create_immutable_from_format( 'Ymd H:i:s', $ymd . ' 00:00:00', wp_timezone() );
	return $date ? $date->format( 'c' ) : '';
}

/**
 * Pieza VideoObject de una guía, o null si no tiene vídeo.
 * Google exige name, thumbnailUrl y uploadDate; se añaden description, duration y embedUrl.
 */
function gwf_guias_video_piece( $post_id, $canonical ) {
	$video_id = gwf_guias_youtube_id( get_post_meta( $post_id, 'gwf_video_url', true ) );
	if ( ! $video_id ) {
		return null;
	}
	$title       = trim( (string) get_post_meta( $post_id, 'gwf_video_titulo', true ) );
	$description = trim( (string) get_post_meta( $post_id, 'gwf_respuesta', true ) );
	$upload      = gwf_guias_iso_date( get_post_meta( $post_id, 'gwf_video_fecha', true ) );
	$reviewer    = gwf_guias_reviewer( $post_id );

	$piece = array(
		'@type'        => 'VideoObject',
		'@id'          => $canonical . '#video',
		'name'         => $title ? $title : get_the_title( $post_id ),
		'description'  => $description ? $description : wp_strip_all_tags( get_the_excerpt( $post_id ) ),
		'thumbnailUrl' => array(
			'https://i.ytimg.com/vi/' . $video_id . '/maxresdefault.jpg',
			'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg',
		),
		'uploadDate'   => $upload ? $upload : get_post_time( 'c', false, $post_id ),
		'embedUrl'     => 'https://www.youtube.com/embed/' . $video_id,
		'url'          => 'https://www.youtube.com/watch?v=' . $video_id,
		'inLanguage'   => str_replace( '_', '-', get_locale() ),
		'isPartOf'     => array( '@id' => $canonical ),
	);
	$duration = gwf_guias_iso_duration( get_post_meta( $post_id, 'gwf_video_duracion', true ) );
	if ( $duration ) {
		$piece['duration'] = $duration;
	}
	if ( $reviewer ) {
		// Quien sale en el vídeo es quien firma la revisión (Estrategia v1.6): coherente con el pie del vídeo.
		$piece['actor'] = array( '@type' => 'Person', 'name' => $reviewer['nombre'] );
	}
	return $piece;
}

/** Añade el VideoObject al grafo de Yoast y lo enlaza desde el WebPage. */
function gwf_guias_schema_graph( $graph, $context ) {
	if ( ! is_singular( 'guia' ) || ! is_array( $graph ) ) {
		return $graph;
	}
	$post_id   = get_queried_object_id();
	$canonical = isset( $context->canonical ) && $context->canonical ? $context->canonical : get_permalink( $post_id );
	$video     = gwf_guias_video_piece( $post_id, $canonical );
	if ( ! $video ) {
		return $graph;
	}
	foreach ( $graph as $i => $piece ) {
		$types = (array) ( $piece['@type'] ?? array() );
		if ( in_array( 'WebPage', $types, true ) ) {
			$graph[ $i ]['video'] = array( '@id' => $video['@id'] );
		}
	}
	$graph[] = $video;
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_guias_schema_graph', 10, 2 );
