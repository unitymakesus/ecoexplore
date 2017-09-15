=== Maps Builder Pro ===
Contributors: wordimpress, dlocc, webdevmattcrom
Donate link: https://wordimpress.com/
Tags: google maps, google map, google map widget, google map shortcode, maps, map, wp map, wp google maps, google maps directions, google maps builder, google maps plugin, google places, google places api, google maps api, google places reviews
Requires at least: 4.2
Tested up to: 4.8
Stable tag: 2.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

One Google Maps plugin to rule them all. Google Maps Builder is intuitive, sleek, powerful and easy to use. Forget the rest, use the best.

== Description ==

Google Maps Builder isn't just another Google Maps plugin. It's built from the ground up to be the easiest, most intuitive and fastest Google Maps plugin for WordPress. Visually build powerful customized Google Maps to use on your WordPress site quickly and easily without ever having to touch a bit of code.

= Plugin Highlights: =

* **Google Places API integration** - Display nearby business locations and points of interest complete with ratings, custom marker icon
* **Snazzy Maps integration** - Create truly unique Google Map themes that look great with any design powered by [Snazzy Maps](http://snazzymaps.com/).
* **Unique Marker Icons** - The only plugin with [Map Icons](map-icons.com) integration; set icon and marker colors for truly unique markers
* **Intuitive UI** that seamlessly integrates with WordPress' - no eye sores or outdated interfaces here
* **Small Footprint** - GMB does not create any new database tables, not even one
* **Optimized** - All scripts and styles are optimized and packaged with Grunt
* **No notices or warnings** We developed this plugins in debug mode. This results in high quality plugins with no errors, warnings or notices.

= Marker Creation =

Google Maps builder features a simple **"Point and Click" marker creation system**. As well, you can add markers using an intuitive Google autocomplete search field. As well, **Bulk edit marker data ** using meta fields attached to each marker's content.

= Map Themes =

Want to add some pazazz to your maps? [Snazzy Maps](http://snazzymaps.com/) themes are baked right in to Google Map Builder. This means your maps can stand out, fit into any design, and look unique and intriguing.

= Granular Map Control =

Fine tune your Google Maps with full control over settings for street view, zooming, panning, dragging, and more. Set defaults for each control so each new map you create is just the way you like it.

= Actively Developed and Supported =

This plugin is actively developed and supported. This means you can expect an answer in the forums and consistent improvements and enhancements to the plugin itself. As well, we won't shy away from bug fixes or code refactoring and optimization.

== Installation ==

This section describes how to install the plugin and get it working.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Google Maps Builder'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `google-maps-builder.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `google-maps-builder.zip`
2. Extract the `google-maps-builder` directory to your computer
3. Upload the `google-maps-builder` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= What sets this plugin apart from all the other Google Maps plugins for WordPress? =

There are a number features and functionality that set Google Maps Builder apart from the many WordPress Google Maps plugins. For starters, we promise this plugin will always have a light footprint. No extra tables or unnecessary overhead. Furthermore, the plugin is built from the ground up to be both easy and fun to use.

We have placed extra care and attention on the map creation process and are always looking to improve the UI with enhancements. It's our goal to integrate the plugin with the native WordPress admin UI without adding any distracting visuals. Finally, there are a number of additional features such as built in integration with Google Places API, Maps Icons and Snazzy Maps.

= Do I need a Google Places API Key to use this plugin? =

No. You do not need a Google Places API plugin to use this plugin.

= Does this plugin create any new database tables? =

Unlike many other Google Maps plugins, Google Maps Builder does not create a single new table in your WordPress database. There is no added database overhead or foreign MySQL queries. It's our guarantee that this plugin will never leave an orphaned table in your WordPress database.

= Where can I find the shortcodes for each map I create? =

You can find the shortcodes for each map on the post listing screen, within the post itself in the shortcode metabox (coming soon). Also coming soon: Map widget and TinyMCE button to include shortcode.

= What the heck is a shortcode and how do I use it? =

Google Maps Builder works by creating a plugin specific [WordPress shortcode](http://codex.wordpress.org/Shortcode). Basically, you can copy the shortcode for a specific map and enter in into a widget or directly within content. As well, you can use the WordPress [do_shortcode()](http://codex.wordpress.org/Function_Reference/do_shortcode) function to use it within your theme template files and even plugins.

= Does this plugin include a widget for displaying maps? =

Soon! For now, you can use the shortcode in the text widget. Soon there will be a Google Maps Builder Widget.

= How do I report a bug? =

We always welcome your feedback for improvements or if you have uncovered a bug. To report a bug please use the WordPress.org support forum.

= Who is behind this plugin? =

The main developer of this plugin is WordImpress. To find out more information about the company and the people behind it please visit [the WordImpress website.](http://wordimpress.com)

= Known Issues =

* Info Window - FOUC: Investigate why sometimes pointer tip of info window flashes before it opens (mainly Chrome)
* Chrome - Look into while map tiles have strange lines in between
* Firefox - Clicking on a marker to open the same info window creates content overflow
* Bug: Fix issue where selecting "None" for map controls doesn't actually work on frontend

== Screenshots ==

1. **Google Map Builder** - A view of the single map view in the WordPress admin panel. Notice the autocomplete search field and "Drop a Marker" button.

2. **Editable Marker** - Customize the content of the map markers directly in the builder. Built to mock Google's own Maps Engine.

3. **Custom Markers** - Configure a marker to fit your location. Easily adjust the marker, icon and color.

4. **Frontend View** - A view the a map on the frontend of a WordPress site using the TwentyTwelve theme. This map displays various Google Places.

5. **Settings Panel** - Adjust the various plugin settings using a UI that is built using WordPress' own styles.

== Changelog ==

= 2.1.2 =
* Fix: Provide compatibility with IE11 because the browser does not support Maps Builders current usage of CustomEvent in JS - https://github.com/WordImpress/maps-builder-core/issues/47
* Fix: Map permalinks no longer require manual refresh after installation - https://github.com/WordImpress/Google-Maps-Builder/issues/240
* Fix: Correct default slug from google_maps to google-maps
* Fix: Rename constructor to prevent PHP 7 notice - https://github.com/WordImpress/Google-Maps-Builder/issues/242
* Fix: Prevent themes from affecting width of close button - https://github.com/WordImpress/Google-Maps-Builder/issues/250
* Improvement: Refactor and improve performance for hidden maps upon reveal - https://github.com/WordImpress/Google-Maps-Builder/issues/251
* Improvement: Support popular tab solutions including Tabby, Elementor, Divi, Bootsrap, Beaver Builder, and Visual Composer
* Improvement: Add new PHP filters and JS triggers for developers - https://github.com/WordImpress/Google-Maps-Builder/issues/249
* Improvement: Use transients if available to load mash-up markers - https://github.com/WordImpress/Google-Maps-Builder/issues/251
* General: Add featured image support to map posts - https://github.com/WordImpress/Google-Maps-Builder/issues/123
* Deprecate: Google Maps has removed support for signed-in functionality - https://github.com/WordImpress/Google-Maps-Builder/issues/231

= 2.1.1 =
* Fix: Conflict with the Give donation plugin using the same function name throwing a fatal error upon activation. Fixed with custom prefix.

= 2.1 =
* Enhancement: Support basic HTML in InfoBubbles - https://github.com/WordImpress/Google-Maps-Builder/issues/218
* Enhancement: Vastly improved InfoBubble CSS and sizing - https://github.com/WordImpress/Google-Maps-Builder/issues/214
* Enhancement: Wider selection of Snazzy Maps added - https://github.com/WordImpress/Google-Maps-Builder/issues/20
* Fix: Add "Get Directions" link to Places in InfoBubble on Frontend - https://github.com/WordImpress/Google-Maps-Builder/issues/103
* Fix: Show thumbnail properly in backend InfoBubbles - https://github.com/WordImpress/Google-Maps-Builder/issues/213
* Fix: Google Places API error outputs badly - https://github.com/WordImpress/Google-Maps-Builder/issues/176
* Fix: Properly support new requirements for Google Places API - https://github.com/WordImpress/Google-Maps-Builder/issues/174
* Fix: Featured image displays in InfoBubble even when disabled - https://github.com/WordImpress/Google-Maps-Builder/issues/165
* Fix: Save Button doesn't appear when editing a Marker that was already edited once - https://github.com/WordImpress/Google-Maps-Builder/issues/210
* Fix: "Save Changes" tool tip stays on screen after saving changes - https://github.com/WordImpress/Google-Maps-Builder/issues/205
* Fix: Map Type Control doesn't affect map properly - https://github.com/WordImpress/Google-Maps-Builder/issues/162
* Fix: The "Get Directions" link wasn't displaying properly in InfoBubbles - https://github.com/WordImpress/Google-Maps-Builder/issues/103
* Tweak: Settings screen CSS made tabs shift in bad ways - https://github.com/WordImpress/Google-Maps-Builder/issues/171
* Tweak: Changed default geocoding option and settings now that Google requires SSL to enable - https://github.com/WordImpress/Google-Maps-Builder/issues/164
* Tweak: Google Marker images were moved. Updated to new location - https://github.com/WordImpress/Google-Maps-Builder/issues/175
* Tweak: Removed sensor parameter to prevent console warning https://github.com/WordImpress/Maps-Builder-Pro/issues/19
* Tweak: Updated Google logo to the newest version - https://github.com/WordImpress/Maps-Builder-Pro/issues/209

= 2.0.3 =
* New: Option to centered maps on marker when the user clicks on it - https://github.com/WordImpress/Maps-Builder-Pro/issues/17
* Fix: Standardized spelling for loading image file extension names; the extensions were uppercase "GIF" causing 404s for some hosts
* Fix: When the "Map Control Type" was set to none the controls would still appear on the map - https://github.com/WordImpress/Maps-Builder-Pro/issues/79
* Fix: Cluster markers changed from Google to Github so the URLs for the images are now updated - https://github.com/WordImpress/Maps-Builder-Pro/issues/84
* Fix: Better way to include CMB2 so there's fewer conflicts with other plugins that are using the same library
* Tweak: Removed 'sensor' arguments to prevent Google's console "Google Maps API warning sensor is not required"

= 2.0.2 =
* New: Introduced the InfoBubble library for better handling of frontend marker info windows
* New: Added link to individual post within a mashup marker's infowindow content
* Fix: Mashups now allow you to load all posts from a CPT without having to select a taxonomy - thanks @thatryan
* Fix: Revamped how Google Maps API scripts are loaded to better prevent conflicts with other themes and plugins on both the admin and frontend

= 2.0.1 =
* Fix: Taxonomy terms checkbox selections now properly loaded via AJAX for mashups filtering @see: https://wordimpress.com/support/topic/mashup-taxonomy-filters-not-working/
* Fix: Licensing system updated to work with Pro release and bypass WordPress.org updates completely
* Fix: Ensure mashup markers have latitude and longitude before creating frontend markers - thanks @RachelC
* Fix: Add ability for mashup markers to be clustered - thanks @RachelC

= 2.0 =
* General: This update focused on fixing a lot of pre-existing bugs commonly submitted to WordPress.org as well as improving the plugin base for future code enhancements.
* New: Enhanced Full Screen Maps Builder mode that allows you to build maps in a customizer-like experience.
* New: Widget for inserting maps into your theme's sidebars #39 @see: https://github.com/WordImpress/google-maps-builder/issues/39
* New: Shortcode builder integrated into TinyMCE to make adding maps to your posts a breeze. @see: https://github.com/WordImpress/google-maps-builder/issues/24
* New: Upgrade process for maps using Google's old reference ID in place for the new Place_ID @see: https://github.com/WordImpress/google-maps-builder/issues/18
* New: Switch and Test all Google Places API calls to "Reference ID" or "ID" to Google's new "Places ID"
* New: Gulp implemented for minifying scripts
* Improvement: Upgraded CMB1 to CMB2
* Improvement: Class improvements and modernized structure organization
* Improvement: Plugin structure significantly changed to better reflect our development preference. The current structure is similar to Give, EDD, and other reputable plugins.
* Improvement: Swapped out Thickbox for Magnific popup @see: https://github.com/WordImpress/google-maps-builder/issues/11
* Improvement: Register scripts and styles properly prior to enqueuing them for other plugins and themes
* Improvement: Marker Creation Improvements - 1) Sometimes markers disappear in the post edit screen. 2) Sometimes markers don't get output on the front end correctly. 3)Sometimes markers don't get generated in the post edit screen at all.
* Fix: Investigated + resolved several Google Maps API conflicts - Often when a user has a theme or plugin that registers Google Maps it breaks our plugin or ours breaks theirs. We now check for other Google Map enqueues, and if present the plugin attempts to dequeue them in favor for ours. So far this has fixed issues with Uber Menu 3, Contact Forms 7, as well as many additional plugins.
* Fix: Maps placed in hidden tabs now redraw properly when the tab is selected
* Fix: Found and resolved conflict with ACF plugin Google Maps field
* Fix: Removed non-functional marker upload field (will be added to Pro version in a much enhanced format).
* Fix: Maps icons fixed to no longer show first character incorrectly @see: https://github.com/scottdejonge/Map-Icons/issues/26
* General: Javascript cleanup and optimization

= 1.0.3 =
* New: New check for multiple Google Maps API calls to ensure more compatibility with themes and plugins which include the same maps API JS. If the check detects multiple enqueues a warning appears in the admin panel.
* Additional Testing: Reviewed WooCommerce and Contact Forms 7 compatibility within WP admin panel
* Fix: Updated a number of field descriptions to be more clear
* Fix: Updated readme to be more accurately reflect past development on plugin
* Removed snazzy.php file since we are using the json file exclusively now

= 1.0.2 =
* Remove Maps Shortcode field from non-Google Maps post types. ie Posts and Pages (thanks [@kalenjohnson](https://github.com/WordImpress/google-maps-builder/pull/1) )
* Fix: Default Menu position conflict with other plugins like WooCommerce and Contact Forms 7
* Readme.txt - New FAQs, Roadmap content and several formatting and typo fixes
* Fixed: Bug with Map shortcode field displaying on all single post types Publish metabox rather than just on the maps post type
* Improved: Moved snazzy JSON data from php file to .json file for more reliable usage across environments; some servers seem to deny any access to php files using wp_remote_fopen()

= 1.0.1 =
* New: Added a custom meta field to the Google Map single post screen that outputs the post's shortcode so it's more easily accessible. Before you could only access the shortcode via the Google Maps post listing page.
* Updated readme.txt file with more information about the plugin, fixed several formatting errors and typos.
* Fixed: Activation error "PHP Strict Standards:  call_user_func_array() expects parameter 1 to be a valid callback, non-static method Google_Maps_Builder::activate() should not be called statically in ..." - Thanks Jon Brown!

= 1.0.0 =
* Plugin released. Yay!
