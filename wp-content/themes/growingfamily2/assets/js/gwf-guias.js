/* ==========================================================================
   Guías: índice que marca la sección en lectura, barra fija de móvil, vídeo
   que solo carga YouTube al pulsar y eventos de GA4 (etapa 8). Se carga con
   defer en las guías, los pilares y las actividades.
   ========================================================================== */

(function () {
	'use strict';

	// Índice lateral: marca la sección que se está leyendo.
	function initToc() {
		var links = document.querySelectorAll('.gwf-g-toc a[href^="#"]');
		if (!links.length || !('IntersectionObserver' in window)) return;

		var byId = {};
		var sections = [];
		links.forEach(function (link) {
			var section = document.getElementById(link.getAttribute('href').slice(1));
			if (section) {
				byId[section.id] = link;
				sections.push(section);
			}
		});

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				links.forEach(function (l) { l.classList.remove('is-active'); });
				byId[entry.target.id].classList.add('is-active');
			});
		}, { rootMargin: '-110px 0px -60% 0px' });

		sections.forEach(function (s) { observer.observe(s); });
	}

	// Barra de móvil: «Índice de la guía» abre el desplegable además de llevar a él.
	function initMobileBar() {
		var tocLink = document.querySelector('.gwf-g-bar-toc');
		var details = document.getElementById('indice');
		if (!tocLink || !details) return;
		tocLink.addEventListener('click', function () { details.open = true; });

		// Al elegir una sección del desplegable, se cierra.
		details.querySelectorAll('a').forEach(function (a) {
			a.addEventListener('click', function () { details.open = false; });
		});
	}

	/* Medición (GA4). Cada evento lleva la guía de origen (slug) y su tipo.
	   En producción gtag existe (header.php) y lo envía; donde no hay etiqueta (staging) queda en dataLayer.
	   Con ?gwf_debug=1 en la URL, cada evento se escribe también en la consola. */
	var main = document.querySelector('main[data-gwf-origen]');
	var debug = /[?&]gwf_debug=1(&|$)/.test(window.location.search);

	function track(name, params) {
		var data = { guia_origen: main ? main.getAttribute('data-gwf-origen') : '', tipo_contenido: main ? main.getAttribute('data-gwf-tipo') : '' };
		Object.keys(params || {}).forEach(function (k) { data[k] = params[k]; });
		if (typeof window.gtag === 'function') {
			window.gtag('event', name, data);
		} else {
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push(Object.assign({ event: name }, data));
		}
		if (debug && window.console) console.log('[gwf-ga4]', name, data);
	}

	// Clics: puente al método, «Cuándo consultar» (índice lateral, índice de móvil o barra fija) y cuento.
	function initTracking() {
		// Imprimible de una actividad: envío del formulario, con la casilla de compromiso (la página navega a admin-post).
		document.querySelectorAll('.gwf-a-print-form').forEach(function (form) {
			form.addEventListener('submit', function () {
				var box = form.querySelector('input[name="compromiso"]');
				track('gwf_imprimible_envio', { compromiso: box && box.checked ? 'si' : 'no', transport_type: 'beacon' });
			});
		});

		document.addEventListener('click', function (e) {
			var a = e.target.closest ? e.target.closest('a') : null;
			if (!a) return;
			if (a.closest('.gwf-g-bridge')) {
				track('gwf_puente_click', { link_url: a.href });
			} else if (a.getAttribute('href') === '#cuando-consultar') {
				var where = a.closest('.gwf-g-bar') ? 'barra_fija' : (a.closest('.gwf-g-toc-mobile') ? 'indice_movil' : 'indice');
				track('gwf_cuando_consultar_click', { ubicacion: where });
			} else if (a.hasAttribute('data-gwf-cuento')) {
				// El bloque 9 (cuento) pone data-gwf-cuento cuando exista el plugin gwf-cuentos.
				track('gwf_cuento_click', { cuento: a.getAttribute('data-gwf-cuento'), link_url: a.href });
			}
		});
	}

	// API de YouTube: se descarga solo después de pulsar el vídeo, para medir el inicio y el 75 %.
	var ytReady = null;
	function loadYouTubeApi() {
		if (ytReady) return ytReady;
		ytReady = new Promise(function (resolve, reject) {
			if (window.YT && window.YT.Player) return resolve(window.YT);
			var prev = window.onYouTubeIframeAPIReady;
			window.onYouTubeIframeAPIReady = function () { if (prev) prev(); resolve(window.YT); };
			var s = document.createElement('script');
			s.src = 'https://www.youtube.com/iframe_api';
			s.async = true;
			s.onerror = reject;
			document.head.appendChild(s);
		});
		return ytReady;
	}

	function trackVideo(iframe, videoId) {
		var started = false;
		var sent75 = false;
		var timer = null;
		function start() {
			if (started) return;
			started = true;
			track('gwf_video_inicio', { video_id: videoId });
		}
		loadYouTubeApi().then(function (YT) {
			var player = new YT.Player(iframe, {
				events: {
					onStateChange: function (ev) {
						if (ev.data === YT.PlayerState.PLAYING) {
							start();
							if (!timer && !sent75) {
								timer = setInterval(function () {
									var d = player.getDuration();
									if (d && player.getCurrentTime() / d >= 0.75) {
										sent75 = true;
										clearInterval(timer);
										timer = null;
										track('gwf_video_75', { video_id: videoId });
									}
								}, 1000);
							}
						} else if (timer) {
							clearInterval(timer);
							timer = null;
						}
					}
				}
			});
		}).catch(start); // Si la API no carga (bloqueador), al menos cuenta el inicio con el clic.
	}

	// Vídeo: la miniatura se cambia por el reproductor solo al pulsar (sin peso de YouTube al cargar).
	function initVideo() {
		document.querySelectorAll('.gwf-g-video[data-yt]').forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				var iframe = document.createElement('iframe');
				iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(link.getAttribute('data-yt')) + '?autoplay=1&rel=0&enablejsapi=1&origin=' + encodeURIComponent(window.location.origin);
				iframe.title = link.querySelector('.gwf-g-video-caption') ? link.querySelector('.gwf-g-video-caption').textContent : 'Vídeo';
				iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
				iframe.allowFullscreen = true;
				link.innerHTML = '';
				link.appendChild(iframe);
				link.removeAttribute('href');
				trackVideo(iframe, link.getAttribute('data-yt'));
			}, { once: true });
		});
	}

	function init() {
		initToc();
		initMobileBar();
		initVideo();
		initTracking();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
