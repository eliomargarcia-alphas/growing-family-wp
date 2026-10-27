<?php
/**
 * Datos estructurados de las guías, sobre el grafo de Yoast.
 *
 * Etapa 4: VideoObject de la guía de problema, coherente con lo visible (miniatura, título del vídeo y
 * quien firma). Etapa 7: Article en guías y pilares (autor: el equipo editorial, como organización) y la
 * revisión experta en el WebPage (reviewedBy y lastReviewed, que en schema.org son propiedades de WebPage),
 * con la persona revisora como pieza propia. Ni FAQPage ni valoraciones: los «me gusta» no se marcan.
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
		$piece['actor'] = array( '@id' => gwf_guias_person_schema_id( $reviewer['id'] ) );
	}
	return $piece;
}

/** @id de una persona del equipo (el mismo en su ficha y en todo lo que revisa o firma). */
function gwf_guias_person_schema_id( $user_id ) {
	return home_url( '/#/schema/person/equipo-' . (int) $user_id );
}

/**
 * Pieza Person de una persona del equipo: nombre, cargo, foto, experiencia, formación, temas, enlaces
 * verificables (sameAs), su ficha y, si lo es, que forma parte del comité editorial.
 */
function gwf_guias_person_piece( $user_id ) {
	$p = gwf_guias_person( $user_id );
	if ( ! $p ) {
		return null;
	}
	$piece = array(
		'@type' => 'Person',
		'@id'   => gwf_guias_person_schema_id( $user_id ),
		'name'  => $p['nombre'],
	);
	if ( $p['cargo'] ) {
		$piece['jobTitle'] = $p['cargo'];
	}
	if ( $p['experiencia'] ) {
		$piece['description'] = wp_strip_all_tags( $p['experiencia'] );
	}
	if ( $p['foto'] ) {
		$piece['image'] = $p['foto'];
	}
	if ( $p['url'] ) {
		$piece['url'] = $p['url'];
	}
	if ( $p['enlaces'] ) {
		$piece['sameAs'] = wp_list_pluck( $p['enlaces'], 'url' );
	}
	if ( $p['temas'] ) {
		$piece['knowsAbout'] = wp_list_pluck( $p['temas'], 'name' );
	}
	foreach ( $p['formacion'] as $f ) {
		$credential = array( '@type' => 'EducationalOccupationalCredential', 'name' => $f['titulo'] );
		if ( $f['institucion'] ) {
			$credential['recognizedBy'] = array( '@type' => 'Organization', 'name' => $f['institucion'] );
		}
		$piece['hasCredential'][] = $credential;
	}
	if ( $p['comite'] ) {
		$piece['memberOf'] = array( '@type' => 'Organization', 'name' => 'Comité editorial de Growing Family', 'parentOrganization' => array( '@id' => home_url( '/#organization' ) ) );
	}
	return $piece;
}

/** Pieza Article de una guía o un pilar: titular, fechas, imagen, autor (equipo editorial, o la persona si firma) y editor. */
function gwf_guias_article_piece( $post_id, $canonical, $has_image ) {
	$org   = array( '@id' => home_url( '/#organization' ) );
	$piece = array(
		'@type'            => 'Article',
		'@id'              => $canonical . '#article',
		'isPartOf'         => array( '@id' => $canonical ),
		'mainEntityOfPage' => array( '@id' => $canonical ),
		'headline'         => mb_strimwidth( wp_strip_all_tags( get_the_title( $post_id ) ), 0, 110, '…' ),
		'datePublished'    => get_post_time( 'c', true, $post_id ),
		'dateModified'     => get_post_modified_time( 'c', true, $post_id ),
		'author'           => array(
			'@type'              => 'Organization',
			'name'               => 'Equipo editorial Growing Family',
			'url'                => home_url( '/' ),
			'parentOrganization' => $org,
		),
		'publisher'        => $org,
		'inLanguage'       => str_replace( '_', '-', get_locale() ),
	);
	$review = gwf_guias_iso_date( get_post_meta( $post_id, 'gwf_fecha_revision', true ) );
	if ( $review ) {
		$piece['dateModified'] = $review; // La fecha visible («Actualizado el») es la de revisión.
	}
	if ( $has_image ) {
		$piece['image'] = array( '@id' => $canonical . '#primaryimage' );
	}
	$section = get_the_terms( $post_id, 'guia' === get_post_type( $post_id ) ? 'tema' : 'gran_tema' );
	if ( $section && ! is_wp_error( $section ) ) {
		$piece['articleSection'] = wp_list_pluck( $section, 'name' );
	}
	$description = get_post_meta( $post_id, 'guia' === get_post_type( $post_id ) ? 'gwf_respuesta' : 'gwf_definicion', true );
	if ( $description ) {
		$piece['description'] = mb_strimwidth( wp_strip_all_tags( $description ), 0, 300, '…' );
	}
	return $piece;
}

