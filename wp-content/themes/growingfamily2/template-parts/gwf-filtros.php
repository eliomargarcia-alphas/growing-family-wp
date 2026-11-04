<?php
/**
 * Filtros de los listados (guías, pilares, actividades, cuentos, blog y búsqueda): columna izquierda en escritorio
 * ('side') o, por debajo de 1200px ('chips'), una barra fija «Filtrar» con el filtro activo y un panel que sube desde
 * abajo con todos los grupos. Cada filtro es un enlace a su página, como en la columna.
 *
 * @param array $args {
 *     @type string $mode   'side' o 'chips'.
 *     @type array  $groups Grupos { label, items: [ { label, url, active } ] }; el primer item de cada grupo es «Todos».
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
	<?php
	// Filtros activos (los que no son el «Todos» del grupo): se muestran en la barra y se quitan con la ✕.
	$gwf_active = array();
	foreach ( $args['groups'] as $gwf_group ) {
		foreach ( array_slice( $gwf_group['items'], 1 ) as $gwf_i ) {
			if ( $gwf_i['active'] ) {
				$gwf_active[] = array( 'label' => $gwf_i['label'], 'clear' => $gwf_group['items'][0]['url'] );
			}
		}
	}
	$gwf_all = $args['groups'][0]['items'][0]['url'] ?? home_url( '/' );
	?>
	<div class="gwf-l-filterbar d-xl-none">
		<a href="#gwf-filtros" class="gwf-l-filterbtn" role="button" aria-haspopup="dialog" aria-controls="gwf-filtros" data-gwf-filtros>
			<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M4 6h16M7 12h10M10 18h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			Filtrar
		</a>
		<?php foreach ( $gwf_active as $gwf_a ) : ?>
			<a class="gwf-l-filterchip" href="<?php echo esc_url( $gwf_a['clear'] ); ?>" aria-label="<?php echo esc_attr( 'Quitar el filtro: ' . $gwf_a['label'] ); ?>"><?php echo esc_html( $gwf_a['label'] ); ?> <span aria-hidden="true">✕</span></a>
		<?php endforeach; ?>
	</div>

	<dialog id="gwf-filtros" class="gwf-l-sheet d-xl-none" aria-labelledby="gwf-filtros-title">
		<div class="gwf-l-sheet-head">
			<p id="gwf-filtros-title" class="gwf-l-sheet-title">Filtrar</p>
			<button type="button" class="gwf-l-sheet-close" data-gwf-filtros-cerrar aria-label="Cerrar los filtros"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
		</div>
		<div class="gwf-l-filters">
			<?php foreach ( $args['groups'] as $gwf_group ) : ?>
				<div class="gwf-l-filter">
					<p class="gwf-l-filter-label"><?php echo esc_html( $gwf_group['label'] ); ?></p>
					<ul><?php $gwf_items( $gwf_group['items'] ); ?></ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $gwf_active ) : ?>
			<a class="gwf-l-sheet-all" href="<?php echo esc_url( $gwf_all ); ?>">Ver todos</a>
		<?php endif; ?>
	</dialog>

	<script>
	/* Panel de filtros: se abre con «Filtrar»; se cierra con la X, Esc, tocando fuera o con el «atrás» del móvil. Sin JS, el enlace lo muestra en la página (:target). */
	(function () {
		var sheet = document.getElementById('gwf-filtros');
		if (!sheet || typeof sheet.showModal !== 'function' || sheet.dataset.ready) return;
		sheet.dataset.ready = '1';
		var opener = document.querySelector('[data-gwf-filtros]');
		var y = 0;
		function lock(on) {
			var b = document.body;
			if (on) { y = window.scrollY; b.style.position = 'fixed'; b.style.top = -y + 'px'; b.style.left = '0'; b.style.right = '0'; }
			else { b.style.position = ''; b.style.top = ''; b.style.left = ''; b.style.right = ''; window.scrollTo(0, y); }
		}
		opener.addEventListener('click', function (e) {
			e.preventDefault();
			history.pushState({ gwfFiltros: true }, '');
			sheet.showModal();
			lock(true);
		});
		sheet.querySelector('[data-gwf-filtros-cerrar]').addEventListener('click', function () { sheet.close(); });
		sheet.addEventListener('click', function (e) {
			var r = sheet.getBoundingClientRect();
			if (e.target === sheet && (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom)) sheet.close();
		});
		sheet.addEventListener('close', function () {
			lock(false);
			if (history.state && history.state.gwfFiltros) history.back();
			opener.focus({ preventScroll: true });
		});
		window.addEventListener('popstate', function () { if (sheet.open) sheet.close(); });
	})();
	</script>
<?php endif; ?>
