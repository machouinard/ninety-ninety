<?php
/**
 * Output contents of help tabs
 *
 * @package     Ninety-Ninety
 * @since       1.0.0
 * @author      machouinard
 */

class ninetyHelpContents {
	
	/**
	 * @var bool | object Class instance.
	 */
	public $instance = false;
	
	/**
	 * ninetyHelpContents constructor.
	 */
	public function __construct() {
		// Nothing to see or do here.
	}
	
	public function meeting_options() {
		
		$contents = '<ul>';
		$contents .= '<li>' . __( 'Default Location, Time and Type are used when creating a new Meeting.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'For the Default Time option to work as expected you\'ll need to make sure the Timezone is set correctly on the General Settings page.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		
		return $contents;
	}
	
	public function map_options() {
		
		$contents = '<ul>';
		$contents .= '<li>' . __( 'API Keys are required for mapping your Meetings.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'The MapBox API key is required for automatically geocoding Location addresses.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'MapBox and Thunderforest API keys are required to display their respective Tile Sets.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'The chosen Tile Set is what determines the appearance of the Meeting Map.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'Default Map Center Lat & Lng determine the initial position of the map.  You use the provided link to obtain coordinates of any location.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'Default Map Zoom Level can be adjusted to make sure all of your Meetings appear when the map first loads.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		
		return $contents;
	}
	
	public function misc_options() {
		$contents = '<p><strong>' . __( 'Keep Meetings Private', 'ninety-ninety' ) . "</strong></p>\n";
		$contents .= '<ul>';
		$contents .= '<li>' . __( 'Prevent non-logged-in users from seeing individual Meetings, Meeting archive pages and the Meeting Map.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		// Use exclude
		$contents .= '<p><strong>' . __( 'Use Exclude option...', 'ninety-ninety' ) . "</strong></p>\n";
		$contents .= '<ul>';
		$contents .= '<li>' . __( 'Each Meeting has an "Exclude" checkbox.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'If this box is checked, any Meetings whose Exclude checkboxes have been checked will be excluded from the PDF, count and Map.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		// Chart options
		$contents .= '<p><strong>' . __( 'Chart Options', 'ninety-ninety' ) . "</strong></p>\n";
		$contents .= '<ul>';
		$contents .= '<li>' . __( 'Whether or not to display the chart ( below the Map ) and which chart type to use.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'Both charts use colors to differentiate between completed and remaining Meetings.  You can choose those colors here.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		
		return $contents;
	}
	
	public function pdf_options() {
		
		$contents  = '<ul>';
		$contents .= '<li>' . __( 'Use PDF Title to customize, well, the title of the PDF.', 'ninety-ninety' ) . '</li>';
		$contents .= '<li>' . __( 'Choose whether or not to include the number of days between your first and last Meetings on the PDF.', 'ninety-ninety' ) . '</li>';
		$contents .= '</ul>';
		
		return $contents;
	}
	
	public function meeting_help() {
		
		$contents = '<p>';
		$contents .= __( 'This should all be pretty self-explanatory.', 'ninety-ninety' );
		$contents .= '</p>';
		$contents .= '<p>';
		$contents .= __( 'Do we really need a help tab here?', 'ninety-ninety' );
		$contents .= '</p>';
		
		return $contents;
		
	}
	
}

/**
 * Return single instance of class.
 *
 * @return ninetyHelpContents
 * @since 1.0.0
 *
 */
function ninety_help() {
	
	global $ninety_help;
	
	if ( ! $ninety_help ) {
		$ninety_help = new ninetyHelpContents();
	}
	
	return $ninety_help;
	
}
