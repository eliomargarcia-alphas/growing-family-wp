<?php
/**
 * Template Name: Acerca - Propuesta
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">
	<section class="top-info bb-20-turquoie mb-5" <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); echo 'style="background: url('.esc_url($featured_img_url).') no-repeat bottom center; background-size: cover;"'?>>
			<div class="pb-5 top-info" style="background: #37425fbd;">
				<div class="container pb-5">
					<div class="migas mb-5 pb-5 pt-3">
						<p class="font-size-12 text-white">
							<a href="https://growingfamily.academy/" class="text-white text-decoration-none"><b>Inicio</b></a> > <a href="https://growingfamily.academy/acerca-de/" class="text-white text-decoration-none"><b>Acerca de Growing Family</b></a> > <?php the_title(); ?></span> 
						</p>
					</div>
					<h1 class="font-size-44 text-white mb-3 text-center">Nuestra Promesa</h1>
				</div>
			</div>
	</section>

	<section class="queremos-dejar py-5 mb-xl-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-7">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Lo que queremos dejar en sus vidas</h2>
					<p class="font-size-20 color-barium fw-semibold fst-italic">Es una forma de estar y acompañar, no una fórmula.</p>
					<p class="font-size-20 color-barium">Hemos aprendido que esto no va de atajos ni promesas falsas.</p>
					<p class="font-size-20 color-barium">Tampoco de padres perfectos.</p>
					<p class="font-size-20 color-barium">Y mucho menos de fórmulas mágicas para encontrar la felicidad.</p>
					<p class="font-size-20 color-barium"><b>Esto va de crecer junto a nuestros hijos hacia una vida plena:</b> una vida con errores, con sufrimiento, con alegrías, derrotas y pequeñas victorias.</p>
				</div>
				<div class="col-xl-5 mb-xl-0">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/09/dejar-en-sus-vidas.jpg" alt="Una madre abraza a su hijo" class="border-radius-16">
				</div>
			</div>
		</div>
	</section>

	<section class="legado my-5 pb-xl-5 mx-3 mx-xl-0">
		<div class="container py-3 px-4 px-xl-5 bg-titanium border-radius-16">
			<div class="row py-3 py-xl-5 px-0 px-xl-5">
				<div>
					<h2 class="font-size-32 color-barium fw-semibold pb-4">El legado real</h2>
					<p class="font-size-20 fw-normal color-barium">Cuando nuestro camino llegue a su fin —porque llegará—, lo que quedará no serán nuestras respuestas... sino nuestra presencia. Esa será la que siga guiando su camino.</p>
					<p class="font-size-20 fw-normal color-barium">Esto va de estar hoy ahí, cuando todo se siente caótico, cuando no tenemos respuestas o sentimos que no lo estamos haciendo bien, pero aun así volvemos a intentarlo al día siguiente.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="promesa py-5 mb-xl-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-7 order-2 order-xl-1">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Nuestra Promesa</h2>
					<p class="font-size-20 color-barium"><b>Creemos en:</b></p>
					<ul class="font-size-20 color-barium icon-check-blue">
						<li><b>El acompañamiento real.</b></li>
						<li><b>Los silencios que también educan.</b></li>
						<li><b>El cansancio que nadie ve y sostiene una familia.</b></li>
						<li><b>Que cada familia es única, especial e incomparable.</b></li>
					</ul>
					<p class="font-size-20 color-barium">En Growing Family no creemos en fórmulas.</p>
				</div>
				<div class="col-xl-5 mb-5 mb-xl-0 order-1 order-xl-2">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/09/familia-cenando.jpg" alt="Una fmailia cenando y disfrutando el momento" class="border-radius-16">
				</div>
			</div>
		</div>
	</section>

	<section class="verdad py-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-5">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/09/ensenanza.jpg" alt="Un padre hace los deberes junto a su hija" class="border-radius-16">
				</div>
				<div class="col-xl-7 pt-4 pt-xl-0">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Por eso, lo que compartimos aquí no es una verdad absoluta.</h2>
					<p class="font-size-20 color-barium">Es guía con criterio, basada en experiencias reales.</p>
					<p class="font-size-20 color-barium">Es contenido creado por madres, padres, psicólogos, educadores y especialistas.</p>
					<p class="font-size-20 color-barium">Respaldado con rigor científico y fuentes abiertas.</p>
					<p class="font-size-20 color-barium">Diseñado para distintas etapas, distintas necesidades, distintas formas de ser familia.</p>
					<p class="font-size-20 color-barium">Escrito con respeto, sin condescendencia, siempre desde un lugar humano.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="legado my-5 pb-5 mx-3 mx-xl-0">
		<div class="container py-3 px-4 px-xl-5 bg-titanium border-radius-16">
			<div class="row py-3 py-xl-5 px-0 px-xl-5">
				<div>
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Porque, en el fondo, no se trata de hacerlo perfecto</h2>
					<p class="font-size-20 fw-normal color-barium fw-bold fst-italic">Se trata de tener propósito en nuestra paternidad. Vivirla con presencia, sentido y amor real.</p>
				</div>
			</div>
		</div>
	</section>
	


</main><!-- #site-content -->

<?php get_footer(); ?>
