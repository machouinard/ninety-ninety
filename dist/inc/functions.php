<?php
/**
 * Helper functions
 */

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
 * @since 0.1.0
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
 * Insert Meeting Location into permalink if on Location archive page
 *
 * @param string  $url       permalink.
 * @param WP_Post $post      Meeting Post object.
 * @param bool    $leavename Whether to keep the post name or page name.
 *
 * @return mixed
 * @since 0.1.0
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

/**
 * Output chart markup
 *
 * @param bool   $return     Whether to echo or return.
 * @param string $chart_type Chart type to display.
 * @param null   $show_chart Whether or not to display chart.
 *
 * @return string|void
 * @since 0.1.0
 */
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

/**
 * Output meeting location link
 *
 * @return void
 * @since 0.1.0
 */
function ninety_print_location_link() {
	$post_id  = get_the_ID();
	$location = get_field( 'ninety_meeting_location', $post_id );

	if ( $location ) {
		$url  = get_site_url( null, 'meetings/' ) . esc_attr( $location->slug ) . '/';
		$link = "<a href='{$url}' alt='Link to {$location->name} meetings'>{$location->name}</a>";
		echo $link;
	}
}
