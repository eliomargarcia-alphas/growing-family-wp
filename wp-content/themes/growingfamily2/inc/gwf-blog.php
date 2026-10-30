<?php
/**
 * Blog (capa 5, notas de padre a padre): autoría, puente, tiempo de lectura, índice e imágenes WebP.
 * Preparado antes de publicar ninguna entrada; ver "Estrategia SEO y GEO", capa 5.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Nombre que se muestra cuando el autor no tiene un nombre público (p. ej. el usuario técnico con email). */
const GWF_FALLBACK_AUTHOR = 'Equipo Growing Family';

/** URL de la página de entradas; mientras esté en borrador, la ruta prevista. */
function gwf_blog_url() {
	$page_id = (int) get_option( 'page_for_posts' );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}
	return home_url( '/blog/' );
}

/** Minutos de lectura (200 palabras por minuto, mínimo 1). */
function gwf_reading_time( $post = null ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/** Nunca mostrar un email como nombre de autor (en la firma, en Yoast ni en ningún otro sitio). */
function gwf_public_author_name( $name ) {
	return ( '' === trim( (string) $name ) || false !== strpos( $name, '@' ) ) ? GWF_FALLBACK_AUTHOR : $name;
}
add_filter( 'get_the_author_display_name', 'gwf_public_author_name' );
add_filter( 'the_author', 'gwf_public_author_name' );
add_filter( 'wpseo_meta_author', 'gwf_public_author_name' );

/** Lo mismo en el schema de Yoast (Person y su imagen). */
function gwf_schema_person_name( $data ) {
	if ( isset( $data['name'] ) ) {
		$data['name'] = gwf_public_author_name( $data['name'] );
	}
	if ( isset( $data['image']['caption'] ) ) {
		$data['image']['caption'] = gwf_public_author_name( $data['image']['caption'] );
	}
	if ( isset( $data['sameAs'] ) && is_array( $data['sameAs'] ) ) {
		$data['sameAs'] = array_values( array_filter( $data['sameAs'], 'gwf_is_own_url' ) );
	}
	if ( isset( $data['url'] ) && ! gwf_is_own_url( $data['url'] ) ) {
		$data['url'] = gwf_blog_url();
	}
	return $data;
}
add_filter( 'wpseo_schema_person', 'gwf_schema_person_name' );

function gwf_schema_article_author( $data ) {
	if ( isset( $data['author']['name'] ) ) {
		$data['author']['name'] = gwf_public_author_name( $data['author']['name'] );
	}
	return $data;
}
add_filter( 'wpseo_schema_article', 'gwf_schema_article_author' );

/** Solo URLs de este sitio cuentan como página de autoría (el perfil técnico apunta al dominio antiguo). */
function gwf_is_own_url( $url ) {
	return $url && wp_parse_url( $url, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST );
}

/** URL de la página de autoría del autor actual o del indicado: su ficha en /equipo/ (plugin gwf-guias) o, si no, su «Sitio web» de este sitio. */
function gwf_author_page_url( $author_id = null ) {
	$author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
	if ( function_exists( 'gwf_guias_person_url' ) && gwf_guias_person_url( $author_id ) ) {
		return gwf_guias_person_url( $author_id );
	}
	$url = get_the_author_meta( 'user_url', $author_id );
	return gwf_is_own_url( $url ) ? $url : '';
}

/** ¿La entrada está firmada? Solo si su autor es un redactor con ficha pública (plugin gwf-guias); si no, no se muestra firma. */
function gwf_author_signs( $author_id = null ) {
	$author_id = $author_id ? $author_id : get_the_author_meta( 'ID' );
	return function_exists( 'gwf_guias_person_signs' ) && gwf_guias_person_signs( $author_id );
}

/** Firma: el redactor con ficha pública, enlazado a ella; si no, el equipo editorial, enlazado al comité. */
function gwf_author_byline() {
	if ( ! gwf_author_signs() ) {
		// Sin redactor con ficha: firma el equipo editorial, enlazado a la página del comité (si existe).
		$committee = function_exists( 'gwf_g_committee_url' ) ? gwf_g_committee_url() : '';
		return $committee
			? '<a href="' . esc_url( $committee ) . '">Equipo editorial Growing Family</a>'
			: 'Equipo editorial Growing Family';
	}
	$name = esc_html( gwf_public_author_name( get_the_author() ) );
	$url  = gwf_author_page_url();
	if ( $url ) {
		return '<a href="' . esc_url( $url ) . '" rel="author">' . $name . '</a>';
	}
	return $name;
}

/** Archivos de autor cerrados salvo fichas públicas: exponían el login del administrador. El enlace de autor va a su ficha o al blog. */
function gwf_author_link( $link, $author_id ) {
	$url = gwf_author_page_url( $author_id );
	return $url ? $url : gwf_blog_url();
}
add_filter( 'author_link', 'gwf_author_link', 10, 2 );

function gwf_close_author_archives() {
	// Las fichas públicas del equipo (/equipo/<persona>/, plugin gwf-guias) sí se ven.
	if ( is_author() && ! ( function_exists( 'gwf_guias_current_person' ) && gwf_guias_current_person() ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'gwf_close_author_archives' );

/** ¿Estamos en /blog/ mientras la página de entradas sigue en borrador? */
function gwf_is_unpublished_blog_index() {
	$page_id = (int) get_option( 'page_for_posts' );
	return is_home() && $page_id && 'publish' !== get_post_status( $page_id );
}

/**
 * Índice: etiquetas fuera (no se usan), archivos de categoría vacíos fuera y /blog/ fuera mientras
 * la página Blog esté en borrador. Las categorías con notas sí se indexan.
 */
function gwf_blog_robots( $robots ) {
	if ( is_tag() || ( is_category() && ! have_posts() ) || gwf_is_unpublished_blog_index() ) {
		return 'noindex, follow';
	}
	return $robots;
}
add_filter( 'wpseo_robots', 'gwf_blog_robots' );

/** Con la página en borrador Yoast usaría ?page_id=133; la URL real es /blog/. Al publicarla, Yoast ya la pone sola. */
function gwf_blog_index_url( $url ) {
	return gwf_is_unpublished_blog_index() ? home_url( '/blog/' ) : $url;
}
add_filter( 'wpseo_canonical', 'gwf_blog_index_url' );
add_filter( 'wpseo_opengraph_url', 'gwf_blog_index_url' );

/** En las migas, «Blog» va a /blog/ aunque la página siga en borrador (Yoast pondría ?page_id=133). */
function gwf_blog_breadcrumb_url( $links ) {
	$page_id = (int) get_option( 'page_for_posts' );
	foreach ( $links as $i => $link ) {
		if ( $page_id && isset( $link['id'] ) && (int) $link['id'] === $page_id ) {
			$links[ $i ]['url'] = gwf_blog_url();
		}
	}
	return $links;
}
add_filter( 'wpseo_breadcrumb_links', 'gwf_blog_breadcrumb_url' );

/** Los vídeos son parte del blog: Inicio › Blog › Vídeos (la página vive en /videos/ porque /blog/<slug>/ son entradas). */
function gwf_videos_breadcrumb( $links ) {
	if ( is_page_template( 'templates/template-videos.php' ) && count( $links ) >= 2 ) {
		array_splice( $links, 1, 0, array( array( 'text' => 'Blog', 'url' => gwf_blog_url() ) ) );
	}
	return $links;
}
add_filter( 'wpseo_breadcrumb_links', 'gwf_videos_breadcrumb', 11 );

/**
 * Campos por entrada para el puente (la estrategia: "el puente al método es un enlace al final,
 * y solo cuando la nota toca algo que el ritual trabaja"; "cada nota enlaza a su guía en cuanto exista").
 */
function gwf_register_post_bridge_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_post_bridge',
			'title'    => 'Puente al final de la nota',
			'position' => 'side',
			'fields'   => array(
				array(
					'key'          => 'field_gwf_guia_url',
					'label'        => 'Guía relacionada (URL)',
					'name'         => 'gwf_guia_url',
					'type'         => 'url',
					'instructions' => 'Guía de la capa 2 o 3 a la que pertenece la nota. Vacío si aún no existe.',
				),
				array(
					'key'   => 'field_gwf_guia_texto',
					'label' => 'Texto del enlace a la guía',
					'name'  => 'gwf_guia_texto',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_gwf_puente_metodo',
					'label'        => 'Mostrar puente al método',
					'name'         => 'gwf_puente_metodo',
					'type'         => 'true_false',
					'ui'           => 1,
					'instructions' => 'Solo si la nota toca algo que el ritual trabaja. Una nota no vende.',
				),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
		)
	);
}
add_action( 'acf/init', 'gwf_register_post_bridge_fields' );

/**
 * Contenido de la entrada con un id en cada H2 (si no lo tiene) y la lista [ancla => texto] para el índice
 * «En este artículo» (panel lateral y desplegable de móvil), como «En esta guía» en las guías.
 *
 * @return array{0:string,1:array}
 */
function gwf_post_content_with_toc() {
	$html = apply_filters( 'the_content', get_the_content() );
	$toc  = array();
	$html = preg_replace_callback(
		'#<h2([^>]*)>(.*?)</h2>#is',
		function ( $m ) use ( &$toc ) {
			$text = trim( wp_strip_all_tags( $m[2] ) );
			if ( '' === $text ) {
				return $m[0];
			}
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $m[1], $id ) ) {
				$anchor = $id[1];
				$attrs  = $m[1];
			} else {
				$anchor = sanitize_title( $text );
				$base   = $anchor;
				for ( $n = 2; isset( $toc[ $anchor ] ); $n++ ) {
					$anchor = $base . '-' . $n;
				}
				$attrs = $m[1] . ' id="' . esc_attr( $anchor ) . '"';
			}
			$toc[ $anchor ] = $text;
			return '<h2' . $attrs . '>' . $m[2] . '</h2>';
		},
		$html
	);
	return array( $html, $toc );
}

