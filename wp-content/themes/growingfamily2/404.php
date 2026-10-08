<?php
/**
 * The template for displaying the 404 template in the Twenty Twenty theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content">

	<div class="container">

	<div class="row py-5 my-5">

	<div class="section-inner thin error404-content col-12 py-5">

		<img src="http://alphasremote.team/wp-content/uploads/2024/06/gears-svgrepo-com.svg" alt="not found" style=" height: 100px; ">

		<h1 class="entry-title lora-font font-size-35rem"><?php _e( 'Pagina no encontrada', 'twentytwenty' ); ?></h1>

		<div class="font-size-125rem color-grey demi"><p><?php _e( 'La pagina que buscas no fue encontrada o ha cambiado, te invitamos a visitar nuestra ', 'twentytwenty' ); ?> <a href="https://alphasremote.team/" class="blue3">Pagina Principal</a></p></div>

	</div><!-- .section-inner -->

	</div>

	</div>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
