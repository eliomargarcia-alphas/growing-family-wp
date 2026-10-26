<?php
/**
 * Tipos de contenido (Guía de problema y Guía de desarrollo), taxonomías (Edad, Tema, Gran tema) y rol Revisor.
 *
 * URLs: /guias/<slug>/ y /desarrollo/<slug>/, fuera del prefijo /blog/ de las entradas (with_front = false).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Permisos propios, compartidos por los dos tipos: el Revisor edita y revisa, pero no publica ni borra. */
const GWF_GUIAS_CAPS = array(
	'edit_guias',
	'edit_others_guias',
	'edit_published_guias',
	'edit_private_guias',
	'publish_guias',
	'read_private_guias',
	'delete_guias',
	'delete_others_guias',
	'delete_published_guias',
	'delete_private_guias',
);

function gwf_guias_type_args( $singular, $plural, $slug, $description, $menu_position ) {
	return array(
		'labels'          => array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new'            => 'Añadir nueva',
			'add_new_item'       => 'Añadir ' . mb_strtolower( $singular ),
			'edit_item'          => 'Editar ' . mb_strtolower( $singular ),
			'new_item'           => 'Nueva ' . mb_strtolower( $singular ),
			'view_item'          => 'Ver ' . mb_strtolower( $singular ),
			'search_items'       => 'Buscar ' . mb_strtolower( $plural ),
			'not_found'          => 'No hay ' . mb_strtolower( $plural ),
			'not_found_in_trash' => 'No hay ' . mb_strtolower( $plural ) . ' en la papelera',
			'all_items'          => 'Todas las ' . mb_strtolower( $plural ),
			'menu_name'          => $plural,
		),
		'description'     => $description,
		'public'          => true,
		'has_archive'     => $slug,
		'rewrite'         => array( 'slug' => $slug, 'with_front' => false ),
		'menu_position'   => $menu_position,
		'menu_icon'       => 'dashicons-welcome-learn-more',
		'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ),
		'show_in_rest'    => true, // Borradores desde el flujo editorial por la API REST.
		'capability_type' => array( 'guia', 'guias' ),
		'map_meta_cap'    => true,
	);
}

function gwf_guias_register_types() {
	register_post_type(
		'guia',
		gwf_guias_type_args( 'Guía de problema', 'Guías de problema', 'guias', 'Capa 2: resuelve una situación concreta y lleva al hábito.', 21 )
	);
	register_post_type(
		'desarrollo',
		gwf_guias_type_args( 'Guía de desarrollo', 'Guías de desarrollo', 'desarrollo', 'Capa 3: pilares por edad y por gran tema.', 22 )
	);
}
add_action( 'init', 'gwf_guias_register_types' );

function gwf_guias_tax_args( $singular, $plural, $slug ) {
	return array(
		'labels'            => array(
			'name'          => $plural,
			'singular_name' => $singular,
			'all_items'     => 'Todos',
			'edit_item'     => 'Editar ' . mb_strtolower( $singular ),
			'add_new_item'  => 'Añadir ' . mb_strtolower( $singular ),
			'search_items'  => 'Buscar',
		),
		'public'            => true,
		'hierarchical'      => true, // Casillas en el editor, sin crear términos al vuelo.
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => $slug, 'with_front' => false ),
		'capabilities'      => array(
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_guias',
		),
	);
}

function gwf_guias_register_taxonomies() {
	// Edad: la comparten guías, pilares y (más adelante) cuentos. Si gwf-cuentos ya la registra, no se duplica.
	if ( ! taxonomy_exists( 'edad' ) ) {
		register_taxonomy( 'edad', GWF_GUIAS_TYPES, gwf_guias_tax_args( 'Edad', 'Edades', 'edad' ) );
	} else {
		foreach ( GWF_GUIAS_TYPES as $type ) {
			register_taxonomy_for_object_type( 'edad', $type );
		}
	}
	// Tema: los temas de la capa 2 del mapa de preguntas (anexo A de la estrategia), los mismos que las categorías del blog.
	register_taxonomy( 'tema', array( 'guia' ), gwf_guias_tax_args( 'Tema', 'Temas', 'guias/tema' ) );
	// Gran tema: los pilares por tema de la capa 3. También en las guías de problema, para que el pilar de un tema las liste.
	register_taxonomy( 'gran_tema', array( 'desarrollo', 'guia' ), gwf_guias_tax_args( 'Gran tema', 'Grandes temas', 'desarrollo/tema' ) );
}
add_action( 'init', 'gwf_guias_register_taxonomies', 5 );

/** Rol Revisor y permisos de los roles existentes. */
function gwf_guias_setup_roles() {
	foreach ( array( 'administrator', 'editor' ) as $role_name ) {
		$role = get_role( $role_name );
		if ( $role ) {
			foreach ( GWF_GUIAS_CAPS as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}

	remove_role( 'gwf_revisor' ); // Se recrea para que los permisos coincidan siempre con el código.
	add_role(
		'gwf_revisor',
		'Revisor experto',
		array(
			'read'                 => true,
			'edit_guias'           => true,
			'edit_others_guias'    => true,
			'edit_published_guias' => true,
			'edit_private_guias'   => true,
			'read_private_guias'   => true,
			'upload_files'         => false,
		)
	);
}

/** Actualiza roles cuando cambia la versión del plugin (sin tener que desactivarlo). */
function gwf_guias_maybe_upgrade() {
	if ( get_option( 'gwf_guias_version' ) !== GWF_GUIAS_VERSION ) {
		gwf_guias_setup_roles();
		gwf_guias_seed_terms();
		update_option( 'gwf_guias_version', GWF_GUIAS_VERSION );
		flush_rewrite_rules();
	}
}
add_action( 'init', 'gwf_guias_maybe_upgrade', 20 );

/** Términos iniciales (no borra ni renombra los que ya existan). */
function gwf_guias_seed_terms() {
	$terms = array(
		'edad'      => array(
			'4-anos' => '4 años',
			'5-anos' => '5 años',
			'6-anos' => '6 años',
		),
		'tema'      => array(
			'no-me-hace-caso-y-limites'         => 'No me hace caso y límites',
			'me-pega'                           => 'Me pega',
			'berrinches'                        => 'Berrinches',
			'enojo-frustracion-y-llanto'        => 'Enojo, frustración y llanto',
			'miedo-y-dormir-solo'               => 'Miedo y dormir solo',
			'pantallas'                         => 'Pantallas',
			'gritos-y-convivencia'              => 'Gritos y convivencia',
			'lo-que-siente-la-madre-o-el-padre' => 'Lo que siente la madre o el padre',
		),
		'gran_tema' => array(
			'emociones'               => 'Emociones',
			'empatia'                 => 'Empatía',
			'historias'               => 'Historias',
			'aprendizaje-por-reflejo' => 'Aprendizaje por reflejo',
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

/**
 * Editor clásico para las guías: los campos ACF y la regla de publicación viajan en el mismo envío.
 * (Con el editor de bloques ACF guarda los campos en una petición aparte, después de publicar.)
 */
function gwf_guias_classic_editor( $use, $post_type ) {
	return in_array( $post_type, GWF_GUIAS_TYPES, true ) ? false : $use;
}
add_filter( 'use_block_editor_for_post_type', 'gwf_guias_classic_editor', 10, 2 );
