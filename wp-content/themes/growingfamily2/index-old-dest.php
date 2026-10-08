<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey blog">

	<section class="bg-blog pt-0 pb-5 bb-20-turquoie title-section">
		<div class="container">
			<div class="migas pb-3 pt-3">
				<p class="font-size-12 text-white">
					<a href="https://growingfamily.academy/" class="text-white text-decoration-none"><b>Inicio</b>
					</a> > <a href="https://growingfamily.academy/blog/" class="text-white text-decoration-none">Blog
					</a></span>
				</p>
			</div>
			<?php if ( is_search() ) { // Mostrar el título de búsqueda si es una página de resultados de búsqueda ?>
		    	<h1 class="font-size-28 text-white mb-0">
			        Resultados de la búsqueda para: <?php echo esc_html( get_search_query() ); ?>
			    </h1>
		    <?php if ( $archive_subtitle ) { ?>
		        <p class="font-size-32 fw-normal text-white mb-0"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></p>
		    	<?php } ?>
			<?php } elseif ( is_home() ) { // Mostrar el título de la página si es una página estática ?>
			    <h1 class="font-size-44 text-white text-center mb-0">Blog</h1>
			<?php } else { // De lo contrario, mostrar el título de la categoría u otros títulos de archivo ?>
			    <h1 class="font-size-44 text-white text-center mb-0"><?php single_cat_title( __( '', 'textdomain' ) ); ?></h1>
			    <?php if ( $archive_subtitle ) { // Mostrar subtítulo de archivo si está disponible (de la lógica de original.php) ?>
			        <p class="font-size-32 fw-normal text-white mb-0"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></p>
			    <?php } ?>
			<?php } ?>
		</div>
	</section>

	<div class="container-fluid mt-5">
		<div class="row">

			<?php if ( is_category( '6' ) ) { ?><div class="d-none"><?php } else { ?><div class="col-md-2"><?php } ?>
				<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>
			</div>

			<?php if ( is_category( '6' ) ) { ?><div class="col-md-12 entradas-resultados" id="resultados"><?php } else { ?><div class="col-md-10 entradas-resultados" id="resultados"><?php } ?>
				<?php if ( is_home() ) { ?>
					<section class="destacada pb-5 pt-5 pt-xl-0">
						<div class="container">
								<?php $my_query = new WP_Query(array('posts_per_page' => 1, 'category__in' => 7));
								if ( have_posts() ) { while ( $my_query->have_posts() ) { 	$my_query->the_post(); ?>
									<article <?php post_class( 'row' ); ?> id="post-<?php the_ID(); ?>">
										<div class="col-xl-6 border-radius-6">
											<a href="<?php the_permalink(); ?>">
												<?php if ( has_post_thumbnail() ) {
											        the_post_thumbnail('full', array(
											            'class' => 'border-radius-8',
											        ));
											    } ?>
											</a>
										</div>
										<div class="col-xl-6 mt-5 mt-xl-0">
											<a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
												<?php the_title( '<h2 class="font-size-32 color-barium fw-semibold pb-4">', '</h2>' ); ?>
											</a>
											<?php if ( ! is_category( '11' ) ) { ?>
												<p class="entry-date font-size-16 color-iron"><?php echo get_the_date('j M Y'); ?></p>
											<?php } else { } ?>
											<p class="font-size-24 color-barium"><?php $excerpt = get_the_excerpt(); $striped_excerpt = strip_tags($excerpt); echo $striped_excerpt; ?></p>
											<?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
												<p class="time font-size-16 color-iron">
													<img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php the_field('tiempo_de_lectura'); ?> de lectura
												</p>
											<?php endif; ?>
										</div>
									</article>
								<?php } } elseif ( is_search() ) { ?>
									<h1 class="font-size-20 color-barium">No se han encontrado resultados que coinsidan con tu busqueda</h1>
								<?php } ?>
						</div>
					</section>

					<section class="otherarticles py-5">
						<div class="container-fluid">
							<div>
							<?php
							// Paso 1: Obtiene los IDs de los posts con likes, ordenados de mayor a menor.
							$popular_post_ids = wp_ulike_get_popular_items_ids(array(
							    'type'   => 'post',
							    'status' => 'like',
							    'period' => 'all'
							));

							// Paso 2: Obtiene los IDs de todos los posts, excluyendo los ya obtenidos.
							$all_posts_query = new WP_Query(array(
							    'posts_per_page' => -1, // Obtiene todos los posts
							    'fields'         => 'ids', // Solo devuelve los IDs
							    'post__not_in'   => $popular_post_ids,
							    'category__not_in' => array(41, 32, 6, 42)
							));
							$other_post_ids = $all_posts_query->posts;

							// Paso 3: Combina las listas para tener todos los IDs, con los populares al principio.
							$all_post_ids_ordered = array_merge($popular_post_ids, $other_post_ids);

							// Paso 4: Realiza la consulta principal usando la nueva lista de IDs ordenada.
							$my_query = new WP_Query(array(
							    'post__in'         => $all_post_ids_ordered,
							    'orderby'          => 'post__in',
							    'posts_per_page'   => 60, // Muestra los primeros 60 posts de la lista combinada
							    'category__not_in' => array(41, 32, 6) // Se mantiene aquí por si el query principal no lo tiene
							));

							// El resto de tu código con el bucle y el HTML es correcto.
							if ( $my_query->have_posts() ) {
							?>
							    <div class="row">
							        <?php while ( $my_query->have_posts() ) {
							            $my_query->the_post();
							        ?>
							            <div class="col-xl-3 article mb-5 resultado">
							                <div class="card h-100 p-0 border-radius-8 box-shadow-30 border-0">
							                    <div class="card-body p-0">
							                        <a href="<?php the_permalink(); ?>">
							                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'class' => 'border-radius-8-top' ) ); } ?>
							                        </a>
							                        <?php echo do_shortcode('[wp_ulike]'); ?>
							                        <div class="article-content p-4 bg-white border-radius-8-bottom">
							                            <p class="cat font-size-12 text-uppercase">
							                                <?php $categories = get_the_category(); if ( ! empty( $categories ) ) {
							                                	$category_to_show = null; foreach ( $categories as $category ) {
							                                	if ( $category->term_id != 44 && strtolower( $category->name ) != 'blog' ) {
							                                		$category_to_show = $category;
							                                		break; 
							                                	} }
							                                	if ( ! is_null( $category_to_show ) ) {
							                                	echo '<a href="' . esc_url( get_category_link( $category_to_show->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'Ver todos los posts en %s', 'textdomain' ), $category_to_show->name ) ) . '" class="color-iron font-size-12 text-decoration-none bg-titanium px-3 py-2 border-radius-8" style=" display: inline-block; ">' . esc_html( $category_to_show->name ) . '</a>';
							                                } } ?>
							                            </p>
							                            <a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
							                                <?php the_title( '<h2 class="font-size-24 color-barium fw-semibold pb-4">', '</h2>' ); ?>
							                            </a>
							                            <div class="row">
							                            	<?php if ( is_category( '6' ) ) { ?><?php } else { ?>
							                                <div class="col-12 col-md-6">
							                                    <p class="entry-date font-size-16 color-iron"><?php echo get_the_date('j M Y'); ?></p>
							                                </div>
							                                <?php } ?>
							                                <div class="col-12 col-md-6">
							                                    
							                                </div>
							                            </div>
							                            <p class="extract font-size-16 color-barium">
							                                <?php echo wp_strip_all_tags( get_the_excerpt() ); ?>
							                            </p>
							                            <?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
							                                <p class="time font-size-16 color-iron">
							                                    <img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php the_field('tiempo_de_lectura'); ?> de lectura
							                                </p>
							                            <?php endif; ?>
							                        </div>
							                    </div>
							                </div>
							            </div>
							        <?php } ?>
							    </div>
							<?php
							    wp_reset_postdata();
							} elseif ( is_search() ) { ?>
							    <div class="no-search-results-form section-inner thin">
							        <?php get_search_form( array(
							            'aria_label' => __( 'search again', 'twentytwenty' ),
							        ) ); ?>
							    </div>
							<?php } ?>
						</div>
					</section>
				<?php } else { ?>
					<?php if ( have_posts() ) { ?>
					<div class="row">
						<?php { while ( have_posts() ) { the_post(); ?>
											<div class="col-xl-3 article mb-5">
							                <div class="card h-100 p-0 border-radius-8 box-shadow-30 border-0">
							                    <div class="card-body p-0">
							                        <a href="<?php the_permalink(); ?>">
							                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'class' => 'border-radius-8-top' ) ); } ?>
							                        </a>
							                        <?php echo do_shortcode('[wp_ulike]'); ?>
							                        <div class="article-content p-4 bg-white border-radius-8-bottom">
							                            <p class="cat font-size-12 text-uppercase">
							                                <?php $categories = get_the_category(); if ( ! empty( $categories ) ) {
							                                	$category_to_show = null; foreach ( $categories as $category ) {
							                                	if ( $category->term_id != 44 && strtolower( $category->name ) != 'blog' ) {
							                                		$category_to_show = $category;
							                                		break; 
							                                	} }
							                                	if ( ! is_null( $category_to_show ) ) {
							                                	echo '<a href="' . esc_url( get_category_link( $category_to_show->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'Ver todos los posts en %s', 'textdomain' ), $category_to_show->name ) ) . '" class="color-iron font-size-12 text-decoration-none bg-titanium px-3 py-2 border-radius-8" style=" display: inline-block; ">' . esc_html( $category_to_show->name ) . '</a>';
							                                } } ?>
							                            </p>
							                            <a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
							                                <?php the_title( '<h2 class="font-size-24 color-barium fw-semibold pb-4">', '</h2>' ); ?>
							                            </a>
							                            <div class="row">
							                            	<?php if ( is_category( '6' ) ) { ?><?php } else { ?>
							                                <div class="col-12 col-md-6">
							                                    <p class="entry-date font-size-16 color-iron"><?php echo get_the_date('j M Y'); ?></p>
							                                </div>
							                                <?php } ?>
							                                <div class="col-12 col-md-6">
							                                    
							                                </div>
							                            </div>
							                            <p class="extract font-size-16 color-barium">
							                                <?php echo wp_strip_all_tags( get_the_excerpt() ); ?>
							                            </p>
							                            <?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
							                                <p class="time font-size-16 color-iron">
							                                    <img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php the_field('tiempo_de_lectura'); ?> de lectura
							                                </p>
							                            <?php endif; ?>
							                        </div>
							                    </div>
							                </div>
							            </div>
	        			<?php } } ?>
					</div>
					<?php } else { ?>
						
						<div class="container" style=" min-height: 50vh; ">
							<div class="col-xl-12 color-barium font-size-20">
								<h1 class="font-size-32 color-barium fw-semibold mb-4">Sin resultados</h1>
								<p class="font-size-24 color-barium fw-semibold">¿Desea intentar con otro término de búsqueda?</p>
										<?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
							</div>
						</div>
				<?php } } ?>
			</div>
		</div>
	</div>

</main><!-- #site-content -->

<?php
get_footer();
