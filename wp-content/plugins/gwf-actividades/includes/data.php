<?php
/**
 * Datos que pinta la plantilla del tema (single-actividad.php). La presentación vive en el tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Texto «a | b | c» por líneas → filas con nombre. */
function gwf_act_lines( $text, $columns ) {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, count( $columns ) ) );
		$row   = array();
		foreach ( $columns as $i => $column ) {
			$row[ $column ] = $parts[ $i ] ?? '';
		}
		$rows[] = $row;
	}
	return $rows;
}

function gwf_act_meta( $post_id, $key ) {
	return trim( (string) get_post_meta( $post_id, $key, true ) );
}

/** Pasos con su imagen (si la tienen). */
function gwf_act_steps( $post_id ) {
	$steps = gwf_act_lines( gwf_act_meta( $post_id, 'gwf_act_pasos' ), array( 'titulo', 'texto', 'para_ti' ) );
	foreach ( $steps as $i => $step ) {
		$steps[ $i ]['imagen'] = $i < GWF_ACT_STEP_IMAGES ? (int) get_post_meta( $post_id, 'gwf_act_paso_img_' . ( $i + 1 ), true ) : 0;
	}
	return $steps;
}

function gwf_act_materials( $post_id ) {
	return wp_list_pluck( gwf_act_lines( gwf_act_meta( $post_id, 'gwf_act_materiales' ), array( 'texto' ) ), 'texto' );
}

/** Fuente «Título | URL». */
function gwf_act_source( $post_id ) {
	$rows = gwf_act_lines( gwf_act_meta( $post_id, 'gwf_act_fuente' ), array( 'titulo', 'url' ) );
	return $rows ? $rows[0] : null;
}

/** Términos de una taxonomía, el principal de Yoast primero. */
function gwf_act_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$primary = function_exists( 'yoast_get_primary_term_id' ) ? (int) yoast_get_primary_term_id( $taxonomy, $post_id ) : 0;
	usort(
		$terms,
		function ( $a, $b ) use ( $primary ) {
			return ( $b->term_id === $primary ) <=> ( $a->term_id === $primary ) ?: strnatcmp( $a->slug, $b->slug );
		}
	);
	return $terms;
}

/** «4, 5 y 6 años» a partir de los términos de Edad. */
function gwf_act_ages_label( $post_id ) {
	$terms = get_the_terms( $post_id, 'edad' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	usort( $terms, function ( $a, $b ) { return strnatcmp( $a->slug, $b->slug ); } );
	if ( 1 === count( $terms ) ) {
		return $terms[0]->name;
	}
	$nums = array_map( function ( $t ) { return (int) $t->name; }, $terms );
	$last = array_pop( $nums );
	return implode( ', ', $nums ) . ' y ' . $last . ' años';
}

/** Revisor (opcional): nombre, cargo y su ficha /equipo/ si gwf-guias está activo y la ficha es pública. */
function gwf_act_reviewer( $post_id ) {
	$user_id = (int) get_post_meta( $post_id, 'gwf_act_revisado_por', true );
	$user    = $user_id ? get_userdata( $user_id ) : false;
	if ( ! $user ) {
		return null;
	}
	$foto_id = (int) get_user_meta( $user_id, 'gwf_foto', true );
	return array(
		'id'     => $user_id,
		'nombre' => $user->display_name,
		'cargo'  => (string) get_user_meta( $user_id, 'gwf_cargo', true ),
		'foto'   => $foto_id ? (string) wp_get_attachment_image_url( $foto_id, 'thumbnail' ) : '',
		'ficha'  => function_exists( 'gwf_guias_person_url' ) ? gwf_guias_person_url( $user_id ) : '',
	);
}

/** Fecha visible («Actualizado el»): la de revisión si la hay; si no, la de modificación. */
function gwf_act_updated_time( $post_id ) {
	$ymd = gwf_act_meta( $post_id, 'gwf_act_fecha_revision' );
	return preg_match( '/^\d{8}$/', $ymd ) ? strtotime( $ymd ) : (int) get_post_modified_time( 'U', false, $post_id );
}

/** Pilar de la edad: el elegido o, si no, uno publicado de la misma edad. */
function gwf_act_pillar( $post_id ) {
	if ( ! post_type_exists( 'desarrollo' ) ) {
		return 0;
	}
	$pilar = (int) get_post_meta( $post_id, 'gwf_act_pilar', true );
	if ( $pilar && 'publish' === get_post_status( $pilar ) ) {
		return $pilar;
	}
	$ages = wp_get_post_terms( $post_id, 'edad', array( 'fields' => 'ids' ) );
	if ( ! $ages || is_wp_error( $ages ) ) {
		return 0;
	}
	$found = get_posts( array( 'post_type' => 'desarrollo', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => 'edad', 'terms' => $ages ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	return $found ? (int) $found[0] : 0;
}

/** Otras actividades de la misma edad (para «Enlaces»). */
function gwf_act_siblings( $post_id, $limit = 3 ) {
	$ages = wp_get_post_terms( $post_id, 'edad', array( 'fields' => 'ids' ) );
	if ( ! $ages || is_wp_error( $ages ) ) {
		return array();
	}
	return get_posts( array( 'post_type' => 'actividad', 'post_status' => 'publish', 'posts_per_page' => $limit, 'post__not_in' => array( $post_id ), 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => 'edad', 'terms' => $ages ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
}
