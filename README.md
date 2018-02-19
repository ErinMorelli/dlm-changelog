# DLM Changelog Add-on #

*by Erin Morelli*

An add-on for Mike Jolley's [Download Monitor](http://wordpress.org/plugins/download-monitor/) which adds version changelog functionality.

**Requires Download Monitor version 1.2 or higher**


## Shortcode ##

Use this shortcode to display a DLM Download's changelog in your posts or pages:

`[dlm_changelog id={DLM Download ID}]`

To **paginate** the changelog's output, use the optional `show` attribute with the number of versions you want displayed on each page:

`[dlm_changelog id={DLM Download ID} show="5"]`

To **hide** download links or release dates (or both), use the optional `hide_links` and/or `hide_release` attributes, respectively:

`[dlm_changelog id={DLM Download ID} hide_links="1" hide_release="1"]`


### Support ###

Use the community support forums for this plugin for questions that are specific to the Changelog Add-on. For support questions specific to other aspects of the Download Monitor plugin, please visit it's [support forum](http://wordpress.org/support/plugin/download-monitor) or log a bug on the [DLM GitHub](https://github.com/mikejolley/download-monitor).


### Screenshots ###

![The DLM Changelog admin area](https://bitbucket.org/repo/MGqdyg/images/1845611380-screenshot-1.jpg)



*****


## Latest Release ##


### [Version 1.2.1 - Critical Bug Fix](https://bitbucket.org/ErinMorelli/dlm-changelog/downloads/dlm-changelog.1.2.1.zip) ###
* Overrides and fixes an issue introduced DLM where saving a download will remove all existing saved changelog content for that download's versions



*****


### Installation ###

1. Unzip the `dlm-changelog.zip` file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the new "Changelog" options page located in the DLM "Downloads" section