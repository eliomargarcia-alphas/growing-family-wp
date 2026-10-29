/* ==========================================================================
   Cuento (single-cuento.php): selector Ana / Max. Se carga con defer solo en los cuentos.
   Las etapas siguientes añaden aquí «Antes de empezar», el modo lectura, el modo nocturno y la pantalla final.
   ========================================================================== */

(function () {
	'use strict';

	var main = document.querySelector('main[data-prota]');
	if (!main) return;

	document.documentElement.classList.add('gwf-cue-js');

	var KEY = 'gwf_cuento_prota';
	function store(value) {
		try { localStorage.setItem(KEY, value); } catch (e) { /* navegador sin almacenamiento */ }
	}
	function stored() {
		try { return localStorage.getItem(KEY); } catch (e) { return null; }
	}

	/* Selector: cambia nombres, palabras con género, portada e ilustraciones de todas las páginas a la vez. */
	function setProta(prota, remember) {
		if (prota !== 'ana' && prota !== 'max') return;
		main.setAttribute('data-prota', prota);
		main.querySelectorAll('.gwf-v').forEach(function (el) {
			el.textContent = el.getAttribute('data-' + prota);
		});
		main.querySelectorAll('.gwf-cue-prota').forEach(function (btn) {
			btn.setAttribute('aria-pressed', btn.getAttribute('data-prota') === prota ? 'true' : 'false');
		});
		if (remember) store(prota);
	}

	main.querySelectorAll('.gwf-cue-prota').forEach(function (btn) {
		btn.addEventListener('click', function () { setProta(btn.getAttribute('data-prota'), true); });
	});

	// Al cargar: ?con=max en un enlace compartido manda; si no, la elección guardada para los siguientes cuentos.
	var param = (window.location.search.match(/[?&]con=(ana|max)\b/) || [])[1];
	var initial = param || stored();
	if (initial && initial !== main.getAttribute('data-prota')) setProta(initial, false);
})();
