<?php
/**
 * Regla de publicación: ninguna guía ni pilar se publica sin revisor experto, fecha de revisión, fuentes
 * mínimas y sus bloques obligatorios (Estrategia v1.6: capas 2 y 3 solo con firma y revisión experta).
 *
 * Flujo: Borrador → Pendiente de revisión → el Revisor aprueba (se guardan su nombre, la fecha y una huella del
 * contenido) → solo entonces se puede Publicar. Si algo falta, la guía queda en «Pendiente de revisión»
 * y el editor explica qué falta. Si el contenido cambia después de revisado, se avisa de que hace falta otra revisión.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valor de un campo para la comprobación: el del formulario que se está guardando si viene en él,
 * si no el guardado.
 */
function gwf_guias_value( $post_id, $name ) {
	$key = 'field_' . $name;
	if ( isset( $_POST['acf'] ) && is_array( $_POST['acf'] ) && array_key_exists( $key, $_POST['acf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- lo valida WordPress/ACF al guardar.
		return wp_unslash( $_POST['acf'][ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	return $post_id ? get_post_meta( $post_id, $name, true ) : '';
}

function gwf_guias_filled( $post_id, $name ) {
	return '' !== trim( (string) gwf_guias_value( $post_id, $name ) );
}

function gwf_guias_count( $post_id, $name ) {
	return count( gwf_guias_items( $post_id, $name, (string) gwf_guias_value( $post_id, $name ) ) );
}

function gwf_guias_is_reviewer( $user_id ) {
	$user = $user_id ? get_userdata( (int) $user_id ) : false;
	return $user && in_array( 'gwf_revisor', (array) $user->roles, true );
}

/**
 * Qué impide publicar (missing) y qué conviene completar (warnings).
 *
 * @return array{missing: string[], warnings: string[]}
 */
function gwf_guias_check( $post_id, $type ) {
	$missing  = array();
	$warnings = array();

	// La casilla «Aprobar ahora» de un revisor cuenta como revisor y fecha (se guardan en el mismo envío).
	$approving = gwf_guias_filled( $post_id, 'gwf_aprobar' ) && (bool) gwf_guias_value( $post_id, 'gwf_aprobar' )
		&& gwf_guias_is_reviewer( get_current_user_id() );

	if ( ! $approving && ! gwf_guias_is_reviewer( gwf_guias_value( $post_id, 'gwf_revisado_por' ) ) ) {
		$missing[] = 'un revisor experto en «Revisado por»';
	}
	if ( ! $approving && ! gwf_guias_filled( $post_id, 'gwf_fecha_revision' ) ) {
		$missing[] = 'la fecha de revisión';
	}
	if ( gwf_guias_count( $post_id, 'gwf_fuentes' ) < 2 ) {
		$missing[] = 'al menos 2 fuentes';
	}

	if ( 'guia' === $type ) {
		foreach ( array(
			'gwf_respuesta'        => 'la respuesta directa',
			'gwf_ya_intentaste'    => '«Reconocer lo que ya intentaste»',
			'gwf_cuando_consultar' => '«Cuándo consultar»',
		) as $name => $label ) {
			if ( ! gwf_guias_filled( $post_id, $name ) ) {
				$missing[] = $label;
			}
		}
		if ( gwf_guias_count( $post_id, 'gwf_pasos' ) < 3 ) {
			$missing[] = 'al menos 3 pasos en «Qué hacer esta noche»';
		}
		if ( gwf_guias_count( $post_id, 'gwf_evitar' ) < 2 ) {
			$missing[] = 'al menos 2 puntos en «Qué conviene evitar»';
		}
		if ( ! gwf_guias_filled( $post_id, 'gwf_video_url' ) ) {
			$warnings[] = 'Falta el vídeo de la guía.';
		}
		if ( ! gwf_guias_filled( $post_id, 'gwf_pilar' ) ) {
			$warnings[] = 'Falta el pilar (guía de desarrollo) en «Relacionados».';
		}
	}

	if ( 'desarrollo' === $type ) {
		if ( ! gwf_guias_filled( $post_id, 'gwf_definicion' ) ) {
			$missing[] = 'la definición';
		}
		if ( ! gwf_guias_filled( $post_id, 'gwf_curriculo' ) ) {
			$missing[] = '«Cómo lo trabajamos en el currículo»';
		}
	}

	return array( 'missing' => $missing, 'warnings' => $warnings );
}

/** Guarda los mensajes para mostrarlos en la siguiente pantalla del editor. */
function gwf_guias_flash( $type, $messages ) {
	if ( $messages ) {
		set_transient( 'gwf_guias_' . $type . '_' . get_current_user_id(), $messages, 120 );
	}
}

/**
 * Antes de guardar: si se intenta publicar (o programar) sin cumplir la regla, se queda en «Pendiente de revisión».
 * Una guía ya publicada no se despublica: se guarda y se avisa de lo que falta.
 */
function gwf_guias_enforce_publish_rule( $data, $postarr ) {
	if ( ! in_array( $data['post_type'], GWF_GUIAS_TYPES, true ) || ! in_array( $data['post_status'], array( 'publish', 'future' ), true ) ) {
		return $data;
	}
	if ( wp_is_post_revision( $postarr['ID'] ?? 0 ) || wp_is_post_autosave( $postarr['ID'] ?? 0 ) ) {
		return $data;
	}

	$post_id = (int) ( $postarr['ID'] ?? 0 );
	$check   = gwf_guias_check( $post_id, $data['post_type'] );
	if ( ! $check['missing'] ) {
		return $data;
	}

	$was_published = $post_id && 'publish' === get_post_status( $post_id );
	if ( $was_published ) {
		gwf_guias_flash( 'error', array( 'La guía sigue publicada, pero ya no cumple la regla. Falta: ' . implode( ', ', $check['missing'] ) . '.' ) );
		return $data;
	}

	$data['post_status'] = 'pending';
	gwf_guias_flash( 'error', array( 'No se ha publicado: queda en «Pendiente de revisión». Para publicar falta: ' . implode( ', ', $check['missing'] ) . '.' ) );
	return $data;
}
add_filter( 'wp_insert_post_data', 'gwf_guias_enforce_publish_rule', 99, 2 );

/** La misma regla por la API REST: publicar desde fuera sin cumplirla devuelve un error y no guarda. */
function gwf_guias_rest_publish_rule( $prepared, $request ) {
	$status = $request->get_param( 'status' );
	if ( ! in_array( $status, array( 'publish', 'future' ), true ) ) {
		return $prepared;
	}
	$post_id = (int) ( $prepared->ID ?? 0 );
	$check   = gwf_guias_check( $post_id, $prepared->post_type ?? '' );
	if ( $check['missing'] ) {
		return new WP_Error( 'gwf_guias_publish_rule', 'No se puede publicar. Falta: ' . implode( ', ', $check['missing'] ) . '.', array( 'status' => 400 ) );
	}
	return $prepared;
}
foreach ( GWF_GUIAS_TYPES as $gwf_guias_type ) {
	add_filter( "rest_pre_insert_{$gwf_guias_type}", 'gwf_guias_rest_publish_rule', 10, 2 );
}

/** Huella del contenido revisable (texto del editor y campos de bloques), para saber si cambió tras la revisión. */
function gwf_guias_content_hash( $post_id ) {
	$parts = array( get_post_field( 'post_title', $post_id ), get_post_field( 'post_content', $post_id ) );
	foreach ( array( 'gwf_respuesta', 'gwf_ya_intentaste', 'gwf_cuando_consultar', 'gwf_pasos', 'gwf_evitar', 'gwf_desacuerdo', 'gwf_definicion', 'gwf_hitos', 'gwf_curriculo', 'gwf_fuentes' ) as $name ) {
		$parts[] = (string) get_post_meta( $post_id, $name, true );
	}
	return md5( implode( "\n--\n", $parts ) );
}

/**
 * Después de guardar los campos: aprobación del revisor, aviso de contenido cambiado y avisos de lo que conviene completar.
 */
function gwf_guias_after_save( $post_id ) {
	$type = get_post_type( $post_id );
	if ( ! in_array( $type, GWF_GUIAS_TYPES, true ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$notices = array();
	$user_id = get_current_user_id();

	if ( get_post_meta( $post_id, 'gwf_aprobar', true ) ) {
		if ( gwf_guias_is_reviewer( $user_id ) ) {
			update_field( 'gwf_revisado_por', $user_id, $post_id );
			update_field( 'gwf_fecha_revision', current_time( 'Ymd' ), $post_id );
			update_post_meta( $post_id, '_gwf_hash_revisado', gwf_guias_content_hash( $post_id ) );
			$notices[] = 'Revisión aprobada: se guardaron tu nombre y la fecha de hoy.';
		} else {
			gwf_guias_flash( 'error', array( 'Solo un usuario con el rol «Revisor experto» puede aprobar la revisión.' ) );
		}
		update_field( 'gwf_aprobar', 0, $post_id );
	} elseif ( get_post_meta( $post_id, 'gwf_revisado_por', true ) && get_post_meta( $post_id, 'gwf_fecha_revision', true ) && ! get_post_meta( $post_id, '_gwf_hash_revisado', true ) ) {
		// Revisor y fecha puestos a mano (sin «Aprobar»): esta versión es la revisada.
		update_post_meta( $post_id, '_gwf_hash_revisado', gwf_guias_content_hash( $post_id ) );
	}

	$hash = get_post_meta( $post_id, '_gwf_hash_revisado', true );
	if ( $hash && gwf_guias_content_hash( $post_id ) !== $hash ) {
		$date      = get_post_meta( $post_id, 'gwf_fecha_revision', true );
		$notices[] = 'El contenido cambió desde la revisión' . ( $date ? ' del ' . date_i18n( 'j \d\e F \d\e Y', strtotime( $date ) ) : '' )
			. ': hace falta una nueva revisión. La fecha visible sigue siendo la de la última revisión.';
	}

	$check = gwf_guias_check( $post_id, $type );
	if ( 'publish' !== get_post_status( $post_id ) && $check['missing'] ) {
		$notices[] = 'Para poder publicar falta: ' . implode( ', ', $check['missing'] ) . '.';
	}
	gwf_guias_flash( 'warning', array_merge( $notices, $check['warnings'] ) );
}
add_action( 'acf/save_post', 'gwf_guias_after_save', 20 );

/** Muestra los mensajes guardados en la pantalla del editor. */
function gwf_guias_admin_notices() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->post_type, GWF_GUIAS_TYPES, true ) ) {
		return;
	}
	foreach ( array( 'error', 'warning' ) as $type ) {
		$key      = 'gwf_guias_' . $type . '_' . get_current_user_id();
		$messages = get_transient( $key );
		if ( $messages ) {
			delete_transient( $key );
			echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible"><p>' . implode( '</p><p>', array_map( 'esc_html', (array) $messages ) ) . '</p></div>';
		}
	}
}
add_action( 'admin_notices', 'gwf_guias_admin_notices' );

/** Con la guía bloqueada en pendiente, WordPress no debe decir «Entrada publicada». */
function gwf_guias_updated_messages( $messages ) {
	foreach ( GWF_GUIAS_TYPES as $type ) {
		$messages[ $type ] = array(
			1  => 'Guía actualizada.',
			4  => 'Guía actualizada.',
			6  => 'Guía publicada.',
			7  => 'Guía guardada.',
			8  => 'Guía enviada a revisión.',
			9  => 'Guía programada.',
			10 => 'Borrador actualizado.',
		);
	}
	return $messages;
}
add_filter( 'post_updated_messages', 'gwf_guias_updated_messages' );

/** Si la regla la dejó en pendiente, el mensaje de «publicada» se cambia por el de «enviada a revisión». */
function gwf_guias_redirect_message( $location, $post_id ) {
	if ( in_array( get_post_type( $post_id ), GWF_GUIAS_TYPES, true ) && 'pending' === get_post_status( $post_id ) ) {
		$location = add_query_arg( 'message', 8, remove_query_arg( 'message', $location ) );
	}
	return $location;
}
add_filter( 'redirect_post_location', 'gwf_guias_redirect_message', 10, 2 );
