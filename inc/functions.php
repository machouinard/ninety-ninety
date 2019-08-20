<?php
/**
 * Helper functions
 */

/**
 * Output single Meeting content
 *
 * @return void
 * @since 1.0.0
 */
function ninety_meeting_entry_content() {

//	$program  = get_field( 'ninety_meeting_program', get_the_ID() );
//	$type     = get_field( 'ninety_meeting_type', get_the_ID() );
	$location = get_field( 'ninety_meeting_location', get_the_ID() );
	$notes    = get_field( 'ninety_meeting_notes', get_the_ID() );
	$speaker  = get_field( 'ninety_meeting_speaker', get_the_ID() );
	$topic    = get_field( 'ninety_meeting_topic', get_the_ID() );

	if ( $location instanceof WP_Term ) {
		$url = get_site_url( null, '/meetings/' ) . esc_attr( $location->slug );
		echo '<h3><a href="' . trailingslashit( $url ) . '">' . esc_html( $location->name ) . '</a></h3>';
	}

	echo '<div class="single-details">';

	if ( $speaker ) {
		echo '<p class="ninety-speaker">';
		echo esc_html( __( 'Speaker', 'ninety-ninety' ) . ':&nbsp;' . $speaker );
		echo '</p>';
	}

	if ( $topic ) {
		echo '<p class="ninety-topic">';
		echo esc_html( __( 'Topic', 'ninety-ninety' ) . ':&nbsp;' . $topic );
		echo '</p>';
	}

	// If we have notes, put 'em on the page.
	if ( $notes ) {
		echo '<div class="ninety-notes-container">';
		echo apply_filters( 'the_content', $notes );
		echo '</div>';
	}

	echo '<span class="locked-bottom"><a href="/meetings/">back to meetings</a></span>';

	echo '</div>';

}

/**
 * Ensure prev/next links include Location if on Location tax page
 *
 * @param $output
 * @param $format
 * @param $link
 * @param $post
 * @param $adjacent
 *
 * @return mixed
 * @since 1.0.0
 */
function ninety_correct_post_link( $output, $format, $link, $post, $adjacent ) {
	if ( empty( $output ) ) {
		return $output;
	}

	if ( 'ninety_meeting' !== $post->post_type ) {
		return $output;
	}

	$location = get_query_var( 'ninety_meeting_location' );
	if ( empty( $location ) ) {
		return $output;
	}

	$pos        = strpos( $output, 'meetings/' ) + 9;
	$new_output = substr_replace( $output, $location . '/', $pos, 0 );

	return $new_output;
}

/**
 * Output prev/next links conditionally using same tax arg
 *
 * @return void
 * @since 1.0.0
 *
 */
function ninety_single_post_nav() {

	echo '<div class="navigation pagination">';

	$location = get_query_var( 'ninety_meeting_location' );
	$same     = ! empty( $location ) ? true : false;

	echo '<div class="pagination-previous alignleft">';
	previous_post_link( '%link', 'Previous Mtg', $same, '', 'ninety_meeting_location' );
	echo '</div>';

	echo '<div class="pagination-next alignright">';
	next_post_link( '%link', 'Next Mtg', $same, '', 'ninety_meeting_location' );
	echo '</div>';
	echo '</div>';

}

/**
 * Insert Meeting Location into permalink if on Location archive page
 *
 * @param string  $url  permalink.
 * @param WP_Post $post Meeting Post object.
 * @param bool    $leavename    Whether to keep the post name or page name.
 *
 * @return mixed
 * @since 1.0.0
 */
function ninety_maybe_tax_specific_permalink( $url, $post ) {

	if ( 'ninety_meeting' === $post->post_type ) {
		$location = get_query_var( 'ninety_meeting_location' );
		if ( empty( $location ) ) {
			return $url;
		}

		$pos        = strpos( $url, 'meetings/' ) + 9;
		$new_output = substr_replace( $url, $location . '/', $pos, 0 );

		return $new_output;
	}

	return $url;
}

function ninety_add_chart_markup( $return = false, $chart_type = 'pie', $show_chart = null ) {

	if ( null === $show_chart ) {
		$show_chart = ninety_ninety()->get_option( 'ninety_show_chart' );
	}

	if ( ! $show_chart ) {
		return;
	}

	if ( $return ) {
		$output = '<div 
			class="ninety-chart-container" 
			id="ninety-chart-container" 
			data-chart-type="' . $chart_type . '"
			data-show-chart="' . $show_chart . '"
			>';
		$output .= '<canvas id="ninety-chart"></canvas>';
		$output .= '</div>';

		return $output;

	} else {
		echo '<div 
			class="ninety-chart-container" 
			id="ninety-chart-container" 
			data-chart-type="' . $chart_type . '"
			data-show-chart="' . $show_chart . '"
			>';
		echo '<canvas id="ninety-chart"></canvas>';
		echo '</div>';
	}

}
