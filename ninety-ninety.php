<?php
/**
 * WordPress plugin used to track AA Meetings
 *
 * @package     Ninety-Ninety
 * @since       1.2.0
 * @author      machouinard
 */

/**
 * Plugin Name: 90 in 90
 * Plugin URI:
 * Description: Track 90 meetings in 90 days.  Built for AA but customizable to any program.
 * Version: 1.0.0
 * Author: Mark Chouinard
 * Author URI: https://chouinard.me
 * Text Domain: ninety-ninety
 * Domain Path: /lang
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NinetyNinety' ) ) :

	/**
	 * Main plugin class
	 */
	class NinetyNinety {

		/**
		 * @var string Plugin version
		 */
		public $version = '1.2.0';
		/**
		 * @var array Settings array
		 */
		public $settings = [];
		/**
		 * @var array array
		 */
		public $options = [];

		/**
		 * NinetyNinety constructor.
		 */
		public function __construct() {
			// Do nothing - dummy constructor.
		}

		/**
		 * Set up plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function initialize() {

			$version  = $this->version;
			$basename = plugin_basename( __FILE__ );
			$path     = plugin_dir_path( __FILE__ );
			$url      = plugin_dir_url( __FILE__ );
			$slug     = dirname( $basename );

			// Define some constants.
			$this->define( 'NINETY_NINETY', true );
			$this->define( 'NINETY_NINETY_VERSION', $version );
			$this->define( 'NINETY_NINETY_PATH', $path );
			$this->define( 'NINETY_NINETY_URL', $url );
			$this->define( 'NINETY_ACF_PATH', $path . 'inc/acf/' );
			$this->define( 'NINETY_ACF_URL', $url . 'inc/acf/' );
			$this->define( 'NINETY_ACF_JSON_PATH', $path . 'acf-json' );
			$this->define( 'NINETY_COUNT_OPTION_KEY', 'ninety_meeting_count' );

			$meeting_count = get_option( NINETY_COUNT_OPTION_KEY, 0 );

			$this->settings = [
				'name'          => __( '90 in 90', 'ninety-ninety' ),
				'version'       => $version,
				'file'          => __FILE__,
				'basename'      => $basename,
				'path'          => $path,
				'url'           => $url,
				'slug'          => $slug,
				'show_admin'    => true,
				'capability'    => 'manage_options',
				'meeting_count' => $meeting_count,
			];

			// Set filterable page templates used by this plugin.
			$this->settings['page_templates'] = apply_filters(
				'ninety_page_templates',
				[
					'template-genesis-90-map.php',
					'template-90-map.php',
				]
			);

			// Get stored options set on plugin options page.
			$this->options = get_option( 'ninety_settings' );

			load_plugin_textdomain( 'ninety-ninety', false, NINETY_NINETY_PATH . 'lang' );

			// Meeting CPT and taxonomies.
			require_once NINETY_NINETY_PATH . 'inc/class-cpt-tax.php';
			// Instantiate CPT class.
			new NinetyNinety_CPT();

			// Add default Meeting Locations on plugin activation.
			register_activation_hook( __FILE__, [ 'NinetyNinety_CPT', 'activate' ] );
			register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
			// Add options page for plugin settings.
			require_once NINETY_NINETY_PATH . 'inc/class-options-page.php';
			// Require included ACF ( free version ) if ACF is not already active.
			if ( ! class_exists( 'ACF' ) ) {
				require_once NINETY_ACF_PATH . 'acf.php';
			}
			// Add custom page templates from this plugin.
			require_once $path . 'inc/class-page-templater.php';

			require_once $path . 'inc/mapbox/Mapbox.php';

			// Setup ACF Fields.
			require_once $path . 'inc/acf-fields.php';

			$this->add_actions_and_filters();

		}

		/**
		 * Add any actions and filters we need
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_actions_and_filters() {

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_stuff' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_stuff' ] );

			add_action( 'acf/init', 'ninety_init_acf_import' );
			add_action( 'acf/save_post', [ $this, 'set_meeting_title_time' ], PHP_INT_MAX );
			// Run as late as possible.
			if ( ! class_exists( 'ACF' ) ) {
				add_filter(
					'acf/settings/dir',
					[
						$this,
						'acf_settings_dir',
					]
				);
			}
			add_filter( 'acf/settings/show_admin', [ $this, 'acf_show_admin' ] );
			add_filter( 'template_include', [ $this, 'ninety_archive_template' ] );
			add_filter( 'wp_setup_nav_menu_item', [ $this, 'hide_meeting_nav_menu_objects' ] );
			add_filter( 'acf/load_field/key=field_5d182c40c6e57', [ $this, 'set_default_meeting_time' ] );
			add_filter( 'acf/load_field/key=field_5d197bc132ced', [ $this, 'ninety_coords_readonly' ] );
			add_action( 'update_option_ninety_settings', [ $this, 'update_meeting_count' ], 10, 2 );
			add_filter( 'acf/load_field/key=field_5d18480b686a6', [ $this, 'set_default_meeting_location' ] );
			add_filter( 'acf/load_field/key=field_5d18255d071c8', [ $this, 'set_default_meeting_type' ] );
			add_filter( 'acf/load_field/key=field_5d184b55fa43e', [ $this, 'filter_meeting_programs' ] );
		}

		/**
		 * For ACF inclusion
		 *
		 * @param string $url ACF settings URL.
		 *
		 * @return string URL to ACF
		 * @since 1.0.0
		 */
		public function acf_settings_dir( $url ) {

			return NINETY_ACF_URL;
		}

		/**
		 * Whether or not to show ACF admin
		 * This depends on the WP_DEBUG setting.  If WP_DEBUG is true, ACF admin is shown.
		 *
		 * @param bool $show_admin .
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function acf_show_admin( $show_admin ) {

			$debug = defined( 'WP_DEBUG' ) && WP_DEBUG ? true : false;

			return $debug;
		}

		/**
		 * Set default Meeting Location in ACF Default Meeting Location field
		 * Default is set in options page
		 *
		 * @param array $field ACF Meeting Location field.
		 *
		 * @return array    ACF Meeting Location taxonomy field settings
		 * @since 1.0.0
		 */
		public function set_default_meeting_location( $field ) {

			$location = $this->get_option( 'ninety_default_mtg_location' );

			$field['default_value'] = $location;

			return $field;

		}

		/**
		 * Set default Meeting Type in ACF Default Meeting Type field
		 * Default is set in options page
		 *
		 * @param array $field ACF Meeting Type field.
		 *
		 * @return array    ACF Meeting Type taxonomy field settings
		 * @since 1.0.0
		 */
		public function set_default_meeting_type( $field ) {

			// get default type option.
			$type = $this->get_option( 'ninety_default_mtg_type' );

			$field['default_value'] = $type;

			return $field;
		}

		/**
		 * Filter Meeting Programs
		 * Adds ability to add/modify Meeting Program selection - AA/NA/GA/OA...
		 *
		 * @param array $field ACF button group field settings.
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function filter_meeting_programs( $field ) {

			$field['choices'] = apply_filters( 'ninety_programs', $field['choices'] );

			return $field;
		}

		/**
		 * Safely define constant
		 *
		 * @param string $name  Name to define.
		 * @param bool   $value Value for definition.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function define( $name, $value = true ) {

			if ( ! defined( $name ) ) {
				define( $name, $value );
			}

		}

		/**
		 * Check if setting exists
		 *
		 * @param string $name Setting name.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function has_setting( $name ) {

			return isset( $this->settings[ $name ] );
		}

		/**
		 * Get setting
		 *
		 * @param string $name Name of setting.
		 *
		 * @return mixed|null
		 * @since 1.0.0
		 */
		public function get_setting( $name ) {

			return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
		}

		/**
		 * Update setting
		 *
		 * @param string $name  Name of setting.
		 * @param mixed  $value Value to assign.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function update_setting( $name, $value ) {

			$this->settings[ $name ] = $value;

			return true;
		}

		/**
		 * Safely get option value
		 *
		 * @param string $name    Name of option.
		 * @param mixed  $default Default value to return.
		 *
		 * @return mixed|null
		 * @since 1.0.0
		 */
		public function get_option( $name, $default = false ) {

			if ( isset( $this->options[ $name ] ) ) {
				return $this->options[ $name ];
			}

			return $default;
		}

		/**
		 * Update instance option
		 *
		 * @param string $name  Name of option.
		 * @param mixed  $value Value to assign option.
		 *                      if false, remove option.
		 *
		 * @return void
		 * @since 1.2.0
		 */
		public function update_option( $name, $value = false ) {

			if ( $value ) {
				$this->options[ $name ] = $value;
			} else {
				unset( $this->options[ $name ] );
			}

		}

		/**
		 * Enqueue admin scripts and styles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_admin_stuff() {
			$admin_script     = NINETY_NINETY_URL . 'assets/js/ninety-admin.js';
			$admin_script_ver = filemtime( NINETY_NINETY_PATH . 'assets/js/ninety-admin.js' );
			wp_enqueue_script( 'ninety-admin-js', $admin_script, [ 'jquery' ], $admin_script_ver, true );
		}

		/**
		 * Enqueue public scripts and styles
		 * Localize data for use on Map page.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_stuff() {

			$map_page             = is_page_template( $this->settings['page_templates'] );
			$meeting_archive_page = is_post_type_archive( 'ninety_meeting' );
			$singular_meeting     = is_singular( 'ninety_meeting' );

			if ( ! ( $map_page || $meeting_archive_page || $singular_meeting ) ) {
				return;
			}

			$style_file                    = NINETY_NINETY_URL . 'assets/css/ninety-style.css';
			$style_ver                     = filemtime( NINETY_NINETY_PATH . 'assets/css/ninety-style.css' );
			$leaflet_cluster_style         = NINETY_NINETY_URL . 'assets/js/markercluster/MarkerCluster.css';
			$leaflet_cluster_default_style = NINETY_NINETY_URL . 'assets/js/markercluster/MarkerCluster.Default.css';

			wp_enqueue_style( 'ninety-ninety-style', $style_file, [], $style_ver );
			wp_enqueue_style( 'ninety-cluster-style', $leaflet_cluster_style, [], $style_ver );
			wp_enqueue_style( 'ninety-cluster-default-style', $leaflet_cluster_default_style, [], $style_ver );

			// Only enqueue JS on map page.
			if ( $map_page ) {
				$script_file            = NINETY_NINETY_URL . 'assets/js/ninety-front.js';
				$script_ver             = filemtime( NINETY_NINETY_PATH . 'assets/js/ninety-front.js' );
				$leaflet_cluster_script = NINETY_NINETY_URL . 'assets/js/markercluster/leaflet.markercluster.js';

				wp_enqueue_script( 'ninety-ninety-script', $script_file, [ 'jquery' ], $script_ver, true );
				wp_enqueue_script( 'ninety-cluster-script', $leaflet_cluster_script, [ 'ninety-ninety-script' ], $script_ver, true );

				$data              = $this->get_meeting_data();
				$data['showChart'] = false;

				global $template;

				$template_name = basename( $template );
				$templates     = [
					'template-90-map.php',
					'template-genesis-90-map.php',
				];

				if ( in_array( $template_name, $templates ) ) {
					$data['showChart'] = true;
				}

				$data['meetingCount'] = $this->get_setting( 'meeting_count' );

				// localize script with geoJSON data for leafletjs.
				wp_localize_script( 'ninety-ninety-script', 'geojson', $data );
			}

		}

		/**
		 * Get Meeting data for Map
		 * We need #meetings per location and location details
		 *
		 * @return int[]|WP_Post[]
		 * @since 1.0.0
		 */
		protected function get_meeting_data() {

			$args = [
				'post_type'      => 'ninety_meeting',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			];

			$meetings = get_posts( $args );

			$geojson = [
				'features' => [],
				'type'     => 'FeatureCollection',

			];

			$coords = [];

			if ( ! empty( $meetings ) ) {

				foreach ( $meetings as $meeting ) {

					// Check ACF fields for excluding meeting from PDF, Map and Count ( ninety_exclude_meeting = field_5d223c6a1e849 ).
					if ( $this->get_option( 'ninety_use_exclude' ) && get_field( 'ninety_exclude_meeting', $meeting->ID ) ) {
						continue;
					}

					// Reduce meetings into array of locations with counts.
					$location = get_field( 'ninety_meeting_location', $meeting->ID );
					$address  = get_term_meta( $location->term_id, 'ninety_location_address', true );
					if ( isset( $coords[ $location->slug ] ) ) {
						$coords[ $location->slug ]['count'] ++;
					} else {
						$coords[ $location->slug ]['count']       = 1;
						$coords[ $location->slug ]['name']        = $location->name;
						$coords[ $location->slug ]['slug']        = $location->slug;
						$coords[ $location->slug ]['address']     = $address;
						$coords[ $location->slug ]['description'] = $location->description;
					}

					$coords[ $location->slug ]['coords'] = get_field( 'ninety_location_coords', $location );

				}
			}

			// Create feature for each location, add properties for displaying on map.
			foreach ( $coords as $coord ) {
				$feature = [
					'type'       => 'Feature',
					'properties' => [
						'title'       => $coord['name'],
						// translators: singular and plural forms of Meeting.
						'count'       => sprintf( _n( '%d meeting', '%d meetings', $coord['count'], 'ninety-ninety' ), number_format_i18n( $coord['count'] ) ),
						'link'        => get_site_url() . '/meetings/' . $coord['slug'],
						'address'     => $coord['address'],
						'description' => $coord['description'],
					],
					'geometry'   => [
						'coordinates' => $coord['coords'],
						'type'        => 'Point',
					],
				];

				$geojson['features'][] = $feature;
			}

			// https://color.adobe.com/ninety-color-theme-12942667.
			$default_marker_colors = [
				'default' => '#FF9912',
				'fifteen' => '#B32410',
				'thirty'  => '#52C7FF',
				'sixty'   => '#1E72FF',
				'ninety'  => '#1B27B3',
			];

			// Allow customizing colors.
			$custom_marker_colors = apply_filters( 'ninety_meeting_colors', $default_marker_colors );

			// Ensure no keys were left out when filtered.
			$marker_colors = wp_parse_args( $custom_marker_colors, $default_marker_colors );

			$geojson['markerColors'] = $marker_colors;

			// For testing geojson file.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$file    = NINETY_NINETY_PATH . 'test.geojson';
				$fh      = fopen( $file, 'w' );
				$encoded = json_encode( $geojson );
				fwrite( $fh, $encoded );
				fclose( $fh );
			}

			return $geojson;
		}

		/**
		 * Set default time for ACF time/date field
		 * Purely for convenience.  I got tired of switching the hour every time I added a Meeting.
		 *
		 * @param array $field ACF field for Meeting date/time.
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function set_default_meeting_time( $field ) {

			$default_time = $this->get_option( 'ninety_default_mtg_time' );

			if ( empty( $default_time ) ) {
				return $field;
			}

			// Get current hour - WordPress local timezone from WP Settings page.
			$hour = current_time( 'H' );
			// If it's before the default Meeting time set date to yesterday.
			$day = (int) $hour < intval( $default_time ) ? 'yesterday ' : 'today ';
			// Get WordPress timezone.
			$tz_string = get_option( 'timezone_string' );

			$timezone = $tz_string ? new DateTimeZone( $tz_string ) : null;

			// Create new DateTime object based on current hour, default meeting hour and WP timezone
			// example strings: today 19:00, yesterday 09:30.
			$default = new DateTime( $day . $default_time, $timezone );

			$field['default_value'] = $default->format( 'd-m-Y G:i:s' );

			return $field;
		}

		/**
		 * Make ACF Location coords field read-only
		 *
		 * @param array $field ACF field for Location coords.
		 *
		 * @return mixed
		 * @since 1.1.0
		 */
		public function ninety_coords_readonly( $field ) {

			$field['disabled'] = '1';

			return $field;
		}

		/**
		 * Set Meeting title to date/time, update slug and post dates
		 * Runs on acf/save-post so we have access to saved ACF field data
		 *
		 * @param int $post_id Post ID.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function set_meeting_title_time( $post_id ) {

			$post = get_post( $post_id );

			// Set Meeting title to date/time, update slug.
			if ( null !== $post && $time = get_field( 'ninety_meeting_time', $post_id ) ) {

				$now           = current_time( 'mysql' );
				$timestamp     = strtotime( $time );
				$post_date     = date( 'Y-m-d H:i:s', $timestamp );
				$post_date_gmt = get_gmt_from_date( $post_date );

				$post->post_title        = $time;
				$post->post_name         = sanitize_title_with_dashes( $time, null, 'save' );
				$post->post_date         = $post_date;
				$post->post_date_gmt     = $post_date_gmt;
				$post->post_modified     = $now;
				$post->post_modified_gmt = get_gmt_from_date( $now );

				wp_update_post( $post );

				$this->update_meeting_count();

			}

			// save_post hook for terms provides 'term_###' instead of ###.
			if ( false !== strpos( $post_id, 'term_' ) ) {

				$term_id = str_replace( 'term_', '', $post_id );
				$address = get_term_meta( $term_id, 'ninety_location_address', true );

				if ( $address ) {
					$this->geocode_meeting_location( $term_id, $address );
				}
			}
		}

		/**
		 * Update Location term meta with geo coords
		 *
		 * @param int    $term_id Location ID.
		 * @param string $address Location address.
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function geocode_meeting_location( $term_id, $address ) {

			$cache_key = md5( '90-address-' . $address );

			if ( ! $geo = get_transient( $cache_key ) ) {

				$geo = $this->geo_lookup( $address );

				set_transient( $cache_key, $geo );

			}

			if ( isset( $geo['center'] ) ) {
				update_term_meta( $term_id, 'ninety_location_coords', $geo['center'] );
			}
			if ( isset( $geo['address'] ) ) {
				update_term_meta( $term_id, 'ninety_location_address', $geo['address'] );
			}

			// This needs to be done when adding Locations TODO: better way?
			flush_rewrite_rules();

		}

		/**
		 * Get lng/lat coords and proper address
		 *
		 * @param string $address Address to geocode.
		 *
		 * @return array $ret
		 * @since 1.1.0
		 */
		protected function geo_lookup( $address ) {

			$key    = $this->get_option( 'ninety_mapbox_api_key' );
			$mapbox = new Mapbox( $key );
			$res    = $mapbox->geocode( $address );
			$data   = $res->getData();

			$geo = [];

			if ( isset( $data[0]['center'] ) ) {
				$geo['center'] = $data[0]['center'];
			}

			if ( isset( $data[0]['place_name'] ) ) {
				$geo['address'] = $data[0]['place_name'];
			}

			return $geo;

		}

		/**
		 * Conditionally add meta query excluding Meetings
		 * Uses the 'ninety_use_exclude' option value
		 *
		 * @param array $args Args to be used for WP_Query.
		 *
		 * @return void
		 * @since 1.2.0
		 */
		public function maybe_add_exclude_meta_query( &$args ) {

			/**
			 * If "Use Exclude Option" is checked on the options page, add meta query to
			 * query all Meetings that are set to not exclude or are missing that meta ( posts created
			 * after that ACF field was created )
			 */
			if ( $this->get_option( 'ninety_use_exclude' ) ) {
				$args['meta_query'] = [
					'relation' => 'OR',
					[
						'key'     => 'ninety_exclude_meeting',
						'value'   => 0,
						'compare' => '=',
					],
					[
						'key'     => 'ninety_exclude_meeting',
						'compare' => 'NOT EXISTS',
					],
				];
			}

		}

		/**
		 * Update Meeting count based on Options
		 *
		 * @param mixed $old_value Old Options.
		 * @param mixed $new_value New Options.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_meeting_count( $old_value = null, $new_value = null ) {

			// If called from options page save, make sure we update option as needed.
			if ( isset( $new_value['ninety_use_exclude'] ) && '1' === $new_value['ninety_use_exclude'] ) {
				$this->update_option( 'ninety_use_exclude', '1' );
			} elseif ( null !== $new_value ) {
				$this->update_option( 'ninety_use_exclude' );
			}

			$count = $this->get_meetings( [], true );

			// Update option if new count differs from existing count.
			if ( $count !== (int) $this->get_setting( 'meeting_count' ) ) {
				$this->update_setting( 'meeting_count', $count );
				update_option( NINETY_COUNT_OPTION_KEY, $count );
			}
		}

		/**
		 * Get Meetings/Meeting count
		 *
		 * @param array $args  Args for WP_Query.
		 * @param bool  $count Used to only return count.
		 *
		 * @return int|int[]|WP_Post[]
		 * @since 1.0.0
		 */
		public function get_meetings( $args = [], $count = false ) {

			// TODO: add option for number of Meetings to include?
			// Query all Meetings that haven't been excluded.
			$default = [
				'post_type'      => 'ninety_meeting',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'order'          => 'ASC',
			];

			$new_args = wp_parse_args( $args, $default );

			// Possibly add meta query to exclude Meetings based on checkbox.
			$this->maybe_add_exclude_meta_query( $new_args );

			$q = get_posts( $new_args );

			// If $count arg is set to true, return just the count.
			if ( $count ) {
				return count( $q );
			}

			// Return all Meetings returned.
			return $q;

		}

		/**
		 * Provide single and archive templates unless overridden by theme
		 *
		 * @param string $template Page template.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function ninety_archive_template( $template ) {

			// Check for archive template.
			if ( is_post_type_archive( 'ninety_meeting' ) ) {

				$plugin_archive_template = plugin_dir_path( __FILE__ ) . 'archive-ninety_meeting.php';
				$theme_files             = [ 'archive-ninety_meeting.php' ];
				$exists                  = locate_template( $theme_files, false );

				if ( $exists ) {
					return $exists;
				} else {
					// Make sure template hasn't been deleted.
					if ( file_exists( $plugin_archive_template ) ) {
						return $plugin_archive_template;
					}
				}
			}

			// Check for single template.
			if ( is_singular( 'ninety_meeting' ) ) {

				$plugin_single_template = plugin_dir_path( __FILE__ ) . 'single-ninety_meeting.php';
				$theme_files            = [ 'single-ninety_meeting.php' ];
				$exists                 = locate_template( $theme_files, false );

				if ( $exists ) {
					return $exists;
				} else {
					// Make sure template hasn't been deleted.
					if ( file_exists( $plugin_single_template ) ) {
						return $plugin_single_template;
					}
				}
			}

			return $template;

		}

		/**
		 * Output Entry meta on Genesis templates.
		 * Used in archive and single templates for Genesis themes
		 *
		 * @param string $post_info Genesis meta header.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public static function ninety_meeting_genesis_entry_meta_header( $post_info ) {

			$program  = get_field( 'ninety_meeting_program', get_the_ID() );
			$type     = get_field( 'ninety_meeting_type', get_the_ID() );
			$location = get_field( 'ninety_meeting_location', get_the_ID() );

			$program = empty( $program ) ? '' : $program;

			$post_info = apply_filters( 'ninety_meeting_genesis_meta', '[post_categories before="" after=" &middot;"] <strong>' . esc_attr( $location->name ) . '</strong> &middot; <em>' . esc_attr( $type->name ) . '</em> &middot; ' . esc_attr( $program ) . ' [post_edit before=" &middot; "]' );

			return $post_info;

		}

		/**
		 * Hide menu items that link to Meetings archive page and map page
		 *
		 * @param WP_POST $item Nav menu item.
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function hide_meeting_nav_menu_objects( $item ) {

			// Get Keep Meetings Private option.
			$keep_private = ninety_ninety()->get_option( 'ninety_keep_private' );

			// Only hide menu items if Keep Meetings Private is not set to No.
			if ( $keep_private ) {

				// Is user logged out?
				$logged_out = ! is_user_logged_in();

				// If menu item links to archive page and user is not logged in, skip it.
				if ( '/meetings' === $item->url && $logged_out ) {
					$item->_invalid = true;
				}

				if ( 'page' === $item->object && $logged_out ) {

					$slug = get_page_template_slug( $item->object_id );

					// If page_template_slug is for either map page template, set _invalid to true to skip menu item.
					if ( in_array( $slug, $this->settings['page_templates'] ) ) {
						$item->_invalid = true;
					}
				}
			}

			return $item;
		}

	}

	/**
	 * Return a single instance of this class providing all its public methods
	 *
	 * @return NinetyNinety
	 * @since 1.0.0
	 */
	function ninety_ninety() {

		global $ninety;

		if ( ! isset( $ninety ) ) {
			$ninety = new NinetyNinety();
			$ninety->initialize();
		}

		return $ninety;

	}

	// Kick it off.
	ninety_ninety();

endif;  // End class exists check.
