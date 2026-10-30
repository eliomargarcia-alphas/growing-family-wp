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
	                </ul>
	            </nav>

	            <!-- Hamburger Button -->
	            <button class="hamburger" id="hamburger-btn" aria-label="Abrir menú">
	                <span class="hamburger-bar"></span>
	                <span class="hamburger-bar"></span>
	                <span class="hamburger-bar"></span>
	            </button>
	        </div>
	    </header>
		<!-- Mobile Drawer Navigation -->
		<div class="mobile-drawer" id="mobile-drawer">
			<ul class="drawer-list">
				<?php wp_nav_menu( array(
					'menu'       => 55,    
					'container'  => false,
					'items_wrap' => '%3$s'
				) ); ?>
			</ul>
		</div>
		<?php
