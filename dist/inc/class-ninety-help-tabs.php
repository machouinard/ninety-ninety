<?php
require_once NINETY_NINETY_PATH .'inc/class-ninety-help-contents.php';
/**
 * Description
 *
 * @package     NINETY-NINETY
 * @since       1.0.0
 * @author      machouinard
 */

/**
 * Class Ninety_Help_tabs
 */
class NinetyHelptabs {

	/**
	 * @var array Tab data
	 */
	public $tabs;

	/**
	 * Ninety_Help_tabs constructor.
	 */
	public function __construct() {
	
	}

	public static function add_options_help_tabs() {

		$cs = get_current_screen();

		$cs->add_help_tab(
			array(
				'id'      => 'meeting_options_tab',
				'title'   => __( 'Meeting Options', 'ninety-ninety' ),
				'content' => ninety_help()->meeting_options(),
			)
		);
		
		$cs->add_help_tab(
			[
				'id' => 'map_options_tab',
				'title' => __( 'Map Options', 'ninety-ninety' ),
				'content' => ninety_help()->map_options(),
			]
		);
		
		$cs->add_help_tab(
			[
				'id' => 'misc_options_tab',
				'title' => __( 'Misc Options', 'ninety-ninety' ),
				'content' => ninety_help()->misc_options(),
			]
		);
		
		$cs->add_help_tab(
			[
				'id' => 'pdf_options_tab',
				'title' => __( 'PDF Options', 'ninety-ninety' ),
				'content' => ninety_help()->pdf_options(),
			]
		);
	}

	public static function add_meeting_help_tab() {
		
		global $typenow;
		
		if ( 'ninety_meeting' !== $typenow ) {
			return;
		}
		
		$cs = get_current_screen();
		
		$cs->add_help_tab(
			[
				'id' => 'ninety_meeting_help_tab',
				'title' => __( 'Meeting Help', 'ninety-ninety' ),
				'content' => ninety_help()->meeting_help(),
			]
		);
		
	}

}
