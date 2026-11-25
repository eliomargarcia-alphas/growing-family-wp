<?php
/**
 * Avisos del escritorio que no aportan nada y salen en cada carga (2026-11-25).
 *
 * - WP ULike: «update WP ULike Pro to 2.3.0» (la licencia Pro caducó y no se puede actualizar).
 * - WP ULike: «faster like storage is ready» (la migración de tablas se puede hacer cuando se quiera desde
 *   WP ULike → Storage upgrade; los «me gusta» funcionan igual).
 * - WP ULike Pro: «Your license has expired».
 *
 * Solo se ocultan los avisos: los plugins siguen funcionando igual. Si se renueva la licencia o se actualiza
 * WP ULike Pro, borrar este archivo.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

// Aviso de «Pro desactualizado»: el propio plugin ofrece este filtro para silenciarlo.
add_filter( 'wp_ulike_show_outdated_pro_notice', '__return_false' );

/** Quita de admin_notices los avisos de WP ULike que no tienen filtro (se registran antes de esta acción). */
function gwf_admin_quitar_avisos() {
	global $wp_filter;
	if ( empty( $wp_filter['admin_notices'] ) ) {
		return;
	}
	$quitar = array(
		'WP_Ulike_Pulse_Admin::storage_upgrade_notice', // «faster like storage is ready».
		'::admin_license_details',                      // WP ULike Pro: «Your license has expired».
	);
	foreach ( $wp_filter['admin_notices']->callbacks as $prioridad => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$fn = $callback['function'];
			if ( ! is_array( $fn ) || 2 !== count( $fn ) ) {
				continue;
			}
			$clase = is_object( $fn[0] ) ? get_class( $fn[0] ) : (string) $fn[0];
			foreach ( $quitar as $patron ) {
				list( $c, $m ) = explode( '::', $patron );
				if ( $fn[1] === $m && ( '' === $c || $clase === $c ) && ( '' !== $c || 0 === strpos( $clase, 'WP_Ulike_Pro' ) ) ) {
					remove_action( 'admin_notices', $fn, $prioridad );
				}
			}
		}
	}
}
add_action( 'in_admin_header', 'gwf_admin_quitar_avisos', 99 );
