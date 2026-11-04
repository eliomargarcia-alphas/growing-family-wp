<?php
/**
 * Formulario de búsqueda (búsqueda normal de WordPress, ?s=): ventana de la cabecera y página de resultados.
 *
 * @param array $args { id: id del campo (único en la página), keep_type: conservar el filtro ?tipo= }
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_id = $args['id'] ?? 'gwf-search-input';
?>
<form role="search" method="get" class="gwf-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $gwf_id ); ?>">Buscar en Growing Family</label>
	<svg viewBox="0 0 24 24" width="20" height="20" class="gwf-search-icon" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
	<input type="search" id="<?php echo esc_attr( $gwf_id ); ?>" name="s" placeholder="Buscar cuentos, guías, artículos…" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off" required>
	<?php if ( ! empty( $args['keep_type'] ) && gwf_search_current_type() ) : // En la página de resultados se busca dentro del filtro elegido. ?>
		<input type="hidden" name="tipo" value="<?php echo esc_attr( gwf_search_current_type() ); ?>">
	<?php endif; ?>
	<button type="submit" class="gwf-search-submit">Buscar</button>
</form>
