<?php

$theme = wp_get_theme();

add_filter( 'post_type_link', 'ninety_maybe_tax_specific_permalink', 10, 2 );

// Include actual template file based on whether or not it's a Genesis theme.
if ( 'genesis' === $theme->get_template() ) {
	include( plugin_dir_path( __FILE__ ) . 'archive-genesis.php' );
} else {
	include( plugin_dir_path( __FILE__ ) . 'archive.php' );
}
