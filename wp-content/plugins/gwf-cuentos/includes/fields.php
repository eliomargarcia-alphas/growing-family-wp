<?php
/**
 * Campos del cuento (ACF gratuito, en código). Las fuentes son texto por líneas («Título | Organismo | URL»), como en
 * las guías; con ACF PRO pasarán a un repetidor con el mismo nombre.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gwf_cue_field( $name, $label, $type, $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

function gwf_cue_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$location = array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'cuento' ) ) );

	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_cuento',
			'title'    => 'Cuento',
			'location' => $location,
			'fields'   => array(
				gwf_cue_field( 'gwf_cue_tab_pagina', 'Página del cuento', 'tab' ),
				gwf_cue_field( 'gwf_cue_protagonista', 'Protagonista por defecto', 'select', array( 'choices' => array( 'ana' => 'Ana', 'max' => 'Max' ), 'default_value' => 'ana', 'required' => 1, 'instructions' => 'Es la versión que queda en el HTML (la que leen Google y los asistentes de IA). Repartir los cuentos entre Ana y Max, nunca al azar.' ) ),
				gwf_cue_field( 'gwf_cue_ilustracion_alt', 'Ilustración alternativa (portada del otro protagonista)', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'La imagen destacada es la portada del protagonista por defecto. Si se deja vacía, la portada no cambia con el selector.' ) ),
				gwf_cue_field( 'gwf_cue_asi_empieza', 'Así empieza (resumen)', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'required' => 1, 'instructions' => 'Dos frases sobre de qué va el cuento, sin contar el final. Admite {Ana|Max}.' ) ),
				gwf_cue_field( 'gwf_cue_minutos', 'Minutos para contarlo', 'number', array( 'min' => 1, 'instructions' => 'Vacío: se calcula solo (unas 90 palabras por minuto, leído en voz alta).' ) ),

				gwf_cue_field( 'gwf_cue_tab_contar', 'Cómo contarlo', 'tab' ),
				gwf_cue_field( 'gwf_cue_como_contarlo', 'Cómo contarlo', 'textarea', array( 'rows' => 4, 'new_lines' => 'wpautop', 'instructions' => 'Pantalla «Antes de empezar». Vacío: se usa el texto por defecto de Cuentos → Ajustes.' ) ),

				gwf_cue_field( 'gwf_cue_tab_trabaja', 'Qué trabaja', 'tab' ),
				gwf_cue_field( 'gwf_cue_que_trabaja', 'Qué trabaja y por qué', 'textarea', array( 'rows' => 4, 'new_lines' => 'wpautop', 'required' => 1 ) ),
				gwf_cue_field( 'gwf_cue_fuentes', 'Fuentes', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'required' => 1, 'instructions' => 'Una por línea: Título | Organismo | URL' ) ),

				gwf_cue_field( 'gwf_cue_tab_enlaces', 'Enlaces y revisión', 'tab' ),
				gwf_cue_field( 'gwf_cue_guia', 'Guía relacionada', 'post_object', array( 'post_type' => array( 'guia' ), 'return_format' => 'id', 'allow_null' => 1, 'instructions' => 'Triángulo cuento – guía – pilar. Si falta, se avisa en el listado del administrador.' ) ),
				gwf_cue_field( 'gwf_cue_pilar', 'Pilar', 'post_object', array( 'post_type' => array( 'desarrollo' ), 'return_format' => 'id', 'allow_null' => 1 ) ),
				gwf_cue_field( 'gwf_cue_requiere_firma', 'Requiere firma experta', 'true_false', array( 'ui' => 1, 'instructions' => 'Solo temas delicados (una pérdida, miedos intensos…).' ) ),
				gwf_cue_field( 'gwf_cue_revisado_por', 'Revisado por', 'user', array( 'allow_null' => 1, 'return_format' => 'id', 'conditional_logic' => array( array( array( 'field' => 'field_gwf_cue_requiere_firma', 'operator' => '==', 'value' => '1' ) ) ) ) ),
				gwf_cue_field( 'gwf_cue_fecha_revision', 'Fecha de revisión', 'date_picker', array( 'display_format' => 'd/m/Y', 'return_format' => 'Ymd', 'conditional_logic' => array( array( array( 'field' => 'field_gwf_cue_requiere_firma', 'operator' => '==', 'value' => '1' ) ) ) ) ),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_cuento_listado',
			'title'    => 'Listado',
			'position' => 'side',
			'location' => $location,
			'fields'   => array( gwf_cue_field( 'gwf_cue_destacado', 'Destacado', 'true_false', array( 'ui' => 1, 'instructions' => 'Aparece primero en su listado.' ) ) ),
		)
	);
}
add_action( 'acf/init', 'gwf_cue_register_fields' );
