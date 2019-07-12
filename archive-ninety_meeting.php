<?php

$theme = wp_get_theme();

//* Include actual template file based on whether or not it's a Genesis theme
if ( 'genesis' === $theme->get_template() ) {
	include( plugin_dir_path( __FILE__ ) . 'templates/archive-genesis.php' );
} else {
	include( plugin_dir_path( __FILE__ ) . 'templates/archive.php' );
}
