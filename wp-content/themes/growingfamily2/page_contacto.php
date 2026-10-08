<?php
/**
 * Template Name: Contacto
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

get_header();
?>

<main id="site-content grey">

	<section class="bg-contacto py-5 bb-20-turquoie">
		<div class="container">
			<p class="font-size-32 fw-normal text-white mb-0">Para cualquier información</p>
			<h1 class="font-size-60 text-white mb-0">Contáctanos</h1>
		</div>
	</section>

	<section class="my-5 py-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					<h2 class="font-size-32 color-barium fw-semibold">Encuentra Respuestas Rápidas</h2>
					<p class="font-size-24 color-barium pb-2">Preguntas Frecuentes</p>
					<div class="accordion">
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta uno, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta dos, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta tres, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta cuatro, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta cinco, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta seis, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
						<?php echo do_shortcode('[lightweight-accordion title="¿Pregunta siete, It is a long established fact that a reader will be distracted by the readable?" title_tag="h3"]
							<p class="font-size-20 color-barium">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isnt anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
						[/lightweight-accordion]') ?>
					</div>

				</div>
			</div>
		</div>
	</section>

	<section class="my-5 py-5">
		<div class="container">
			<div class="row">
				<div class="col-xl-6">
					<h2 class="font-size-32 color-barium fw-semibold pb-3">Valoramos tus comentarios</h2>
					<p class="font-size-20 color-barium pb-3">Estamos aquí para atender tus inquietudes, no dudes en utilizar el formulario a continuación para ponerte en contacto con nosotros. Estaremos encantados de leerte.</p>
					<div class="p-4 border-radius-8 b-1-iron">
						<?php echo do_shortcode('[contact-form-7 id="6a5dad7" title="Formulario de Contacto"]') ?>	
					</div>
				</div>
				<div class="col-xl-5 offset-xl-1">
					<p class="font-size-20"><a href="" class="color-barium text-decoration-none"><img src="https://growingfamily.academy/wp-content/uploads/2024/08/mail.svg" alt="envelope icon" class="me-4">growingfamilycenter@.com</a></p>
					<p class="font-size-20"><a href="" class="color-barium text-decoration-none"><img src="https://growingfamily.academy/wp-content/uploads/2024/08/location.svg" alt="map pin icon" class="me-4">Colombia Calle 14 # 65c - 44, Cali, Valle del Cauca.</a></p>
					<p class="font-size-20 pb-5 mb-5"><a href="" class="color-barium text-decoration-none"><img src="https://growingfamily.academy/wp-content/uploads/2024/08/call.svg" alt="phone icon" class="me-4">+00 0000000</a></p>
					<p class="font-size-24 color-barium fw-semibold pb-3">Encuéntranos y síguenos por:</p>
					<p>
						<a href="https://www.facebook.com/growingfamilyacad/" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/facebook.svg" alt="facebook"></a>
						<a href="https://www.instagram.com/growingfamilyacademy/" rel="nofollow" target="_blank" class="me-4"><img src="https://growing.family/wp-content/uploads/2024/08/instagram.svg" alt="instagram"></a>
						<a href="https://www.tiktok.com/@growingfamily.academy" rel="nofollow" target="_blank"><img src="https://growing.family/wp-content/uploads/2024/08/tiktok.svg" alt="tiktok"></a>
					</p>
				</div>
			</div>
		</div>
	</section>

	<section class="frase bg-contacto py-5 mt-5 text-center">
		<div class="container py-5">
			<h2 class="font-playfair font-size-32 fw-normal text-white">“Cada ser humano está programado con el empuje necesario para conquistar su autonomía y felicidad."</h2>
			<p class="font-size-24 text-white">-Ann Landers-</p>
		</div>
	</section>

</main><!-- #site-content -->

<?php get_footer(); ?>
