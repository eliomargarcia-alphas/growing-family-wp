<?php
/**
 * Template Name: Habitos - Encuesta
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">

	<section class="my-5 py-5">
		<div class="container">
				<div class="col-xl-12 migas">
					<p class="font-size-16 color-blue">
						<a href="https://growingfamily.academy/habitos-en-la-familia/" class="color-blue text-decoration-none">Habitos
						</a> > Test hábitos positivos en familia
					</p>
				</div>
			<div class="row align-items-center equal-height-row d-flex align-items-stretch">
				<div class="col-xl-8 pe-4 d-flex flex-column">
					<h1 class="font-size-44 color-barium fw-semibold pb-4">¡Pon a prueba tu habilidad sobre hábitos positivos en familia!</h1>
					<img src="https://growingfamily.academy/wp-content/uploads/2024/08/habitos.jpg" alt="" class="border-radius-8 mb-5">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Este cuestionario está diseñado para hacerte ver qué tan bien están tu comprensión e implementación de hábitos en la vida diaria de la familia.</h2>
					<p class="time font-size-20 color-barium"><img src="https://growingfamily.academy/wp-content/uploads/2024/07/clock.svg" alt="clock">Duración: 2 minutos</p>
					<p class="font-size-20 color-barium">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.</p>
					<p class="font-size-20 color-barium">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
				</div>
				<div class="col-xl-4 bg-titanium border-radius-16 p-5 d-flex align-items-stretch">
					<div class="d-flex">
						<div class="d-flex flex-column">
							<h3 class="font-size-24 color-barium fw-semibold pb-4">Más artículos como este que te pueden interesar</h3>
							<?php $my_query = new WP_Query(array('posts_per_page' => 2));
							if ( have_posts() ) { while ( $my_query->have_posts() ) { 	$my_query->the_post(); ?>
								<div class="col-xl-12 bg-white article mb-5">
									<div class="p-0 border-radius-8 box-shadow-30">
										<a href="<?php the_permalink(); ?>">
											<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'class' => 'border-radius-8-top' ) ); } ?>
										</a>
										<div class="article-content p-4">
											<a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
												<?php the_title( '<p class="font-size-18 color-barium fw-semibold">', '</p>' ); ?>
											</a>
											<p class="time font-size-14 color-iron"><img src="https://growingfamily.academy/wp-content/uploads/2024/07/clock.svg" alt="clock">
												<?php $currentlang = get_bloginfo('language'); if($currentlang=="en-US"): ?>
													Reading Time 2 min
												<?php elseif($currentlang=="es"): ?>
													Tiempo de Lectura: 2 min
												<?php endif; ?>
											</p>
										</div>
									</div>
								</div>
							<?php } } elseif ( is_search() ) { } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="my-5">
				<div class="row">
					<div class="col-xl-8 pe-4 d-flex flex-column">
						<?php echo do_shortcode('[qsm quiz=1]') ?>
					</div>
					<div class="col-xl-4 border-radius-16">
						<img src="https://growingfamily.academy/wp-content/uploads/2024/08/app-banner.png" alt="">
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="frase bg-habitos py-5 mt-5 text-center">
		<div class="container py-5">
			<h2 class="font-playfair font-size-32 fw-normal text-white">“No es lo que haces por tus hijos, sino lo que les has enseñado a hacer por sí mismos, lo que les convertirá en seres humanos de éxito"</h2>
			<p class="font-size-24 text-white">-Ann Landers-</p>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
