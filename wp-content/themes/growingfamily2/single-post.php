<?php
/**
 * Entrada del blog (nota de padre a padre, capa 5) con el mismo diseño que las guías y las actividades: migas,
 * título, «Escrito por» y fecha, ficha en el panel lateral (franja en móvil), contenido, puente, «¿Te ha gustado
 * este contenido?» y relacionados (otras entradas del blog).
 *
 * Todas las entradas usan esta plantilla aunque tengan asignada una antigua (Tipo carta, Cómo me siento…):
 * ver gwf_post_single_template() en inc/gwf-blog.php.
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gwf_id      = get_the_ID();
	$gwf_cats    = array_values(
		array_filter(
			(array) get_the_category( $gwf_id ),
			function ( $c ) {
				return 'blog' !== strtolower( $c->name );
			}
		)
	);
	$gwf_minutes = gwf_reading_time();
	$gwf_byline  = gwf_author_byline();
	$gwf_updated = (int) get_the_modified_time( 'U' );
	$gwf_date    = gwf_g_date_html( $gwf_updated - (int) get_the_time( 'U' ) > DAY_IN_SECONDS ? $gwf_updated : (int) get_the_time( 'U' ) );
	$gwf_summary = get_post_meta( $gwf_id, 'mostrar_resumen_del_post', true ) ? trim( (string) get_post_meta( $gwf_id, 'resumen_del_post', true ) ) : '';

	// Contenido con ancla en cada H2 e índice «En este artículo» (gwf_post_content_with_toc en inc/gwf-blog.php).
	list( $gwf_content, $gwf_toc ) = gwf_post_content_with_toc();
	$gwf_toc_html                  = gwf_g_toc_html( $gwf_toc );

	// Relacionados: otras entradas del mismo tema y, si no llegan a 3, las más recientes.
	$gwf_related = $gwf_cats ? get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array( $gwf_id ), 'category__in' => wp_list_pluck( $gwf_cats, 'term_id' ), 'fields' => 'ids' ) ) : array();
	if ( count( $gwf_related ) < 3 ) {
		$gwf_related = array_merge( $gwf_related, get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3 - count( $gwf_related ), 'post__not_in' => array_merge( array( $gwf_id ), $gwf_related ), 'fields' => 'ids' ) ) );
	}
	?>

<main id="site-content" class="gwf-guia gwf-post" data-gwf-origen="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>" data-gwf-tipo="post">
	<div class="container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-g-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<div class="gwf-g-layout">
			<?php // div y no article: las reglas antiguas del blog (.single article h2/p, con !important) no deben aplicarse. ?>
			<div <?php post_class( 'gwf-g-main' ); ?> id="post-<?php the_ID(); ?>">

				<header class="gwf-g-header">
					<?php the_title( '<h1 class="gwf-g-title">', '</h1>' ); ?>
					<p class="gwf-g-byline">
						<?php if ( $gwf_byline ) : ?>Escrito por <?php echo $gwf_byline; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en gwf_author_byline. ?> · <?php endif; ?>
						<?php echo $gwf_updated - (int) get_the_time( 'U' ) > DAY_IN_SECONDS ? 'Actualizado el ' : ''; ?><?php echo $gwf_date; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>
					<?php // Franja compacta con la ficha (solo móvil). ?>
					<p class="gwf-g-strip">
						<?php if ( $gwf_cats ) : ?><?php echo esc_html( $gwf_cats[0]->name ); ?> · <?php endif; ?>
						<?php echo gwf_g_read_html( $gwf_minutes ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</p>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="gwf-a-cover"><?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'alt' => '' ) ); ?></figure>
				<?php endif; ?>

				<?php if ( $gwf_summary ) : // Resumen del post (campo de la entrada), como la respuesta directa de las guías. ?>
					<section class="gwf-g-block gwf-g-respuesta" aria-label="Resumen">
						<p><?php echo esc_html( $gwf_summary ); ?></p>
					</section>
				<?php endif; ?>

				<?php if ( $gwf_toc_html ) : // Índice desplegable (solo móvil). ?>
					<details class="gwf-g-toc-mobile" id="indice">
						<summary>En este artículo</summary>
						<ol><?php echo $gwf_toc_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en gwf_g_toc_html. ?></ol>
					</details>
				<?php endif; ?>

				<section class="gwf-g-block gwf-g-body entry-content">
					<?php echo $gwf_content; // phpcs:ignore WordPress.Security.EscapeOutput -- salida de the_content. ?>
				</section>

				<?php gwf_post_bridge(); // Enlace a su guía y puente al método, solo si la entrada los pide. ?>
			</div>

			<aside class="gwf-g-side" aria-label="Sobre esta nota">
				<div class="gwf-g-side-inner">
					<div class="gwf-g-card">
						<p class="gwf-g-card-title">Ficha</p>
						<ul class="gwf-g-facts">
							<?php foreach ( $gwf_cats as $gwf_cat ) : ?>
								<li>Tema · <a href="<?php echo esc_url( get_category_link( $gwf_cat ) ); ?>"><?php echo esc_html( $gwf_cat->name ); ?></a></li>
							<?php endforeach; ?>
							<li><?php echo gwf_g_read_html( $gwf_minutes ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
						</ul>
					</div>

					<?php if ( $gwf_toc_html ) : ?>
						<nav class="gwf-g-card gwf-g-toc" aria-label="En este artículo">
							<p class="gwf-g-card-title">En este artículo</p>
							<ol><?php echo $gwf_toc_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></ol>
						</nav>
					<?php endif; ?>
				</div>
			</aside>
		</div>

		<div class="gwf-g-bottom">
			<?php gwf_g_like(); ?>

			<?php if ( $gwf_related ) : ?>
				<nav class="gwf-g-block gwf-g-related" aria-label="Relacionados">
					<h2>Relacionados</h2>
					<ul>
						<?php
						foreach ( $gwf_related as $gwf_r ) {
							echo gwf_g_card( $gwf_r, 'Nota de padre a padre' ); // phpcs:ignore WordPress.Security.EscapeOutput -- gwf_g_card escapa.
						}
						echo gwf_g_method_card(); // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div class="comments-wrapper section-inner"><?php comments_template(); ?></div>
			<?php endif; ?>
		</div>
	</div>
</main>

	<?php
endwhile;

get_footer();
