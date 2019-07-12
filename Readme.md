# 90 Meetings in 90 Days
## Requirements
* WordPress 5.2
* PHP 5.6.2

## Setup
#### Meetings
1. Add Meeting Locations ( Meetings > Meeting Locations )
1. Add Meeting Types ( Meetings > Meeting Types )


#### Options ( Meetings > Options ). 
There are currently 2 available mapping APIs for use.  Both have free usage tiers.
1. Enter your [MapBox API key](https://docs.mapbox.com/help/how-mapbox-works/access-tokens/). This is necessary for geocoding
Location addresses and displaying the map.
1. Enter your [Thunderforest API key](https://www.thunderforest.com/pricing/) if you wish to use any of those Tile Sets. 
1. Select a Tile Set to display on the map ( there must be a valid API key associated with the selected tile set ).

#### Optional
* Set your preferred timezone in the WordPress admin ( for the Default Meeting Time option )

## ACF Notes
* Requires Advanced Custom Fields plugin, standard or Pro.
* Loads ACF standard version if no ACF plugin is active.
* ACF standard version included: 5.8.1
* ACF fields are used for Meetings.
* Options page is not built with ACF since that requires Pro version.

## Options
* Open

## Actions & Filters
* `ninety-programs` customize the available Meeting Programs to choose from - AA, NA, GA, OA...
* `ninety-page-templates` not sure if this should be filterable...
* `ninety_meeting_genesis_meta` customize Genesis entry meta
* `ninety_map_page_title` filter title on the Meetings Map page

## Misc.
* Location addresses are geocoded upon saving.  Lat/Lng is displayed but not editable.  Address is 
updated with results from API call.
* Meeting Location name, description and address are displayed using information entered when 
creating/updating Locations.  The displayed Meeting count is a dynamic value. 
