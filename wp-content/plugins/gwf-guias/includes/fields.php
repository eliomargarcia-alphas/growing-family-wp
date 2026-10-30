<?php
/**
 * Campos de las guías (ACF gratuito, definidos en PHP y versionados en git).
 *
 * Las listas (pasos, qué evitar, fuentes, hitos) son hoy un texto con un elemento por línea y columnas
 * separadas por « | ». Cuando haya ACF PRO pasan a repetidores con el mismo nombre de campo; la plantilla
 * las lee con gwf_guias_items(), que entiende los dos formatos (ver includes/items.php).
 *
 * Los campos obligatorios no se marcan como "required" en ACF (impediría guardar borradores):
 * los exige la regla de publicación (includes/publish-rule.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Atajo para definir un campo con clave estable field_<nombre>. */
function gwf_guias_field( $name, $label, $type, $extra = array() ) {
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

function gwf_guias_location( $types ) {
	$location = array();
	foreach ( (array) $types as $type ) {
		$location[] = array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $type ) );
	}
	return $location;
}

function gwf_guias_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$lines_help = 'Un elemento por línea.';

	// Grupo compartido: revisión experta, fuentes y enlaces.
	acf_add_local_field_group(
		array(
			'key'        => 'group_gwf_guia_base',
			'title'      => 'Guía: revisión, fuentes y enlaces',
			'menu_order' => 0,
			'position'   => 'normal',
			'location'   => gwf_guias_location( GWF_GUIAS_TYPES ),
			'fields'     => array(
				gwf_guias_field(
					'gwf_revisado_por',
					'Revisado por',
					'user',
					array(
						'role'          => array( 'gwf_revisor' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'instructions'  => 'Obligatorio para publicar. Solo usuarios con el rol «Revisor experto».',
						'wrapper'       => array( 'width' => '50' ),
					)
				),
				gwf_guias_field(
					'gwf_fecha_revision',
					'Fecha de revisión',
					'date_picker',
					array(
						'display_format' => 'j/m/Y',
						'return_format'  => 'Ymd',
						'first_day'      => 1,
						'instructions'   => 'Obligatoria para publicar. Es la fecha que se muestra en la guía.',
						'wrapper'        => array( 'width' => '50' ),
					)
				),
				gwf_guias_field(
					'gwf_aprobar',
					'Aprobar la revisión ahora',
					'true_false',
					array(
						'ui'           => 1,
						'instructions' => 'Para el revisor: al guardar, registra tu nombre y la fecha de hoy como revisión del contenido actual.',
					)
				),
				gwf_guias_field(
					'gwf_fuentes',
					'Fuentes',
					'textarea',
					array(
						'rows'         => 5,
						'new_lines'    => '',
						'instructions' => 'Al menos 2 para publicar. ' . $lines_help . ' Formato: Título | Organismo | URL',
						'placeholder'  => 'Desarrollo emocional en la primera infancia | Center on the Developing Child, Harvard | https://developingchild.harvard.edu/…',
					)
				),
				gwf_guias_field(
					'gwf_guias_relacionadas',
					'Guías relacionadas',
					'relationship',
					array(
						'post_type'     => array( 'guia' ),
						'filters'       => array( 'search', 'taxonomy' ),
						'return_format' => 'id',
						'instructions'  => 'Opcional: si se deja vacío, se completan solas con guías del mismo tema y edad.',
					)
				),
				gwf_guias_field(
					'gwf_notas',
					'Notas de padre a padre',
					'relationship',
					array(
						'post_type'     => array( 'post' ),
						'post_status'   => array( 'publish', 'draft' ),
						'filters'       => array( 'search', 'taxonomy' ),
						'return_format' => 'id',
						'instructions'  => 'Opcional: notas del blog de este tema.',
					)
				),
			),
		)
	);

	// Guía de problema: bloques 2 a 13 de la Estrategia v1.6 (el 5 va en el editor).
	acf_add_local_field_group(
		array(
			'key'        => 'group_gwf_guia_problema',
			'title'      => 'Guía de problema: bloques',
			'menu_order' => 1,
			'location'   => gwf_guias_location( 'guia' ),
			'fields'     => array(
				gwf_guias_field( 'gwf_respuesta', '2. Respuesta directa', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Obligatoria. 2 o 3 frases que responden la pregunta del título. También se usa como meta descripción.' ) ),
				gwf_guias_field( 'gwf_ya_intentaste', '3. Reconocer lo que ya intentaste', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop', 'instructions' => 'Obligatorio.' ) ),
				gwf_guias_field( 'gwf_cuando_consultar', '4. Cuándo consultar', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop', 'instructions' => 'Obligatorio. Va en un recuadro naranja, arriba en móvil.' ) ),
				gwf_guias_field( 'gwf_pasos', '6. Qué hacer esta noche', 'textarea', array( 'rows' => 6, 'new_lines' => '', 'instructions' => 'Al menos 3 pasos. ' . $lines_help . ' Formato: Título del paso | Texto del paso' ) ),
				gwf_guias_field( 'gwf_evitar', '7. Qué conviene evitar', 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => 'Al menos 2. ' . $lines_help ) ),
				gwf_guias_field( 'gwf_desacuerdo', '8. Madre y padre en desacuerdo', 'textarea', array( 'rows' => 3, 'new_lines' => 'wpautop', 'instructions' => 'Opcional según el tema: si está vacío, el bloque no aparece.' ) ),
				// 9. Un cuento para esta noche: se elige en el cuento («Guía relacionada», plugin gwf-cuentos).
				gwf_guias_field( 'gwf_video_url', '10. Vídeo: URL de YouTube', 'url', array( 'instructions' => 'Al publicar se avisa si falta.', 'wrapper' => array( 'width' => '50' ) ) ),
				gwf_guias_field( 'gwf_video_titulo', '10. Vídeo: título', 'text', array( 'wrapper' => array( 'width' => '50' ) ) ),
				gwf_guias_field( 'gwf_video_duracion', '10. Vídeo: duración (segundos)', 'number', array( 'min' => 1, 'wrapper' => array( 'width' => '50' ) ) ),
				gwf_guias_field( 'gwf_video_fecha', '10. Vídeo: fecha de subida', 'date_picker', array( 'display_format' => 'j/m/Y', 'return_format' => 'Ymd', 'first_day' => 1, 'wrapper' => array( 'width' => '50' ) ) ),
				gwf_guias_field(
					'gwf_pilar',
					'13. Pilar (guía de desarrollo)',
					'post_object',
					array(
						'post_type'     => array( 'desarrollo' ),
						'post_status'   => array( 'publish', 'draft', 'pending' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'instructions'  => 'Se avisa si falta.',
					)
				),
			),
		)
	);

	// Guía de desarrollo (pilar): bloques 2, 3 y 5 de la sección 7 (el 4 va en el editor).
	acf_add_local_field_group(
		array(
			'key'        => 'group_gwf_guia_desarrollo',
			'title'      => 'Guía de desarrollo: bloques',
			'menu_order' => 1,
			'location'   => gwf_guias_location( 'desarrollo' ),
			'fields'     => array(
				gwf_guias_field( 'gwf_definicion', '2. Definición', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Obligatoria. 2 o 3 frases claras, citables tal cual.' ) ),
				gwf_guias_field( 'gwf_hitos', '3. Hitos', 'textarea', array( 'rows' => 6, 'new_lines' => '', 'instructions' => $lines_help . ' Formato: Hito | Rango de edad | Nota de variabilidad' ) ),
				gwf_guias_field( 'gwf_curriculo', '5. Cómo lo trabajamos en el currículo', 'textarea', array( 'rows' => 4, 'new_lines' => 'wpautop', 'instructions' => 'Obligatorio. Sin adelantar la app.' ) ),
			),
		)
	);
}
add_action( 'acf/init', 'gwf_guias_register_fields' );
