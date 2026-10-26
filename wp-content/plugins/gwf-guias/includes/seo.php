<?php
/**
 * SEO de guías y pilares en Yoast (etapa 7): title «<Pregunta de la familia> | Growing Family» de hasta 60
 * caracteres y meta description desde la respuesta directa (o la definición del pilar), recortada a 155.
 * Si el equipo escribe un title o una descripción propios en Yoast, se respetan.
 * Guías, pilares y sus taxonomías van en el sitemap de Yoast (ninguno tiene noindex en sus ajustes).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** ID de la guía o pilar que se está viendo, o 0. */
function gwf_guias_seo_post() {
	return is_singular( GWF_GUIAS_TYPES ) ? get_queried_object_id() : 0;
}

function gwf_guias_seo_title( $title ) {
	$post_id = gwf_guias_seo_post();
	if ( ! $post_id || get_post_meta( $post_id, '_yoast_wpseo_title', true ) ) {
		return $title;
	}
	$question = wp_strip_all_tags( get_the_title( $post_id ) );
	$full     = $question . ' | Growing Family';
	// Si con la marca pasa de 60, va solo la pregunta (Google cortaría la marca de todas formas).
	return mb_strlen( $full ) <= 60 ? $full : $question;
}
add_filter( 'wpseo_title', 'gwf_guias_seo_title' );

function gwf_guias_seo_description( $desc ) {
	$post_id = gwf_guias_seo_post();
	if ( ! $post_id || get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ) {
		return $desc;
	}
	$text = get_post_meta( $post_id, 'guia' === get_post_type( $post_id ) ? 'gwf_respuesta' : 'gwf_definicion', true );
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $text ) ) );
	return $text ? mb_strimwidth( $text, 0, 155, '…' ) : $desc;
}
add_filter( 'wpseo_metadesc', 'gwf_guias_seo_description' );
add_filter( 'wpseo_opengraph_desc', 'gwf_guias_seo_description' );
