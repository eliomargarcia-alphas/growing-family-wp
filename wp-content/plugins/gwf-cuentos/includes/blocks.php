<?php
/**
 * Notas de lectura e ilustraciones por protagonista, sin ACF PRO: estilos de bloque propios, solo en el editor de cuentos.
 *
 * - Párrafo «Nota: pregunta» → «Pregunta para tu hija o hijo», discreta dentro de la página.
 * - Párrafo «Tono: …» (alegre, sorpresa, susurro, grito, triste) → el párrafo se lee en ese tono; lleva su icono delante.
 * - Imagen «Solo Ana» / «Solo Max» → se ve solo con ese protagonista; sin estilo, vale para los dos.
 *
 * Cada página del cuento es lo que hay entre dos bloques «Salto de página» (includes/content.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Tonos de lectura: clave => [nombre en el editor, texto que lee el lector de pantalla]. */
const GWF_CUE_TONES = array(
	'alegre'   => array( 'Tono: alegre', 'Con alegría' ),
	'sorpresa' => array( 'Tono: sorpresa', 'Con sorpresa' ),
	'susurro'  => array( 'Tono: susurro', 'En voz baja' ),
	'grito'    => array( 'Tono: grito', 'Gritando' ),
	'triste'   => array( 'Tono: triste', 'Con tristeza' ),
);

/** Estilos de bloque, registrados en JS solo al editar un cuento (en entradas y páginas no aparecen). */
function gwf_cue_editor_styles() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'cuento' !== $screen->post_type ) {
		return;
	}
	$styles = array( array( 'core/paragraph', 'gwf-pregunta', 'Nota: pregunta' ) );
	foreach ( GWF_CUE_TONES as $key => $tone ) {
		$styles[] = array( 'core/paragraph', 'gwf-tono-' . $key, $tone[0] );
	}
	$styles[] = array( 'core/image', 'gwf-solo-ana', 'Solo Ana' );
	$styles[] = array( 'core/image', 'gwf-solo-max', 'Solo Max' );

	$js = 'wp.domReady(function(){' . wp_json_encode( $styles ) . '.forEach(function(s){wp.blocks.registerBlockStyle(s[0],{name:s[1],label:s[2]});});});';
	wp_add_inline_script( 'wp-blocks', $js );

	// Cómo se ven en el editor: la nota con su raya azul, el tono con su etiqueta y las imágenes con su protagonista.
	$css = '.is-style-gwf-pregunta{border-left:4px solid #0049db;padding:.4em .8em;background:#eaf2fd}'
		. '.is-style-gwf-pregunta::before{content:"Pregunta para tu hija o hijo";display:block;font-size:.75em;text-transform:uppercase;color:#0049db}'
		. '[class*="is-style-gwf-tono-"]::before{display:inline-block;margin-right:.5em;padding:0 .4em;border-radius:4px;background:#eaf2fd;color:#0049db;font-size:.75em}'
		. '[class*="is-style-gwf-solo-"]{outline:2px dashed #0049db}';
	foreach ( GWF_CUE_TONES as $key => $tone ) {
		$css .= '.is-style-gwf-tono-' . $key . '::before{content:"' . $tone[0] . '"}';
	}
	$css .= '.is-style-gwf-solo-ana::before{content:"Solo Ana"}.is-style-gwf-solo-max::before{content:"Solo Max"}';
	wp_add_inline_style( 'wp-block-library', $css );
}
add_action( 'enqueue_block_editor_assets', 'gwf_cue_editor_styles' );
