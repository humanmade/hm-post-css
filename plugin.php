<?php
/**
 * Plugin Name: HM Post CSS
 * Description: Enables adding custom CSS for a post.
 * Author: Human Made Limited
 * Author URL: https://humanmade.com
 * Version: 1.0.0
 */

namespace HM\PostCSS;

use WP_Post;

function add_meta_boxes( $post_type ) {
	if ( ! in_array( $post_type, get_post_types( [ 'public' => true ] ), true ) ) {
		return;
	}

	add_meta_box(
		'hm-post-css',
		__( 'Custom CSS', 'hm-post-css' ),
		__NAMESPACE__ . '\meta_box',
		$post_type
	);
}

add_action( 'add_meta_boxes', __NAMESPACE__ . '\add_meta_boxes' );

function meta_box( WP_POST $post ) {
	$css = wp_get_custom_css( 'hm-post-css-' . $post->ID );
	$css = esc_textarea( $css );

	wp_enqueue_code_editor( [
		'type' => 'text/css',
		'codemirror' => array(
			'indentUnit' => 2,
			'tabSize' => 2,
		),
	] );

	wp_enqueue_script( 'hm-post-css', plugins_url( 'src/index.js', __FILE__ ), [ 'jquery' ], null, true );

	echo '<style>
		#hm-post-css .CodeMirror-line { padding-left: 5px; }
		#hm-post-css .CodeMirror-gutters { left: 0 !important; }
	</style>';
	echo '<textarea class="widefat" name="hm_post_css" rows="15" cols="100%">' . $css . '</textarea>';
}

function save_post( $post_id, WP_Post $post ) {
	if ( $post->post_type === 'custom_css' ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) ) {
		return;
	}

	if ( wp_is_post_revision( $post ) ) {
		return;
	}

	$css = filter_input( INPUT_POST, 'hm_post_css', FILTER_SANITIZE_STRING );

	wp_update_custom_css_post( $css, [
		'stylesheet' => 'hm-post-css-' . $post_id,
	] );
}

add_action( 'save_post', __NAMESPACE__ . '\save_post', 10, 2 );

function output_css() {
	if ( ! is_singular() ) {
		return;
	}

	$post_id = get_queried_object_id();
	$css = wp_get_custom_css( 'hm-post-css-' . $post_id );

	if ( empty( $css ) ) {
		return;
	}

	printf( '<style type="text/css">%s</style>',
		$css
	);
}

add_action( 'wp_head', __NAMESPACE__ . '\output_css', 200 );
