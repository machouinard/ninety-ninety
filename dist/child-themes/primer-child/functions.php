<?php

// Include parent and child stylesheets.
add_action( 'wp_enqueue_scripts', 'primer_child_enqueue_styles' );
function primer_child_enqueue_styles() {

	wp_enqueue_style( 'primer-child-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'nineteen-child-style',
	                  get_stylesheet_directory_uri() . '/style.css',
	                  array( 'primer-child-style' ),
	                  wp_get_theme()->get( 'Version' )
	);
}

/**
 * Conditionally modify next/previous links to limit to same meeting location
 * This is based on whether or not on meeting location specific page
 *
 * @param $args
 *
 * @return mixed
 * @since 1.0.0
 */
function primer_child_post_nav_default_args( $args ) {

	$in_same_term         = ninety_in_same_term();
	$args['taxonomy']     = 'ninety_meeting_location';
	$args['in_same_term'] = $in_same_term;

	return $args;
}
