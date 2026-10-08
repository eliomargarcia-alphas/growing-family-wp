<?php
/**
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

	<section class="recursos my-xl-5 py-5">
		<div class="container">
			<div class="col-xl-12 my-5 text-center">
				<h2 class="font-size-44 font-size-35-m color-barium fw-semibold">Hoy… ¿Qué te pesa más en la crianza?</h2>
				<p class="font-size-20 color-barium">Esto no va de padres o madres perfectos que todo lo saben o que no sienten… Esto va de familias que crecen juntas. ¿Hoy qué te pesa más en la crianza?</p>
			</div>
			<?php echo do_shortcode('[rev_slider alias="slider-2"][/rev_slider]'); ?>
		</div>
	</section>

	<section class="mt-xl-5 pt-5">
		<div class="container">
			<?php echo do_shortcode('[rev_slider alias="Manifiesto-Growing-Family---slider-2"][/rev_slider]'); ?>
		</div>
	</section>

	<section class="mb-xl-5 pb-5">
		<div class="container">
			<?php echo do_shortcode('[rev_slider alias="Lo-que-queremos-en-sus-vidas---slider-blanco"][/rev_slider]'); ?>
		</div>
	</section>

	<section class="my-5 pt-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 text-center">
					<h2 class="font-size-44 color-barium">Cada familia es única</h2>
					<p class="font-size-32 color-barium">Explora por etapa de desarrollo o tipo de familia.</p>
					<p class="font-size-20 color-barium">Porque no es lo mismo acompañar a un niño de 3 que convivir con un adolescente. Y tampoco es lo mismo criar por primera vez, hacerlo en solitario o reconstruir una familia desde lo nuevo.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="cuadros-azules">
		<div class="container">
			<div class="row">
				<?php echo do_shortcode('[slide-anything id="746"]') ?>
			</div>
		</div>
	</section>

	<section class="my-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 text-center">
					<p class="font-size-32 color-barium">Por tipo de familia</p>
					<p class="font-size-20 color-barium">Familias reconstituidas Nuevas configuraciones, vínculos que se construyen sin borrar lo anterior.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="cuadros-azules mb-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-4 mb-4">
					<div class="card h-100 bg-titanium border-radius-16 py-3" style=" border-left: 6px solid #005B96; border-top: 0; border-right: 0; border-bottom: 0; ">
      					<div class="card-body">
      						<div class="row align-items-center">
      							<div class="col-xl-12">
      								<h2 class="font-size-20 color-barium fw-semibold">Padres primerizos</h2>
      								<p class="font-size-16 color-barium">Todo es nuevo. Todo parece urgente. Todo se siente como si hubiera una forma “correcta” de hacerlo.</p>
      							</div>
      						</div>
      					</div>
      					<div class="card-footer bg-titanium border-0">
						    <p class="font-size-16 fw-bold"><a href="#" class="color-barium">Explorar ></a></p>
						</div>
    				</div>
				</div>
				<div class="col-xl-4 mb-4">
					<div class="card h-100 bg-titanium border-radius-16 py-3" style=" border-left: 6px solid #D06F47; border-top: 0; border-right: 0; border-bottom: 0; ">
      					<div class="card-body">
      						<div class="row align-items-center">
      							<div class="col-xl-12">
      								<h2 class="font-size-20 color-barium fw-semibold">Familias monoparentales</h2>
      								<p class="font-size-16 color-barium">Crianza desde la presencia total y la sobrecarga silenciosa.</p>
      							</div>
      						</div>
      					</div>
      					<div class="card-footer bg-titanium border-0">
						    <p class="font-size-16 fw-bold"><a href="#" class="color-barium">Explorar ></a></p>
						</div>
    				</div>
				</div>
				<div class="col-xl-4 mb-4">
					<div class="card h-100 bg-titanium border-radius-16 py-3" style=" border-left: 6px solid #9370DB; border-top: 0; border-right: 0; border-bottom: 0; ">
      					<div class="card-body">
      						<div class="row align-items-center">
      							<div class="col-xl-12">
      								<h2 class="font-size-20 color-barium fw-semibold"> Familias reconstituidas</h2>
      								<p class="font-size-16 color-barium">Nuevas configuraciones, vínculos que se construyen sin borrar lo anterior.</p>
      							</div>
      						</div>
      					</div>
      					<div class="card-footer bg-titanium border-0">
						    <p class="font-size-16 fw-bold"><a href="#" class="color-barium">Explorar ></a></p>
						</div>
    				</div>
				</div>
			</div>
		</div>
	</section>

	<section class="blog mt-5 pt-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 text-center mb-5">
					<h2 class="font-size-44 font-size-35-m color-barium fw-semibold">Recursos destacados</h2>
					<p class="font-size-32 color-barium">Herramientas, lecturas, videos y guías seleccionadas con intención: para acompañarte justo en lo que hoy necesitas sostener.</p>
				</div>
				<?php echo do_shortcode('[rev_slider alias="Carrusel-Entradas-Home"][/rev_slider]'); ?>
				<div class="col-xl-12 text-center my-3 mt-5">
					<p class="font-size-32 color-barium">El despertar de una paternidad consciente - Videos Cortos</p>
				</div>
				<?php echo do_shortcode('[rev_slider alias="Carrusel-de-Videos"][/rev_slider]'); ?>
			</div>
		</div>
	</section>


</main><!-- #site-content -->

<?php get_footer(); ?>
