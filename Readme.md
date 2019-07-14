# 90 Meetings in 90 Days

Please post any issues you encounter [on GitHub](https://github.com/machouinard/ninety-ninety/issues)

## What is this?
Often referred to as “90 in 90”, the practice of attending a meeting of Alcoholics Anonymous every day for 90 days in a row is a common suggestion for a newcomer to AA.

This plugin is a simple way to track your progress making it to these meetings.

It's flexible enough to be used for any 12 Step program.

...



### Requirements
* WordPress 5.2
* PHP 5.6.2

# Setup
### Meetings
1. Add Meeting Locations ( Meetings > Meeting Locations )
1. Add additional Meeting Types if desired ( Meetings > Meeting Types )



### Options ( Meetings > Options ). 

#### Map Options
There are currently 2 available mapping APIs for use.  Both have free usage tiers.
1. Enter your [MapBox API key](https://docs.mapbox.com/help/how-mapbox-works/access-tokens/). This is required for geocoding
Location addresses and displaying certain tile sets.
1. Enter your [Thunderforest API key](https://www.thunderforest.com/pricing/) if you wish to use any of the Thunderforest tile sets. 
1. Select a Tile Set to display on the map ( there must be a valid API key associated with the selected tile set ).

1. (Optional) Set your preferred timezone in the WordPress admin ( for the Default Meeting Time option to work correctly )


### Miscellaneous Options
* `Keep Meetings Private` checkbox - Prevents non-logged-in users from viewing Meetings, archive pages, Maps, etc...  Also prevents
Meeting related menu items from appearing when not logged in.
* `Use Exclude option (PDF, Maps, Count, etc...` Each Meeting has an "exclude" checkbox.  If this box is checked, any
Meetings that have their "exclude" checkboxes checked will not be shown on the PDF or the Map page.

## ACF Notes
* Requires Advanced Custom Fields plugin, standard or Pro.
* Loads ACF standard version if no ACF plugin is active.
* ACF standard version included: 5.8.1
* ACF fields are used for Meetings.
* Options page is not built with ACF since that requires Pro version.



## Actions & Filters
* `ninety_programs` customize the available Meeting Programs to choose from - AA, NA, GA, OA...
* `ninety_page_templates` not sure if this should be filterable...
* `ninety_meeting_genesis_meta` customize Genesis entry meta for Genesis child themes.
* `ninety_map_page_title` filter title on the Meetings Map page.

## Misc.
* Location addresses are geocoded upon saving.  Lat/Lng is displayed but not editable.  Address is 
replaced with results from API call.
* Meeting Location name, description and address are displayed on the map using information entered when 
creating/updating Locations.  The displayed Meeting count is a dynamic value. 

## Development
* This plugin can be [forked on GitHub](https://github.com/machouinard/ninety-ninety)
* After downloading, run `npm install` from within the directory
* JS development files are in the `src/` directory and are compiled into the `assets/js/` directory by WebPack
* While developing, run `npm run watch` from the plugin directory.  Changes will be automatically copied to the `assets/js` directory.
* Running `npm run build` will minify the JavaScript, resulting in a much smaller file.
* ...
