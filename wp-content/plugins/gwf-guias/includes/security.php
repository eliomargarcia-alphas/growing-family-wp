<?php
/**
 * Permisos de los campos que dan autoridad en la web y en el schema.
 *
 * - Ficha pública del equipo y área del comité (perfil de usuario): solo quien puede editar usuarios (administradores).
 *   Sin esto, cualquier autor podría marcarse en su propio perfil como miembro del comité o revisor con ficha.
 * - «Revisado por» y «Fecha de revisión» de guías y pilares: a mano solo editores y administradores; el revisor experto
 *   los rellena con «Aprobar revisión» (gwf_guias_after_save), que no pasa por el envío del formulario.
 *
 * Los campos se ocultan en el formulario y, si llegan igualmente en el envío, se descartan antes de que ACF guarde.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_GUIAS_ADMIN_USER_GROUPS = array( 'group_gwf_revisor', 'group_gwf_comite_area' );
const GWF_GUIAS_EDITOR_FIELDS     = array( 'field_gwf_revisado_por', 'field_gwf_fecha_revision' );

/** ¿Puede el usuario actual tocar este campo? */
function gwf_guias_can_edit_field( $field ) {
	if ( in_array( $field['parent'] ?? '', GWF_GUIAS_ADMIN_USER_GROUPS, true ) ) {
		return current_user_can( 'edit_users' );
	}
	if ( in_array( $field['key'] ?? '', GWF_GUIAS_EDITOR_FIELDS, true ) ) {
		return current_user_can( 'edit_others_posts' );
	}
	return true;
}

function gwf_guias_hide_protected_field( $field ) {
	return ( ! $field || gwf_guias_can_edit_field( $field ) ) ? $field : false;
}
add_filter( 'acf/prepare_field', 'gwf_guias_hide_protected_field' );

/** Descarta del envío los campos protegidos que el usuario no puede editar (antes del guardado de ACF, prioridad 10). */
function gwf_guias_strip_protected_fields() {
	if ( empty( $_POST['acf'] ) || ! is_array( $_POST['acf'] ) || ! function_exists( 'acf_get_field' ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- ACF ya comprobó el nonce.
		return;
	}
	foreach ( array_keys( $_POST['acf'] ) as $key ) { // phpcs:ignore WordPress.Security.NonceVerification
		$field = acf_get_field( $key );
		if ( $field && ! gwf_guias_can_edit_field( $field ) ) {
			unset( $_POST['acf'][ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification
		}
	}
}
add_action( 'acf/save_post', 'gwf_guias_strip_protected_fields', 1 );
