<?php

if ( ! class_exists( 'Ninety_Map_JS' ) ) {

	/**
	 * Class Ninety_Map_JS
	 */
	class Ninety_Map_JS {

		/**
		 * Ninety_Map_JS constructor.
		 */
		public function __construct() {
			$this->enqueue();
		}

		/**
		 * Add enqueue actions for scripts, styles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		/**
		 * Enqueue Map styles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles() {

			$leaflet_css_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.css';

			$version = ninety_ninety()->version;
			wp_enqueue_style( 'map-style', $leaflet_css_url, [], $version );

		}

		/**
		 * Enqueue Map scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			global $post;
			if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'ninety_map' ) ) {
				return;
			}
			$leaflet_js_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.js';

			wp_enqueue_script( 'map-js', $leaflet_js_url, array( 'jquery' ), '1.0' );

			// Get default latitude from options page.
			$lat = ninety_ninety()->get_option( 'ninety_map_center_lat' );

			// If Lat hasn't been set, set it to Sacramento because, well, that's where I live.
			if ( ! $lat ) {
				$lat = 54.525963;
			}

			// Get default longitude from options page.
			$lng = ninety_ninety()->get_option( 'ninety_map_center_lng' );

			// If Lng hasn't been set, set it to Sacramento.
			if ( ! $lng ) {
				$lng = - 105.255119;
			}

			// Get default zoom from options page.
			$zoom = ninety_ninety()->get_option( 'ninety_map_zoom' );

			// If Zoom hasn't been set, set it to 1.
			if ( ! $zoom ) {
				$zoom = 1;
			}
			$center      = [ $lat, $lng ];
			$tile_server = ninety_ninety()->get_option( 'ninety_tile_server' );
			if ( ! $tile_server ) {
				$tile_server = ninety_ninety()->default_tile_server;
			}

			$api_key = ( false !== strpos( $tile_server, 'thunderforest' ) ) ? ninety_ninety()->get_option( 'ninety_thunderforest_api_key' ) : ninety_ninety()->get_option( 'ninety_mapbox_api_key' );

			wp_localize_script(
				'map-js',
				'mapOptions',
				[
					'apiKey'     => $api_key,
					'tileServer' => $tile_server,
					'mapCenter'  => $center,
					'zoom'       => $zoom,
				]
			);

		}

	}

	new Ninety_Map_JS();
}
