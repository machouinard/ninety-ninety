<?php
//* TODO: build template
// Filter entry meta ( static function in NinetyNinety class )
add_filter( 'genesis_post_info', [
	'NinetyNinety',
	'ninety_meeting_genesis_entry_meta_header',
] );

//* Replace Entry content with our own
add_action( 'genesis_entry_content', 'ninety_meeting_entry_content' );
function ninety_meeting_entry_content() {

//	$program  = get_field( 'ninety_meeting_program', get_the_ID() );
//	$type     = get_field( 'ninety_meeting_type', get_the_ID() );
	$location = get_field( 'ninety_meeting_location', get_the_ID() );
	$notes    = get_field( 'ninety_meeting_notes', get_the_ID() );
	$speaker  = get_field( 'ninety_meeting_speaker', get_the_ID(), true );
	$topic    = get_field( 'ninety_meeting_topic', get_the_ID(), true );
	
	if ( $location instanceof WP_Term ) {
		echo '<h3><a href="/meetings/' . $location->slug . '">' . $location->name . '</a></h3>';
	}
	
	echo '<div class="single-details">';
	
	if ( $speaker ) {
		echo wpautop( 'Speaker:&nbsp;' . esc_html( $speaker ) );
	}
	
	if ( $topic ) {
		echo wpautop( 'Topic:&nbsp;' . esc_html( $topic ) );
	}
	
	if ( ! $notes ) {
		$notes = __( 'No notes', 'ninety-ninety' );
	}
	
	echo wpautop( $notes );
	
	if ( $maps = get_field( 'ninety_meeting_ride_map', get_the_ID() ) ) {
		
		echo '<div class="scroll-wrapper">';
		
		echo $maps;
		
		echo '</div>';
		
	}
	
	echo '<span class="locked-bottom"><a href="/meetings/">back to meetings</a></span>';
	
	echo '</div>';
	
}

genesis();
