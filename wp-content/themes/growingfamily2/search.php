<?php
/**
 * Resultados de la búsqueda (inc/gwf-search.php): cabecera de los listados con el buscador, filtro por tipo
 * (Todo, Cuentos, Guías, Actividades, Blog) con el número de resultados de cada uno y las tarjetas de los listados.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

$gwf_search = get_search_query( false );
$gwf_type   = gwf_search_current_type();
$gwf_counts = '' !== trim( $gwf_search ) ? gwf_search_counts( $gwf_search ) : array();
$gwf_total  = array_sum( $gwf_counts );
$gwf_found  = (int) $GLOBALS['wp_query']->found_posts;

$gwf_base  = add_query_arg( 's', rawurlencode( $gwf_search ), home_url( '/' ) );
$gwf_items = array(
	array(
		'label'  => 'Todo (' . $gwf_total . ')',
		'url'    => $gwf_base,
		'active' => ! $gwf_type,
	),
);
foreach ( gwf_search_types() as $gwf_key => $gwf_t ) {
	if ( empty( $gwf_counts[ $gwf_key ] ) && $gwf_key !== $gwf_type ) {
		continue; // Solo los tipos con resultados.
	}
	$gwf_items[] = array(
		'label'  => $gwf_t['label'] . ' (' . (int) ( $gwf_counts[ $gwf_key ] ?? 0 ) . ')',
		'url'    => add_query_arg( 'tipo', $gwf_key, $gwf_base ),
		'active' => $gwf_key === $gwf_type,
	);
}
$gwf_groups = $gwf_total ? array( array( 'label' => 'Tipo', 'items' => $gwf_items ) ) : array();

if ( '' === trim( $gwf_search ) ) {
	$gwf_title = 'Buscar en Growing Family';
	$gwf_intro = 'Cuentos, guías, actividades y artículos para acompañar a tu hija o tu hijo.';
} else {
	$gwf_title = 'Resultados para «' . $gwf_search . '»';
	$gwf_intro = $gwf_found ? sprintf( _n( '%d resultado', '%d resultados', $gwf_found ), $gwf_found ) : '';
}
?>

<main id="site-content" class="gwf-guia gwf-listado gwf-busqueda">
	<?php get_template_part( 'template-parts/gwf-listado-cabecera', null, array( 'title' => $gwf_title, 'intro' => $gwf_intro ) ); ?>

	<div class="gwf-busqueda-form container">
		<?php get_template_part( 'template-parts/gwf-buscador', null, array( 'id' => 'gwf-busqueda-input', 'keep_type' => true ) ); ?>
	</div>

	<div class="container-fluid mt-5">
		<div class="row">
			<?php if ( $gwf_groups ) : ?>
				<div class="d-none d-xl-block col-md-2 mb-5">
					<?php get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'side', 'groups' => $gwf_groups ) ); ?>
				</div>
			<?php endif; ?>

			<div class="<?php echo $gwf_groups ? 'col-md-10' : 'col-md-12'; ?> entradas-resultados" id="resultados">
				<?php
				if ( $gwf_groups ) {
					get_template_part( 'template-parts/gwf-filtros', null, array( 'mode' => 'chips', 'groups' => $gwf_groups ) );
				}
				?>

				<?php if ( have_posts() ) : ?>
					<div class="row">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/gwf-tarjeta', null, gwf_search_card_args( get_the_ID() ) );
						endwhile;
						?>
					</div>

					<?php
					$gwf_pages = paginate_links(
						array(
							'type'      => 'list',
							'prev_text' => '‹ Anteriores',
							'next_text' => 'Siguientes ›',
							'mid_size'  => 1,
						)
					);
					if ( $gwf_pages ) {
						echo '<nav class="gwf-l-pages" aria-label="Páginas">' . $gwf_pages . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de WordPress.
					}
					?>
				<?php elseif ( '' !== trim( $gwf_search ) ) : ?>
					<div class="gwf-g-block gwf-l-empty gwf-busqueda-empty">
						<h2>No encontramos nada con «<?php echo esc_html( $gwf_search ); ?>»</h2>
						<p>Prueba con otra palabra (por ejemplo «celos», «miedo» o «berrinches») o explora por secciones:</p>
						<ul class="gwf-busqueda-links">
							<?php foreach ( array( 'cuentos' => '/cuentos/', 'guias' => '/guias/', 'actividades' => '/actividades/' ) as $gwf_key => $gwf_path ) : ?>
								<?php if ( isset( gwf_search_types()[ $gwf_key ] ) ) : ?>
									<li><a class="gwf-g-button" href="<?php echo esc_url( home_url( $gwf_path ) ); ?>"><?php echo esc_html( gwf_search_types()[ $gwf_key ]['label'] ); ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
							<li><a class="gwf-g-button" href="<?php echo esc_url( function_exists( 'gwf_blog_url' ) ? gwf_blog_url() : home_url( '/blog/' ) ); ?>">Blog</a></li>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
