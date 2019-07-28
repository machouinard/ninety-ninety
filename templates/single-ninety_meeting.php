<?php
$location = get_query_var( 'ninety_meeting_location' );
$theme    = wp_get_theme();

//add_filter( 'post_type_link', 'ninety_maybe_tax_specific_permalink', 10, 2 );

// Filter prev/next links to include meeting location for proper pagination.
add_filter( 'previous_post_link', 'ninety_correct_post_link', 10, 5 );

add_filter( 'next_post_link', 'ninety_correct_post_link', 10, 5 );

// Include actual template file based on whether or not it's a Genesis theme.
if ( 'genesis' === $theme->get_template() ) {

	add_action( 'genesis_entry_content', 'ninety_single_post_nav', 12 );

	include( plugin_dir_path( __FILE__ ) . 'single-genesis.php' );
} else {
	include( plugin_dir_path( __FILE__ ) . 'single.php' );
}
