<?php
/**
 * Description
 *
 * @package     Ninety-Ninety
 * @since       1.0.0
 * @author      machouinard
 */

if ( ! class_exists( 'Ninety_Options' ) ) {

	/**
	 * Class Ninety_Options
	 */
	class Ninety_Options {

		/**
		 * Ninety_Options constructor.
		 */
		public function __construct() {
			add_action(
				'admin_menu',
				[ $this, 'ninety_add_admin_menu' ]
			);
			add_action(
				'admin_init',
				[ $this, 'ninety_settings_init' ]
			);
		}

		/**
		 * Description
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_add_admin_menu() {
			add_submenu_page(
				'edit.php?post_type=ninety_meeting',
				'Ninety Ninety',
				'Options',
				'manage_options',
				'ninety-settings',
				[ $this, 'ninety_options_page' ]
			);
		}

		/**
		 * Description
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_settings_init() {
			register_setting(
				'ninety-ninety-options',
				'ninety_settings',
				[
					'sanitize_callback' => [
						$this,
						'sanitize_callback',
					],
				]
			);

			add_settings_section(
				'ninety_pluginPage_section',
				__(
					'Meeting Options',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_settings_section_callback',
				],
				'pluginPage'
			);

			add_settings_section(
				'ninety_pluginPage_map_section',
				__(
					'Map Options',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_settings_map_section_callback',
				],
				'pluginMap'
			);

			add_settings_section(
				'ninety_pluginPage_misc_section',
				__(
					'Miscellaneous Options',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_settings_misc_section_callback',
				],
				'pluginMisc'
			);

			add_settings_section(
				'ninety_pluginPage_pdf_section',
				__(
					'PDF Options',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_settings_pdf_section_callback',
				],
				'pluginPdf'
			);

			add_settings_field(
				'ninety_default_mtg_location',
				__(
					'Default Meeting Location',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_default_mtg_location_render',
				],
				'pluginPage',
				'ninety_pluginPage_section'
			);

			add_settings_field(
				'ninety_default_mtg_time',
				__(
					'Default Meeting Time',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_default_mtg_time_render',
				],
				'pluginPage',
				'ninety_pluginPage_section'
			);

			add_settings_field(
				'ninety_default_mtg_type',
				__(
					'Default Meeting Type',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_default_mtg_type_render',
				],
				'pluginPage',
				'ninety_pluginPage_section'
			);

			add_settings_field(
				'ninety_mapbox_api_key',
				__(
					'MapBox API Key',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_mapbox_api_key_render',
				],
				'pluginMap',
				'ninety_pluginPage_map_section'
			);

			add_settings_field(
				'ninety_thunderforest_api_key',
				__(
					'Thunderforest API Key',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_thunderforest_api_key_render',
				],
				'pluginMap',
				'ninety_pluginPage_map_section'
			);

			add_settings_field(
				'ninety_tile_server',
				__(
					'Tile Set',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_tile_server_render',
				],
				'pluginMap',
				'ninety_pluginPage_map_section'
			);

			// TODO: If we use marker bounds we don't need lat/lng/zoom.
			add_settings_field(
				'ninety_map_center_lat',
				__( 'Default Map Center Lat', 'ninety-ninety' ),
				array(
					$this,
					'ninety_map_center_lat_render',
				),
				'pluginMap',
				'ninety_pluginPage_map_section'
			);

			add_settings_field(
				'ninety_map_center_lng',
				__( 'Default Map Center Lng', 'ninety-ninety' ),
				array(
					$this,
					'ninety_map_center_lng_render',
				),
				'pluginMap',
				'ninety_pluginPage_map_section'
			);
//
			add_settings_field(
				'ninety_map_zoom',
				__( 'Default Map Zoom Level', 'ninety-ninety' ),
				array(
					$this,
					'ninety_map_zoom_render',
				),
				'pluginMap',
				'ninety_pluginPage_map_section'
			);

			add_settings_field(
				'ninety_keep_private',
				__(
					'Keep Meetings Private',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_keep_private_render',
				],
				'pluginMisc',
				'ninety_pluginPage_misc_section'
			);

			add_settings_field(
				'ninety_use_exclude',
				__(
					'Use Exclude option (PDF, Maps, Count, etc...',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_use_exclude_render',
				],
				'pluginMisc',
				'ninety_pluginPage_misc_section'
			);

			add_settings_field(
				'ninety_pdf_title',
				__(
					'PDF Title',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_pdf_title_render',
				],
				'pluginPdf',
				'ninety_pluginPage_pdf_section'
			);

			add_settings_field(
				'ninety_pdf_show_days',
				__(
					'Show number of days on PDF?',
					'ninety-ninety'
				),
				[
					$this,
					'ninety_pdf_show_days_render',
				],
				'pluginPdf',
				'ninety_pluginPage_pdf_section'
			);

			add_settings_field(
				'ninety_pdf_printout',
				__(
					'PDF Printout',
					'ninety-ninety'
				),
				[ $this, 'ninety_pdf_printout_render' ],
				'pluginPdf',
				'ninety_pluginPage_pdf_section'
			);
		}

		/**
		 * Sanitize callback for register_setting()
		 *
		 * @param array $input Input to be sanitized.
		 *
		 * @return array Options page settings
		 * @since 1.2.0
		 */
		public function sanitize_callback( $input = [] ) {
			static $count = 0;

			if ( $count < 1 ) {
				if ( '' === $input['ninety_mapbox_api_key'] ) {
					add_settings_error(
						'ninety_mapbox_key',
						esc_attr( 'settings_updated' ),
						'MapBox API key is needed for geocoding Locations and displaying some of the tile sets.',
						'error'
					);
				}
				if ( '' === $input['ninety_thunderforest_api_key'] ) {
					add_settings_error(
						'ninety_thunderforest_key',
						esc_attr( 'settings_updated' ),
						'Thunderforest API key is needed for displaying some of the tile sets.',
						'error'
					);
				}
			}

			$count ++;

			return $input;
		}

		/**
		 * Output Default Location field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_default_mtg_location_render() {
			$options = ninety_ninety()->options;
			$terms   = get_terms(
				[
					'taxonomy'   => 'ninety_meeting_location',
					'hide_empty' => false,
				]
			); ?>
			<select name='ninety_settings[ninety_default_mtg_location]'>
				<option value="0"
					<?php
					selected( $options['ninety_default_mtg_location'], 0 );
					?>
				>
					-----
				</option>
				<?php

				foreach ( $terms as $term ) {
					$option = '<option 
							value="' . $term->term_id . '" ' .
					          selected( $options['ninety_default_mtg_location'], $term->term_id ) .
					          '>' .
					          $term->name .
					          "</option>\n";
					echo $option;
				}
				?>

			</select>

			<?php

		}

		/**
		 * Output Default Meeting Time field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_default_mtg_time_render() {
			$options = ninety_ninety()->options;
			?>
			<select name='ninety_settings[ninety_default_mtg_time]'>
				<option value=""
					<?php
					selected( $options['ninety_default_mtg_time'], '' );
					?>
				>
					-----
				</option>
				<?php

				// Build options for every 1/2 hour from 06:00 to midnight ( 00:00 )
				// https://www.php.net/manual/en/dateperiod.construct.php - $period = new DatePeriod($iso);.
				foreach ( new DatePeriod( 'R36/2019-07-04T06:00:00Z/PT30M' ) as $d ) {
					$time   = $d->format( 'H:i' );
					$option = "<option value='{$time}' " . selected( $options['ninety_default_mtg_time'], $time ) . ">{$time}</option>\n";
					echo $option;
				}
				?>

			</select>

			<?php

		}

		/**
		 * Output Default Meeting Type dropdown
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_default_mtg_type_render() {
			$options = ninety_ninety()->options;
			$terms   = get_terms(
				[
					'taxonomy'   => 'ninety_meeting_type',
					'hide_empty' => false,
				]
			);

			if ( is_wp_error( $terms ) ) {
				printf( '<p><strong>%s</strong></p>', esc_html( $terms->get_error_message() ) );

				// Debug info.
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					echo '<p><strong>WPError: ' . __CLASS__ . ':' . __LINE__ . '</strong></p>';
				}

				return;
			}
			?>
			<select name='ninety_settings[ninety_default_mtg_type]'>
				<option value="0"
					<?php
					selected( $options['ninety_default_mtg_type'], 0 );
					?>
				>
					-----
				</option>
				<?php

				foreach ( $terms as $term ) {
					$option = "<option value='{$term->term_id}' " . selected( $options['ninety_default_mtg_type'], $term->term_id ) . ">{$term->name}</option>\n";
					echo $option;
				}
				?>

			</select>

			<?php

		}

		/**
		 * Output Mapbox API Key field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_mapbox_api_key_render() {
			$key = ninety_ninety()->get_option( 'ninety_mapbox_api_key' );
			?>
			<label for="ninety_settings[ninety_mapbox_api_key]">API info<a
						href="https://docs.mapbox.com/help/how-mapbox-works/access-tokens/"
						target="_blank">&nbsp;here.</a> </label>
			<input type='text' name='ninety_settings[ninety_mapbox_api_key]' id='ninety_settings[ninety_mapbox_api_key]'
				   value='<?php echo esc_attr( $key ); ?>' size="125">
			<?php

		}

		/**
		 * Output Thunderforest API Key field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_thunderforest_api_key_render() {
			$key = ninety_ninety()->get_option( 'ninety_thunderforest_api_key' );
			?>
			<label for="ninety_settings[ninety_thunderforest_api_key]">Signup info<a
						href="https://www.thunderforest.com/pricing/"
						target="_blank">&nbsp;here.</a> </label>
			<input id='ninety_settings[ninety_thunderforest_api_key]'
				   name='ninety_settings[ninety_thunderforest_api_key]'
				   size="125"
				   type='text' value='<?php echo esc_attr( $key ); ?>'>
			<?php

		}

		/**
		 * Output Tile Server dropdown
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_tile_server_render() {

			$tile_server_option = ninety_ninety()->get_option( 'ninety_tile_server' );

			// Array of tile providers and style URLs.
			$servers = [
				'MapBox'        => [
					'MapBox' => 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}',
					'OSM'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png?access_token={accessToken}',
				],
				'Thunderforest' => [
					'Neighbourhood' => 'https://tile.thunderforest.com/neighbourhood/{z}/{x}/{y}.png?apikey={accessToken}',
					'Cycle'         => 'https://tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey={accessToken}',
					'Transport'     => 'https://tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey={accessToken}',
					'TransportDark' => 'https://tile.thunderforest.com/transport-dark/{z}/{x}/{y}.png?apikey={accessToken}',
					'Landscape'     => 'https://tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey={accessToken}',
					'Outdoors'      => 'https://tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey={accessToken}',
					'Pioneer'       => 'https://tile.thunderforest.com/pioneer/{z}/{x}/{y}.png?apikey={accessToken}',
				],
				'Carto [free]'  => [
					'Light' => 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png',
				],
			];
			?>
			<select name='ninety_settings[ninety_tile_server]'>
				<option value="0" <?php selected( $tile_server_option, 0 ); ?>>
					-----
				</option>
				<?php

				foreach ( $servers as $n => $server ) {
					foreach ( $server as $name => $url ) {
						$option = "<option value='{$url}' " . selected( $tile_server_option, $url ) . ">{$name} ({$n})</option>\n";
						echo $option;
					}
				}
				?>

			</select>

			<?php

		}

		/**
		 * Output Default Map Center latitude input field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_map_center_lat_render() {
			$center_lat = ninety_ninety()->get_option( 'ninety_map_center_lat' );
			?>
			<label style="display: block;" for="ninety_settings[ninety_map_center_lat]">Lat/Lng Lookup <a
						href="https://www.latlong.net/" target="_blank">here.</a> </label>
			<input type='text' name='ninety_settings[ninety_map_center_lat]' id='ninety_settings[ninety_map_center_lat]'
				   value='<?php echo esc_attr( $center_lat ); ?>' size="20">
			<?php

		}

		/**
		 * Output Default Map Center longitude input field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_map_center_lng_render() {
			$center_lng = ninety_ninety()->get_option( 'ninety_map_center_lng' );
			?>
			<label style="display: block;" for="ninety_settings[ninety_map_center_lng]">Lat/Lng Lookup <a
						href="https://www.latlong.net/" target="_blank">here.</a> </label>
			<input type='text' name='ninety_settings[ninety_map_center_lng]'
				   value='<?php echo esc_attr( $center_lng ); ?>' size="20">
			<?php

		}

		/**
		 * Output Default Map Zoom Level input field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_map_zoom_render() {
			$zoom = ninety_ninety()->get_option( 'ninety_map_zoom' );
			?>
			<input type='number' name='ninety_settings[ninety_map_zoom]'
				   value='<?php echo (int) $zoom; ?>' min="1" max="18" step="1">
			<?php

		}

		/**
		 * Output Keep Private checkbox
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_keep_private_render() {
			$private = ninety_ninety()->get_option( 'ninety_keep_private' );
			?>
			<input type='checkbox'
				   name='ninety_settings[ninety_keep_private]' <?php checked( $private, 1 ); ?>
				   value='1'>
			<?php

		}

		/**
		 * Output Exclude checkbox
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_use_exclude_render() {
			$exclude = ninety_ninety()->get_option( 'ninety_use_exclude' );
			?>
			<input type='checkbox'
				   name='ninety_settings[ninety_use_exclude]' <?php checked( $exclude, 1 ); ?>
				   value='1'>
			<?php

		}

		/**
		 * Output PDF Title field
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_pdf_title_render() {
			$title = ninety_ninety()->get_option( 'ninety_pdf_title' );
			?>
			<input type='text' name='ninety_settings[ninety_pdf_title]' value='<?php echo esc_html( $title ); ?>'
				   size="125">
			<?php

		}

		/**
		 * Output Show Days checkbox
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_pdf_show_days_render() {
			$show_days = ninety_ninety()->get_option( 'ninety_pdf_show_days' );
			?>
			<input type='checkbox'
				   name='ninety_settings[ninety_pdf_show_days]' <?php checked( $show_days, 1 ); ?> value='1'>
			<?php

		}

		/**
		 * Call function to output PDF
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_pdf_printout_render() {
			$this->ninety_create_pdf();
		}

		/**
		 * Output Meeting defaults section title
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_settings_section_callback() {
			echo esc_attr__( 'Set Meeting defaults', 'ninety-ninety' );
		}

		/**
		 * Output Map defaults section title
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_settings_map_section_callback() {
			echo esc_attr__( 'Set Map defaults', 'ninety-ninety' );
		}

		/**
		 * Output Other options section title
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_settings_misc_section_callback() {
			echo esc_attr__( 'Set Other options', 'ninety-ninety' );
		}

		/**
		 * Output PDF options section title
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_settings_pdf_section_callback() {
			echo esc_attr__( 'Set PDF Options', 'ninety-ninety' );
		}

		/**
		 * Create PDF list of Meetings
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function ninety_create_pdf() {
			$meetings = ninety_ninety()->get_meetings();

			// Bail if no Meetings found.
			if ( empty( $meetings ) ) {
				echo '<h2>A PDF of your meetings will appear here when there is data.</h2>';

				return;
			}

			// Count returned Meetings.
			$count = count( $meetings );

			// Get beginning and ending dates from queried Meetings, based on post_date.
			$begin         = $meetings[0]->post_date;
			$end           = $meetings[ $count - 1 ]->post_date;
			$first_meeting = null;
			$last_meeting  = null;

			try {
				$first_meeting = new DateTime( $begin );
			} catch ( Exception $e ) {
				$error = $e->getMessage();
			}
			try {
				$last_meeting = new DateTime( $end );
			} catch ( Exception $e ) {
				$error = $e->getMessage();
			}
			$time_diff = $first_meeting->diff( $last_meeting );

			$meeting_lines = [];

			foreach ( $meetings as &$meeting ) {
				$id        = $meeting->ID;
				$post_date = $meeting->post_date;
				$date      = date( 'm/d/Y', strtotime( $post_date ) );
				$time      = date( 'g:iA', strtotime( $post_date ) );
				$location  = get_field( 'ninety_meeting_location', $id );

				// AA/NA/GA, etc...
				$program = get_field( 'ninety_meeting_program', $id );

				// Build line.
				$line = $date . ';' . $location->name . ';' . $time . ';' . $program . PHP_EOL;

				// Add line to array for later.
				array_push( $meeting_lines, $line );

			}

			// Date range of Meetings from Query.
			$range = date( 'M d, Y', strtotime( $begin ) ) . ' to ' . date( 'M d, Y', strtotime( $end ) ) . PHP_EOL;

			// MachPDF class extends FPDF for PDF creation.
			require_once NINETY_NINETY_PATH . 'inc/class-ninety-pdf.php';

			$pdf = new NinetyPDF();
			$pdf->AliasNbPages();
			$header = [ 'Date', 'Group', 'Time', 'Type' ];
			$data   = $pdf->loadData( $meeting_lines );
			$pdf->SetFont(
				'Helvetica',
				'',
				16
			);
			$pdf->AddPage();
			$pdf->Cell(
				0,
				6,
				$range,
				0,
				1
			);
			$pdf->SetFont(
				'Helvetica',
				'',
				14
			);
			$pdf->Ln();
			// translators: this is for the number of meetings attended.
			$meeting_stats = sprintf( _n( '%s meeting', '%s meetings', $count ), $count );

			if ( ninety_ninety()->get_option( 'ninety_pdf_show_days' ) ) {
				$meeting_stats .= ' | ' . $time_diff->days . ' days';
			}

			$pdf->Cell(
				0,
				6,
				$meeting_stats,
				0,
				1
			);
			$pdf->Ln();
			$pdf->FancyTable(
				$header,
				$data
			);

			// Use date range for PDF file title.
			$title = date( 'm-d-y_', strtotime( $begin ) ) . date( 'm-d-y', strtotime( $end ) ) . '.pdf';

			// Make sure meeting-files directory exists so we can store PDFs.
			if ( ! file_exists( NINETY_NINETY_PATH . 'meeting-files' ) ) {
				mkdir(
					NINETY_NINETY_PATH . 'meeting-files',
					0755,
					true
				);
			}

			// Output file path and filename.
			$meetings_pdf_file = NINETY_NINETY_PATH . 'meeting-files/' . $title;

			// Create PDF.
			$pdf->Output(
				'F',
				$meetings_pdf_file
			);

			// URL to PDF.
			$meetings_file_url = NINETY_NINETY_URL . 'meeting-files/' . $title;

			// If PDF file exists, display it.
			if ( file_exists( $meetings_pdf_file ) ) {
				?>

				<object
						width="750"
						height="1000"
						type="application/pdf"
						data="<?php echo esc_url( $meetings_file_url ); ?>">

				</object>

				<?php

			}
		}

		/**
		 * Output Options Page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ninety_options_page() {
			?>
			<div class="wrap">

				<?php settings_errors(); ?>

				<form action='options.php' method='post'>

					<?php
					settings_fields( 'ninety-ninety-options' );
					do_settings_sections( 'pluginPage' );
					submit_button();
					do_settings_sections( 'pluginMap' );
					submit_button();
					do_settings_sections( 'pluginMisc' );
					submit_button();
					do_settings_sections( 'pluginPdf' );
					submit_button();
					?>

				</form>
			</div>
			<?php

		}
	}

	new Ninety_Options();
}
