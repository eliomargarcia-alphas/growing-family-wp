/* ==========================================================================
   Cuento (single-cuento.php): selector Ana / Max, «Antes de empezar», modo lectura, modo nocturno y pantalla final.
   Se carga con defer solo en los cuentos.
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

	/* Pantalla 3 · Modo lectura: capa a pantalla completa con el mismo HTML del cuento (#leer se mueve dentro y vuelve
	   a su sitio al cerrar). La dirección pasa a #leer y #pagina-n: el botón «atrás» del móvil cierra la lectura. */
	var reader = document.getElementById('gwf-cue-reader');
	var story = document.getElementById('leer');
	var pages = story ? story.querySelectorAll('.gwf-cue-page') : [];
	var total = pages.length;
	var current = 0;      // Página abierta (0 = primera); al volver a abrir, se sigue donde se dejó.
	var finished = false; // Si se llegó al final (para no contar como abandono).
	var home = story ? story.parentNode : null;
	var homeNext = story ? story.nextSibling : null;
	var canReader = !!reader && typeof reader.showModal === 'function' && total > 0;

	/* Modo nocturno: la luna lo activa y la elección queda guardada para los siguientes cuentos. Sin elección guardada,
	   se propone solo si el dispositivo está en modo oscuro (a cualquier hora). */
	var NIGHT = 'gwf_cuento_noche';
	var nightBtn = reader ? reader.querySelector('[data-gwf-cue="noche"]') : null;

	function setNight(on) {
		reader.classList.toggle('is-night', on);
		nightBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
	}

	function initialNight() {
		var saved = null;
		try { saved = localStorage.getItem(NIGHT); } catch (e) { /* sin almacenamiento */ }
		if (saved === '1' || saved === '0') return saved === '1';
		return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
	}

	/* Pantalla encendida mientras se lee (Wake Lock); el navegador la suelta al ocultar la pestaña y se pide de nuevo. */
	var wakeLock = null;
	function keepAwake() {
		if (!('wakeLock' in navigator) || wakeLock || !reader.open) return;
		navigator.wakeLock.request('screen').then(function (lock) {
			wakeLock = lock;
			lock.addEventListener('release', function () { wakeLock = null; });
		}).catch(function () { /* sin permiso o batería baja: la pantalla se apaga como siempre */ });
	}
	function releaseAwake() {
		if (wakeLock) wakeLock.release().catch(function () {});
		wakeLock = null;
	}

	/* Pantalla 4 · Final. El «me gusta» es el mismo botón de WP ULike de la página: se trae aquí y vuelve al cerrar. */
	var end = reader ? reader.querySelector('.gwf-cue-end') : null;
	var like = main.querySelector('.gwf-g-like:not(.gwf-cue-end-like) .wpulike');
	var likeHome = like ? like.parentNode : null;

	function showEnd(on) {
		reader.classList.toggle('is-end', on);
		end.hidden = !on;
		if (like) (on ? end.querySelector('.gwf-cue-end-like') : likeHome).appendChild(like);
	}

	function pageHash(i) { return i === 0 ? '#leer' : '#pagina-' + (i + 1); }

	function showPage(i, announce) {
		current = Math.max(0, Math.min(total - 1, i));
		pages.forEach(function (pg, n) { pg.classList.toggle('is-current', n === current); });
		var label = (current + 1) + ' de ' + total;
		reader.querySelector('.gwf-cue-r-count').textContent = label;
		reader.querySelector('.gwf-cue-r-progress').textContent = label;
		reader.querySelectorAll('.gwf-cue-r-dots span').forEach(function (d, n) { d.classList.toggle('is-current', n === current); });
		reader.querySelector('[data-gwf-cue="anterior"]').disabled = current === 0;
		var next = reader.querySelector('[data-gwf-cue="siguiente"]');
		next.innerHTML = current === total - 1 ? 'Terminar <span aria-hidden="true">›</span>' : 'Siguiente <span aria-hidden="true">›</span>';
		reader.querySelector('.gwf-cue-r-stage').scrollTop = 0;
		if (announce) reader.querySelector('.gwf-cue-r-live').textContent = 'Página ' + label;
		if (history.state && history.state.gwfReader) history.replaceState({ gwfReader: true }, '', pageHash(current));
		if (announce) track('cuento_pagina', { pagina: current + 1, paginas: total });
	}

	function openReader(fromHash) {
		if (!canReader) {
			main.classList.add('gwf-cue-inline'); // Navegadores sin <dialog>: el cuento completo en la página.
			story.scrollIntoView({ behavior: 'smooth', block: 'start' });
			return;
		}
		if (reader.open) return; // «Empezar» desde «¿Cómo contarlo?» con la lectura ya abierta: se sigue leyendo.
		reader.querySelector('.gwf-cue-r-stage').appendChild(story);
		document.documentElement.classList.add('gwf-cue-reading');
		setNight(initialNight());
		keepAwake();
		if (!fromHash) history.pushState({ gwfReader: true }, '', pageHash(current));
		reader.showModal();
		showPage(current, false);
		reader.querySelector('.gwf-cue-r-live').textContent = 'Página ' + (current + 1) + ' de ' + total;
		// Pantalla completa real donde se puede (Android); en iPhone la capa ocupa toda la página.
		if (window.matchMedia('(pointer: coarse)').matches && reader.requestFullscreen) {
			reader.requestFullscreen().catch(function () { /* el navegador no lo permite: se queda la capa */ });
		}
		track('cuento_inicio', { pagina: current + 1, paginas: total });
	}

	// Cerrar con la X o Esc: si abrimos una entrada en el historial, se vuelve atrás y el popstate termina de cerrar.
	function closeReader() {
		if (history.state && history.state.gwfReader) {
			history.back();
		} else {
			finishClose();
		}
	}

	function finishClose() {
		if (!reader.open) return;
		if (document.fullscreenElement && document.exitFullscreen) document.exitFullscreen().catch(function () {});
		reader.close();
		releaseAwake();
		if (reader.classList.contains('is-end')) current = 0; // Cuento terminado: al volver a abrirlo, desde el principio.
		showEnd(false);
		home.insertBefore(story, homeNext);
		document.documentElement.classList.remove('gwf-cue-reading');
		if (/^#(leer|pagina-\d+|fin)$/.test(window.location.hash)) history.replaceState(null, '', window.location.pathname + window.location.search);
		if (!finished) track('cuento_cierre', { pagina: current + 1, paginas: total });
		var start = main.querySelector('.gwf-cue-start-btn');
		if (start) start.focus();
	}

	// Al pasar la última página: pantalla final en la misma capa (el «atrás» del móvil la cierra como la lectura).
	function finish() {
		finished = true;
		showEnd(true);
		if (history.state && history.state.gwfReader) history.replaceState({ gwfReader: true }, '', '#fin');
		end.scrollTop = 0;
		end.querySelector('.gwf-cue-end-title').focus();
		reader.querySelector('.gwf-cue-r-live').textContent = 'Fin del cuento';
		track('cuento_completado', { paginas: total });
	}

	function startReading() { openReader(false); }

	if (canReader) {
		// Puntos de progreso, uno por página.
		var dots = reader.querySelector('.gwf-cue-r-dots');
		for (var d = 0; d < total; d++) dots.appendChild(document.createElement('span'));

		reader.querySelector('[data-gwf-cue="anterior"]').addEventListener('click', function () { showPage(current - 1, true); });
		reader.querySelector('[data-gwf-cue="siguiente"]').addEventListener('click', function () {
			if (current === total - 1) { finish(); } else { showPage(current + 1, true); }
		});
		reader.querySelector('[data-gwf-cue="cerrar"]').addEventListener('click', closeReader);
		reader.querySelector('[data-gwf-cue="volver"]').addEventListener('click', closeReader);
		reader.querySelector('[data-gwf-cue="releer"]').addEventListener('click', function () {
			showEnd(false);
			showPage(0, true);
			reader.querySelector('[data-gwf-cue="siguiente"]').focus();
		});
		reader.querySelector('[data-gwf-cue="puente"]').addEventListener('click', function (e) {
			track('cuento_puente', { link_url: e.currentTarget.href, ubicacion: 'pantalla_final' });
		});
		var other = reader.querySelector('[data-gwf-cue="otro-cuento"]');
		if (other) {
			other.addEventListener('click', function () {
				track('cuento_siguiente', { cuento: other.getAttribute('data-gwf-slug'), link_url: other.href });
			});
		}
		reader.querySelector('[data-gwf-cue="ayuda"]').addEventListener('click', function () { openBefore('repaso'); });
		nightBtn.addEventListener('click', function () {
			var on = !reader.classList.contains('is-night');
			setNight(on);
			try { localStorage.setItem(NIGHT, on ? '1' : '0'); } catch (e) { /* sin almacenamiento: dura esta lectura */ }
			track('cuento_nocturno', { activo: on ? 'si' : 'no', pagina: current + 1 });
		});
		document.addEventListener('visibilitychange', function () {
			if (document.visibilityState === 'visible') keepAwake();
		});
		reader.addEventListener('cancel', function (e) { e.preventDefault(); closeReader(); }); // Esc
		window.addEventListener('popstate', function () { if (reader.open) finishClose(); });

		// Teclado: flechas para pasar de página (Esc lo maneja el diálogo).
		reader.addEventListener('keydown', function (e) {
			if (reader.classList.contains('is-end')) return;
			if (e.key === 'ArrowRight') { e.preventDefault(); reader.querySelector('[data-gwf-cue="siguiente"]').click(); }
			if (e.key === 'ArrowLeft' && current > 0) { e.preventDefault(); showPage(current - 1, true); }
		});

		// Deslizar en el móvil.
		var touchX = null;
		var touchY = null;
		reader.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; touchY = e.touches[0].clientY; }, { passive: true });
		reader.addEventListener('touchend', function (e) {
			if (touchX === null || reader.classList.contains('is-end')) return;
			var dx = e.changedTouches[0].clientX - touchX;
			var dy = e.changedTouches[0].clientY - touchY;
			touchX = null;
			if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;
			if (dx < 0) { reader.querySelector('[data-gwf-cue="siguiente"]').click(); } else if (current > 0) { showPage(current - 1, true); }
		});

		// Enlace compartido a #leer o #pagina-n: abre la lectura en esa página (el «atrás» la cierra sin salir).
		var m = window.location.hash.match(/^#(?:leer|pagina-(\d+))$/);
		if (m) {
			current = m[1] ? parseInt(m[1], 10) - 1 : 0;
			history.replaceState(null, '', window.location.pathname + window.location.search);
			history.pushState({ gwfReader: true }, '', pageHash(Math.max(0, Math.min(total - 1, current))));
			openReader(true);
		}
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
