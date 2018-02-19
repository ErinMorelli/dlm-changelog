=== DLM Changelog Add-on ===
Contributors: ErinMorelli
Donate link: https://www.erinmorelli.com/projects/dlm-changelog/
Tags: download monitor, changelog, downloads, versions
Requires at least: 3.0.1
Tested up to: 4.9.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An add-on for Mike Jolley's Download Monitor which adds version changelog functionality.

== Description ==

An add-on for Mike Jolley's [Download Monitor](http://wordpress.org/plugins/download-monitor/) which adds version changelog functionality.

**Requires Download Monitor version 1.2 or higher**


= Shortcode =

Use this shortcode to display a DLM Download's changelog in your posts or pages:

`[dlm_changelog id={DLM Download ID}]`

To **paginate** the changelog's output, use the optional `show` attribute with the number of versions you want displayed on each page:

`[dlm_changelog id={DLM Download ID} show="5"]`

To **hide** download links or release dates (or both), use the optional `hide_links` and/or `hide_release` attributes, respectively:

`[dlm_changelog id={DLM Download ID} hide_links="1" hide_release="1"]`


= Support =

Use the community support forums for this plugin for questions that are specific to the Changelog Add-on. For support questions specific to other aspects of the Download Monitor plugin, please visit it's [support forum](http://wordpress.org/support/plugin/download-monitor) or log a bug on the [DLM GitHub](https://github.com/mikejolley/download-monitor).



== Installation ==

1. Unzip the `dlm-changelog.zip` file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the new "Changelog" options page located in the DLM "Downloads" section


== Frequently Asked Questions ==


= How do I add changelog version notes? =

Go to "Downloads" then "Changelogs" in the WordPress admin. Select the download you wish to add/edit the changelog for from the dropdown. Under the "Notes" column of the download's Changelog table, click inside the area where your pre-existing notes are or where it says "Click to add notes". An inline text editor will appear for you to use. Click Save" to save your note. Basic HTML, such as *italics*, **bold**, underline, and ordered/unordered lists is able to be used in this text area.


= How do I display a changelog on my site? =

Use the `[dlm_changelog id={DLM Download ID}]` shortcode inside the WordPress page editor to display your changelog.


= How do I disable pagination? =

Set the `[dlm_changelog]` show attribute to 0, or don't set it at all:

`[dlm_changelog id={DLM Download ID}]`

OR

`[dlm_changelog id={DLM Download ID} show="0"]`


= How can I hide the release dates? =

Use the `hide_release` option in your shortcode like this:

`[dlm_changelog id={DLM Download ID} hide_release="1"]`


= How can I hide the download links? =

Use the `hide_links` option in your shortcode like this:

`[dlm_changelog id={DLM Download ID} hide_links="1"]`


== Screenshots ==

1. The DLM Changelog admin area


== Changelog ==

= 1.2.1 =
* Overrides and fixes an issue introduced DLM where saving a download will remove all existing saved changelog content for that download's versions

= 1.2.0 = 
* Fixed incompatibility issues with DLM versions 4.0+
* Added two new shortcode options, `hide_links` and `hide_release`

= 1.1.1 =
* Changed admin permissions to match Download Monitor's (many thanks to Craig Morin!)

= 1.1.0 =
* Added simple success/error notifications for save events

= 1.0.1 =
* Fixed issue where not all published downloads were showing in admin dropdown

= 1.0.0 =
* Added TinyMCE inline WYSIWYG editor to changelog admin
* Lots of under-the-hood code improvements and cleanup

= 0.1.2 =
* Fixed issue with plugin breaking on Download Monitor upgrade

= 0.1.1 =
* Fixed jQuery pagination issue with shortcode output

= 0.1.0 =
* Initial plugin release



== Upgrade Notice ==

= 1.2.1 =
* CRITICAL UPDATE to fix a bug conflict with DLM that caused all changelog data to be deleted when updating a download via DLM

= 1.0.1 =
* Fixed issue where not all published downloads were showing in admin dropdown

= 1.0.0 =
Added WYSIWYG editor to changelog admin

= 0.1.2 =
This version fixes issue with plugin breaking due to a Download Monitor upgrade.  Upgrade immediately.

= 0.1.0 =
Initial plugin release
