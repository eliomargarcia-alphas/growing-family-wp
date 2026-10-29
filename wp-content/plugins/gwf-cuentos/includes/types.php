<?php
/**
 * Tipo Cuento (/cuentos/<slug>/) y taxonomías: Edad (compartida con guías y actividades), Emoción, Situación y
 * Para contar (forma de uso: corto, para dormir…). Editor de bloques: las páginas del cuento se separan con
 * «Salto de página» y las notas de lectura son estilos de párrafo (includes/blocks.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gwf_cue_register_type() {
	register_post_type(
		'cuento',
		array(
			'labels'        => array(
				'name'               => 'Cuentos',
				'singular_name'      => 'Cuento',
				'add_new'            => 'Añadir nuevo',
				'add_new_item'       => 'Añadir cuento',
				'edit_item'          => 'Editar cuento',
				'new_item'           => 'Nuevo cuento',
				'view_item'          => 'Ver cuento',
				'search_items'       => 'Buscar cuentos',
				'not_found'          => 'No hay cuentos',
				'not_found_in_trash' => 'No hay cuentos en la papelera',
				'all_items'          => 'Todos los cuentos',
				'menu_name'          => 'Cuentos',
			),
			'description'   => 'Capa 1: cuentos con propósito, por edad, emoción y situación, para contar esta noche.',
			'public'        => true,
			'has_archive'   => 'cuentos',
			'rewrite'       => array( 'slug' => 'cuentos', 'with_front' => false ),
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-book-alt',
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
			'show_in_rest'  => true, // Editor de bloques y borradores desde el flujo editorial por la API REST.
			'template'      => array( array( 'core/paragraph', array( 'placeholder' => 'Primera página del cuento. Usa «Salto de página» para empezar la siguiente.' ) ) ),
		)
	);
	// Edad registrada por gwf-guias: se une aquí, con el tipo ya registrado (antes WordPress no lo permite).
	register_taxonomy_for_object_type( 'edad', 'cuento' );
}
add_action( 'init', 'gwf_cue_register_type' );

function gwf_cue_register_taxonomies() {
	if ( ! taxonomy_exists( 'edad' ) ) {
		register_taxonomy( 'edad', array( 'cuento' ), gwf_cue_tax_args( 'Edad', 'Edades', 'edad' ) );
	}
	register_taxonomy( 'emocion', array( 'cuento' ), gwf_cue_tax_args( 'Emoción', 'Emociones', 'cuentos/emocion' ) );
	register_taxonomy( 'situacion', array( 'cuento' ), gwf_cue_tax_args( 'Situación', 'Situaciones', 'cuentos/situacion' ) );
	register_taxonomy( 'para_contar', array( 'cuento' ), gwf_cue_tax_args( 'Para contar', 'Para contar', 'cuentos/para-contar' ) );
}
add_action( 'init', 'gwf_cue_register_taxonomies', 6 ); // Después de gwf-guias (5) y antes del tipo: sus reglas de URL van primero.

function gwf_cue_tax_args( $singular, $plural, $slug ) {
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

/** Términos iniciales (no borra ni renombra los existentes). Los del mapa de la estrategia, capa 1. */
function gwf_cue_seed_terms() {
	$terms = array(
		'edad'        => array( '4-anos' => '4 años', '5-anos' => '5 años', '6-anos' => '6 años' ),
		'emocion'     => array(
			'miedo'       => 'Miedo',
			'rabia'       => 'Rabia',
			'tristeza'    => 'Tristeza',
			'celos'       => 'Celos',
			'frustracion' => 'Frustración',
			'alegria'     => 'Alegría',
		),
		'situacion'   => array(
			'llega-un-hermanito'    => 'Llega un hermanito',
			'primer-dia-de-colegio' => 'Primer día de colegio',
			'una-perdida'           => 'Una pérdida',
			'una-mudanza'           => 'Una mudanza',
			'miedo-a-la-oscuridad'  => 'Miedo a la oscuridad',
		),
		'para_contar' => array(
			'para-dormir' => 'Para dormir',
			'corto'       => 'Corto, para contar en 5 minutos',
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
