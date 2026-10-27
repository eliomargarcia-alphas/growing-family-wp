<?php
/**
 * Ficha de revisor o redactor (wireframe «plantilla-revisor»): foto, nombre, cargo y rol, enlaces verificables,
 * formación, experiencia, temas que domina y su contenido (revisado o firmado), paginado.
 * La consulta principal ya trae ese contenido (gwf_guias_person_query); los datos, gwf_guias_person().
 *
 * @package WordPress
 * @subpackage growingfamily2
 */

$gwf_p = gwf_guias_person( get_queried_object_id() );

// Bloque 10: título y fecha según lo que hace la persona.
$gwf_list_title = array(
	'revisa'  => 'Contenido revisado',
	'redacta' => 'Contenido escrito',
	'ambos'   => 'Contenido revisado y escrito',
);
$gwf_types = array(
	'guia'       => 'Guía',
	'desarrollo' => 'Pilar',
	'post'       => 'Artículo',
);

/** Fecha de una fila: la de revisión si la revisó esta persona; si no, la de publicación. */
$gwf_row_date = function ( $post_id ) use ( $gwf_p ) {
	$review = (int) get_post_meta( $post_id, 'gwf_revisado_por', true ) === $gwf_p['id'] ? gwf_guias_review_time( $post_id ) : null;
	return $review
		? array( 'Revisado el', $review )
		: array( 'Publicado el', (int) get_post_time( 'U', false, $post_id ) );
};

$gwf_initial  = mb_substr( preg_replace( '/[^\p{L}]/u', '', preg_replace( '/\[[^\]]*\]/', '', $gwf_p['nombre'] ) ), 0, 1 );
$gwf_comite   = get_page_by_path( 'comite-editorial' );
$gwf_comite   = ( $gwf_p['comite'] && $gwf_comite && 'publish' === $gwf_comite->post_status ) ? $gwf_comite : null;
$gwf_politica = get_page_by_path( 'politica-editorial' );
$gwf_politica = ( $gwf_politica && 'publish' === $gwf_politica->post_status ) ? $gwf_politica : null;
?>

