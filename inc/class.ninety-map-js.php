<?php

if ( ! class_exists( 'Ninety_Map_JS' ) ) {
	
	class Ninety_Map_JS {
		
		
		public function __construct() {
			
			$this->enqueue();
		}
		
		/**
		 * Add enqueue actions for scripts, styles
		 *
		 * @return void
		 * @since 1.0.0
		 *
		 */
		public function enqueue() {
			
			add_action( 'wp_enqueue_scripts', [ $this, 'ninety_enqueue_map_style' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'ninety_enqueue_map_scripts' ] );
			//			add_filter( 'script_loader_tag', [ $this, 'ninety_map_scripts_modifier' ], 10, 3 );
			//			add_filter( 'style_loader_tag', [ $this, 'ninety_map_style_loader_modifier' ], 10, 4 );
		}
		
		/**
		 * Enqueue Map styles
		 *
		 * @return void
		 * @since 1.0.0
		 *
		 */
		public function ninety_enqueue_map_style() {
			
			$leaflet_css_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.css';
			
			wp_enqueue_style( 'map-style', $leaflet_css_url );
			
		}
		
		/**
		 * Enqueue Map scripts
		 *
		 * @return void
		 * @since 1.0.0
		 *
		 */
		public function ninety_enqueue_map_scripts() {
			
			$leaflet_js_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.js';
			
			wp_enqueue_script( 'map-js', $leaflet_js_url, array( 'jquery' ), '1.0' );
			
			//			$lat    = ninety_ninety()->get_option( 'ninety_map_center_lat' );
			//			$lng    = ninety_ninety()->get_option( 'ninety_map_center_lng' );
			//			$zoom   = ninety_ninety()->get_option( 'ninety_map_zoom' );
			//			$center = [ $lat, $lng ];
			$tile_server           = ninety_ninety()->get_option( 'ninety_tile_server' );
			
			$api_key = ( false !== strpos( $tile_server, 'thunderforest' ) ) ? ninety_ninety()->get_option( 'ninety_thunderforest_api_key' ) : ninety_ninety()->get_option( 'ninety_mapbox_api_key' );
			
			wp_localize_script( 'map-js', 'mapOptions', [
				'apiKey'     => $api_key,
				'tileServer' => $tile_server,
			] );
			
		}
		
		//* Add integrity and crossorigin
		public function ninety_map_style_loader_modifier( $html, $handle, $href, $media ) {
			
			$leaflet_css_sri = 'sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==';
			
			if ( 'map-style' === $handle ) {
				$new = "<link rel='stylesheet' href='{$href}'  integrity='{$leaflet_css_sri}' crossorigin=''/>\n";
				
				return $new;
			}
			
			return $html;
			
		}
		
		//* Add integrity and crossorigin
		public function ninety_map_scripts_modifier( $tag, $handle, $src ) {
			
			$leaflet_js_sri = 'sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==';
			
			if ( 'map-js' === $handle ) {
				$tag = '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
   integrity="' . $leaflet_js_sri . '"
   crossorigin=""></script>';
			}
			
			return $tag;
		}
		
	}
	
	new Ninety_Map_JS();
}
