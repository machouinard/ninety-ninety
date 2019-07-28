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


genesis();
