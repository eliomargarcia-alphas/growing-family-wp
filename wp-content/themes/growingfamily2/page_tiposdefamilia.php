<?php
/**
 * Template Name: Tipos de Familia
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">
	<section class="top-info bb-20-turquoie" <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); echo 'style="background: url('.esc_url($featured_img_url).') no-repeat center; background-size: cover;"'?>>
			<div class="pb-5 top-info" style="background: #37425fbd;">
				<div class="container pb-5">
					<div class="migas mb-5 pb-5 pt-3">
						<p class="font-size-12 text-white">
							<a href="https://growingfamily.academy/" class="text-white text-decoration-none"><b>Inicio</b>
							</a> > <?php the_title(); ?></span>
						</p>
					</div>
					<h1 class="font-size-44 text-white mb-3 text-center">Cada familia es única</h1>
					<p class="font-size-20 fw-normal text-white text-center mb-0">Explora el contenido según los <b>Tipos de Familia:</b></p>
				</div>
			</div>
	</section>

	<section class="item mt-5 mb-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/padresprimerizos.jpg) center center no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #a0d8ef80;border-radius: 30px;padding: 3px 10px">El inicio de todo</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Padres primerizos</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Todo es nuevo y parece urgente. Todo se siente como si hubiera una forma “correcta” de hacerlo.</p>
					<a href="https://growingfamily.academy/padres-primerizos-navegar-la-urgencia-desde-la-construccion-gradual-de-confianza/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-r-16 border-radius-t-16-m height-300-m order-xl-2" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/monoparental-1.jpg) center 40% no-repeat;background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m order-xl-1">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #F6B96080;border-radius: 30px;padding: 3px 10px">La fuerza de uno</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Familias monoparentales</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Crianza desde la presencia total y la sobrecarga silenciosa.</p>
					<a href="https://growingfamily.academy/familias-monoparentales-navegar-la-crianza-desde-la-fortaleza-invisible-y-la-sobrecarga-real/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/reconstituidas.jpg) center 40% no-repeat;background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #F86D6D80;border-radius: 30px;padding: 3px 10px">Un nuevo comienzo</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Familias reconstituidas</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Nuevas configuraciones, vínculos que se construyen desde una nueva etapa.</p>
					<a href="https://growingfamily.academy/familias-reconstituidas-construir-amor-sobre-historias-que-continuan-no-que-se-borran/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
