=== Ninety in Ninety ===
Contributors: machouinard
Donate link: https://example.com
Tags: AA, NA, Recovery
Requires at least: 5.2
Tested up to: 5.2.2
Stable tag: 1.0.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.

== Description ==

Often referred to as “90 in 90”, the practice of attending an AA meeting every day for 90 days in a row is a common suggestion for a newcomer.  This is a way to track those meetings.



Keep track of each meeting:

* Location/Group
* Meeting Type
* Date/Time
* Speaker
* Topics

Also:

* Display all meetings on a map
* Display progress as a chart; pie, doughnut, bar.
* Display meetings by Location/Group
* Search meetings by your meeting notes or any other detail
* Meeting calendar widget
* Meeting archive widget

== Plugin Options ==

1. Meeting Options: defaults used when creating new meeting posts.
    * **Location** (you'll need to set up at least one location first)
    * **Time** (save valuable seconds by specifying a default meeting time)
    * **Type** (save even more time with a default meeting type)
1. Map Options: used for displaying meetings on a map.
	* [**MapBox API Key**](https://account.mapbox.com/auth/signup/): For geocoding location addresses to display on map.  Also for dislaying certain map tile sets.
	* [**Thunderforest API Key**](https://manage.thunderforest.com/): For displaying map using additional tile sets.
	* **Default Map Center Latitude**
	* **Default Map Center Longitude**
	* **Default Map Zoom Level**
1. Misc. Options
	* **Keep Meetings Private**: only show meetings/maps/widgets to logged in users
	* **Display Chart**: Default setting. Customizable in shortcode.
	* **Chart Type**: Default chart type. Customizable in shortcode.
	* **Completed Meetings Color**: Chart color used for completed meetings.
	* **Remaining Meetings Color**: Chart color used for remaining meetings.
	* **Remove data when deleting plugin**: Option to remove meetings and associated details from the database.
1. PDF Options
	* **Create PDF**: Whether or not to create a PDF listing of your meetings.
	* **PDF Title**
	* **Show number of days**: Whether or not to show the number of days next to the meeting count.
	* **Start Date / End Date for PDF**: Optionally specify a date range of meetings to include

== Installation ==

1. Upload "ninety-ninety" folder to the "/wp-content/plugins/" directory.
1. Activate the plugin ( 90 in 90 ) through the "Plugins" menu in WordPress.
1. [Sign up for free](https://www.mapbox.com/pricing/) at [MapBox ](https://account.mapbox.com/auth/signup/) to obtain an API key for geolocating meeting addresses.
1. Use **Meetings -> Options** screen to set up plugin options
1. Start tracking your meetings.

== Development ==
* Development is on [GitHub].(https://github.com/machouinard/ninety-ninety)
* Support issues will be addressed on [GitHub](https://github.com/machouinard/ninety-ninety/issues)

== Frequently Asked Questions ==

= I found a problem =
Support issues will be addressed on [GitHub](https://github.com/machouinard/ninety-ninety/issues)

= Source Code? =
Development is on [GitHub].(https://github.com/machouinard/ninety-ninety)



== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. This is the second screen shot

== Changelog ==


== Upgrade Notice ==