/** Imprime el puente al final del contenido de una nota (nada si la entrada no lo pide). */
function gwf_post_bridge() {
	$guia_url   = get_post_meta( get_the_ID(), 'gwf_guia_url', true );
	$guia_texto = get_post_meta( get_the_ID(), 'gwf_guia_texto', true );
	$metodo     = (bool) get_post_meta( get_the_ID(), 'gwf_puente_metodo', true );

	if ( ! $guia_url && ! $metodo ) {
		return;
	}
	// Mismo bloque que el puente de las guías y las actividades (.gwf-g-bridge, que también mide GA4).
	echo '<aside class="gwf-g-block gwf-g-bridge gwf-puente">';
	if ( $guia_url ) {
		echo '<p>Si quieres profundizar: <a href="' . esc_url( $guia_url ) . '">' . esc_html( $guia_texto ? $guia_texto : 'lee la guía completa' ) . '</a>.</p>';
	}
	if ( $metodo ) {
		echo '<p>Esto es lo que trabaja el método Growing Family, diez minutos cada noche.</p>';
		echo '<a class="gwf-g-button" href="' . esc_url( home_url( '/acerca-de/el-metodo/' ) ) . '">Conoce el método</a>';
	}
	echo '</aside>';
}

/**
 * Todas las entradas usan single-post.php (diseño de guías y actividades), aunque tengan asignada una plantilla
 * antigua (Tipo carta, Cómo me siento, Etapas del desarrollo, Tipos de familia, Artículos fundamentales…).
 */
