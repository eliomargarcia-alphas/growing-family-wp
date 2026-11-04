<?php header("Cache-Control: no-cache");

/**
 * Header file for the Twenty Twenty WordPress default theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<meta name="color-scheme" content="light">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php if ( 'growing.family' === wp_parse_url( home_url(), PHP_URL_HOST ) ) : // Analytics solo en producción, nunca en staging ?>
		<!-- Google tag (gtag.js): la cola dataLayer se crea al instante; la librería se descarga en la primera
		     interacción (scroll, toque, clic, tecla) o a los 5 s de cargar la página, para no competir con el renderizado. -->
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-VDD509FKDM');

		  (function () {
		    var loaded = false, events = ['scroll', 'pointerdown', 'keydown', 'touchstart', 'mousemove'];
		    function loadGtag() {
		      if (loaded) return;
		      loaded = true;
		      events.forEach(function (e) { window.removeEventListener(e, loadGtag, { passive: true }); });
		      var s = document.createElement('script');
		      s.async = true;
		      s.src = 'https://www.googletagmanager.com/gtag/js?id=G-VDD509FKDM';
		      document.head.appendChild(s);
		    }
		    events.forEach(function (e) { window.addEventListener(e, loadGtag, { passive: true, once: true }); });
		    window.addEventListener('load', function () { setTimeout(loadGtag, 5000); });
		  })();
		</script>
		<?php endif; ?>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>
		<!-- Header Navigation -->
	    <header class="header">
	        <div class="container header-container">
	            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
	                <img src="https://growing.family/wp-content/uploads/2026/08/logo.png" alt="Growing Family Logo" class="logo-img" width="562" height="160">
	            </a>

	            <nav class="nav">
	                <ul class="nav-list mb-0">
	                    <?php wp_nav_menu( array(
						    'menu'       => 54,
						    'container'  => false,
						    'items_wrap' => '%3$s'
						) ); ?>
						<li class="nav-link nav-search"><button type="button" class="gwf-search-btn" data-gwf-search aria-label="Buscar" aria-haspopup="dialog" aria-controls="gwf-search"><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button></li>
	                </ul>
	            </nav>

	            <div class="header-actions">
	                <!-- Buscar (móvil): abre la misma ventana de búsqueda que la lupa del menú de escritorio -->
	                <button type="button" class="gwf-search-btn gwf-search-btn-mobile" data-gwf-search aria-label="Buscar" aria-haspopup="dialog" aria-controls="gwf-search"><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
	                <!-- Hamburger Button -->
	                <button type="button" class="hamburger" id="hamburger-btn" aria-label="Abrir menú" aria-controls="mobile-drawer" aria-expanded="false">
	                    <span class="hamburger-bar"></span>
	                    <span class="hamburger-bar"></span>
	                    <span class="hamburger-bar"></span>
	                </button>
	            </div>
	        </div>
	    </header>
		<!-- Mobile Drawer Navigation: menú 55; los padres (Acerca de, Guías, Blog, Legal) se despliegan (gwf-main.js) -->
		<nav class="mobile-drawer" id="mobile-drawer" aria-label="Menú">
			<ul class="drawer-list">
				<?php wp_nav_menu( array(
					'menu'       => 55,
					'container'  => false,
					'items_wrap' => '%3$s'
				) ); ?>
			</ul>
		</nav>
		<!-- Búsqueda: búsqueda normal de WordPress (?s=) hasta que se defina la búsqueda del sitio -->
		<dialog id="gwf-search" class="gwf-search" aria-labelledby="gwf-search-title">
			<div class="gwf-search-head">
				<p id="gwf-search-title" class="gwf-search-title">Buscar</p>
				<button type="button" class="gwf-search-close" data-gwf-search-close aria-label="Cerrar la búsqueda"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
			</div>
			<form role="search" method="get" class="gwf-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="gwf-search-input">Buscar en Growing Family</label>
				<svg viewBox="0 0 24 24" width="20" height="20" class="gwf-search-icon" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><path d="M20 20l-4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
				<input type="search" id="gwf-search-input" name="s" placeholder="Buscar cuentos, guías, artículos…" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off" required>
				<button type="submit" class="gwf-search-submit">Buscar</button>
			</form>
		</dialog>
		<?php
