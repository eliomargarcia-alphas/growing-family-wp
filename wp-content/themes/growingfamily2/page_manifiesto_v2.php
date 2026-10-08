<?php
/**
 * Template Name: Manifiesto V2.0
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">
	<section class="bg-manifiesto pb-5 bb-20-turquoie">
		<div class="container pb-5">
			<div class="migas mb-5 pb-5 pt-3">
				<p class="font-size-12 text-white">
					<a href="https://growingfamily.academy/" class="text-white text-decoration-none"><b>Inicio</b></a> > <a href="https://growingfamily.academy/acerca-de/" class="text-white text-decoration-none"><b>Acerca de Growing Family</b></a> > <?php the_title(); ?></span> 
				</p>
			</div>
			<h1 class="font-size-44 text-white mb-0 pb-5 text-center">Manifiesto Growing Family</h1>
		</div>
	</section>

	<section class="quenostrajo pt-5 py-xl-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-5 order-2 order-xl-1">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/09/felicidad.jpg" alt="Una familia feliz" class="border-radius-16">
				</div>
				<div class="col-xl-7 pt-4 pt-xl-0 order-1 order-xl-2">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">Qué nos trajo hasta aquí</h2>
					<p class="font-size-20 color-barium">Nos formamos para muchas cosas,<br>pero poco para aquello en donde somos irreemplazables…</p>
					<p class="font-size-20 color-barium">Nos preparamos para encontrar un buen trabajo.<br>
					Nos decimos que hay que estudiar, formarse, mejorar.</p>
					<p class="color-barium font-size-20 fw-semibold fst-italic">Pero casi nunca nos preparamos para criar, para guiar.</p>
					<p class="font-size-20 color-barium">Como si el solo hecho de tener hijos, o haber tenido padres,<br>fuera suficiente para saber acompañar… ser madre o padre.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="quenostrajo pt-5 py-xl-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-7">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">El mito del "camino"</h2>
					<p class="font-size-20 color-barium">Nos repetimos que se aprende en el camino…<br>pero en ese camino a veces perdemos el control,<br>nos culpamos, nos sentimos solos,<br>repetimos lo que no queríamos repetir.</p>
					<p class="color-barium font-size-20 fw-semibold fst-italic">Aprender solo desde el error ya no es suficiente.</p>
					<p class="font-size-20 color-barium">En el mundo actual, ese riesgo lo terminan pagando nuestros hijos.</p>
					<p class="font-size-20 color-barium">Muchas veces, sin querer, sin saber,<br>nos perdemos y dejamos de ver lo increíble que puede ser<br>ser madre o padre desde la presencia y el propósito.</p>
				</div>
				<div class="col-xl-5 mb-xl-0">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/09/elcamino.jpg" alt="Un padre camina con su hijo hacia el atardecer" class="border-radius-16">
				</div>
			</div>
		</div>
	</section>

	<section class="quenostrajo pt-5 py-xl-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-5 order-2 order-xl-1">
					<img src="https://growingfamily.academy/wp-content/uploads/2025/08/familia-cenando.jpg" alt="Familia cenando" class="border-radius-16">
				</div>
				<div class="col-xl-7 pt-xl-0 order-1 order-xl-2">
					<h2 class="font-size-32 color-barium fw-semibold pb-4">En lo que creemos</h2>
					<p class="font-size-20 color-barium">En Growing Family, ante todo, somos madres y padres que creemos<br>que la paternidad en el mundo actual debe ser mucho más<br>que instinto, buenas intenciones e improvisación.</p>
					<p class="color-barium font-size-20 fw-semibold fst-italic">Es un proceso de autorrealización y transformación humana<br>que merece espacio, conciencia y acompañamiento real.</p>
					<p class="font-size-20 color-barium">Aquí no hay manuales milagrosos, pero sí herramientas, palabras y formas de acompañarnos mientras crecemos y ayudamos a crecer a nuestros hijos…</p>
					<p class="font-size-20 color-barium">La crianza con propósito no es tener todas las respuestas,<br>ni tener todo el tiempo del mundo.</p>
					<p class="font-size-20 color-barium">Es atreverse a derribar mitos, a crecer desde la duda, a aprovechar lo cotidiano y a mirar hacia adentro, con amor y sin miedo.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="creemos py-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-12 text-center">
					<p class="color-barium font-size-24 fst-italic"><b>Eso, para  nosotros es Growing Family.</b></p>
					<p class="color-barium font-size-24 fst-italic"><b>Crecer en familia.</b></p>
				</div>
			</div>
		</div>
	</section>

	<section class="blog mt-5 pt-5 bg-titanium">
		<div class="container pb-5">
			<div class="row py-5">
				<div class="col-xl-12 mb-5 text-center text-xl-start">
					<h2 class="font-size-32 color-barium fw-semibold">Nuestros articulos fundamentales</h2>
					<p class="font-size-20 color-barium">Herramientas, lecturas, videos y guías seleccionadas con intención: para acompañarte justo en lo que hoy necesitas sostener.</p>
				</div>
				<div class="px-3 px-xl-0 mb-5">
					<?php echo do_shortcode('[rev_slider alias="Carrusel-Entradas-Manifiesto"][/rev_slider]'); ?>
				</div>
			</div>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
