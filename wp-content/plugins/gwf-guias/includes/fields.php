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

/** Campo de título de una sección, con el de por defecto ya escrito (GWF_GUIAS_SECTION_TITLES, includes/template-tags.php). */
function gwf_guias_title_field( $section, $label, $extra_help = '' ) {
	$def = GWF_GUIAS_SECTION_TITLES[ $section ];
	return gwf_guias_field(
		$def['field'],
		$label,
		'text',
		array(
			'default_value' => $def['default'],
			'placeholder'   => $def['default'],
			'instructions'  => trim( 'Puedes cambiarlo. Vacío: se usa «' . $def['default'] . '». ' . $extra_help ),
		)
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
	// Textos de bloque: editor visual sencillo (negrita, cursiva, listas, enlaces). El texto guardado antes (párrafos separados
	// por una línea en blanco) se sigue viendo igual: la plantilla lo pasa por gwf_guias_rich().
	$rich        = array( 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 );
	$inline_help = ' Negrita: <strong>texto</strong> o **texto**.';

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

	// Guía de problema: bloques 2 a 13 en el orden de la página (el 4, «Por qué pasa a esta edad», va en el editor).
	acf_add_local_field_group(
		array(
			'key'        => 'group_gwf_guia_problema',
			'title'      => 'Guía de problema: bloques',
			'menu_order' => 1,
			'location'   => gwf_guias_location( 'guia' ),
			'fields'     => array(
				gwf_guias_field( 'gwf_respuesta', '2. Respuesta directa', 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Obligatoria. 2 o 3 frases que responden la pregunta del título. También se usa como meta descripción.' ) ),
				gwf_guias_title_field( 'lo-que-intentaste', '3. Lo que ya intentaste: título' ),
				gwf_guias_field( 'gwf_ya_intentaste', '3. Lo que ya intentaste: texto', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Obligatorio.' ) ) ),
				gwf_guias_title_field( 'por-que-pasa', '4. Por qué pasa a esta edad: título', 'El texto se escribe en el editor de arriba, con subtítulos «Encabezado 2» que son preguntas de la familia. Si el editor está vacío, el bloque no aparece.' ),
				gwf_guias_title_field( 'que-hacer', '5. Qué hacer esta noche: título' ),
				gwf_guias_field( 'gwf_pasos_intro', '5. Qué hacer esta noche: texto antes de los pasos', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional: solo si el texto de la guía lo trae (por ejemplo, «Elige un solo momento… y prueba esto:»).' ) ) ),
				gwf_guias_field( 'gwf_pasos', '5. Qué hacer esta noche: pasos', 'textarea', array( 'rows' => 6, 'new_lines' => '', 'instructions' => 'Al menos 3 pasos. ' . $lines_help . ' Formato: Título del paso | Texto del paso. El título sale en negrita.' . $inline_help ) ),
				gwf_guias_field( 'gwf_pasos_nota', '5. Qué hacer esta noche: texto después de los pasos', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional: solo si el texto de la guía trae párrafos después de los pasos.' ) ) ),
				gwf_guias_title_field( 'si-no-funciona', '5b. Si aun así no funciona: título' ),
				gwf_guias_field( 'gwf_si_no_funciona', '5b. Si aun así no funciona: texto', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional: solo si el texto de la guía lo trae. Va justo después de los pasos. Si está vacío, el bloque no aparece.' ) ) ),
				gwf_guias_title_field( 'cuando-consultar', '6. Cuándo consultar: título' ),
				gwf_guias_field( 'gwf_cuando_consultar', '6. Cuándo consultar: texto', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Obligatorio. Va en un recuadro azul claro, al final de la guía. Las señales, como lista con viñetas.' ) ) ),
				gwf_guias_title_field( 'que-evitar', '7. Qué conviene evitar: título' ),
				gwf_guias_field( 'gwf_evitar', '7. Qué conviene evitar: lista', 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => 'Al menos 2. ' . $lines_help . $inline_help ) ),
				gwf_guias_field( 'gwf_evitar_nota', '7. Qué conviene evitar: texto después de la lista', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional: solo si el texto de la guía trae párrafos después de la lista.' ) ) ),
				gwf_guias_title_field( 'desacuerdo', '8. Si cada adulto responde distinto: título' ),
				gwf_guias_field( 'gwf_desacuerdo', '8. Si cada adulto responde distinto: texto', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional según el tema: si está vacío, el bloque no aparece.' ) ) ),
				// 9. Un cuento para esta noche: el cuento se elige en el cuento («Guía relacionada», plugin gwf-cuentos); aquí solo el título.
				gwf_guias_title_field( 'cuento', '9. Un cuento para esta noche: título', 'El cuento no se elige aquí: se enlaza desde el propio cuento, en su campo «Guía relacionada». Si ningún cuento eligió esta guía, el bloque no aparece.' ),
				gwf_guias_title_field( 'sostener', '9b. Cómo sostenerlo en el tiempo: título' ),
				gwf_guias_field( 'gwf_sostener', '9b. Cómo sostenerlo en el tiempo: texto', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Opcional: solo si el texto de la guía lo trae. Va después del cuento. Si está vacío, el bloque no aparece.' ) ) ),
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

	// Secciones adicionales (las dos clases de guía): lo que trae el texto de una guía y no tiene bloque propio.
	acf_add_local_field_group(
		array(
			'key'        => 'group_gwf_guia_extra',
			'title'      => 'Guía: secciones adicionales',
			'menu_order' => 2,
			'location'   => gwf_guias_location( GWF_GUIAS_TYPES ),
			'fields'     => array(
				gwf_guias_field(
					'gwf_secciones_extra',
					'Secciones adicionales',
					'wysiwyg',
					array(
						'tabs'         => 'all',
						'toolbar'      => 'full',
						'media_upload' => 0,
						'instructions' => 'Opcional: solo para secciones que trae el texto de la guía y no encajan en ningún bloque. Cada sección empieza con su título en «Encabezado 2»; cada una sale como bloque propio y en el índice. Guía de problema: van después de «Si cada adulto responde distinto». Guía de desarrollo: después de «Cómo lo trabajamos».',
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
				gwf_guias_field( 'gwf_curriculo', '5. Cómo lo trabajamos en el currículo', 'wysiwyg', array_merge( $rich, array( 'instructions' => 'Obligatorio. Sin adelantar la app.' ) ) ),
			),
		)
	);
}
add_action( 'acf/init', 'gwf_guias_register_fields' );
