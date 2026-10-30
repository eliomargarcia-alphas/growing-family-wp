<?php
/**
 * Conexión con guías, pilares y actividades (etapa 9). La relación vive en el cuento («Guía relacionada» y «Pilar»):
 * la guía muestra su cuento en el bloque 9, el pilar lista los cuentos que lo eligieron (y los de su edad si es un
 * pilar por edad) y la actividad sugiere un cuento de su edad. En el listado de cuentos del escritorio, la columna
 * «Enlazado» avisa de los que no tienen guía o pilar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Orden de los cuentos sugeridos: Destacado primero, luego los más recientes. */
function gwf_cue_ordered( $args ) {
	$base = array(
		'post_type'      => 'cuento',
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'orderby'        => 'date',
		'posts_per_page' => 1,
	);
	$ids  = get_posts( array_merge( $base, $args ) );
	// Destacados delante sin perder el orden por fecha (usort es estable; pocos cuentos por consulta).
	usort(
		$ids,
		function ( $a, $b ) {
			return (int) get_post_meta( $b, 'gwf_cue_destacado', true ) <=> (int) get_post_meta( $a, 'gwf_cue_destacado', true );
		}
	);
	return $ids;
}

/** Cuento de una guía (bloque 9): el que la eligió como «Guía relacionada». */
function gwf_cue_for_guide( $guia_id ) {
	$ids = gwf_cue_ordered( array( 'posts_per_page' => 10, 'meta_query' => array( array( 'key' => 'gwf_cue_guia', 'value' => (string) $guia_id ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	return $ids ? (int) $ids[0] : 0;
}

/** Cuentos de un pilar: los que lo eligieron y, si es un pilar por edad, los de esa edad. Ordenados por título. */
function gwf_cue_for_pillar( $pilar_id, $taxonomy = '', $terms = array() ) {
	$by_meta = gwf_cue_ordered( array( 'posts_per_page' => 50, 'meta_query' => array( array( 'key' => 'gwf_cue_pilar', 'value' => (string) $pilar_id ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$by_term = ( 'edad' === $taxonomy && $terms ) ? gwf_cue_ordered( array( 'posts_per_page' => 50, 'tax_query' => array( array( 'taxonomy' => 'edad', 'terms' => wp_list_pluck( $terms, 'term_id' ) ) ) ) ) : array(); // phpcs:ignore WordPress.DB.SlowDBQuery
	$ids     = array_values( array_unique( array_merge( $by_meta, $by_term ) ) );
	usort( $ids, function ( $a, $b ) { return strnatcasecmp( get_the_title( $a ), get_the_title( $b ) ); } );
	return $ids;
}

/** Cuento sugerido en una actividad: uno de su misma edad. */
function gwf_cue_for_activity( $act_id ) {
	$ages = wp_get_post_terms( $act_id, 'edad', array( 'fields' => 'ids' ) );
	if ( ! $ages || is_wp_error( $ages ) ) {
		return 0;
	}
	$ids = gwf_cue_ordered( array( 'posts_per_page' => 10, 'tax_query' => array( array( 'taxonomy' => 'edad', 'terms' => $ages ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	return $ids ? (int) $ids[0] : 0;
}

/** Edad · emoción · minutos (tarjetas de cuento). */
function gwf_cue_card_meta( $post_id ) {
	$meta = array();
	foreach ( array( 'edad', 'emocion' ) as $taxonomy ) {
		$terms = gwf_cue_terms( $post_id, $taxonomy );
		if ( $terms ) {
			$meta[] = $terms[0]->name;
		}
	}
	$meta[] = gwf_cue_minutes( $post_id ) . ' min';
	return implode( ' · ', $meta );
}

/** Escritorio: columna «Enlazado» en Cuentos (guía y pilar, o aviso si faltan). */
function gwf_cue_admin_columns( $columns ) {
	$columns['gwf_cue_enlazado'] = 'Enlazado';
	return $columns;
}
add_filter( 'manage_cuento_posts_columns', 'gwf_cue_admin_columns' );

function gwf_cue_admin_column( $column, $post_id ) {
	if ( 'gwf_cue_enlazado' !== $column ) {
		return;
	}
	$lines = array();
	foreach ( array( 'gwf_cue_guia' => 'Guía', 'gwf_cue_pilar' => 'Pilar' ) as $key => $label ) {
		$id      = (int) get_post_meta( $post_id, $key, true );
		$lines[] = $id
			? esc_html( $label ) . ': <a href="' . esc_url( get_edit_post_link( $id ) ) . '">' . esc_html( get_the_title( $id ) ) . '</a>'
			: '<span style="color:#b32d2e">⚠ Sin ' . esc_html( mb_strtolower( $label ) ) . '</span>';
	}
	echo implode( '<br>', $lines ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'manage_cuento_posts_custom_column', 'gwf_cue_admin_column', 10, 2 );