<main id="site-content" class="gwf-guia gwf-persona">
	<div class="container gwf-p-wrap">
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="gwf-l-migas" aria-label="Migas de pan">', '</nav>' ); } ?>

		<?php // 1-3, 7, 8. Foto, nombre, cargo, rol y enlaces verificables. ?>
		<section class="gwf-p-hero">
			<?php if ( $gwf_p['foto'] ) : ?>
				<img class="gwf-p-photo" src="<?php echo esc_url( $gwf_p['foto'] ); ?>" alt="<?php echo esc_attr( $gwf_p['nombre'] ); ?>" width="220" height="220" fetchpriority="high">
			<?php else : ?>
				<span class="gwf-p-photo gwf-p-initial" aria-hidden="true"><?php echo esc_html( $gwf_initial ); ?></span>
			<?php endif; ?>
			<div class="gwf-p-id">
				<span class="gwf-p-badge"><?php echo esc_html( GWF_GUIAS_PERSON_ROLES[ $gwf_p['rol'] ] ); ?></span>
				<h1 class="gwf-p-name"><?php echo esc_html( $gwf_p['nombre'] ); ?></h1>
				<?php if ( $gwf_p['cargo'] ) : ?><p class="gwf-p-role"><?php echo esc_html( $gwf_p['cargo'] ); ?></p><?php endif; ?>
				<?php if ( $gwf_p['redes'] || $gwf_p['enlaces'] ) : ?>
					<ul class="gwf-p-links">
						<?php foreach ( $gwf_p['redes'] as $gwf_red ) : // Redes del perfil: botón con su icono. ?>
							<li><a class="gwf-p-social" href="<?php echo esc_url( $gwf_red['url'] ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $gwf_red['nombre'] . ' de ' . $gwf_p['nombre'] ); ?>" title="<?php echo esc_attr( $gwf_red['nombre'] ); ?>"><?php echo gwf_g_social_icon( $gwf_red['red'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fijo. ?></a></li>
						<?php endforeach; ?>
						<?php foreach ( $gwf_p['enlaces'] as $gwf_link ) : ?>
							<li><a href="<?php echo esc_url( $gwf_link['url'] ); ?>" target="_blank" rel="noopener me"><?php echo esc_html( $gwf_link['texto'] ? $gwf_link['texto'] : wp_parse_url( $gwf_link['url'], PHP_URL_HOST ) ); ?> <span aria-hidden="true">↗</span></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( $gwf_p['formacion'] || $gwf_p['experiencia'] ) : // 4 y 5. ?>
			<div class="gwf-p-grid">
				<?php if ( $gwf_p['formacion'] ) : ?>
					<section class="gwf-g-block gwf-p-block">
						<h2>Formación</h2>
						<ul class="gwf-p-formacion">
							<?php foreach ( $gwf_p['formacion'] as $gwf_f ) : ?>
								<li>
									<span><strong><?php echo esc_html( $gwf_f['titulo'] ); ?></strong><?php echo $gwf_f['institucion'] ? ' · ' . esc_html( $gwf_f['institucion'] ) : ''; ?></span>
									<?php if ( $gwf_f['anio'] ) : ?><span class="gwf-p-year"><?php echo esc_html( $gwf_f['anio'] ); ?></span><?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
				<?php if ( $gwf_p['experiencia'] ) : ?>
					<section class="gwf-g-block gwf-p-block">
						<h2>Experiencia</h2>
						<?php echo wp_kses_post( wpautop( $gwf_p['experiencia'] ) ); ?>
					</section>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $gwf_p['temas'] ) : // 6. Temas que domina: enlazan a su listado. ?>
			<section class="gwf-g-block gwf-p-block">
				<h2>Temas que domina</h2>
				<ul class="gwf-p-chips">
					<?php foreach ( $gwf_p['temas'] as $gwf_t ) : ?>
						<li><a href="<?php echo esc_url( gwf_guias_filter_url( 'gran_tema' === $gwf_t->taxonomy ? 'desarrollo' : 'guia', $gwf_t ) ); ?>"><?php echo esc_html( $gwf_t->name ); ?> ›</a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( have_posts() ) : // 10. Su contenido, automático y paginado. ?>
			<section class="gwf-g-block gwf-p-block" id="contenido">
				<h2><?php echo esc_html( $gwf_list_title[ $gwf_p['rol'] ] ); ?></h2>
				<table class="gwf-p-table">
					<thead><tr><th scope="col">Tipo</th><th scope="col">Título</th><th scope="col">Fecha</th></tr></thead>
					<tbody>
						<?php
						while ( have_posts() ) :
							the_post();
							list( $gwf_date_label, $gwf_ts ) = $gwf_row_date( get_the_ID() );
							$gwf_type = get_post_type();
							?>
							<tr>
								<td><span class="gwf-p-type gwf-p-type-<?php echo esc_attr( $gwf_type ); ?>"><?php echo esc_html( $gwf_types[ $gwf_type ] ?? '' ); ?></span></td>
								<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
								<td><span class="screen-reader-text"><?php echo esc_html( $gwf_date_label ); ?> </span><time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', $gwf_ts ) ); ?>" title="<?php echo esc_attr( $gwf_date_label ); ?>"><?php echo esc_html( date_i18n( 'j M Y', $gwf_ts ) ); ?></time></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
				<?php
				$gwf_pages = paginate_links(
					array(
						'type'         => 'list',
						'prev_text'    => '‹ Anterior',
						'next_text'    => 'Siguiente ›',
						'mid_size'     => 1,
						'add_fragment' => '#contenido',
					)
				);
				if ( $gwf_pages ) {
					echo '<nav class="gwf-l-pages" aria-label="Páginas">' . $gwf_pages . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de WordPress.
				}
				?>
			</section>
		<?php endif; ?>

		<?php if ( $gwf_comite || $gwf_politica ) : // V. Volver al comité (cuando existan sus páginas). ?>
			<nav class="gwf-p-back" aria-label="Comité editorial">
				<?php if ( $gwf_comite ) : ?><a class="gwf-p-button" href="<?php echo esc_url( get_permalink( $gwf_comite ) ); ?>">‹ Comité editorial</a><?php endif; ?>
				<?php if ( $gwf_politica ) : ?><a class="gwf-p-button" href="<?php echo esc_url( get_permalink( $gwf_politica ) ); ?>">Política editorial</a><?php endif; ?>
			</nav>
		<?php endif; ?>
	</div>
</main>
