<?php
/**
 * Conditional single template for Genesis child themes
 */

// TODO: build template.

// Filter entry meta ( static function in NinetyNinety class ).
add_filter(
	'genesis_post_info',
	[
		'NinetyNinety',
		'ninety_meeting_genesis_entry_meta_header',
	]
);
//remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

// Prepend Entry content with our own.
add_action( 'genesis_entry_content', 'ninety_meeting_entry_content', 5 );
/**
 * Output single Meeting content
 *
 * @return void
 * @since 1.0.0
 */
function ninety_meeting_entry_content() {

//	$program  = get_field( 'ninety_meeting_program', get_the_ID() );
//	$type     = get_field( 'ninety_meeting_type', get_the_ID() );
	$location      = get_field( 'ninety_meeting_location', get_the_ID() );
	$notes         = get_field( 'ninety_meeting_notes', get_the_ID() );
	$speaker       = get_field( 'ninety_meeting_speaker', get_the_ID() );
	$topic         = get_field( 'ninety_meeting_topic', get_the_ID() );

	if ( $location instanceof WP_Term ) {
		echo '<h3><a href="/meetings/' . esc_attr( $location->slug ) . '">' . esc_html( $location->name ) . '</a></h3>';
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

genesis();
