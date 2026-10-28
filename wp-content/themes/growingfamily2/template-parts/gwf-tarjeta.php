<?php
/**
 * Tarjeta de los listados (guías, pilares y blog) para la entrada actual del loop: tema sobre la foto,
 * «me gusta» abajo sobre la foto, título, resumen, tiempo de lectura y «Leer más».
 *
 * @param array $args {
 *     @type WP_Term|null $badge     Término de la etiqueta sobre la foto.
 *     @type string       $badge_url Enlace de la etiqueta.
 *     @type string       $summary   Resumen (se recorta a 24 palabras).
 *     @type int          $minutes    Minutos (de lectura o, en actividades, de la actividad).
 *     @type string       $time_label Texto tras los minutos (por defecto «min de lectura»).
 * }
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_badge   = $args['badge'] ?? null;
$gwf_summary = $args['summary'] ?? '';
?>
<div class="col-xl-4 article mb-5 resultado">
	<article class="blog-card">
		<div class="blog-image-wrapper">
			<?php if ( $gwf_badge ) : ?>
				<p class="cat font-size-12 text-uppercase">
					<a href="<?php echo esc_url( $args['badge_url'] ); ?>" class="text-white font-size-12 text-decoration-none gwf-l-badge px-3 py-2 border-radius-24 d-inline-block"><?php echo esc_html( $gwf_badge->name ); ?></a>
				</p>
			<?php endif; ?>
			<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium_large', array( 'class' => 'blog-image', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1200px) 30vw, 100vw' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( content_url( '/uploads/2026/08/article-1-opt.webp' ) ); ?>" alt="" class="blog-image" width="374" height="200" loading="lazy" decoding="async">
				<?php endif; ?>
			</a>
			<?php echo do_shortcode( '[wp_ulike]' ); ?>
		</div>
		<div class="blog-card-body">
			<h2 class="blog-card-title"><a href="<?php the_permalink(); ?>" class="gwf-l-title-link"><?php the_title(); ?></a></h2>
			<?php if ( $gwf_summary ) : ?><p class="gwf-l-summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $gwf_summary ), 24, '…' ) ); ?></p><?php endif; ?>
			<?php if ( ! empty( $args['minutes'] ) ) : ?><div class="blog-meta"><?php echo gwf_g_read_html( $args['minutes'], $args['time_label'] ?? 'min de lectura' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
			<a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">Leer más<span class="screen-reader-text">: <?php the_title(); ?></span></a>
		</div>
	</article>
</div>
