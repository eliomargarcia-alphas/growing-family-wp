<?php
/**
 * Template Name: Temas Centrales - Tecnologia
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">

	<section class="py-5" style="background: url(https://growingfamily.academy/wp-content/uploads/2024/08/bg-habitos.svg) bottom right no-repeat #DAF8FF; background-size: contain;">
		<div class="container">
			<p class="font-size-24 fw-normal color-barium mb-0">Lo que necesitas saber para una crianza efectiva</p>
			<h1 class="font-size-52 color-barium mb-0">Hábitos y trascendencia en la familia</h1>
			<div class="row mt-5">
				<div class="col-xl-3 bg-white py-4 ps-4 br-2-iron bb-6-turquoise2">
					<p class="font-size-16 color-barium mb-0">Hábitos y transcendencia familia</p>
				</div>
				<div class="col-xl-3 bg-white py-4 ps-4 br-2-iron">
					<p class="font-size-16 mb-0">
						<a href="https://growingfamily.academy/temas-centrales/importancia-del-tiempo-en-familia/" class="color-barium text-decoration-none">
						Importancia del tiempo en familia <span><img src="https://growingfamily.academy/wp-content/uploads/2024/08/chevron-right.svg" alt=">" class="ms-3"></span>
						</a>
					</p>
				</div>
				<div class="col-xl-3 bg-white py-4 ps-4 br-2-iron">
					<p class="font-size-16 color-barium mb-0">
						<a href="https://growingfamily.academy/temas-centrales/capacidad-de-imaginar/" class="color-barium text-decoration-none">
						Capacidad de imaginar <span><img src="https://growingfamily.academy/wp-content/uploads/2024/08/chevron-right.svg" alt=">" class="ms-3"></span>
						</a>
					</p>
				</div>
				<div class="col-xl-3 bg-white py-4 ps-4">
					<p class="font-size-16 color-barium mb-0">
						<a href="https://growingfamily.academy/temas-centrales/nuevos-retos-y-habilidades/" class="color-barium text-decoration-none">
						Nuevos retos y habilidades <span><img src="https://growingfamily.academy/wp-content/uploads/2024/08/chevron-right.svg" alt=">" class="ms-3"></span>
						</a>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="my-5 py-5">
		<div class="container">
				<div class="col-xl-12 migas">
					<p class="font-size-16 color-blue">
						<a href="https://growingfamily.academy/temas-centrales/" class="color-blue text-decoration-none">Temas centrales
						</a> > Hábitos y trascendencia en la familia
					</p>
				</div>
			<div class="row align-items-center equal-height-row d-flex align-items-stretch">
				<div class="col-xl-8 pe-4 d-flex flex-column">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">La importancia de la cultura de hábitos como modelo a seguir en nuestros niños.</h2>
					<p class="font-size-20 color-barium">En la era digital, la tecnología es una herramienta poderosa que puede enriquecer la vida de nuestros hijos. Sin embargo, su uso responsable es fundamental para asegurar un desarrollo saludable y equilibrado. En esta sección, exploramos cómo los padres pueden guiar a sus hijos en el uso de la tecnología de manera segura y constructiva. Desde establecer límites de tiempo frente a las pantallas hasta seleccionar contenido educativo y fomentar el equilibrio entre el mundo digital y el real.</p>
					<p class="font-size-20 color-barium">A continuación te ofrecemos varios articulos para el abordaje tecnológico con responsabilidad donde encontraras ideas y recomendaciones que pueden ayudarte en el dia a dia para lograr una crianza feliz en tus hijos.</p>
				</div>
				<div class="col-xl-4 bg-iconbg border-radius-16 p-5 d-flex align-items-stretch">
					<div class="d-flex align-items-center">
						<div class="d-flex flex-column">
							<p class="font-size-20 color-barium">“La irrupción de las nuevas tecnologías nos obliga a educar a los niños de forma distinta”</p>
							<p class="font-size-20 color-barium">-Howard Gardner-</p>
						</div>
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

	<section class="frase bg-frase2 py-5 mt-5 text-center">
		<div class="container py-5">
			<h2 class="font-playfair font-size-32 fw-normal text-white">“Cada ser humano está programado con el empuje necesario para conquistar su autonomía y felicidad."</h2>
			<p class="font-size-24 text-white">-Álvaro Bilbao-</p>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
