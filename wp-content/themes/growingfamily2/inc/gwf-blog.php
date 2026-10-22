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

/** URL de la página de autoría del autor actual o del indicado, si es de este sitio. */
function gwf_author_page_url( $author_id = null ) {
	$url = get_the_author_meta( 'user_url', $author_id );
	return gwf_is_own_url( $url ) ? $url : '';
}

/**
 * Firma del autor. Enlaza a su página de autoría si el perfil tiene "Sitio web" (user_url) en growing.family:
 * cuando exista la página del comité/autor, basta con poner su URL en el perfil del usuario.
 */
function gwf_author_byline() {
	$name = esc_html( gwf_public_author_name( get_the_author() ) );
	$url  = gwf_author_page_url();
	if ( $url ) {
		return '<a href="' . esc_url( $url ) . '" rel="author" class="color-barium fw-semibold">' . $name . '</a>';
	}
	return '<b class="fw-semibold">' . $name . '</b>';
}

/** Archivos de autor cerrados: exponían el login del administrador. El enlace de autor va a su página de autoría o al blog. */
function gwf_author_link( $link, $author_id ) {
	$url = gwf_author_page_url( $author_id );
	return $url ? $url : gwf_blog_url();
}
add_filter( 'author_link', 'gwf_author_link', 10, 2 );

function gwf_close_author_archives() {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'gwf_close_author_archives' );

/** Índice: etiquetas fuera (no se usan) y archivos de categoría vacíos fuera; las categorías con notas sí se indexan. */
function gwf_blog_robots( $robots ) {
	if ( is_tag() || ( is_category() && ! have_posts() ) ) {
		return 'noindex, follow';
	}
	return $robots;
}
add_filter( 'wpseo_robots', 'gwf_blog_robots' );

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

/** Imprime el puente al final del contenido de una nota (nada si la entrada no lo pide). */
function gwf_post_bridge() {
	$guia_url   = get_post_meta( get_the_ID(), 'gwf_guia_url', true );
	$guia_texto = get_post_meta( get_the_ID(), 'gwf_guia_texto', true );
	$metodo     = (bool) get_post_meta( get_the_ID(), 'gwf_puente_metodo', true );

	if ( ! $guia_url && ! $metodo ) {
		return;
	}
	echo '<aside class="gwf-puente resumen-post p-4 border-radius-24 box-shadow-820 mt-5">';
	if ( $guia_url ) {
		echo '<p class="font-size-20 color-barium">Si quieres profundizar: <a href="' . esc_url( $guia_url ) . '">' . esc_html( $guia_texto ? $guia_texto : 'lee la guía completa' ) . '</a>.</p>';
	}
	if ( $metodo ) {
		echo '<p class="font-size-20 color-barium mb-0">Esto es lo que trabaja <a href="' . esc_url( home_url( '/acerca-de/el-metodo/' ) ) . '">el método Growing Family</a>, diez minutos cada noche.</p>';
	}
	echo '</aside>';
}

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

function gwf_webp_attachment_src( $image ) {
	if ( is_array( $image ) && ! empty( $image[0] ) && ! is_admin() ) {
		$image[0] = gwf_webp_url( $image[0] );
	}
	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'gwf_webp_attachment_src' );

function gwf_webp_srcset( $sources ) {
	if ( is_admin() || ! is_array( $sources ) ) {
		return $sources;
	}
	foreach ( $sources as $w => $source ) {
		$sources[ $w ]['url'] = gwf_webp_url( $source['url'] );
	}
	return $sources;
}
add_filter( 'wp_calculate_image_srcset', 'gwf_webp_srcset' );

function gwf_webp_content_img( $tag ) {
	return preg_replace_callback(
		'/(?<=src=["\']|srcset=["\']|, |,)(https?:[^"\'\s,]+\.(?:jpe?g|png))(?=[\s"\',])/i',
		function ( $m ) {
			return gwf_webp_url( $m[1] );
		},
		$tag
	);
}
add_filter( 'wp_content_img_tag', 'gwf_webp_content_img' );
