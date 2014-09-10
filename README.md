# DLM Changelog Add-on #

*by Erin Morelli*

An add-on for Mike Jolley's [Download Monitor](http://wordpress.org/plugins/download-monitor/) which adds version changelog functionality.

**Requires Download Monitor verson 1.2 or higher**


## Shortcode ##

Use this shortcode to display a DLM Download's changelog in your posts or pages:

`[dlm_changelog id={DLM Download ID}]`

To **paginate** the changelog's output, use the optional `show` attribute with the number of versions you want displayed on each page:

`[dlm_changelog id={DLM Download ID} show="5"]`


### Support ###

Use the community support forums for this plugin for questions that are specific to the Changelog Add-on. For support questions specific to other aspects of the Download Monitor plugin, please visit it's [support forum](http://wordpress.org/support/plugin/download-monitor) or log a bug on the [DLM GitHub](https://github.com/mikejolley/download-monitor).


### Screenshots ###

1. [The DLM Changelog admin area](https://raw.github.com/ErinMorelli/dlm-changelog/master/screenshot-1.jpg)



*****


## Latest Release ##


### [Version 0.1.1 - Minor Bug Fix](https://bitbucket.org/ErinMorelli/dlm-changelog/get/v0.1.1.zip) ###
* Fixed jQuery pagination issue with shortcode output



*****


### Installation ###

1. Unzip the `dlm-changelog.zip` file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the new "Changelog" options page located in the DLM "Downloads" section