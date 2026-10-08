<?php
/**
 * Template Name: Home Test
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">

    <section class="slider-principal bb-20-turquoie-">
    	<div class="d-none d-sm-block">
    		<?php echo do_shortcode('[rev_slider alias="typewritereffect"][/rev_slider]'); ?>
    	</div>
    	<div class="d-block d-sm-none">
			<?php echo do_shortcode('[rev_slider alias="slider-3"][/rev_slider]'); ?>
		</div>
	</section>

	<section class="container my-5 pt-5">
		<div class="row mt-5">
			<div class="col-12 col-xl-6  text-center px-2">
				<div class="bg-titanium border-radius-16 px-3 py-5 bg-ovalo-1">
					<div class="mb-5">
						<h3 class="color-iron font-size-16 fw-semibold text-uppercase pt-5 letter-spacing-5">Qué nos trajo hasta aquí</h3>
						<h2 class="font-size-32 color-barium fw-bold px-5 py-4">Manifiesto<br>Growing Family</h2>
						<p class="font-size-20 color-barium px-5 pb-4">Nos formamos para muchas cosas, pero poco para aquello en donde somos irreemplazables…</p>
						<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Seguir leyendo <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-6 text-center px-2">
				<div class="bg-titanium border-radius-16 px-3 py-5 bg-ovalo-2">
					<div class="mb-5">
						<h3 class="color-iron font-size-16 fw-semibold text-uppercase pt-5 letter-spacing-5">Desde lo humano y cotidiano</h3>
						<h2 class="font-size-32 color-barium fw-bold px-5 py-4">Nuestro<br>Compromiso</h2>
						<p class="font-size-20 color-barium px-5 mx-5 pb-4">En Growing Family no creemos en fórmulas. Creemos en el acompañamiento real.</p>
						<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Seguir leyendo <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="recursos my-xl-5 py-5">
		<div class="container">
			<div class="col-xl-12 my-5">
				<h2 class="font-size-32 color-barium fw-semibold">Hoy… ¿Qué te pesa más en la crianza?</h2>
				<p class="font-size-20 color-barium">Esto no va de padres o madres perfectos que todo lo saben o que no sienten… Esto va de familias que crecen juntas. ¿Hoy qué te pesa más en la crianza?</p>
			</div>
			<?php echo do_shortcode('[rev_slider alias="carousel-verde"][/rev_slider]'); ?>
		</div>
	</section>

	<section class="my-5 pt-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					<h2 class="font-size-32 color-barium fw-semibold">Cada familia es única</h2>
					<p class="font-size-20 color-barium">Porque no es lo mismo acompañar a un niño de 3 que convivir con un adolescente. Y tampoco es lo mismo criar por primera vez, hacerlo en solitario o reconstruir una familia desde lo nuevo.</p>
					<div class='tabs'>
						<label class="font-size-20 color-barium">Explorar por:</label>  
						<!-- Tab 1 & Content -->
						<input type="radio" name="tab" id="tab1" role="tab" checked>
						<label for="tab1" id="tab1-label" class="font-size-20 color-barium hover-active">Etapa de desarrollo</label>
						<section aria-labelledby="tab1-label">
						    <?php echo do_shortcode('[slide-anything id="746"]') ?>
						</section>
						<!-- Tab 2 & Content -->
						<input type="radio" name="tab" id="tab2" role="tab">
						<label for="tab2" id="tab2-label" class="font-size-20 color-barium hover-active">Tipo de Desarrollo</label>
						<section aria-labelledby="tab2-label">
						    <div class="row justify-content-center py-5">
								<div class="col-xl-3 mb-4">
									<div class="card h-100 bg-white border-radius-16 py-3 border-0">
				      					<div class="card-body">
				      						<div class="row align-items-center">
				      							<div class="col-xl-12">
				      								<h2 class="font-size-20 color-barium fw-bold">Padres primerizos</h2>
				      								<p class="font-size-16 color-barium">Todo es nuevo. Todo parece urgente. Todo se siente como si hubiera una forma “correcta” de hacerlo.</p>
				      							</div>
				      						</div>
				      					</div>
				      					<div class="card-footer bg-white border-0">
										    <p>
										    	<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Explorar <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
										    </p>
										</div>
				    				</div>
								</div>
								<div class="col-xl-3 mb-4">
									<div class="card h-100 bg-white border-radius-16 py-3 border-0">
				      					<div class="card-body">
				      						<div class="row align-items-center">
				      							<div class="col-xl-12">
				      								<h2 class="font-size-20 color-barium fw-bold">Familias monoparentales</h2>
				      								<p class="font-size-16 color-barium">Crianza desde la presencia total y la sobrecarga silenciosa.</p>
				      							</div>
				      						</div>
				      					</div>
				      					<div class="card-footer bg-white border-0">
										    <p>
										    	<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Explorar <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
										    </p>
										</div>
				    				</div>
								</div>
								<div class="col-xl-3 mb-4">
									<div class="card h-100 bg-white border-radius-16 py-3 border-0">
				      					<div class="card-body">
				      						<div class="row align-items-center">
				      							<div class="col-xl-12">
				      								<h2 class="font-size-20 color-barium fw-bold"> Familias reconstituidas</h2>
				      								<p class="font-size-16 color-barium">Nuevas configuraciones, vínculos que se construyen sin borrar lo anterior.</p>
				      							</div>
				      						</div>
				      					</div>
				      					<div class="card-footer bg-white border-0">
										    <p>
										    	<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Explorar <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
										    </p>
										</div>
				    				</div>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="my-5 pt-5 para-ti">
		<div class="container pt-5 mt-5 pb-5 ps-5 border-radius-16">
			<div class="row pt-5 pb-3">
				<div class="col-xl-3 pt-5 mt-5">
					<h2 class="font-size-32 text-white fw-semibold mb-4 pt-5 mt-5">Growing Family está aquí para ti</h2>
					<a href="#" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Descrubre más<img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="blog mt-5 pt-5 mb-5">
		<div class="container mb-5 pb-5">
			<div class="row pt-5">
				<div class="col-xl-12 mb-5">
					<h2 class="font-size-32 color-barium fw-semibold">Un blog con propósito</h2>
					<p class="font-size-20 color-barium">Herramientas, lecturas, videos y guías seleccionadas con intención: para acompañarte justo en lo que hoy necesitas sostener.</p>
				</div>
				<?php echo do_shortcode('[rev_slider alias="carrusel-entradas-home-1"][/rev_slider]'); ?>
				<div class="my-5"><?php echo do_shortcode('[youtube-feed feed=1]'); ?></div>
			</div>
		</div>
	</section>


</main><!-- #site-content -->

<?php get_footer(); ?>
