<?php
/**
 * El imprimible a cambio del correo.
 *
 * 1. El formulario de la actividad (correo + casilla de compromiso) se envía a admin-post.php (sin JS ni REST:
 *    SiteGround Antibot desafía /wp-json/ y las páginas van en caché).
 * 2. Se suscribe el correo a la lista de la lista de espera de Mailchimp (plugin Mailchimp for WP), con la etiqueta
 *    de la actividad y, si marcó la casilla, el interés «Disposición». Solo en producción: en staging se registra
 *    en el log (nunca añade contactos de prueba a la lista real), salvo que se defina GWF_ACT_MAILCHIMP_LIVE.
 * 3. WordPress envía un correo con el enlace de descarga.
 * 4. La descarga (?gwf_imprimible=<id>&k=<firma>) sirve el PDF con X-Robots-Tag: noindex; el PDF nunca se enlaza en la página.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_ACT_MC_LIST        = '13fb45b1fa'; // Lista «Growing Family» (la de la lista de espera).
const GWF_ACT_MC_COMMITMENT  = '2ca320de4b'; // Interés «Estoy dispuesta o dispuesto a poner yo esos diez minutos, casi cada noche».
const GWF_ACT_COMMITMENT_TXT = 'Estoy dispuesta o dispuesto a poner yo esos diez minutos, casi cada noche.';

/** ¿Se escribe en Mailchimp? Solo en growing.family (o si se fuerza con la constante). */
function gwf_act_mailchimp_live() {
	if ( defined( 'GWF_ACT_MAILCHIMP_LIVE' ) ) {
		return (bool) GWF_ACT_MAILCHIMP_LIVE;
	}
	return 'growing.family' === wp_parse_url( home_url(), PHP_URL_HOST );
}

/** PDF de la actividad (id de adjunto) o 0. */
function gwf_act_print_file( $post_id ) {
	$file = (int) get_post_meta( $post_id, 'gwf_act_imprimible', true );
	return ( $file && 'application/pdf' === get_post_mime_type( $file ) ) ? $file : 0;
}

/** Enlace de descarga firmado (el que va en el correo). */
function gwf_act_print_url( $post_id ) {
	return add_query_arg( array( 'gwf_imprimible' => $post_id, 'k' => gwf_act_print_key( $post_id ) ), home_url( '/' ) );
}

function gwf_act_print_key( $post_id ) {
	return substr( wp_hash( 'gwf-imprimible-' . $post_id ), 0, 16 );
}

/** Descarga: comprueba la firma y sirve el PDF sin indexar. */
function gwf_act_print_download() {
	if ( empty( $_GET['gwf_imprimible'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$post_id = absint( $_GET['gwf_imprimible'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$key     = isset( $_GET['k'] ) ? sanitize_text_field( wp_unslash( $_GET['k'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$file    = 'actividad' === get_post_type( $post_id ) && 'publish' === get_post_status( $post_id ) ? gwf_act_print_file( $post_id ) : 0;
	$path    = $file ? get_attached_file( $file ) : '';
	if ( ! $path || ! hash_equals( gwf_act_print_key( $post_id ), $key ) || ! is_readable( $path ) ) {
		status_header( 404 );
		nocache_headers();
		exit( 'El enlace no es válido.' );
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow', true );
	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: inline; filename="' . sanitize_file_name( get_post_field( 'post_name', $post_id ) ) . '.pdf"' );
	header( 'Content-Length: ' . filesize( $path ) );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'template_redirect', 'gwf_act_print_download', 1 );

/** Envío del formulario. */
function gwf_act_print_submit() {
	$post_id  = isset( $_POST['actividad'] ) ? absint( $_POST['actividad'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification -- formulario público en páginas en caché; se valida todo abajo.
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$commit   = ! empty( $_POST['compromiso'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$honeypot = ! empty( $_POST['web'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$back     = ( 'actividad' === get_post_type( $post_id ) ) ? get_permalink( $post_id ) : home_url( '/actividades/' );

	$result = 'ok';
	if ( $honeypot ) {
		$result = 'ok'; // A un bot no se le dice nada distinto.
	} elseif ( ! $post_id || 'publish' !== get_post_status( $post_id ) || ! gwf_act_print_file( $post_id ) ) {
		$result = 'error';
	} elseif ( ! is_email( $email ) ) {
		$result = 'correo';
	} elseif ( gwf_act_print_rate_limited() ) {
		$result = 'error';
	} else {
		gwf_act_print_subscribe( $email, $post_id, $commit );
		$result = gwf_act_print_send( $email, $post_id ) ? 'ok' : 'error';
	}
	wp_safe_redirect( add_query_arg( 'imprimible', $result, $back ) . '#imprimible' );
	exit;
}
add_action( 'admin_post_nopriv_gwf_act_imprimible', 'gwf_act_print_submit' );
add_action( 'admin_post_gwf_act_imprimible', 'gwf_act_print_submit' );

/** Máximo 5 envíos por hora y por IP. */
function gwf_act_print_rate_limited() {
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'gwf_act_rl_' . md5( $ip );
	$count = (int) get_transient( $key );
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	return $count >= 5;
}

/** Alta o actualización en Mailchimp: etiqueta de la actividad y, si la marcó, la casilla de compromiso. */
function gwf_act_print_subscribe( $email, $post_id, $commit ) {
	$tag  = 'actividad-' . get_post_field( 'post_name', $post_id );
	$args = array(
		'email_address' => $email,
		'status_if_new' => 'subscribed',
	);
	if ( $commit ) {
		$args['interests'] = array( GWF_ACT_MC_COMMITMENT => true ); // Nunca se desmarca si ya la tenía.
	}
	if ( ! gwf_act_mailchimp_live() || ! function_exists( 'mc4wp_get_api_v3' ) ) {
		error_log( sprintf( '[gwf-actividades] Mailchimp (no se envía fuera de producción): %s, etiqueta %s, compromiso %s', $email, $tag, $commit ? 'sí' : 'no' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		do_action( 'gwf_act_print_subscribed', $email, $post_id, $commit, false );
		return false;
	}
	try {
		$api = mc4wp_get_api_v3();
		$api->add_list_member( GWF_ACT_MC_LIST, $args );
		$api->update_list_member_tags( GWF_ACT_MC_LIST, $email, array( 'tags' => array( array( 'name' => $tag, 'status' => 'active' ) ) ) );
		do_action( 'gwf_act_print_subscribed', $email, $post_id, $commit, true );
		return true;
	} catch ( Exception $e ) {
		error_log( '[gwf-actividades] Error de Mailchimp: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return false; // El imprimible se envía igual.
	}
}

/** Correo con el enlace de descarga. */
function gwf_act_print_send( $email, $post_id ) {
	$title   = get_the_title( $post_id );
	$subject = 'Tu imprimible: ' . wp_strip_all_tags( $title );
	$body    = '<p>¡Hola!</p>'
		. '<p>Aquí tienes el imprimible de <strong>' . esc_html( $title ) . '</strong>:</p>'
		. '<p><a href="' . esc_url( gwf_act_print_url( $post_id ) ) . '">Descargar el imprimible (PDF)</a></p>'
		. '<p>La actividad completa sigue aquí: <a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $title ) . '</a></p>'
		. '<p>Que lo disfruten juntos.<br>Equipo de Growing Family</p>';
	return wp_mail( $email, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );
}
