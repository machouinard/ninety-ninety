<?php

include_once NINETY_NINETY_PATH . 'inc/class-ninety-map-js.php';

get_header();

?>

<div class="wrap">
	<?php echo apply_filters( 'ninety_map_page_title', sprintf( '<h2>%s</h2>', __( 'Meetings Map', 'ninety-ninety' ) ) ); ?>

    <div id="ninety-map"></div>

</div>

<?php

ninety_add_chart_markup();

get_footer();
