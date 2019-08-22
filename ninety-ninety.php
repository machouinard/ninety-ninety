<?php
/**
 * WordPress plugin used to track AA Meetings
 *
 * @package     Ninety-Ninety
 * @since       1.0.0
 * @author      machouinard
 */

/**
 * Plugin Name: 90 in 90+
 * Plugin URI:
 * Description: Track 90 meetings in 90 days, for starters.  Built for AA but customizable to any program.
 * Version: 1.0.0
 * Author: Mark Chouinard
 * Author URI: https://chouinard.me
 * Text Domain: ninety-ninety
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
		public $version = '1.0.0';
		/**
		 * @var array Settings array
		 */
		public $settings = [];
		/**
		 * @var array array
		 */
		public $options = [];
		/**
		 * @var string Default Tile Set
		 */
		public $default_tile_server;

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
			$last_updated  = get_option( 'ninety_last_updated', current_time( 'timestamp' ) + 60 );// make sure default is ahead of who's asking to trigger update

			$this->default_tile_server = 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png';

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
				'last_updated'  => $last_updated,
			];

			// Get stored options set on plugin options page.
			$this->options = get_option( 'ninety_settings' );

			// Meeting CPT and taxonomies.
			require_once NINETY_NINETY_PATH . 'inc/class-cpt-tax.php';
			// Instantiate CPT class to create meeting post type and taxonomies.
			new NinetyNinety_CPT();

			// Add default Meeting Locations on plugin activation.
			register_activation_hook( __FILE__, [ 'NinetyNinety_CPT', 'activate' ] );
			register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
			// Do stuff when plugin is deleted.
			register_uninstall_hook( __FILE__, [ 'NinetyNinety_CPT', 'uninstall' ] );

			// Add options page for plugin settings.
			require_once NINETY_NINETY_PATH . 'inc/class-options-page.php';
			// Require included ACF ( free version ) if ACF is not already active.
			if ( ! class_exists( 'ACF' ) ) {
				require_once NINETY_ACF_PATH . 'acf.php';
			}

			require_once $path . 'inc/mapbox/Mapbox.php';

			// Setup ACF Fields.
			require_once $path . 'inc/acf-fields.php';

			require_once $path . 'inc/widget-meeting-calendar.php';

			require_once $path . 'inc/widget-meeting-search.php';

			require_once $path . 'inc/class-widget-meeting-archives.php';

			require_once $path . 'inc/class-ninety-help-tabs.php';

			require_once $path . 'inc/functions.php';

			$this->add_actions_and_filters();

		}

		/**
		 * Add any actions and filters we need
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_actions_and_filters() {

			// Actions.
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_stuff' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_stuff' ] );
			add_action( 'acf/init', 'ninety_init_acf_import' );
			add_action( 'create_ninety_meeting_location', [ $this, 'geocode_meeting_location' ], PHP_INT_MAX );
			add_action( 'edited_ninety_meeting_location', [ $this, 'geocode_meeting_location' ], PHP_INT_MAX );
			add_action( 'acf/save_post', [ $this, 'set_meeting_title_time' ], PHP_INT_MAX );// Run as late as possible.
			add_action( 'template_redirect', [ $this, 'redirect_single_result' ] );
			add_action( 'widgets_init', [ $this, 'register_widgets' ] );
			add_action( 'load-post.php', 'NinetyHelpTabs::add_meeting_help_tab' );
			add_action( 'load-post-new.php', 'NinetyHelptabs::add_meeting_help_tab' );
			add_action( 'init', [ $this, 'load_text_domain' ] );
			add_action( 'init', [ $this, 'setup_shortcodes' ] );
			add_action( 'save_post_ninety_meeting', [ $this, 'update_timestamp' ] );
			add_action( 'edited_ninety_meeting_type', [ $this, 'update_timestamp' ] );
			// Filters.
			add_filter( 'manage_edit-ninety_meeting_location_columns', [ $this, 'manage_location_columns' ] );
			add_filter( 'manage_ninety_meeting_location_custom_column', [
				$this,
				'manage_location_custom_column',
			], 10, 3 );
			add_filter( 'acf/settings/show_admin', [ $this, 'acf_show_admin' ] );
			add_filter( 'template_include', [ $this, 'archive_template' ] );
			add_filter( 'acf/load_field/key=field_5d182c40c6e57', [ $this, 'set_default_meeting_time' ] );
			add_filter( 'acf/load_field/key=field_5d18480b686a6', [ $this, 'set_default_meeting_location' ] );
			add_filter( 'acf/load_field/key=field_5d18255d071c8', [ $this, 'set_default_meeting_type' ] );
			add_filter( 'acf/load_field/key=field_5d184b55fa43e', [ $this, 'filter_meeting_programs' ] );
			add_filter( 'acf/load_field/key=field_5d197bc132ced', [ $this, 'ninety_coords_readonly' ] );
			add_action( 'update_option_ninety_settings', [ $this, 'update_meeting_count' ], 10, 2 );
			add_filter( 'wp_setup_nav_menu_item', [ $this, 'hide_meeting_nav_menu_objects' ] );
			add_filter( 'posts_search', 'Ninety_Meeting_Search::advanced_custom_search', 500, 2 );

			if ( ! class_exists( 'ACF' ) ) {
				add_filter( 'acf/settings/dir', [ $this, 'acf_settings_dir' ] );
			}
			// Disable emoji detection on admin screens.
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		}

		/**
		 * Display admin notice if MapBox API key is not set
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function admin_notices() {

			if ( ! $this->get_option( 'ninety_mapbox_api_key' ) ) {
				$this->missing_api_key_error();
			}

		}

		/**
		 * Output admin notice for missing MapBox API key
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function missing_api_key_error() {

			$class = 'notice notice-warning is-dismissible';

			$message = __(
				'Missing MapBox API key.',
				'ninety-ninety'
			);

			printf(
				'<div class="%1$s"><p>%2$s</p></div>',
				esc_attr( $class ),
				esc_html( $message )
			);
		}

		/**
		 * Add custom column to Meeting Locations admin screen
		 *
		 * @param array $cols Array of admin columns.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function manage_location_columns( $cols ) {

			$valid_col['valid'] = 'Status';

			// Add column between 'Slug' and 'Count'.
			$new_cols = array_slice( $cols, 0, 4, true ) + $valid_col + array_slice( $cols, 4, null, true );

			return $new_cols;
		}

		/**
		 * Add check mark to custom admin column if it has geo coords.
		 *
		 * @param string $content     Column content.
		 * @param string $column_name Column name.
		 * @param int    $term_id     Term ID.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function manage_location_custom_column( $content, $column_name, $term_id ) {

			// Check column name in case more columns are added.
			if ( 'valid' === $column_name ) {
				$term = get_term( $term_id, 'ninety_meeting_location' );

				if ( ! $term || is_wp_error( $term ) ) {
					return '<span style="color: #ff0000;">&#x2718;</span>'; // X mark
				}

				$coords = get_field( 'ninety_location_coords', $term );

				if ( empty( $coords ) ) {
					$content = '<span style="color: #ff0000;">&#x2718;</span>'; // X mark
				} else {
					$content = '<span style="color: #49b74e;">&#10004;</span>'; // Check mark.
				}
			}

			return $content;
		}

		/**
		 * Enqueue admin scripts and styles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_admin_stuff() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-ui-datepicker-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', [], $this->get_setting( 'version' ) );
			$admin_script     = NINETY_NINETY_URL . 'assets/js/ninety-admin.js';
			$admin_script_ver = filemtime( NINETY_NINETY_PATH . 'assets/js/ninety-admin.js' );
			wp_enqueue_script(
				'ninety-admin-js',
				$admin_script,
				[
					'jquery',
					'wp-color-picker',
					'jquery-ui-datepicker',
				],
				$admin_script_ver,
				true
			);
		}

		/**
		 * Enqueue public scripts and styles
		 * Localize data for use on Map page.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_stuff() {

			global $post;

			$map_page             = ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'ninety_map' ) );
			$meeting_archive_page = is_post_type_archive( 'ninety_meeting' );
			$singular_meeting     = is_singular( 'ninety_meeting' );

			if ( ! ( $map_page || $meeting_archive_page || $singular_meeting ) ) {
				return;
			}

			$leaflet_css_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.css';

			$version = ninety_ninety()->version;
			wp_enqueue_style( 'map-style', $leaflet_css_url, [], $version );

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

				$remaining_color = ninety_ninety()->get_option( 'ninety_remaining_color', '#dd3333' );
				$done_color      = ninety_ninety()->get_option( 'ninety_done_color', '#81d742' );

				$data['colors'] = [
					'remaining' => $remaining_color,
					'done'      => $done_color,
				];

				$data['meetingCount'] = $this->get_setting( 'meeting_count' );

				// localize script with geoJSON data for leafletjs.
				wp_localize_script( 'ninety-ninety-script', 'geojson', $data );

				/******** Leaflet JS ************* */
				$leaflet_js_url = NINETY_NINETY_URL . 'assets/js/leaflet/leaflet.js';

				wp_enqueue_script( 'map-js', $leaflet_js_url, array( 'jquery' ), $this->get_setting( 'version' ) );

				// Get default latitude from options page.
				$lat = ninety_ninety()->get_option( 'ninety_map_center_lat' );

				// If Lat hasn't been set, set it to Sacramento because, well, that's where I live.
				if ( ! $lat ) {
//					$lat = 54.525963;
					$lat = 38.581573;
				}

				// Get default longitude from options page.
				$lng = ninety_ninety()->get_option( 'ninety_map_center_lng' );

				// If Lng hasn't been set, set it to Sacramento.
				if ( ! $lng ) {
//					$lng = - 105.255119;
					$lng = - 121.494400;
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
					]
				);
			}

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

		}

		/**
		 * Redirect to single template if only 1 Meeting archive
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function redirect_single_result() {
			if ( is_post_type_archive( 'ninety_meeting' ) ) {
				global $wp_query;
				if ( 1 == $wp_query->post_count ) {
					// Get Location using term ID from post meta
					$location = get_term_by( 'id', $wp_query->post->ninety_meeting_location, 'ninety_meeting_location' );

					if ( ! $location ) {
						return;
					}

					// Redirect to single Meeting page with location to hide next/prev links
					wp_redirect( site_url( 'meetings/' . $location->slug . '/' ) . $wp_query->posts[0]->post_name );
					exit;
				}
			}

			if ( is_search() || is_archive() ) {
				global $wp_query;
				if ( 1 === $wp_query->post_count ) {
					wp_safe_redirect( get_permalink( $wp_query->posts[0]->ID ) );
					exit;
				}
			}
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
		 * Register our widgets
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_widgets() {

			if ( $this->get_option( 'ninety_keep_private' ) && ! is_user_logged_in() ) {
				return;
			}

			register_widget( 'Ninety_Meeting_Calendar' );
			register_widget( 'Ninety_Meeting_Search' );
			register_widget( 'Ninety_Meeting_Archives' );
		}

		/**
		 * Load text domain
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function load_text_domain() {
			load_plugin_textdomain( 'ninety-ninety', false, 'ninety-ninety/lang' );
		}

		/**
		 * Add shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function setup_shortcodes() {
			add_shortcode( 'ninety_map', [ $this, 'handle_map_shortcode' ] );
		}

		/**
		 * Map shortcode handler
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @param string $tag     Tag for use in shared callbacks.
		 *
		 * @return string|void
		 * @since 1.0.0
		 */
		public function handle_map_shortcode( $atts, $content, $tag ) {

			if ( ninety_ninety()->get_option( 'ninety_keep_private' ) && ! is_user_logged_in() ) {
				return;
			}

			if ( ! is_singular() ) {
				return;
			}

			$defaults = [
				'show_count' => false,
				'chart_type' => false,
				'show_chart' => false,
				'title'      => false,
				'zoom'       => false,
			];

			$map_options = wp_parse_args( $atts, $defaults );

			$chart_type = false !== $map_options['chart_type'] ? $map_options['chart_type'] : ninety_ninety()->get_option( 'ninety_chart_type', 'pie' );

			$show_chart = false !== $map_options['show_chart'] ? true : ninety_ninety()->get_option( 'ninety_show_chart', false );

			$zoom = false !== $map_options['zoom'] ? intval( $map_options['zoom'] ) : ninety_ninety()->get_option( 'ninety_map_zoom', 1 );

			$count = ninety_ninety()->get_setting( 'meeting_count' );

			$output = '';

			if ( $map_options['title'] ) {
				$output .= sprintf( '<h4>%s</h4>', esc_html( $map_options['title'] ) );
			}

			if ( $map_options['show_count'] ) {
				$output .= sprintf( '<h4>%d %s</h4>', $count, __( 'Meetings', 'ninety-ninety' ) );
			}

			$output .= '<div id="ninety-map" style="height: 400px"></div>';

			$output .= ninety_add_chart_markup( true, $chart_type, $show_chart, $zoom );

			return $output;

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
		 * Provide single and archive templates unless overridden by theme
		 *
		 * @param string $template Page template.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function archive_template( $template ) {

			// Check for archive template.
			if ( is_post_type_archive( 'ninety_meeting' ) ) {

				$archive_template = plugin_dir_path( __FILE__ ) . 'templates/archive-ninety_meeting.php';
				$theme_files      = [ 'archive-ninety_meeting.php' ];
				$exists           = locate_template( $theme_files, false );

				if ( $exists ) {
					return $exists;
				} else {
					// Make sure template hasn't been deleted.
					if ( file_exists( $archive_template ) ) {
						return $archive_template;
					}
				}
			}

			// Check for single template.
			if ( is_singular( 'ninety_meeting' ) ) {

				$single_template = plugin_dir_path( __FILE__ ) . 'templates/single-ninety_meeting.php';
				$theme_files     = [ 'single-ninety_meeting.php' ];
				$exists          = locate_template( $theme_files, false );

				if ( $exists ) {
					return $exists;
				} else {
					// Make sure template hasn't been deleted.
					if ( file_exists( $single_template ) ) {
						return $single_template;
					}
				}
			}

			return $template;

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

			if ( ! $default_time ) {
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
			try {
				$default = new DateTime( $day . $default_time, $timezone );
			} catch ( Exception $e ) {
				error_log( $e->getMessage() );
			}

			$field['default_value'] = $default->format( 'd-m-Y G:i:s' );

			return $field;
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

			if ( ! $location ) {
				return $field;
			}

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

			if ( ! $type ) {
				return $field;
			}

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
		 * Make ACF Location coords field read-only
		 *
		 * @param array $field ACF field for Location coords.
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function ninety_coords_readonly( $field ) {

			$field['disabled'] = '1';

			return $field;
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

			$count = $this->get_meetings( [], true );

			// Update option if new count differs from existing count.
			if ( $count !== (int) $this->get_setting( 'meeting_count' ) ) {
				$this->update_setting( 'meeting_count', $count );
				update_option( NINETY_COUNT_OPTION_KEY, $count );
			}
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

					$page = get_post( $item->object_id );
					if ( is_a( $page, 'WP_POST' ) && has_shortcode( $page->post_content, 'ninety_map' ) ) {
						$item->_invalid = true;
					}

				}
			}

			return $item;
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
		private function has_setting( $name ) {

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

			return $this->has_setting( $name ) ? $this->settings[ $name ] : null;
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

			// Can't update something that doesn't exist.
			if ( ! $this->has_setting( $name ) ) {
				return false;
			}

			$this->settings[ $name ] = $value;

			return true;
		}

		/**
		 * Check if option exists
		 *
		 * @param string $name Option name.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		private function has_option( $name ) {

			return isset( $this->options[ $name ] );
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

			if ( $this->has_option( $name ) ) {
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
		 * @return bool
		 * @since 1.0.0
		 */
		public function update_option( $name, $value = false ) {

			// Can't update something that doesn't exist.
			if ( ! $this->has_option( $name ) ) {
				return false;
			}

			if ( $value ) {
				$this->options[ $name ] = $value;
			} else {
				unset( $this->options[ $name ] );
			}

			return true;

		}

		/**
		 * Update Location term meta with geo coords
		 *
		 * @param int    $term_id Location ID.
		 * @param string $address Location address.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function geocode_meeting_location( $term_id ) {

			// If we don't have an API key, bail.
			if ( ! $this->get_option( 'ninety_mapbox_api_key' ) ) {
				return;
			}

			$address = get_term_meta( $term_id, 'ninety_location_address', true );

			if ( ! $address ) {
				return;
			}

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

			// Update last_updated timestamp - future use.
			$this->update_timestamp();

			// This needs to be done when adding Locations TODO: better way?
			flush_rewrite_rules();

		}

		/**
		 * Get lng/lat coords and proper address
		 *
		 * @param string $address Address to geocode.
		 *
		 * @return array $ret
		 * @since 1.0.0
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
		 * Get Meetings/Meeting count
		 *
		 * @param array $args  Args for WP_Query.
		 * @param bool  $count Used to only return count.
		 *
		 * @return int|int[]|WP_Post[]
		 * @since 1.0.0
		 */
		public function get_meetings( $args = [], $count = false ) {

			// Query Meetings
			$default = [
				'post_type'      => 'ninety_meeting',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'order'          => 'ASC',
			];

			$new_args = wp_parse_args( $args, $default );

			$q = get_posts( $new_args );

			// If $count arg is set to true, return just the count.
			if ( $count ) {
				return count( $q );
			}

			// Return all Meetings returned.
			return $q;

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

					// Reduce meetings into array of locations with counts.
					$location = get_field( 'ninety_meeting_location', $meeting->ID );

					if ( ! $location ) {
						continue;
					}

					$address = get_term_meta( $location->term_id, 'ninety_location_address', true );

					if ( ! $address ) {
						continue;
					}

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
			$default_colors = [
				'default' => '#FF9912',
				'fifteen' => '#B32410',
				'thirty'  => '#52C7FF',
				'sixty'   => '#1E72FF',
				'ninety'  => '#1B27B3',
			];

			// Allow customizing colors.
			$custom_colors = apply_filters( 'ninety_meeting_colors', $default_colors );

			// Ensure no keys were left out when filtered.
			$marker_colors = wp_parse_args( $custom_colors, $default_colors );

			$geojson['markerColors'] = $marker_colors;

			// For testing geojson file.
//			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
//				$file    = NINETY_NINETY_PATH . 'test.geojson';
//				$fh      = fopen( $file, 'w' );
//				$encoded = json_encode( $geojson );
//				fwrite( $fh, $encoded );
//				fclose( $fh );
//			}

			return $geojson;
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

			// If any of these items are missing, return $post_info, unchanged.
			if ( ! $program || ! $type || ! $location ) {
				return $post_info;
			}

			$program = empty( $program ) ? '' : $program;
			$link    = get_site_url( null, 'meetings/' . $location->slug );

			$post_info = apply_filters( 'ninety_meeting_genesis_meta', '[post_categories before="" after=" &middot;"] <strong><a href="' . $link . '">' . esc_attr( $location->name ) . '</a></strong> &middot; <em>' . esc_attr( $type->name ) . '</em> &middot; ' . esc_attr( $program ) . ' [post_edit before=" &middot; "]' );

			return $post_info;

		}

		/**
		 * Update option when Meetings/Taxonomies change
		 * This is for future plans
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_timestamp() {

			$time = current_time( 'timestamp' );
			update_option( 'ninety_last_updated', $time );
			$this->update_setting( 'last_updated', $time );

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
