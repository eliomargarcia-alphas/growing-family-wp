<?php
/**
 * Template Name: Articulos Como me Siento
 * Template Post Type: post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content">
	<?php if ( have_posts() ) { while ( have_posts() ) { the_post(); 
		$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
		<section class="top-info bb-20-turquoie" style="background: url(<?php the_field('imagen_de_fondo'); ?>) no-repeat center; center;background-size: cover;">
			<div class="pt-3 pb-5 top-info" style="background: #37425fbd;">
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div class="migas mb-5">
								<p class="font-size-14 text-white">
									<a href="https://growing.family/" class="text-white text-decoration-none"><b>Inicio</b>
									</a> > <a href="https://growing.family/como-te-sientes-hoy/" class="text-white text-decoration-none"><b>Como me siento</b></a> > <span class="text-white"><?php the_title(); ?></span>
								</p>
							</div>
							<div class="text-center pt-5 px-xl-5">
								<?php the_title( '<h1 class="font-size-44 font-size-35-m text-white fw-semibold pb-4 px-xl-5">', '</h1>' ); ?>
							</div>
							<div class="text-center pt-3 pb-5">
								<?php if( get_field('mostrar_tiempo_de_lectura') ): ?>
									<span class="time font-size-16 text-white">
										<img src="https://growing.family/wp-content/uploads/2024/07/clock.svg" alt="clock"> <?php the_field('tiempo_de_lectura'); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="my-5 py-5">
			<div class="mb-5">
				<div class="row">
					<div class="col-xl-10 pe-xl-4">
						<div class="container">
							<div class="row">
								<div class="col-xl-12">
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
								<div class="comments-wrapper section-inner">
									<?php comments_template(); ?>
								</div><!-- .comments-wrapper -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="bg-titanium" style="z-index: 100000000; position: relative;">
			<div class="row">
					<div class="col-xl-10 pe-4">
						<div class="container">
							<div class="row">
								<div class="col-xl-12">
				<!-- related posts -->
								<?php $current_post_id = get_the_ID();
								$my_query = new WP_Query(array(
								    'posts_per_page' => 4,
								    'category__in' => 41,
								    'post__not_in' => array($current_post_id) 
								));
								if ($my_query->have_posts()) {
								?>
								    <div class="my-5"><div class="">
								            <h3 class="font-size-24 color-barium fw-semibold pb-4 text-center text-xl-start">Más artículos de como me siento</h3>
								            <div class="row">
								                <?php while ($my_query->have_posts()) {
								                    $my_query->the_post(); ?>
								                    <div class="col-xl-3 article mb-5">
								                        <div class="card h-100 p-0 border-radius-8 box-shadow-30 border-0">
								                            <div class="card-body p-0">
								                                <a href="<?php the_permalink(); ?>">
								                                    <?php if (has_post_thumbnail()) {
								                                        the_post_thumbnail('large', array('class' => 'border-radius-8-top'));
								                                    } ?>
								                                </a>
								                                <div class="article-content p-4 bg-white border-radius-8-bottom">
								                                    <a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
								                                        <?php the_title('<h2 class="font-size-24 color-barium fw-semibold pb-3">', '</h2>'); ?>
								                                    </a>
								                                    <p class="extract font-size-16 color-barium">
								                                        <?php $excerpt = get_the_excerpt();
								                                        $striped_excerpt = strip_tags($excerpt);
								                                        echo $striped_excerpt; ?>
								                                    </p>
								                                </div>
								                            </div>
								                        </div>
								                    </div>
								                <?php } ?>
								            </div>
								        </div>
								    </div>
								<?php } wp_reset_postdata(); ?>
			</div>
			</div>
			</div>
			</div>
			</div>
		</section>
</main><!-- #site-content -->


<?php get_footer(); ?>
