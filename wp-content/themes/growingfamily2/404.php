<?php
/**
 * Página 404: responde con código 404 (WordPress) y noindex (Yoast), y ofrece caminos útiles
 * hacia las secciones principales en lugar de dejar a la familia en un callejón sin salida.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

$gwf_404_links = array(
	array( 'url' => home_url( '/acerca-de/el-metodo/' ), 'title' => 'El método', 'desc' => 'Diez minutos cada noche para conectar con tu hija o tu hijo.' ),
	array( 'url' => home_url( '/acerca-de/la-edad-extraordinaria/' ), 'title' => 'La edad extraordinaria', 'desc' => 'Qué pasa entre los 4 y los 6 años y por qué importa tanto.' ),
	array( 'url' => home_url( '/acerca-de/' ), 'title' => 'Acerca de Growing Family', 'desc' => 'Quiénes somos y por qué hacemos esto.' ),
	array( 'url' => home_url( '/contacto-y-soporte/' ), 'title' => 'Contacto y soporte', 'desc' => 'Si buscabas algo concreto, escríbenos.' ),
);
// El blog solo aparece cuando su página esté publicada.
if ( function_exists( 'gwf_blog_url' ) && 'publish' === get_post_status( (int) get_option( 'page_for_posts' ) ) ) {
	array_splice( $gwf_404_links, 3, 0, array( array( 'url' => gwf_blog_url(), 'title' => 'Blog', 'desc' => 'Notas de madres y padres que están donde tú estás.' ) ) );
}
?>

<main id="site-content" class="gwf-404">
	<section class="container py-5 my-5 text-center">
		<img src="https://growing.family/wp-content/uploads/2026/09/maxyana-1-opt.webp" alt="Max y Ana, los personajes de Growing Family" width="592" height="540" class="gwf-404__img" decoding="async">

		<h1 class="section-title mt-4">No encontramos esta página</h1>
		<p class="section-subtitle gwf-404__lead">Puede que el enlace haya cambiado o que la página ya no exista. No pasa nada: desde aquí puedes seguir por donde quieras.</p>

		<p class="mt-4">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-accent">Volver al inicio</a>
			<a href="<?php echo esc_url( home_url( '/lista-de-espera/' ) ); ?>" class="btn btn-outline">Unirme a la lista de espera</a>
		</p>

		<nav class="gwf-404__links mt-5" aria-label="Secciones principales">
			<?php foreach ( $gwf_404_links as $link ) : ?>
				<a href="<?php echo esc_url( $link['url'] ); ?>" class="gwf-404__card">
					<span class="gwf-404__card-title"><?php echo esc_html( $link['title'] ); ?></span>
					<span class="gwf-404__card-desc"><?php echo esc_html( $link['desc'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
	</section>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
