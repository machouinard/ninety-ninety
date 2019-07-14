<?php
/**
 * Template Name: Genesis Map
 */

require_once NINETY_NINETY_PATH . 'inc/class.ninety-map-js.php';

//* TODO: Build map page

add_filter( 'genesis_post_title_output', 'ninety_map_title_output', 15 );
function ninety_map_title_output( $title ) {

	//* Filter Map page title
	return apply_filters( 'ninety_map_page_title', sprintf( '<h2>%s</h2>', __( 'Meetings Map', 'ninety-ninety' ) ) );

}

//* Remove the post content (requires HTML5 theme support)
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
add_action( 'genesis_entry_content', 'ninety_map_page_content' );

function ninety_map_page_content() {

	$count = ninety_ninety()->get_setting( 'meeting_count' );

	printf( '<h4>%d %s</h4>', $count, __( 'Meetings', 'ninety-ninety' ) );

	echo '<div id="ninety-map"></div>';

	echo '<div style="width: 250px; height: 250px; margin: 0 auto;">';
	echo '<canvas id="ninety-chart" width="250" height="250"></canvas>';
	echo '</div>';

}

genesis();
