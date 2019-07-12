<?php
//* TODO: build template
add_filter( 'genesis_attr_entry-title-link', 'ninety_modify_archive_location_link', 10, 3 );

function ninety_modify_archive_location_link( $permalink, $post, $leavename ) {
	
	$location = get_query_var( 'ninety_meeting_location' );
	
	if ( ! empty( $location ) ) {
		$pos               = strpos( $permalink['href'], 'meetings/' ) + 9;
		$permalink['href'] = substr_replace( $permalink['href'], $location . '/', $pos, 0 );
	}
	
	return $permalink;
}

// Filter entry meta on Genesis templates ( static function in NinetyNinety class )
add_filter( 'genesis_post_info', [
	'NinetyNinety',
	'ninety_meeting_genesis_entry_meta_header',
] );

genesis();
