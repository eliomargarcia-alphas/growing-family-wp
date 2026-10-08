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
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-VDD509FKDM"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'G-VDD509FKDM');
		</script>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>
		<!-- Header Navigation -->
	    <header class="header">
	        <div class="container header-container">
	            <a href="https://growing.family/" class="logo">
	                <img src="https://growing.family/wp-content/uploads/2026/08/logo.png" alt="Growing Family Logo" class="logo-img" width="562" height="160">
	            </a>
	            
	            <nav class="nav">
	                <ul class="nav-list mb-0">
	                    <?php wp_nav_menu( array(
						    'menu'       => 54,    
						    'container'  => false,
						    'items_wrap' => '%3$s'
						) ); ?>
						<!--<li>
	                        <a href="#" class="nav-link login-link">
	                            Iniciar sesión
	                            <svg class="icon-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
	                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
	                                <circle cx="12" cy="7" r="4"></circle>
	                            </svg>
	                        </a>
	                    </li>
	                    <li><a href="#descargar" class="btn btn-primary btn-sm">Descargar App</a></li>
	                    <li><a href="#popupsearch"><img src="https://growing.family/wp-content/uploads/2025/09/search-1.svg" alt="buscar" loading="lazy" decoding="async"></a></li>-->
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
				<!--<li>
					<a href="#login" class="drawer-link login-link">
						<svg class="icon-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
							<circle cx="12" cy="7" r="4"></circle>
						</svg>
						Iniciar sesión
					</a>
				</li>
				<li><a href="#descargar" class="btn btn-primary btn-full">Descargar App</a></li>-->
			</ul>
		</div>
		<!--<div id="popupsearch" class="overlay">
			<div class="popup">
				<a class="close" href="#">×</a>
				<div class="content">
					<div class="formsection contactonuevo">
						<div class="container px-3">
							<div class="col-xl-12 color-barium font-size-20">
								<p class="font-size-32 color-barium fw-semibold">Buscar</p>
								<?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>-->
		<?php
