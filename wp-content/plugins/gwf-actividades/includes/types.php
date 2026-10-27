<?php
/**
 * Tipo Actividad (/actividades/<slug>/) y taxonomías: Edad (compartida con guías y pilares) y Qué trabaja
 * (/actividades/trabaja/<término>/). Sin revisión obligatoria: la revisión experta solo si el tema lo pide.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gwf_act_register_type() {
	register_post_type(
		'actividad',
		array(
			'labels'        => array(
				'name'               => 'Actividades',
				'singular_name'      => 'Actividad',
				'add_new'            => 'Añadir nueva',
				'add_new_item'       => 'Añadir actividad',
				'edit_item'          => 'Editar actividad',
				'new_item'           => 'Nueva actividad',
				'view_item'          => 'Ver actividad',
				'search_items'       => 'Buscar actividades',
				'not_found'          => 'No hay actividades',
				'not_found_in_trash' => 'No hay actividades en la papelera',
				'all_items'          => 'Todas las actividades',
				'menu_name'          => 'Actividades',
			),
			'description'   => 'Capa 1: actividades sin pantalla para hacer juntos, con imprimible a cambio del correo.',
			'public'        => true,
			'has_archive'   => 'actividades',
			'rewrite'       => array( 'slug' => 'actividades', 'with_front' => false ),
			'menu_position' => 23,
			'menu_icon'     => 'dashicons-art',
			// Sin editor: todo el contenido son campos (ficha, pasos, imprimible, por qué).
			'supports'      => array( 'title', 'thumbnail', 'excerpt', 'author', 'revisions' ),
			'show_in_rest'  => true,
		)
	);
}
add_action( 'init', 'gwf_act_register_type' );

function gwf_act_register_taxonomies() {
	// Edad: si gwf-guias (o gwf-cuentos) ya la registró, solo se añade a las actividades.
	if ( taxonomy_exists( 'edad' ) ) {
		register_taxonomy_for_object_type( 'edad', 'actividad' );
	} else {
		register_taxonomy( 'edad', array( 'actividad' ), gwf_act_tax_args( 'Edad', 'Edades', 'edad' ) );
	}
	register_taxonomy( 'trabaja', array( 'actividad' ), gwf_act_tax_args( 'Qué trabaja', 'Qué trabaja', 'actividades/trabaja' ) );
}
add_action( 'init', 'gwf_act_register_taxonomies', 6 ); // Después de gwf-guias (5), antes del tipo: sus reglas de URL van primero.

function gwf_act_tax_args( $singular, $plural, $slug ) {
	return array(
		'labels'            => array(
			'name'          => $plural,
			'singular_name' => $singular,
			'all_items'     => 'Todos',
			'edit_item'     => 'Editar',
			'add_new_item'  => 'Añadir',
			'search_items'  => 'Buscar',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => $slug, 'with_front' => false ),
	);
}

/** Términos iniciales (no borra ni renombra los existentes). */
function gwf_act_seed_terms() {
	$terms = array(
		'edad'    => array( '4-anos' => '4 años', '5-anos' => '5 años', '6-anos' => '6 años' ),
		'trabaja' => array(
			'imaginacion'      => 'Imaginación',
			'emociones'        => 'Emociones',
			'lenguaje'         => 'Lenguaje',
			'motricidad'       => 'Motricidad',
			'juego-en-familia' => 'Juego en familia',
		),
	);
	foreach ( $terms as $taxonomy => $list ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		foreach ( $list as $slug => $name ) {
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			}
		}
	}
}