function gwf_post_single_template( $template ) {
	if ( is_singular( 'post' ) ) {
		$single = locate_template( 'single-post.php' );
		return $single ? $single : $template;
	}
	return $template;
}
add_filter( 'template_include', 'gwf_post_single_template', 20 );

/*
 * Imágenes WebP de las entradas: si junto a una imagen de uploads existe "<archivo>.webp"
 * (generado en el servidor con cwebp), se sirve esa versión. Sin el .webp, todo queda igual.
 */
function gwf_webp_url( $url ) {
	static $cache = array();
	if ( isset( $cache[ $url ] ) ) {
		return $cache[ $url ];
	}
	$uploads = wp_get_upload_dir();
	$new     = $url;
	if ( preg_match( '/\.(jpe?g|png)$/i', $url ) && 0 === strpos( $url, $uploads['baseurl'] ) ) {
		$file = $uploads['basedir'] . substr( $url, strlen( $uploads['baseurl'] ) ) . '.webp';
		if ( file_exists( $file ) ) {
			$new = $url . '.webp';
		}
	}
	$cache[ $url ] = $new;
	return $new;
}

// Solo en el HTML de la página (imágenes del contenido y destacadas). Open Graph y schema siguen en JPG/PNG,
// que todas las redes sociales aceptan.
function gwf_webp_content_img( $tag ) {
	if ( is_admin() ) {
		return $tag;
	}
	return preg_replace_callback(
		'/(?<=src=["\']|srcset=["\']|, |,)(https?:[^"\'\s,]+\.(?:jpe?g|png))(?=[\s"\',])/i',
		function ( $m ) {
			return gwf_webp_url( $m[1] );
		},
		$tag
	);
}
add_filter( 'wp_content_img_tag', 'gwf_webp_content_img' );
add_filter( 'wp_get_attachment_image', 'gwf_webp_content_img' );

/**
 * Listados por término (tema, edad, emoción, «qué trabaja», categoría, etiqueta…): Yoast solo pone Inicio › Término.
 * Se añade la sección: Inicio › Cuentos › Celos, Inicio › Guías de problema › Rabietas, Inicio › Blog › Crianza.
 * La edad es común a varios tipos, así que la sección sale del tipo del listado (/cuentos/edad/x/ → Cuentos).
 */
function gwf_listing_breadcrumb_section( $links ) {
	if ( ! ( is_tax() || is_category() || is_tag() ) || count( $links ) < 2 ) {
		return $links;
	}
	$type = get_query_var( 'post_type' );
	$type = is_array( $type ) ? reset( $type ) : $type;
	if ( is_category() || is_tag() ) {
		$section = array( 'text' => 'Blog', 'url' => gwf_blog_url() );
	} elseif ( $type && post_type_exists( $type ) && get_post_type_archive_link( $type ) ) {
		$titles  = (array) get_option( 'wpseo_titles', array() );
		$text    = ! empty( $titles[ 'bctitle-ptarchive-' . $type ] ) ? $titles[ 'bctitle-ptarchive-' . $type ] : get_post_type_object( $type )->labels->name;
		$section = array( 'text' => $text, 'url' => get_post_type_archive_link( $type ) );
	} else {
		return $links;
	}
	foreach ( $links as $link ) {
		if ( isset( $link['url'] ) && untrailingslashit( $link['url'] ) === untrailingslashit( $section['url'] ) ) {
			return $links; // Ya está (p. ej. si se activa en Yoast «mostrar el archivo del tipo» en la taxonomía).
		}
	}
	array_splice( $links, 1, 0, array( $section ) );
	return $links;
}
add_filter( 'wpseo_breadcrumb_links', 'gwf_listing_breadcrumb_section', 12 );
