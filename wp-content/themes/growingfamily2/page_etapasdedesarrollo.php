<?php
/**
 * Template Name: Etapa de Desarrollo
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">
	<section class="top-info bb-20-turquoie" <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); echo 'style="background: url('.esc_url($featured_img_url).') center 30% no-repeat; background-size: cover;"'?>>
			<div class="pb-5 top-info" style="background: #37425fbd;">
				<div class="container pb-5">
					<div class="migas mb-5 pb-5 pt-3">
						<p class="font-size-12 text-white">
							<a href="https://growingfamily.academy/" class="text-white text-decoration-none"><b>Inicio</b>
							</a> > <?php the_title(); ?></span>
						</p>
					</div>
					<h1 class="font-size-44 text-white mb-3 text-center">Cada familia es única</h1>
					<p class="font-size-20 fw-normal text-white text-center mb-0">Explora el contenido según las <b>Etapas de desarrollo:</b></p>
				</div>
			</div>
	</section>

	<section class="item mt-5 mb-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/bebe.jpg) center center no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #a0d8ef80;border-radius: 30px;padding: 3px 10px">0-1 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">El inicio del vínculo</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Vínculo corporal, seguridad emocional, y apego.</p>
					<a href="https://growingfamily.academy/01-anos-comprender-el-mundo-emocional-de-tu-bebe-para-conectar-sin-presion/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-xl-0">
				<div class="col-12 col-xl-4 border-radius-r-16 border-radius-t-16-m height-300-m order-xl-2" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/nino4anos.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m order-xl-1">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #F6B96080;border-radius: 30px;padding: 3px 10px">1–3 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">La autonomía que necesita brazos</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Emergencia del yo, exploración con necesidad de refugio.</p>
					<a href="https://growingfamily.academy/1-3-anos-acompanar-emociones-intensas-sin-culpa/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-xl-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/nina5anos.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #F86D6D80;border-radius: 30px;padding: 3px 10px">3–5 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Emociones grandes en cuerpos pequeños</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Regulación emocional incipiente, imaginación desbordante.</p>
					<a href="https://growingfamily.academy/3-5-anos-navegando-el-mundo-de-los-por-que-y-las-emociones-grandes/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-r-16 border-radius-t-16-m height-300-m order-xl-2" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/nino-bici.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m order-xl-1">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #B38DDB80;border-radius: 30px;padding: 3px 10px">6–9 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">La edad de los logros y las dudas</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Pensamiento moral, comparación, sensibilidad al error.</p>
					<a href="https://growingfamily.academy/6-9-anos-cuando-tu-hijo-descubre-que-no-eres-un-superheroe-y-duele-un-poco/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/nina12anos.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #A7A9AC80;border-radius: 30px;padding: 3px 10px">10–12 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Entre el espejo y la distancia</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Construcción de identidad, tensión entre necesidad y rechazo del adulto.</p>
					<a href="https://growingfamily.academy/10-12-anos-el-territorio-desconocido-entre-ninez-y-adolescencia/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-r-16 border-radius-t-16-m height-300-m order-xl-2" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/joven15anos.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m order-xl-1">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #C0392B80;border-radius: 30px;padding: 3px 10px">13–15 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">El fuego de la afirmación</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Rebeldía, redefinición, deseo de ser escuchado sin ser moldeado.</p>
					<a href="https://growingfamily.academy/13-15-anos-acompanar-con-firmeza-amorosa-en-la-tormenta-adolescente/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

	<section class="item my-5 pb-xl-3 mx-3 mx-xl-0">
		<div class="container p-xl-0 bg-titanium border-radius-16">
			<div class="row p-0">
				<div class="col-12 col-xl-4 border-radius-l-16 border-radius-t-16-m height-300-m" style="background: url(https://growingfamily.academy/wp-content/uploads/2025/09/chicauniversidad.jpg) center 30%no-repeat; background-size: cover;">
				</div>
				<div class="col-12 col-xl-8 p-4 p-xl-5 bg-titanium border-radius-16 border-radius-b-16-m">
					<h2 class="font-size-16 color-barium fw-bold mb-4">
						<span style="background: #A7A9AC80;border-radius: 30px;padding: 3px 10px">16–18 años</span>
					</h2>
				    <h2 class="font-size-32 color-barium fw-semibold mb-4">Separarse sin perderse</h2>
				    <p class="font-size-20 fw-normal color-barium mb-4">Autonomía en ensayo. Piden distancia, pero observan si seguimos ahí.</p>
					<a href="https://growingfamily.academy/16-18-anos-soltar-las-riendas-sin-soltar-la-conexion/" class="btn-green font-size-16 fw-semibold text-uppercase mb-5 letter-spacing-3">Ir al contenido <img src="https://growingfamily.academy/wp-content/uploads/2025/08/more.svg" alt=">"></a>
				</div>
			</div>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
