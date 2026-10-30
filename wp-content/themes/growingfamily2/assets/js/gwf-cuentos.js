/* ==========================================================================
   Cuento (single-cuento.php): selector Ana / Max y «Antes de empezar». Se carga con defer solo en los cuentos.
   Las etapas siguientes añaden aquí el modo lectura, el modo nocturno y la pantalla final.
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

	/* Medición (GA4): como en las guías, con el cuento de origen; sin gtag (staging) queda en dataLayer. */
	function track(name, params) {
		var data = { guia_origen: main.getAttribute('data-gwf-origen'), tipo_contenido: 'cuento' };
		Object.keys(params || {}).forEach(function (k) { data[k] = params[k]; });
		if (typeof window.gtag === 'function') {
			window.gtag('event', name, data);
		} else {
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push(Object.assign({ event: name }, data));
		}
	}

	/* Empezar a leer. Hasta que exista el modo lectura (etapa 5), muestra el cuento completo en la página (#leer). */
	function startReading() {
		main.classList.add('gwf-cue-inline');
		var story = document.getElementById('leer');
		if (story) story.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	/* Pantalla 2 · Antes de empezar: la primera vez al pulsar «Comenzar el cuento»; después, solo con «¿Cómo contarlo?». */
	var SEEN = 'gwf_cuento_antes_visto';
	var dialog = document.getElementById('como-contarlo');
	var canDialog = !!dialog && typeof dialog.showModal === 'function';

	function seen() {
		try { return localStorage.getItem(SEEN) === '1'; } catch (e) { return false; }
	}

	function openBefore(mode) {
		if (!canDialog) {
			if (mode === 'primera') startReading();
			return;
		}
		dialog.setAttribute('data-mode', mode);
		dialog.showModal();
		if (mode === 'primera') {
			// Ya vista: no vuelve a salir al pulsar «Comenzar el cuento» (solo con «¿Cómo contarlo?»).
			try { localStorage.setItem(SEEN, '1'); } catch (e) { /* sin almacenamiento: volverá a salir */ }
		}
		track('cuento_como_contarlo', { modo: mode });
	}

	if (canDialog) {
		dialog.querySelector('[data-gwf-cue="empezar"]').addEventListener('click', function (e) {
			e.preventDefault();
			dialog.close();
			startReading();
		});
		// La X la primera vez salta la pantalla y abre directamente la lectura; en el repaso solo cierra.
		dialog.querySelector('[data-gwf-cue="cerrar-antes"]').addEventListener('click', function () {
			var first = dialog.getAttribute('data-mode') === 'primera';
			dialog.close();
			if (first) startReading();
		});
		// Pulsar en el fondo oscurecido (fuera de la tarjeta) cierra, como la X.
		dialog.addEventListener('click', function (e) {
			var r = dialog.getBoundingClientRect();
			var outside = e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
			if (e.target === dialog && outside) dialog.querySelector('[data-gwf-cue="cerrar-antes"]').click();
		});
	}

	// «Comenzar el cuento» (botón de la página, panel lateral y barra de móvil) y «¿Cómo contarlo?».
	main.addEventListener('click', function (e) {
		var a = e.target.closest ? e.target.closest('a[href="#leer"], a[href="#como-contarlo"]') : null;
		if (!a || (dialog && dialog.contains(a))) return;
		e.preventDefault();
		if (a.getAttribute('href') === '#como-contarlo') {
			openBefore('repaso');
		} else if (seen()) {
			startReading();
		} else {
			openBefore('primera');
		}
	});
})();
