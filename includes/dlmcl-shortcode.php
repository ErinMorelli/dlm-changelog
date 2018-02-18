<?php
/**
 * Copyright (c) 2013-2018, Erin Morelli.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package DLMCL\Shortcode
 */


/**
 * Loads the [dlm_changelog] changelog display shortcode
 *
 * @param array $atts List of supported shortcode attributes
 *
 * @return string/html
 */
function DLMCL_shortcode($atts)
{
    extract(
        shortcode_atts(
            array(
                'id'           => '0',  // download id
                'show'         => '0',  // number of downloads to show
                'hide_links'   => '0',  // whether or not to show download link
                'hide_release' => '0'   // whether or not to show release date
            ),
            $atts,
            'dlm_changelog'
        )
    );

    return DLMCL_Shortcode_display($id, floatval($show), (bool) $hide_links, (bool) $hide_release);
}

// Load shortcode
add_shortcode('dlm_changelog', 'DLMCL_shortcode');


/**
 * Displays changelog shortcode
 *
 * @param int  $id           DLM download ID
 * @param int  $show         Number of versions to display for pagination
 * @param bool $hide_links   Whether or not to show download links
 * @param bool $hide_release Whether or not to show release dates
 *
 * @return string/HTML
 */
function DLMCL_Shortcode_display($id, $show=5, $hide_links=false, $hide_release=false)
{
    return DLMCL_Shortcode_output(
        array(
            'id'           => $id,
            'show'         => $show,
            'hide_links'   => $hide_links,
            'hide_release' => $hide_release
        )
    );
}


/**
 * Loads the changelog shortcode HTML
 *
 * @param array $attrs Display attributes
 *
 * @return string/HTML
 */
function DLMCL_Shortcode_output($attrs)
{
    // Load DLMCL shortcode javascript
    wp_enqueue_script('dlmcl-load-posts', DLMCL_PLUGIN_URL .'assets/js/shortcode.js', array('jquery'), '1.0', true);

    // Set attributes
    $id = $attrs['id'];
    $show = $attrs['show'];
    $hide_links = $attrs['hide_links'];
    $hide_release = $attrs['hide_release'];

    // Initialize output string
    $output = '';

    // Setup DLM object
    if (DLMCL_USE_LEGACY) {
        $download = new DLM_Download($id);
    } else {
        $download = download_monitor()->service('download_repository')->retrieve_single($id);
    }

    if ($download->exists()) {
        // Get all versions of download
        $downloads = DLMCL_USE_LEGACY ? $download->get_file_versions() : $download->get_versions();

        if ($show != 0) {
            // Set pagination data
            $version_count = count($downloads);
            $max_pages = ceil($version_count / $show);
            $pages = array_chunk($downloads, $show);
            if (isset($_GET['more'])) {
                $pgkey = (int)$_GET['more'];
            } else {
                $pgkey = 0;
            }
            $next_page = '?more='.($pgkey+1);

            // Load pagination
            wp_localize_script(
                'dlmcl-load-posts',
                'dlmcl_load',
                array(
                    'startPage' => $pgkey,
                    'maxPages'  => $max_pages,
                    'nextLink'  => $next_page,
                    'moreText'  => __('Show More', 'dlm-changelog'),
                    'loadText'  => __('Loading More', 'dlm-changelog')
                )
            );

            // Output version list
            $output .= '<div class="dlm-changelog-list">'."\n";
            $output .= DLMCL_Shortcode_Display_versions($pages[$pgkey], $hide_links, $hide_release);
            $output .= '<noscript><p class="dlm-changelog-load-posts"><a href="'.$next_page.'" class="dlmcl-button">'.__('Show More', 'dlm-changelog').'</a></p></noscript>'."\n";
            $output .= '</div>'."\n";
        } else {
            // Output version list
            $output .= '<div class="dlm-changelog-list">'."\n";
            $output .= DLMCL_Shortcode_Display_versions($downloads, $hide_links, $hide_release);
            $output .= '</div>'."\n";
        }
    }

    // Return HTML output
    return $output;
}


/**
 * Outputs DLM versions in changelog shortcode
 *
 * @param array $versions     List of DLM version objects
 * @param bool  $hide_links   Whether or not to show download links
 * @param bool  $hide_release Whether or not to show release dates
 *
 * @return string/HTML
 */
function DLMCL_Shortcode_Display_versions($versions, $hide_links=false, $hide_release=false)
{
    // Initialize output string
    $output = '';

    // Iterate over DLM versions
    foreach ($versions as $version) {
        $version_id = DLMCL_USE_LEGACY ? $version->id : $version->get_id();
        $version_filename = DLMCL_USE_LEGACY ? $version->filename : $version->get_filename();
        $version_version = DLMCL_USE_LEGACY ? $version->version : $version->get_version();
        $version_url = DLMCL_USE_LEGACY ? $version->url : $version->get_url();

        // Get post data for version
        $release = get_post($version_id);

        // Start version output
        $output .= '<div class="dlm-changelog-item" id="dlm-changelog-item-'.$version_id.'">'."\n";

        // Version Title
        $output .= '<h3 class="dlm-changelog-item-title">';
        $output .= $version_version;
        $output .= '</h3>'."\n";

        // Version meta
        if (!$hide_release || !$hide_links) {
            $output .= '<p class="dlm-changelog-item-meta">';
        }

        // Download Link
        if (!$hide_links) {
            $output .= '<span class="download-link"><a href="';
            $output .= $version_url;
            $output .= '" target="_blank">'.__('Download', 'dlm-changelog');
            $output .= '</a></span>';

            // Add emdash, if needed
            if (!$hide_release) {
                $output .= ' &ndash; ';
            }
        }

        // Release Date
        if (!$hide_release) {
            $output .= '<span class="released-date">';
            $output .= __('Released', 'dlm-changelog').' ';
            $output .= date('m/d/y', strtotime($release->post_date));
            $output .= '</span>';
        }

        // End Version meta
        if (!$hide_release || !$hide_links) {
            $output .= '</p>'."\n";
        }

        // Release Notes
        $output .= $release->post_content ."\n";

        // End version output
        $output .= '</div>'."\n";
    }

    // Return HTML output
    return $output;
}
