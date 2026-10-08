<?php
/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content">
	<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); ?>
		<section class="my-5 py-5">
			<div class="container">
				<div class="row">
					<div class="col-xl-9 pe-4">
						<div class="col-xl-12 migas mb-4">
							<p class="font-size-16 color-barium">
								<a href="https://growing.family/blog/" class="color-barium text-decoration-none">Blog
								</a> > <span class="color-iron"><?php the_title(); ?></span>
							</p>
						</div>
						<div class="categories col-xl-12 mb-4 d-none d-sm-block">
							<p class="cat font-size-16 text-uppercase">
								<?php $categories = get_the_category();
									$separator = ' ';
									$output = '';
									if ( ! empty( $categories ) ) {
										foreach( $categories as $category ) {
											$output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $category->name ) ) . '" class="color-iron text-decoration-none bg-titanium px-3 py-2 border-radius-8 d-inline-block mt-2">' . esc_html( $category->name ) . '</a>' . $separator;
									}
									echo trim( $output, $separator );
								} ?>
							</p>
						</div>
						<?php the_title( '<h1 class="font-size-44 color-barium pb-4">', '</h1>' ); ?>
						<div class="row">
							<div class="col-12 col-md-10">
								<p class="entry-date font-size-18 color-iron">
									<?php echo get_the_date('j M Y'); ?> - 
									Escrito por <b class="fw-semibold"><?php the_author(); ?></b>
								</p>
							</div>
							<div class="col-12 col-md-2">
								<?php echo do_shortcode('[wp_ulike]'); ?>
							</div>
						</div>
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">
								<div class="entry-content">
									<?php if( get_field('mostrar_resumen_del_post') ): ?>
										<div class="resumen-post p-4 border-radius-24 box-shadow-820 mb-5">
											<h3 class="font-size-28 color-barium mb-3">Resumen del post</h3>
											<p class="font-size-20 color-barium"><?php the_field('resumen_del_post'); ?></p>
										</div>
									<?php endif; ?>
									<?php the_content( __( 'Continue reading', 'twentytwenty' ) ); ?>
								</div>
							</div>
						</article>
						<?php }
							}
						?>
					</div>
					<div class="sidebar col-xl-3">
						<div class="otherarticles bg-titanium border-radius-16 p-3 p-xl-4 mb-5">
							<h3 class="font-size-24 color-barium pb-4 text-center text-xl-start">Más artículos como este que te pueden interesar</h3>
							<?php $current_post_id = get_the_ID();
							$my_query = new WP_Query(array(
							    'posts_per_page' => 2,
							    'category__not_in' => 11,
							    'post__not_in' => array($current_post_id) 
							)); if ( have_posts() ) { while ( $my_query->have_posts() ) { 	$my_query->the_post(); ?>
								<div class="col-xl-12 bg-white article mb-5">
									<div class="p-0 border-radius-8 box-shadow-30">
										<a href="<?php the_permalink(); ?>">
											<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'class' => 'border-radius-8-top' ) ); } ?>
										</a>
										<div class="article-content p-4">
											<a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
												<?php the_title( '<p class="font-size-18 color-barium">', '</p>' ); ?>
											</a>
											<?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
												<p class="time font-size-14 color-iron"><img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock">
													<?php the_field('tiempo_de_lectura'); ?>
												</p>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php } } elseif ( is_search() ) { } ?>
						</div>
						<div class="socials mb-5 mb-xl-0 text-center text-xl-start">
							<p class="font-size-24 color-barium pb-3">Únete a nuestras Redes Sociales</p>
							<p>
								<a href="https://www.facebook.com/growingfamilyacad/" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/facebook.svg" alt="facebook"></a>
								<a href="https://www.instagram.com/growingfamilyacademy/" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/instagram.svg" alt="instagram"></a>
								<a href="https://www.tiktok.com/@growingfamily.academy" rel="nofollow" target="_blank"><img src="https://growing.family/wp-content/uploads/2024/08/tiktok.svg" alt="tiktok"></a>
							</p>
						</div>
					</div>
					<div class="col-xl-12">
						<div class="comments-wrapper section-inner">
							<?php comments_template(); ?>
						</div><!-- .comments-wrapper -->
					</div>		
				</div>
			</div>
		</section>
</main><!-- #site-content -->

<?php get_footer(); ?>
