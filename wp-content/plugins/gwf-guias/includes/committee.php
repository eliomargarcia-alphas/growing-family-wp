<?php
/**
 * Página del Comité editorial (/comite-editorial/, wireframe «plantilla-comite»), con la plantilla del tema
 * templates/template-comite.php. Los miembros salen solos de los usuarios marcados «Miembro del comité»,
 * agrupados por su área; los textos fijos de la página son campos de la propia página.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_GUIAS_COMMITTEE_TEMPLATE = 'templates/template-comite.php';

/** Áreas del comité, en el orden en que se muestran. */
const GWF_GUIAS_COMMITTEE_AREAS = array(
	'clinica'   => 'Revisión clínica',
	'pedagogia' => 'Pedagogía',
	'redaccion' => 'Redacción y producción',
	'fundador'  => 'Fundador',
);

function gwf_guias_register_committee_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	// Área del comité, en el perfil del usuario (junto a «Miembro del comité editorial»).
	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_comite_area',
			'title'    => 'Comité editorial',
			'location' => array( array( array( 'param' => 'user_form', 'operator' => '==', 'value' => 'all' ) ) ),
			'fields'   => array(
				gwf_guias_field( 'gwf_area', 'Área del comité', 'select', array( 'choices' => GWF_GUIAS_COMMITTEE_AREAS, 'allow_null' => 1, 'instructions' => 'Solo cuenta si la persona es miembro del comité. El fundador va en una tarjeta aparte.' ) ),
			),
		)
	);
	// Textos de la página del comité (bloques 2 y 4 a 10 del wireframe).
	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_comite_pagina',
			'title'    => 'Página del comité editorial',
			'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => GWF_GUIAS_COMMITTEE_TEMPLATE ) ) ),
			'fields'   => array(
				gwf_guias_field( 'gwf_comite_compromiso', 'Nuestro compromiso', 'text' ),
				gwf_guias_field( 'gwf_comite_pasos', 'Cómo creamos el contenido', 'textarea', array( 'rows' => 5, 'new_lines' => '', 'instructions' => 'Un paso por línea. El último se resalta.' ) ),
				gwf_guias_field( 'gwf_comite_ia', 'Cómo usamos la IA', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop' ) ),
				gwf_guias_field( 'gwf_comite_fuentes', 'Qué fuentes usamos', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop' ) ),
				gwf_guias_field( 'gwf_comite_no_hacemos', 'Lo que no hacemos', 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => 'Una frase por línea.' ) ),
				gwf_guias_field( 'gwf_comite_errores', 'Cómo corregimos errores', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop' ) ),
				gwf_guias_field( 'gwf_comite_empresa', 'Empresa', 'text', array( 'default_value' => 'Human Rise Education LLC' ) ),
				gwf_guias_field( 'gwf_comite_empresa_pagina', 'Página de empresa', 'post_object', array( 'post_type' => array( 'page' ), 'return_format' => 'id', 'allow_null' => 1, 'instructions' => 'El botón «Página de empresa» aparece cuando la página elegida esté publicada.' ) ),
			),
		)
	);
}
add_action( 'acf/init', 'gwf_guias_register_committee_fields' );

/** @id del comité en el schema (también lo usa el memberOf de cada miembro). */
function gwf_guias_committee_schema_id() {
	return home_url( '/#/schema/comite-editorial' );
}

/** ¿Es la página del comité? */
function gwf_guias_is_committee_page( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	return $post_id && GWF_GUIAS_COMMITTEE_TEMPLATE === get_page_template_slug( $post_id );
}

/** Miembros del comité agrupados por área (en el orden de las áreas); dentro de cada área, por nombre. */
function gwf_guias_committee_members() {
	$users  = get_users( array( 'meta_key' => 'gwf_comite', 'meta_value' => '1', 'orderby' => 'display_name' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$groups = array_fill_keys( array_keys( GWF_GUIAS_COMMITTEE_AREAS ), array() );
	foreach ( $users as $user ) {
		$area = (string) get_user_meta( $user->ID, 'gwf_area', true );
		$area = isset( $groups[ $area ] ) ? $area : 'redaccion';
		$groups[ $area ][] = gwf_guias_person( $user->ID ) + array( 'ancla' => $user->user_nicename );
	}
	return array_filter( $groups );
}

/** Últimas guías y pilares revisados por miembros del comité. */
function gwf_guias_committee_reviewed( $limit = 4 ) {
	$ids = get_users( array( 'meta_key' => 'gwf_comite', 'meta_value' => '1', 'fields' => 'ID' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( ! $ids ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => GWF_GUIAS_TYPES,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_query'     => array( array( 'key' => 'gwf_revisado_por', 'value' => array_map( 'strval', $ids ), 'compare' => 'IN' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

/** Schema: la página es un AboutPage del comité, que figura como organización con sus miembros. */
function gwf_guias_committee_schema_graph( $graph ) {
	if ( ! is_array( $graph ) || ! is_page() || ! gwf_guias_is_committee_page() ) {
		return $graph;
	}
	$comite_id = gwf_guias_committee_schema_id();
	$members   = array();
	foreach ( gwf_guias_committee_members() as $group ) {
		foreach ( $group as $p ) {
			$members[] = $p['id'];
		}
	}
	foreach ( $graph as $i => $piece ) {
		if ( in_array( 'WebPage', (array) ( $piece['@type'] ?? array() ), true ) ) {
			$graph[ $i ]['@type']      = array( 'WebPage', 'AboutPage' );
			$graph[ $i ]['mainEntity'] = array( '@id' => $comite_id );
		}
	}
	$graph[] = array(
		'@type'              => 'Organization',
		'@id'                => $comite_id,
		'name'               => 'Comité editorial de Growing Family',
		'url'                => get_permalink( get_queried_object_id() ),
		'parentOrganization' => array( '@id' => home_url( '/#organization' ) ),
		'member'             => array_map(
			function ( $id ) {
				return array( '@id' => gwf_guias_person_schema_id( $id ) );
			},
			$members
		),
	);
	foreach ( $members as $id ) {
		$graph[] = gwf_guias_person_piece( $id );
	}
	return $graph;
}
add_filter( 'wpseo_schema_graph', 'gwf_guias_committee_schema_graph', 12 );
