<?php

/**
 * Filter entry meta on Genesis templates ( static function in NinetyNinety class )
 */
add_filter(
	'genesis_post_info',
	[
		'NinetyNinety',
		'ninety_meeting_genesis_entry_meta_header',
	]
);

genesis();
