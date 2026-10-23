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
		<svg class="gwf-404__icon" viewBox="0 0 512 512" width="120" height="120" fill="none" stroke="currentColor" stroke-width="22" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M329.6 174.8 L369.2 183.4 L369.0 218.1 L329.4 226.3 L309.4 273.8 L331.4 307.9 L306.8 332.3 L272.9 310.1 L225.2 329.6 L216.6 369.2 L181.9 369.0 L173.7 329.4 L126.2 309.4 L92.1 331.4 L67.7 306.8 L89.9 272.9 L70.4 225.2 L30.8 216.6 L31.0 181.9 L70.6 173.7 L90.6 126.2 L68.6 92.1 L93.2 67.7 L127.1 89.9 L174.8 70.4 L183.4 30.8 L218.1 31.0 L226.3 70.6 L273.8 90.6 L307.9 68.6 L332.3 93.2 L310.1 127.1 Z"/><path d="M117.3 169.9 A88 88 0 1 1 143.4 267.4"/><circle cx="200" cy="200" r="42"/><path d="M469.7 399.9 L493.0 414.7 L484.7 436.0 L457.5 431.2 L434.3 455.4 L440.3 482.4 L419.4 491.6 L403.6 469.0 L370.1 469.7 L355.3 493.0 L334.0 484.7 L338.8 457.5 L314.6 434.3 L287.6 440.3 L278.4 419.4 L301.0 403.6 L300.3 370.1 L277.0 355.3 L285.3 334.0 L312.5 338.8 L335.7 314.6 L329.7 287.6 L350.6 278.4 L366.4 301.0 L399.9 300.3 L414.7 277.0 L436.0 285.3 L431.2 312.5 L455.4 335.7 L482.4 329.7 L491.6 350.6 L469.0 366.4 Z"/><path d="M408.0 345.2 A46 46 0 1 1 355.4 349.8"/></svg>

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