/** Añade al grafo de Yoast el Article, la persona revisora y el VideoObject, y los enlaza desde el WebPage. */
function gwf_guias_schema_graph( $graph, $context ) {
	if ( ! is_singular( GWF_GUIAS_TYPES ) || ! is_array( $graph ) ) {
		return $graph;
	}
	$post_id   = get_queried_object_id();
	$canonical = isset( $context->canonical ) && $context->canonical ? $context->canonical : get_permalink( $post_id );
	$reviewer  = gwf_guias_reviewer( $post_id );
	$review    = gwf_guias_iso_date( get_post_meta( $post_id, 'gwf_fecha_revision', true ) );
	$video     = 'guia' === get_post_type( $post_id ) ? gwf_guias_video_piece( $post_id, $canonical ) : null;

	$has_image = false;
	foreach ( $graph as $piece ) {
		if ( ( $piece['@id'] ?? '' ) === $canonical . '#primaryimage' ) {
			$has_image = true;
		}
	}
	$article = gwf_guias_article_piece( $post_id, $canonical, $has_image );

	foreach ( $graph as $i => $piece ) {
		$types = (array) ( $piece['@type'] ?? array() );
		if ( ! in_array( 'WebPage', $types, true ) ) {
			continue;
		}
		$graph[ $i ]['mainEntity'] = array( '@id' => $article['@id'] );
		if ( $reviewer ) {
			$graph[ $i ]['reviewedBy'] = array( '@id' => gwf_guias_person_schema_id( $reviewer['id'] ) );
		}
		if ( $review ) {
			$graph[ $i ]['lastReviewed'] = substr( $review, 0, 10 );
			$graph[ $i ]['dateModified'] = $review; // Igual que el Article y que la fecha visible.
		}
		if ( $video ) {
			$graph[ $i ]['video'] = array( '@id' => $video['@id'] );
		}
	}
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$people    = $reviewer ? array( $reviewer['id'] ) : array();
	if ( gwf_guias_person_signs( $author_id ) ) {
		$article['author'] = array( '@id' => gwf_guias_person_schema_id( $author_id ) ); // Redactor único: firma la persona.
		$people[]          = $author_id;
	}
	$graph[] = $article;
	foreach ( array_unique( $people ) as $person_id ) {
		$graph[] = gwf_guias_person_piece( $person_id );
	}
	if ( $video ) {
		$graph[] = $video;
	}
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_guias_schema_graph', 10, 2 );

/**
 * Ficha /equipo/<persona>/: la ProfilePage de Yoast pasa a describir a la persona con la pieza propia.
 * Entradas del blog: firma la persona si es redactora con ficha pública; si no, la organización.
 */
function gwf_guias_people_schema_graph( $graph ) {
	if ( ! is_array( $graph ) ) {
		return $graph;
	}
	$person_id = gwf_guias_current_person();
	if ( ! $person_id && is_singular( 'post' ) ) {
		$author_id = (int) get_post_field( 'post_author', get_queried_object_id() );
		$person_id = gwf_guias_person_signs( $author_id ) ? $author_id : 0;
		$author    = $person_id ? array( '@id' => gwf_guias_person_schema_id( $person_id ) ) : array( '@id' => home_url( '/#organization' ) );
	} elseif ( ! $person_id ) {
		return $graph;
	}
	$out = array();
	foreach ( $graph as $piece ) {
		$types = (array) ( $piece['@type'] ?? array() );
		if ( in_array( 'Person', $types, true ) ) {
			continue; // La persona de Yoast (el usuario técnico) se sustituye por la nuestra.
		}
		if ( isset( $author ) && in_array( 'Article', $types, true ) ) {
			$piece['author'] = $author;
		}
		if ( ! isset( $author ) && in_array( 'ProfilePage', $types, true ) ) {
			$piece['mainEntity'] = array( '@id' => gwf_guias_person_schema_id( $person_id ) );
			unset( $piece['about'] );
		}
		$out[] = $piece;
	}
	if ( $person_id ) {
		$out[] = gwf_guias_person_piece( $person_id );
	}
	if ( ! isset( $author ) ) {
		$out = gwf_guias_profile_page_pieces( $out, $person_id );
	}
	return $out;
}

/**
 * ProfilePage y migas de la ficha. Yoast no las genera para quien no tiene entradas del blog (una revisora,
 * por ejemplo), así que se añaden aquí si faltan.
 */
function gwf_guias_profile_page_pieces( $graph, $person_id ) {
	foreach ( $graph as $piece ) {
		if ( in_array( 'ProfilePage', (array) ( $piece['@type'] ?? array() ), true ) ) {
			return $graph;
		}
	}
	$url    = gwf_guias_person_url( $person_id );
	$name   = get_userdata( $person_id )->display_name;
	$crumbs = array();
	foreach ( gwf_guias_person_breadcrumbs( array( array( 'text' => 'Inicio', 'url' => home_url( '/' ) ) ) ) as $i => $crumb ) {
		$item = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['text'] );
		if ( ! empty( $crumb['url'] ) ) {
			$item['item'] = $crumb['url'];
		}
		$crumbs[] = $item;
	}
	$graph[] = array(
		'@type'      => 'ProfilePage',
		'@id'        => $url,
		'url'        => $url,
		'name'       => $name . ' | Growing Family',
		'isPartOf'   => array( '@id' => home_url( '/#website' ) ),
		'mainEntity' => array( '@id' => gwf_guias_person_schema_id( $person_id ) ),
		'breadcrumb' => array( '@id' => $url . '#breadcrumb' ),
		'inLanguage' => str_replace( '_', '-', get_locale() ),
	);
	$graph[] = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $url . '#breadcrumb',
		'itemListElement' => $crumbs,
	);
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_guias_people_schema_graph', 11 );
