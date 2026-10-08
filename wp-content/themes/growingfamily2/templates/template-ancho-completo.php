<?php
/**
 * Template Name: Ancho completo
 * Template Post Type: post, page
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
					<div class="col-xl-12 pe-4">
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
						<?php the_title( '<h1 class="font-size-44 color-barium fw-semibold pb-4">', '</h1>' ); ?>
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
