<?php
/**
 * Listas de las guías (pasos, qué evitar, fuentes, hitos) en un formato que no depende de ACF PRO.
 *
 * Hoy: campo de texto, un elemento por línea, columnas separadas por « | ».
 * Con ACF PRO: un repetidor con el mismo nombre de campo y subcampos con los nombres de $columns.
 * La plantilla siempre usa gwf_guias_items(), así que el cambio no la toca.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Columnas de cada lista, en el orden en que se escriben en el texto. */
const GWF_GUIAS_LISTS = array(
	'gwf_pasos'   => array( 'titulo', 'texto' ),
	'gwf_evitar'  => array( 'texto' ),
	'gwf_fuentes' => array( 'titulo', 'organismo', 'url' ),
	'gwf_hitos'   => array( 'hito', 'rango', 'variabilidad' ),
);

/** Convierte el texto «a | b | c» por líneas en filas con nombre. */
function gwf_guias_parse_lines( $text, $columns ) {
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

/**
 * Filas de una lista de la guía. $raw permite pasar el valor sin guardar (p. ej. el del formulario).
 *
 * @return array[] Filas con las claves de GWF_GUIAS_LISTS[ $name ].
 */
function gwf_guias_items( $post_id, $name, $raw = null ) {
	$columns = GWF_GUIAS_LISTS[ $name ] ?? array( 'texto' );
	$value   = null !== $raw ? $raw : get_post_meta( $post_id, $name, true );

	// Repetidor de ACF PRO: get_field devuelve un array de filas.
	if ( null === $raw && function_exists( 'get_field' ) ) {
		$field = get_field( $name, $post_id );
		if ( is_array( $field ) ) {
			return array_values(
				array_filter(
					array_map(
						function ( $row ) use ( $columns ) {
							return array_intersect_key( array_merge( array_fill_keys( $columns, '' ), (array) $row ), array_flip( $columns ) );
						},
						$field
					),
					function ( $row ) {
						return '' !== trim( implode( '', $row ) );
					}
				)
			);
		}
	}
	return is_string( $value ) ? gwf_guias_parse_lines( $value, $columns ) : array();
}
