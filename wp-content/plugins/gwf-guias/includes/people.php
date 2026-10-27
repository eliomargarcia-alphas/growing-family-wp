<?php
/**
 * Fichas de personas del equipo: /equipo/<persona>/ (wireframe «plantilla-revisor»).
 *
 * Una misma ficha sirve para quien revisa (revisor experto) y para quien redacta con su nombre (redactor único
 * fuera del comité). La persona es un usuario de WordPress; solo tienen página los que marcan «Ficha pública»
 * (el resto de archivos de autor sigue redirigiendo a la portada, ver inc/gwf-blog.php del tema).
 * Sin ACF PRO: formación y enlaces son texto «a | b | c» por líneas, como las listas de las guías.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_GUIAS_TEAM_BASE     = 'equipo';
const GWF_GUIAS_TEAM_PER_PAGE = 10;

/** Roles de la ficha: lo que hace la persona. */
const GWF_GUIAS_PERSON_ROLES = array(
	'revisa'  => 'Revisa',
	'redacta' => 'Redacta',
	'ambos'   => 'Revisa y redacta',
);

/** Campos de la ficha, en el perfil de cualquier usuario. */
function gwf_guias_register_person_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_revisor',
			'title'    => 'Ficha pública del equipo (/equipo/)',
			'location' => array( array( array( 'param' => 'user_form', 'operator' => '==', 'value' => 'all' ) ) ),
			'fields'   => array(
				gwf_guias_field( 'gwf_ficha_publica', 'Ficha pública', 'true_false', array( 'ui' => 1, 'instructions' => 'Con la ficha pública, la persona tiene página en /equipo/ y su nombre enlaza a ella desde lo que revisa o firma.' ) ),
				gwf_guias_field( 'gwf_rol', 'Rol', 'select', array( 'choices' => GWF_GUIAS_PERSON_ROLES, 'default_value' => 'revisa', 'instructions' => '«Redacta» firma con su nombre las guías o entradas en las que figure como autor.' ) ),
				gwf_guias_field( 'gwf_comite', 'Miembro del comité editorial', 'true_false', array( 'ui' => 1 ) ),
				gwf_guias_field( 'gwf_cargo', 'Cargo visible', 'text', array( 'instructions' => 'Lo que se lee junto al nombre, p. ej. «Revisora clínica del comité editorial · Psicóloga infantil».' ) ),
				gwf_guias_field( 'gwf_foto', 'Foto', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'mime_types' => 'jpg,jpeg,png,webp' ) ),
				gwf_guias_field( 'gwf_formacion', 'Formación', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Una línea por título: Título | Institución | Año' ) ),
				gwf_guias_field( 'gwf_experiencia', 'Experiencia', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop' ) ),
				gwf_guias_field( 'gwf_temas', 'Temas que domina (guías de problema)', 'taxonomy', array( 'taxonomy' => 'tema', 'field_type' => 'multi_select', 'return_format' => 'id', 'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0, 'allow_null' => 1 ) ),
				gwf_guias_field( 'gwf_grandes_temas', 'Temas que domina (desarrollo)', 'taxonomy', array( 'taxonomy' => 'gran_tema', 'field_type' => 'multi_select', 'return_format' => 'id', 'add_term' => 0, 'save_terms' => 0, 'load_terms' => 0, 'allow_null' => 1 ) ),
				gwf_guias_field( 'gwf_enlaces', 'Enlaces verificables', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Una línea por enlace: Texto | URL (LinkedIn, colegio profesional con n.º de registro…)' ) ),
			),
		)
	);
}
add_action( 'acf/init', 'gwf_guias_register_person_fields' );

/** /equipo/<persona>/ en lugar de /author/<login>/ (sin el /blog/ de las entradas delante). */
function gwf_guias_team_base() {
	$GLOBALS['wp_rewrite']->author_base      = GWF_GUIAS_TEAM_BASE;
	$GLOBALS['wp_rewrite']->author_structure = '/' . GWF_GUIAS_TEAM_BASE . '/%author%';
}
add_action( 'init', 'gwf_guias_team_base' );

function gwf_guias_person_is_public( $user_id ) {
	return $user_id && (bool) get_user_meta( (int) $user_id, 'gwf_ficha_publica', true );
}

/** URL de la ficha, o '' si no es pública. Se arma a mano: el tema filtra author_link. */
function gwf_guias_person_url( $user_id ) {
	$user = gwf_guias_person_is_public( $user_id ) ? get_userdata( (int) $user_id ) : false;
	return $user ? home_url( '/' . GWF_GUIAS_TEAM_BASE . '/' . $user->user_nicename . '/' ) : '';
}

function gwf_guias_person_role( $user_id ) {
	$role = (string) get_user_meta( (int) $user_id, 'gwf_rol', true );
	return isset( GWF_GUIAS_PERSON_ROLES[ $role ] ) ? $role : 'revisa';
}

/** ¿Firma con su nombre lo que tiene como autor? (ficha pública y rol que redacta) */
function gwf_guias_person_signs( $user_id ) {
	return gwf_guias_person_is_public( $user_id ) && 'revisa' !== gwf_guias_person_role( $user_id );
}

/** Términos guardados en un campo de taxonomía del usuario. */
function gwf_guias_person_terms( $user_id, $field, $taxonomy ) {
	$ids = array_filter( array_map( 'intval', (array) get_user_meta( (int) $user_id, $field, true ) ) );
	if ( ! $ids ) {
		return array();
	}
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'include' => $ids, 'hide_empty' => false ) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/** Todo lo que pinta la ficha. */
function gwf_guias_person( $user_id ) {
	$user = get_userdata( (int) $user_id );
	if ( ! $user ) {
		return null;
	}
	$meta    = function ( $key ) use ( $user_id ) {
		return (string) get_user_meta( (int) $user_id, $key, true );
	};
	$foto_id = (int) $meta( 'gwf_foto' );
	$enlaces = array_values(
		array_filter(
			gwf_guias_parse_lines( $meta( 'gwf_enlaces' ), array( 'texto', 'url' ) ),
			function ( $l ) {
				return wp_http_validate_url( $l['url'] );
			}
		)
	);
	return array(
		'id'          => (int) $user_id,
		'nombre'      => $user->display_name,
		'cargo'       => $meta( 'gwf_cargo' ),
		'rol'         => gwf_guias_person_role( $user_id ),
		'comite'      => (bool) $meta( 'gwf_comite' ),
		'foto'        => $foto_id ? (string) wp_get_attachment_image_url( $foto_id, 'medium' ) : '',
		'formacion'   => gwf_guias_parse_lines( $meta( 'gwf_formacion' ), array( 'titulo', 'institucion', 'anio' ) ),
		'experiencia' => $meta( 'gwf_experiencia' ),
		'temas'       => array_merge( gwf_guias_person_terms( $user_id, 'gwf_temas', 'tema' ), gwf_guias_person_terms( $user_id, 'gwf_grandes_temas', 'gran_tema' ) ),
		'enlaces'     => $enlaces,
		'url'         => gwf_guias_person_url( $user_id ),
	);
}

/** Guías y pilares que revisa y lo que firma (guías, pilares y entradas del blog), según su rol. */
function gwf_guias_person_content_ids( $user_id ) {
	$role = gwf_guias_person_role( $user_id );
	$base = array( 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true, 'suppress_filters' => true );
	$ids  = array();
	if ( 'redacta' !== $role ) {
		$ids = get_posts( $base + array( 'post_type' => GWF_GUIAS_TYPES, 'meta_key' => 'gwf_revisado_por', 'meta_value' => (string) $user_id ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	if ( 'revisa' !== $role ) {
		$ids = array_merge( $ids, get_posts( $base + array( 'post_type' => array_merge( GWF_GUIAS_TYPES, array( 'post' ) ), 'author' => (int) $user_id ) ) );
	}
	return array_values( array_unique( array_map( 'intval', $ids ) ) );
}

/** Persona de la ficha que se está viendo, o 0. */
function gwf_guias_current_person() {
	if ( ! is_author() ) {
		return 0;
	}
	$user = get_queried_object();
	return ( $user instanceof WP_User && gwf_guias_person_is_public( $user->ID ) ) ? $user->ID : 0;
}

/**
 * La consulta principal de la ficha lista su contenido (revisado o firmado, de cualquier tipo), 10 por página.
 * El objeto consultado se fija antes de quitar el filtro de autor, para que la plantilla y Yoast sigan viendo a la persona.
 */
function gwf_guias_person_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
		return;
	}
	$user = $query->get_queried_object();
	if ( ! $user instanceof WP_User || ! gwf_guias_person_is_public( $user->ID ) ) {
		return;
	}
	$ids = gwf_guias_person_content_ids( $user->ID );
	$query->set( 'author', '' );
	$query->set( 'author_name', '' );
	$query->set( 'post_type', array_merge( GWF_GUIAS_TYPES, array( 'post' ) ) );
	$query->set( 'post__in', $ids ? $ids : array( 0 ) );
	$query->set( 'posts_per_page', GWF_GUIAS_TEAM_PER_PAGE );
	$query->set( 'ignore_sticky_posts', true );
	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'gwf_guias_person_query' );

/** Migas: Inicio › Comité editorial (o Equipo) › Nombre. El comité tendrá página propia más adelante. */
function gwf_guias_person_breadcrumbs( $links ) {
	$user_id = gwf_guias_current_person();
	if ( ! $user_id ) {
		return $links;
	}
	$comite = get_page_by_path( 'comite-editorial' );
	$middle = array( 'text' => get_user_meta( $user_id, 'gwf_comite', true ) ? 'Comité editorial' : 'Equipo' );
	if ( $middle['text'] === 'Comité editorial' && $comite && 'publish' === $comite->post_status ) {
		$middle['url'] = get_permalink( $comite );
	}
	return array( $links[0], $middle, array( 'text' => get_userdata( $user_id )->display_name, 'url' => gwf_guias_person_url( $user_id ) ) );
}
add_filter( 'wpseo_breadcrumb_links', 'gwf_guias_person_breadcrumbs' );

/** Descripción para buscadores: cargo y experiencia. */
function gwf_guias_person_description( $desc ) {
	$user_id = gwf_guias_current_person();
	if ( ! $user_id ) {
		return $desc;
	}
	$p    = gwf_guias_person( $user_id );
	$text = trim( $p['cargo'] . '. ' . wp_strip_all_tags( $p['experiencia'] ), '. ' );
	return $text ? mb_strimwidth( preg_replace( '/\s+/', ' ', $text ), 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_guias_person_description' );
add_filter( 'wpseo_opengraph_desc', 'gwf_guias_person_description' );

/** La ficha se indexa aunque la persona no tenga entradas del blog (Yoast pondría noindex). */
function gwf_guias_person_robots( $robots ) {
	return gwf_guias_current_person() ? 'index, follow' : $robots;
}
add_filter( 'wpseo_robots', 'gwf_guias_person_robots', 30 );
