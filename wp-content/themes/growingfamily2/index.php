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

// Helper para calcular el tiempo de lectura dinámico
function gf_get_reading_time() {
    $word_count = str_word_count( strip_tags( get_the_content() ) );
    $reading_time = ceil( $word_count / 200 );
    return ( $reading_time < 1 ) ? 1 : $reading_time;
}
?>

<main id="site-content grey blog">

	<section class="pt-0 pb-5 title-section">
		<div class="container">
			<div class="migas pb-3 pt-3">
				<p class="font-size-12 text-black">
					<a href="https://growing.family/" class="text-decoration-none text-black"><b>Inicio</b>
					</a> > <a href="https://growing.family/blog/" class="text-decoration-none text-black">Blog
					</a></span>
				</p>
			</div>
			<?php if ( is_search() ) { // Mostrar el título de búsqueda si es una página de resultados de búsqueda ?>
				<h1 class="font-size-32 color-navy text-center mt-5">
					Resultados de la búsqueda para: <?php echo esc_html( get_search_query() ); ?>
				</h1>
				<?php if ( isset( $archive_subtitle ) && $archive_subtitle ) { ?>
					<p class="font-size-32 color-navy mt-5 text-center"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></p>
				<?php } ?>
			<?php } elseif ( is_home() ) { // Mostrar el título de la página si es una página estática ?>
				<h1 class="font-size-44 text-center mb-4 color-navy mt-5">Recursos para padres</h1>
				<p class="font-size-16 text-center mb-0 text-black mb-0">Acompañándote en la etapa más importante de la vida de tus hijos con artículos basados en neurociencia y pedagogía.</p>
			<?php } else { // De lo contrario, mostrar el título de la categoría u otros títulos de archivo ?>
				<h1 class="font-size-44 color-navy text-center"><?php single_cat_title( __( '', 'textdomain' ) ); ?></h1>
				<?php if ( isset( $archive_subtitle ) && $archive_subtitle ) { // Mostrar subtítulo de archivo si está disponible ?>
					<p class="font-size-32 color-navy mt-5"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></p>
				<?php } ?>
			<?php } ?>
		</div>
	</section>

	<div class="container-fluid mt-5">
		<div class="row">

			<?php if ( is_category( '6' ) ) { ?><div class="d-none"><?php } else { ?><div class="d-none d-xl-block col-md-2 mb-5"><?php } ?>
				<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>
			</div>

			<?php if ( is_category( '6' ) ) { ?><div class="col-md-12 entradas-resultados" id="resultados"><?php } else { ?><div class="col-md-10 entradas-resultados" id="resultados"><?php } ?>
				<?php if ( is_home() ) { ?>
					<section class="pb-5 pt-0">
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
								'posts_per_page'   => -1, // Obtiene todos los posts
								'fields'           => 'ids', // Solo devuelve los IDs
								'post__not_in'     => $popular_post_ids,
								'category__not_in' => array(41, 32, 6, 42)
							));
							$other_post_ids = $all_posts_query->posts;

							// Paso 3: Combina las listas para tener todos los IDs, con los populares al principio.
							$all_post_ids_ordered = array_merge($popular_post_ids, $other_post_ids);

							// Helper: unique preservando el orden
							if ( ! function_exists( 'unique_preserve_order' ) ) {
								function unique_preserve_order($array) {
									$seen = [];
									$out  = [];
									foreach ($array as $id) {
										if (!isset($seen[$id])) {
											$seen[$id] = true;
											$out[] = $id;
										}
									}
									return $out;
								}
							}

							// --- NUEVO ORDEN: Cat. 7 primero --- //
							$cat7_popular = array_filter($popular_post_ids, function($pid){
								return has_term(7, 'category', $pid);
							});

							$popular_other = array_values(array_diff($popular_post_ids, $cat7_popular));

							$cat7_other = array_filter($other_post_ids, function($pid){
								return has_term(7, 'category', $pid);
							});
							$other_rest = array_values(array_diff($other_post_ids, $cat7_other));

							$ordered_ids = array_merge(
								array_values($cat7_popular),
								array_values($cat7_other),
								array_values($popular_other),
								array_values($other_rest)
							);

							$ordered_ids = unique_preserve_order($ordered_ids);
							$ordered_ids = array_slice($ordered_ids, 0, 60);

							// Paso 4: Consulta principal con la lista ordenada.
							$my_query = new WP_Query(array(
								'post__in'         => $ordered_ids,
								'orderby'          => 'post__in',
								'posts_per_page'   => 60,
								'category__not_in' => array(41, 32, 6)
							));

							if ( $my_query->have_posts() ) {
							?>
								<div class="row">
									<?php while ( $my_query->have_posts() ) {
										$my_query->the_post();
										$is_cat7 = has_category(7);
										$reading_time = gf_get_reading_time();
									?>
									<?php if ( $is_cat7 ) : ?>
										<article <?php post_class( 'row mb-5 bg-turquoise border-radius-16 p-0 align-items-center' ); ?> id="post-<?php the_ID(); ?>">
											<div class="col-xl-5 border-radius-6 p-0">
												<a href="<?php the_permalink(); ?>">
													<?php if ( has_post_thumbnail() ) {
														the_post_thumbnail('full', array(
															'class' => 'border-radius-8',
														));
													} ?>
												</a>
											</div>
											<div class="col-xl-7 mt-md-5 mt-xl-0 p-4 p-md-5">
												<p class="cat font-size-12 text-uppercase">
													<?php $categories = get_the_category();  if ( ! empty( $categories ) ) {
													    $category_to_show = null; 
													    foreach ( $categories as $category ) {
													        if ( $category->term_id != 44 && $category->term_id != 7 && strtolower( $category->name ) != 'blog' ) {
													            $category_to_show = $category;
													            break;
													        } 
													    }
													    if ( ! is_null( $category_to_show ) ) {
													        echo '<a href="' . esc_url( get_category_link( $category_to_show->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'Ver todos los posts en %s', 'textdomain' ), $category_to_show->name ) ) . '" class="text-white font-size-12 text-decoration-none bg-navy px-3 py-2 border-radius-24" style=" display: inline-block; ">' . esc_html( $category_to_show->name ) . '</a>';
													    } 
													} 
													?>
												</p>
												<a href="<?php the_permalink(); ?>" class="text-black text-decoration-none">
													<?php the_title( '<h2 class="font-size-32 text-black pb-4">', '</h2>' ); ?>
												</a>
												<p class="font-size-20 text-gray"><?php $excerpt = get_the_excerpt(); $striped_excerpt = strip_tags($excerpt); echo $striped_excerpt; ?></p>

												<div class="row align-items-center mb-4">
													<div class="col-12 col-md-6 text-aling-right">
														<?php echo do_shortcode('[wp_ulike]'); ?>
													</div>
													<div class="col-12 col-md-6">
														<p class="time font-size-16 text-black mb-0">
															<img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php echo $reading_time; ?> min de lectura
														</p>
													</div>
												</div>

												<a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">Leer más</a>
											</div>
										</article>
									<?php else : ?>
										<div class="col-xl-4 article mb-5 resultado">
											<article class="blog-card">
								                <div class="blog-image-wrapper">
								                	<p class="cat font-size-12 text-uppercase">
														<?php $categories = get_the_category();  if ( ! empty( $categories ) ) {
														    $category_to_show = null; 
														    foreach ( $categories as $category ) {
														        if ( $category->term_id != 44 && $category->term_id != 7 && strtolower( $category->name ) != 'blog' ) {
														            $category_to_show = $category;
														            break; 
														        } 
														    }
														    if ( ! is_null( $category_to_show ) ) {
														        echo '<a href="' . esc_url( get_category_link( $category_to_show->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'Ver todos los posts en %s', 'textdomain' ), $category_to_show->name ) ) . '" class="text-white font-size-12 text-decoration-none bg-navy px-3 py-2 border-radius-24" style=" display: inline-block; ">' . esc_html( $category_to_show->name ) . '</a>';
														    } 
														} 
														?>
													</p>
								                    <?php if (has_post_thumbnail()) : ?>
								                        <?php the_post_thumbnail('medium_large', array('class' => 'blog-image', 'alt' => get_the_title())); ?>
								                    <?php else : ?>
								                        <img src="https://growing.family/wp-content/uploads/2026/08/article-1.jpg" alt="<?php the_title_attribute(); ?>" class="blog-image">
								                    <?php endif; ?>
								                    <?php echo do_shortcode('[wp_ulike]'); ?>
								                </div>
								                <div class="blog-card-body">
								                    <h3 class="blog-card-title"><?php the_title(); ?></h3>
								                    <div class="blog-meta"><?php echo $reading_time; ?> min de lectura</div>
								                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">Leer más</a>
								                </div>
								            </article>
										</div>

									<?php endif; ?>
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
						<?php while ( have_posts() ) { the_post(); 
							$reading_time = gf_get_reading_time();
						?>
							<div class="col-xl-4 article mb-5 resultado">
											<article class="blog-card">
								                <div class="blog-image-wrapper">
								                	<p class="cat font-size-12 text-uppercase">
														<?php $categories = get_the_category();  if ( ! empty( $categories ) ) {
														    $category_to_show = null; 
														    foreach ( $categories as $category ) {
														        if ( $category->term_id != 44 && $category->term_id != 7 && strtolower( $category->name ) != 'blog' ) {
														            $category_to_show = $category;
														            break; 
														        } 
														    }
														    if ( ! is_null( $category_to_show ) ) {
														        echo '<a href="' . esc_url( get_category_link( $category_to_show->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'Ver todos los posts en %s', 'textdomain' ), $category_to_show->name ) ) . '" class="text-white font-size-12 text-decoration-none bg-navy px-3 py-2 border-radius-24" style=" display: inline-block; ">' . esc_html( $category_to_show->name ) . '</a>';
														    } 
														} 
														?>
													</p>
								                    <?php if (has_post_thumbnail()) : ?>
								                        <?php the_post_thumbnail('medium_large', array('class' => 'blog-image', 'alt' => get_the_title())); ?>
								                    <?php else : ?>
								                        <img src="https://growing.family/wp-content/uploads/2026/08/article-1.jpg" alt="<?php the_title_attribute(); ?>" class="blog-image">
								                    <?php endif; ?>
								                    <?php echo do_shortcode('[wp_ulike]'); ?>
								                </div>
								                <div class="blog-card-body">
								                    <h3 class="blog-card-title"><?php the_title(); ?></h3>
								                    <div class="blog-meta"><?php echo $reading_time; ?> min de lectura</div>
								                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">Leer más</a>
								                </div>
								            </article>
										</div>
						<?php } ?>
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