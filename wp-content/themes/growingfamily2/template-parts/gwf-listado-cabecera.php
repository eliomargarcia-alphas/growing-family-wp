<?php
/**
 * Cabecera común de los listados (guías, pilares y blog): migas de Yoast, título centrado e introducción.
 *
 * @param array $args { title, intro }
 * @package WordPress
 * @subpackage growingfamily2
 */
?>
<section class="gwf-l-head pt-0 pb-5 title-section">
	<div class="container">
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-l-migas" aria-label="Migas de pan">', '</nav>' ); } ?>
		<h1 class="font-size-44 text-center mb-4 mt-5 gwf-l-h1"><?php echo esc_html( $args['title'] ); ?></h1>
		<?php if ( ! empty( $args['intro'] ) ) : ?>
			<p class="font-size-16 text-center text-black mb-0 gwf-l-intro"><?php echo esc_html( $args['intro'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
