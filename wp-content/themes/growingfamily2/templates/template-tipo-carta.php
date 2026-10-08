<?php
/**
 * Template Name: Contenido tipo Carta
 * Template Post Type: post
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
						<?php the_title( '<h1 class="font-size-44 color-barium fw-semibold pb-4">', '</h1>' ); ?>
						<div class="row">
							<?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
								<div class="col-12 col-md-10">
									<p class="time font-size-16 color-iron">
										<img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php the_field('tiempo_de_lectura'); ?> de lectura
									</p>
								</div>
							<?php endif; ?>
							<div class="col-12 col-md-2">
								<?php echo do_shortcode('[wp_ulike]'); ?>
							</div>
						</div>
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">
								<div class="entry-content">
									<?php the_content( __( 'Continue reading', 'twentytwenty' ) ); ?>
								</div>
							</div>
						</article>
						<?php }
							}
						?>
					</div>
					<div class="sidebar sidebar-sticky col-xl-3 d-none d-xl-block">
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
												<?php the_title( '<p class="font-size-18 color-barium fw-semibold">', '</p>' ); ?>
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
							<p class="font-size-24 color-barium fw-semibold pb-3">Únete a nuestras Redes Sociales</p>
							<p>
								<a href="https://www.facebook.com/WeAreGrowing.Family" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/facebook.svg" alt="facebook"></a>
								<a href="https://www.instagram.com/wearegrowing.family/" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/instagram.svg" alt="instagram"></a>
								<a href="https://www.tiktok.com/@growing.family" rel="nofollow" target="_blank"><img src="https://growing.family/wp-content/uploads/2024/08/tiktok.svg" alt="tiktok"></a>
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
