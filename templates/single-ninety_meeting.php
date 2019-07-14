<?php
$location = get_query_var( 'ninety_meeting_location' );
$theme = wp_get_theme();

//* Include actual template file based on whether or not it's a Genesis theme
if ( 'genesis' === $theme->get_template() ) {

	// Are we looking at meetings from a certain location - i.e. /group-one ?


	if ( ! empty( $location ) ) {

		//* Filter prev/next links to include meeting location for proper pagination
		add_filter( 'previous_post_link', 'ninety_prev_post_link', 10, 5 );
		function ninety_prev_post_link( $output, $format, $link, $post, $adjacent ) {
			if ( empty( $output ) ) {
				return $output;
			}
			$location = get_query_var( 'ninety_meeting_location' );
			$pos = strpos( $output, 'meetings/' ) + 9;
			$new_output = substr_replace( $output, $location . '/', $pos, 0 );
			return $new_output;
		}
		add_filter( 'next_post_link', 'ninety_next_post_link', 10, 5 );
		function ninety_next_post_link( $output, $format, $link, $post, $adjacent ) {
			if ( empty( $output ) ) {
				return $output;
			}
			$location = get_query_var( 'ninety_meeting_location' );
			$pos = strpos( $output, 'meetings/' ) + 9;
			$new_output = substr_replace( $output, $location . '/', $pos, 0 );
			return $new_output;
		}

		add_action( 'genesis_entry_content', 'custom_single_post_nav', 12 );
		function custom_single_post_nav() {

			echo '<div class="pagination-previous alignleft">';
			previous_post_link('%link', 'Previous Mtg', true, '', 'ninety_meeting_location' );
			echo '</div>';

			echo '<div class="pagination-next alignright">';
			next_post_link('%link', 'Next Mtg', true, '', 'ninety_meeting_location' );
			echo '</div>';

		}

	} else {

		add_action( 'genesis_entry_content', 'custom_single_post_nav', 12 );
		function custom_single_post_nav() {

			echo '<div class="pagination-previous alignleft">';
			previous_post_link('%link', 'Previous Mtg', false );
			echo '</div>';

			echo '<div class="pagination-next alignright">';
			next_post_link('%link', 'Next Mtg', false );
			echo '</div>';

		}

	}



	include( plugin_dir_path( __FILE__ ) . 'single-genesis.php' );
} else {
	include( plugin_dir_path( __FILE__ ) . 'single.php' );
}
