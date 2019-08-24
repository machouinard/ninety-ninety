<?php
/**
 * Template tags used for displaying Meetings
 */

if ( ! function_exists( 'ninety_meeting_entry_content' ) ) {

	/**
	 * Output single Meeting content
	 *
	 * @return void
	 * @since 0.1.0
	 */
	function ninety_meeting_entry_content() {

		$location = get_field( 'ninety_meeting_location', get_the_ID() );
		$notes    = get_field( 'ninety_meeting_notes', get_the_ID() );
		$speaker  = get_field( 'ninety_meeting_speaker', get_the_ID() );
		$topic    = get_field( 'ninety_meeting_topic', get_the_ID() );

		if ( $location instanceof WP_Term ) {
			$url = get_site_url( null, '/meetings/' ) . esc_attr( $location->slug );
			echo '<h3><a href="' . esc_url_raw( trailingslashit( $url ) ) . '">' . esc_html( $location->name ) . '</a></h3>';
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

}

if ( ! function_exists( 'ninety_single_post_nav' ) ) {

	/**
	 * Output prev/next links conditionally using same tax arg
	 *
	 * @return void
	 * @since 0.1.0
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
}

if ( ! function_exists( 'ninety_map' ) ) {

	/**
	 * Display meeting map
	 *
	 * @param int|string $title      Whether or not to show title, string for title.
	 * @param int        $show_count Whether or not to show meeting count.
	 * @param bool       $return     Whether to return or echo.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function ninety_map( $title = 0, $show_count = 0, $return = false ) {
		if ( $return ) {
			return "[ninety_map show_chart=0 title={$title} show_count={$show_count}]";
		} else {
			echo esc_html( "[ninety_map show_chart=0 title={$title} show_count={$show_count}]" );
		}
	}
}

if ( ! function_exists( 'ninety_chart' ) ) {

	/**
	 * Display meeting chart
	 *
	 * @param string $type   Chart type ( pie | doughnut | bar ).
	 * @param bool   $return Whether to return or echo.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function ninety_chart( $type = 'pie', $return = false ) {
		if ( $return ) {
			return "[ninety_map show_map=0 show_chart=1 chart_type={$type}]";
		} else {
			echo esc_html( "[ninety_map show_map=0 show_chart=1 chart_type={$type}]" );
		}
	}
}
if ( ! function_exists( 'ninety_map_and_chart' ) ) {

	/**
	 * Display meeting map and chart
	 *
	 * @param int|string $title      String for title, 0 for nothing.
	 * @param int        $show_count Whether or not to show meeting count.
	 * @param bool       $return     Whether to return or echo.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function ninety_map_and_chart( $title = 0, $show_count = 0, $return = false ) {
		if ( $return ) {
			return "[ninety_map show_chart=1 title={$title} show_count={$show_count}]";
		} else {
			echo esc_html( "[ninety_map show_chart=1 title={$title} show_count={$show_count}]" );
		}
	}

}
