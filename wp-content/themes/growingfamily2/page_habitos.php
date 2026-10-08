<?php
/**
 * Template Name: Habitos
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">

	<section class="bg-habitos py-5 bb-20-turquoie">
		<div class="container">
			<p class="font-size-32 fw-normal text-white mb-0">La importancia de adquirir</p>
			<h1 class="font-size-60 text-white mb-0">Hábitos en la familia</h1>
		</div>
	</section>

	<section class="my-5 py-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 pe-4">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">La Clave del Ejemplo: La Importancia de Adquirir Hábitos Positivos en la Crianza de los Hijos</h2>
					<p class="font-size-20 color-barium">En la crianza, los padres son los primeros y más importantes modelos a seguir para sus hijos. Los hábitos que los adultos establecen impactan tanto su propio bienestar como el desarrollo de sus hijos. Esta serie de artículos está diseñada para ayudar a los padres a desarrollar y mantener hábitos positivos que inspiren y guíen a sus hijos hacia un futuro saludable y exitoso.</p>
					<p class="font-size-20 color-barium">Exploraremos estrategias para establecer rutinas saludables, la importancia de la consistencia y cómo ser un ejemplo positivo en todos los aspectos de la vida. Desde el ejercicio y la alimentación equilibrada hasta la gestión del tiempo y el desarrollo personal, cada artículo ofrecerá consejos prácticos y acciones concretas para implementar en el día a día.</p>
				</div>
				<div class="col-xl-6">
					<img src="https://growingfamily.academy/wp-content/uploads/2024/08/imagenhabitos.jpg" alt="">
				</div>
			</div>
		</div>
	</section>

	<section class="habitospositivos my-5 pb-5">
		<div class="container">
			<div class="col-xl-12">
				<h2 class="font-size-44 color-barium fw-semibold pb-4">Descubre cuántos hábitos positivos tiene tu familia ¡Haz el test ahora!</h2>
				<div class="bg-approval border-radius-16 p-5">
					<div class="col-xl-7 py-5">
						<h2 class="font-size-32 color-barium fw-semibold pb-4">¡Pon a prueba tu habilidad sobre hábitos positivos en familia!</h2>
						<p class="font-size-20 color-barium">Este cuestionario está diseñado para hacerte ver qué tan bien están tu comprensión e implementación de hábitos en la vida diaria de la familia.</p>
						<p class="time font-size-16 color-iron"><img src="https://growingfamily.academy/wp-content/uploads/2024/07/clock.svg" alt="clock" class="align-bottom">Duración: 2 min</p>
						<p class="mt-5"><a href="https://growingfamily.academy/habitos-en-la-familia/encuesta/" class="btn-blue font-size-20 px-5">Realizar test</a></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="subtemas my-5 py-5">
		<div class="container mb-5">
			<div class="row">
				<?php $my_query = new WP_Query(array('posts_per_page' => 3));
				if ( have_posts() ) { while ( $my_query->have_posts() ) { 	$my_query->the_post(); ?>
					<div class="col-xl-4 article mb-5">
						<div class="p-0 border-radius-8 box-shadow-30">
							<a href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'class' => 'border-radius-8-top' ) ); } ?>
							</a>
							<div class="article-content p-4 bg-white border-radius-8-bottom">
								<a href="<?php the_permalink(); ?>" class="color-barium text-decoration-none">
									<?php the_title( '<h2 class="font-size-24 color-barium fw-semibold pb-4">', '</h2>' ); ?>
								</a>
								<p class="extract font-size-16 color-barium">
									<?php $excerpt = get_the_excerpt(); $striped_excerpt = strip_tags($excerpt); 
									echo $striped_excerpt; ?>
								</p>
								<p class="mt-4">
									<a href="<?php the_permalink(); ?>" class="btn-blue font-size-16 px-5">Leer más</a>
								</p>
							</div>
						</div>
					</div>
				<?php } } elseif ( is_search() ) { } ?>
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
