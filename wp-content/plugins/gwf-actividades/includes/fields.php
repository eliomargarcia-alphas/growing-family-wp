<?php
/**
 * Campos de la actividad (ACF gratuito, definidos en código). Sin repetidores: los pasos y los materiales
 * son texto por líneas («a | b | c»), y las imágenes de los pasos van en 8 huecos fijos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_ACT_STEP_IMAGES = 8;

function gwf_act_field( $name, $label, $type, $extra = array() ) {
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

function gwf_act_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$location = array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'actividad' ) ) );

	$step_images = array();
	for ( $i = 1; $i <= GWF_ACT_STEP_IMAGES; $i++ ) {
		$step_images[] = gwf_act_field( 'gwf_act_paso_img_' . $i, 'Imagen del paso ' . $i, 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => '25' ) ) );
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_actividad',
			'title'    => 'Actividad',
			'location' => $location,
			'fields'   => array_merge(
				array(
					gwf_act_field( 'gwf_act_tab_ficha', 'Ficha', 'tab' ),
					gwf_act_field( 'gwf_act_minutos', 'Minutos que lleva', 'number', array( 'min' => 1, 'required' => 1, 'instructions' => 'La edad y «Qué trabaja» se marcan en sus casillas del lateral.' ) ),
					gwf_act_field( 'gwf_act_materiales', 'Materiales (de casa)', 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => 'Uno por línea. Nada que haya que comprar.' ) ),
					gwf_act_field( 'gwf_act_participantes', 'Participantes', 'text', array( 'default_value' => '1 adulto y 1 o más niños' ) ),
					gwf_act_field( 'gwf_act_lugar', 'Lugar', 'text', array( 'placeholder' => 'Mesa' ) ),

					gwf_act_field( 'gwf_act_tab_pasos', 'Pasos', 'tab' ),
					gwf_act_field( 'gwf_act_pasos', 'La actividad completa', 'textarea', array( 'rows' => 8, 'new_lines' => '', 'required' => 1, 'instructions' => 'Un paso por línea: Título | Texto | Para ti (consejo para la madre o el padre, opcional). Siempre con la persona adulta: nunca a solas ni frente a una pantalla.' ) ),
				),
				$step_images,
				array(
					gwf_act_field( 'gwf_act_tab_imprimible', 'Imprimible', 'tab' ),
					gwf_act_field( 'gwf_act_imprimible', 'PDF imprimible', 'file', array( 'return_format' => 'id', 'mime_types' => 'pdf', 'instructions' => 'Se envía por correo a quien lo pide; nunca se enlaza en la página ni se indexa.' ) ),
					gwf_act_field( 'gwf_act_imprimible_preview', 'Vista previa del imprimible', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
					gwf_act_field( 'gwf_act_imprimible_titulo', 'Título del bloque', 'text', array( 'placeholder' => 'Descarga las caras y el guion para imprimir' ) ),
					gwf_act_field( 'gwf_act_imprimible_texto', 'Texto del bloque', 'textarea', array( 'rows' => 2, 'new_lines' => '', 'default_value' => 'Te lo enviamos por correo.' ) ),

					gwf_act_field( 'gwf_act_tab_porque', 'Qué trabaja y por qué', 'tab' ),
					gwf_act_field( 'gwf_act_porque_titulo', 'Pregunta', 'text', array( 'placeholder' => '¿Por qué jugar con marionetas a los 5 años?' ) ),
					gwf_act_field( 'gwf_act_porque', 'Respuesta', 'textarea', array( 'rows' => 4, 'new_lines' => 'wpautop' ) ),
					gwf_act_field( 'gwf_act_fuente', 'Fuente', 'text', array( 'instructions' => 'Título | URL' ) ),

					gwf_act_field( 'gwf_act_tab_enlaces', 'Enlaces y revisión', 'tab' ),
					gwf_act_field( 'gwf_act_pilar', 'Pilar de la edad', 'post_object', array( 'post_type' => array( 'desarrollo' ), 'return_format' => 'id', 'allow_null' => 1, 'instructions' => 'Si se deja vacío, se usa el pilar publicado de la misma edad (si hay uno).' ) ),
					gwf_act_field( 'gwf_act_revisado_por', 'Revisado por (opcional)', 'user', array( 'allow_null' => 1, 'return_format' => 'id', 'instructions' => 'Solo si el tema pide revisión experta.' ) ),
					gwf_act_field( 'gwf_act_fecha_revision', 'Fecha de revisión', 'date_picker', array( 'display_format' => 'd/m/Y', 'return_format' => 'Ymd' ) ),
				)
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_gwf_actividad_listado',
			'title'    => 'Listado',
			'position' => 'side',
			'location' => $location,
			'fields'   => array( gwf_act_field( 'gwf_act_destacado', 'Destacado', 'true_false', array( 'ui' => 1, 'instructions' => 'Aparece primero en su listado.' ) ) ),
		)
	);
}
add_action( 'acf/init', 'gwf_act_register_fields' );
