<?php
/**
 * Textos globales de los cuentos (Cuentos → Ajustes), editables sin tocar cada cuento. Sustituye a la página de
 * opciones de ACF PRO: recomendaciones del espacio (pantalla «Antes de empezar»), «Cómo contarlo» por defecto y
 * el texto del puente al método (página del cuento y pantalla final).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GWF_CUE_OPTION = 'gwf_cuentos_textos';

/** Textos con su valor por defecto (borradores [PRUEBA] hasta tener los definitivos). */
function gwf_cue_texts() {
	$defaults = array(
		'espacio'       => "[PRUEBA] Baja la luz y deja el móvil lejos.\n[PRUEBA] Siéntate a su lado, a su altura.\n[PRUEBA] Cuéntalo tú, con tu voz: no hace falta leerlo perfecto.",
		'como_contarlo' => '[PRUEBA] Lee despacio y deja pausas. Cuando veas una pregunta, házsela y escucha su respuesta sin corregirla. Si te pide repetir una página, repítela.',
		'puente'        => 'En Growing Family hay un cuento así cada noche.',
	);
	$saved = get_option( GWF_CUE_OPTION, array() );
	foreach ( $defaults as $key => $value ) {
		if ( isset( $saved[ $key ] ) && '' !== trim( (string) $saved[ $key ] ) ) {
			$defaults[ $key ] = (string) $saved[ $key ];
		}
	}
	return $defaults;
}

function gwf_cue_settings_menu() {
	add_submenu_page( 'edit.php?post_type=cuento', 'Ajustes de los cuentos', 'Ajustes', 'manage_options', 'gwf-cuentos-ajustes', 'gwf_cue_settings_page' );
}
add_action( 'admin_menu', 'gwf_cue_settings_menu' );

function gwf_cue_settings_register() {
	register_setting(
		'gwf_cuentos',
		GWF_CUE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => function ( $value ) {
				$value = is_array( $value ) ? $value : array();
				return array_map( 'sanitize_textarea_field', array_intersect_key( $value, array_flip( array( 'espacio', 'como_contarlo', 'puente' ) ) ) );
			},
		)
	);
}
add_action( 'admin_init', 'gwf_cue_settings_register' );

function gwf_cue_settings_page() {
	$texts  = gwf_cue_texts();
	$fields = array(
		'espacio'       => array( 'Recomendaciones del espacio', 'Pantalla «Antes de empezar». Una recomendación por línea.' ),
		'como_contarlo' => array( '«Cómo contarlo» por defecto', 'Se usa cuando el cuento no tiene su propio «Cómo contarlo».' ),
		'puente'        => array( 'Puente al método', 'Página del cuento y pantalla final.' ),
	);
	?>
	<div class="wrap">
		<h1>Ajustes de los cuentos</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'gwf_cuentos' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $field ) : ?>
					<tr>
						<th scope="row"><label for="gwf-cue-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
						<td>
							<textarea id="gwf-cue-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( GWF_CUE_OPTION . '[' . $key . ']' ); ?>" rows="4" class="large-text"><?php echo esc_textarea( $texts[ $key ] ); ?></textarea>
							<p class="description"><?php echo esc_html( $field[1] ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
