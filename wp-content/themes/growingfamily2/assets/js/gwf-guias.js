/* ==========================================================================
   Guías: índice que marca la sección en lectura, barra fija de móvil y vídeo
   que solo carga YouTube al pulsar. Se carga con defer solo en las guías.
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

	// Vídeo: la miniatura se cambia por el reproductor solo al pulsar (sin peso de YouTube al cargar).
	function initVideo() {
		document.querySelectorAll('.gwf-g-video[data-yt]').forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				var iframe = document.createElement('iframe');
				iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(link.getAttribute('data-yt')) + '?autoplay=1&rel=0';
				iframe.title = link.querySelector('.gwf-g-video-caption') ? link.querySelector('.gwf-g-video-caption').textContent : 'Vídeo';
				iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
				iframe.allowFullscreen = true;
				link.innerHTML = '';
				link.appendChild(iframe);
				link.removeAttribute('href');
			}, { once: true });
		});
	}

	function init() {
		initToc();
		initMobileBar();
		initVideo();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
