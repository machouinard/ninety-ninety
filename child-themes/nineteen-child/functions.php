<?php

add_action( 'wp_enqueue_scripts', 'nineteen_child_enqueue_styles' );
/**
 * Include parent and child stylesheets
 *
 * @return void
 * @since 1.0.0
 */
function nineteen_child_enqueue_styles() {

	wp_enqueue_style( 'twentynineteen-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'nineteen-child-style',
	                  get_stylesheet_directory_uri() . '/style.css',
	                  array( 'twentynineteen-style' ),
	                  wp_get_theme()->get( 'Version' )
	);
}
