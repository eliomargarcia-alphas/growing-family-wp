<?php
/**
 * Avisos de contenido huérfano en el administrador (etapa 7, enlazado en triángulo).
 *
 * Guías: columna «Enlazado» con su pilar (campo de «Relacionados») y su cuento, y aviso con las publicadas sin
 * pilar. El pilar elegido siempre lista la guía (gwf_guias_pillar_guides). Pilares: columna con cuántas guías
 * enlaza hacia abajo. El cuento (plugin gwf-cuentos) es el que eligió la guía como «Guía relacionada».
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Pilar publicado de una guía, o 0. */
function gwf_guias_guide_pillar( $post_id ) {
	$pilar = (int) get_post_meta( $post_id, 'gwf_pilar', true );
	return ( $pilar && 'publish' === get_post_status( $pilar ) ) ? $pilar : 0;
}

function gwf_guias_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'date' === $key ) {
			$new['gwf_enlazado'] = 'Enlazado';
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_guia_posts_columns', 'gwf_guias_admin_columns' );
add_filter( 'manage_desarrollo_posts_columns', 'gwf_guias_admin_columns' );

function gwf_guias_admin_column( $column, $post_id ) {
	if ( 'gwf_enlazado' !== $column ) {
		return;
	}
	$warn = '<span style="color:#b32d2e">⚠ %s</span>';
	if ( 'guia' === get_post_type( $post_id ) ) {
		$pilar = gwf_guias_guide_pillar( $post_id );
		echo $pilar
			? 'Pilar: <a href="' . esc_url( get_edit_post_link( $pilar ) ) . '">' . esc_html( get_the_title( $pilar ) ) . '</a>'
			: sprintf( $warn, 'Sin pilar' ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( function_exists( 'gwf_cue_for_guide' ) ) {
			$cuento = gwf_cue_for_guide( $post_id );
			echo '<br>' . ( $cuento ? 'Cuento: <a href="' . esc_url( get_edit_post_link( $cuento ) ) . '">' . esc_html( get_the_title( $cuento ) ) . '</a>' : sprintf( $warn, 'Sin cuento' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		return;
	}
	$count = count( gwf_guias_down_links( $post_id )['guias'] );
	echo $count
		? esc_html( sprintf( _n( 'Enlaza %d guía', 'Enlaza %d guías', $count ), $count ) )
		: sprintf( $warn, 'No enlaza ninguna guía' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'manage_guia_posts_custom_column', 'gwf_guias_admin_column', 10, 2 );
add_action( 'manage_desarrollo_posts_custom_column', 'gwf_guias_admin_column', 10, 2 );

/** Guías publicadas sin pilar publicado. */
function gwf_guias_orphan_guides() {
	$ids = get_posts( array( 'post_type' => 'guia', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' ) );
	return array_values( array_filter( $ids, function ( $id ) { return ! gwf_guias_guide_pillar( $id ); } ) );
}

/** Aviso en el listado de guías con las huérfanas y un enlace que las filtra. */
function gwf_guias_orphan_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-guia' !== $screen->id ) {
		return;
	}
	$orphans = gwf_guias_orphan_guides();
	if ( ! $orphans ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%s <a href="%s">Ver cuáles</a>.</p></div>',
		esc_html( sprintf( _n( '%d guía publicada no tiene pilar en «Relacionados»: el triángulo guía, cuento y pilar queda incompleto.', '%d guías publicadas no tienen pilar en «Relacionados»: el triángulo guía, cuento y pilar queda incompleto.', count( $orphans ) ), count( $orphans ) ) ),
		esc_url( add_query_arg( array( 'post_type' => 'guia', 'gwf_huerfanas' => 1 ), admin_url( 'edit.php' ) ) )
	);
}
add_action( 'admin_notices', 'gwf_guias_orphan_notice' );

/** Filtro ?gwf_huerfanas=1 del listado de guías. */
function gwf_guias_orphan_filter( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'guia' !== $query->get( 'post_type' ) || empty( $_GET['gwf_huerfanas'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	remove_action( 'pre_get_posts', 'gwf_guias_orphan_filter' );
	$orphans = gwf_guias_orphan_guides();
	$query->set( 'post__in', $orphans ? $orphans : array( 0 ) );
}
add_action( 'pre_get_posts', 'gwf_guias_orphan_filter' );
