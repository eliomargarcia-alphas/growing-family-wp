<?php
/**
 * Filtros de los listados (guías, pilares y blog): columna izquierda en escritorio ('side') o chips en móvil ('chips').
 *
 * @param array $args {
 *     @type string $mode   'side' o 'chips'.
 *     @type array  $groups Grupos { label, items: [ { label, url, active } ] }.
 * }
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_items = function ( $items ) {
	foreach ( $items as $i ) {
		echo '<li><a href="' . esc_url( $i['url'] ) . '"' . ( $i['active'] ? ' class="is-active" aria-current="page"' : '' ) . '>' . esc_html( $i['label'] ) . '</a></li>';
	}
};
?>
<?php if ( 'side' === $args['mode'] ) : ?>
	<nav class="gwf-l-side" aria-label="Filtrar">
		<?php foreach ( $args['groups'] as $gwf_group ) : ?>
			<p class="gwf-l-side-title"><?php echo esc_html( $gwf_group['label'] ); ?></p>
			<ul><?php $gwf_items( $gwf_group['items'] ); ?></ul>
		<?php endforeach; ?>
	</nav>
<?php else : ?>
	<nav class="gwf-l-filters d-xl-none" aria-label="Filtrar">
		<?php foreach ( $args['groups'] as $gwf_group ) : ?>
			<div class="gwf-l-filter">
				<p class="gwf-l-filter-label"><?php echo esc_html( $gwf_group['label'] ); ?></p>
				<ul><?php $gwf_items( $gwf_group['items'] ); ?></ul>
			</div>
		<?php endforeach; ?>
	</nav>
<?php endif; ?>
