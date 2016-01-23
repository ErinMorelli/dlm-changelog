<?php
/**
 * Copyright (c) 2013-2016, Erin Morelli.
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
                'id'    => '0', // download id
                'show'  => '0'  // number of downloads to show
            ),
            $atts,
            'dlm_changelog'
        )
    );

    return DLMCL_Shortcode_display($id, floatval($show));
}

// Load shortcode
add_shortcode('dlm_changelog', 'DLMCL_shortcode');


/**
 * Displays changelog shortcode
 *
 * @param int $id   DLM download ID
 * @param int $show Number of versions to display for pagination
 *
 * @return string/HTML
 */
function DLMCL_Shortcode_display($id, $show=5)
{
    return DLMCL_Shortcode_output(
        array(
            'id'    => $id,
            'show'  => $show
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

    // Initialize output string
    $output = '';

    // Setup DLM object
    $download = new DLM_Download($id);

    if ($download->exists()) {
        // Get all versions of download
        $downloads = $download->get_file_versions();

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
            $output .= DLMCL_Shortcode_Display_versions($pages[$pgkey]);
            $output .= '<noscript><p class="dlm-changelog-load-posts"><a href="'.$next_page.'" class="dlmcl-button">'.__('Show More', 'dlm-changelog').'</a></p></noscript>'."\n";
            $output .= '</div>'."\n";
        } else {
            // Output version list
            $output .= '<div class="dlm-changelog-list">'."\n";
            $output .= DLMCL_Shortcode_Display_versions($downloads);
            $output .= '</div>'."\n";
        }
    }

    // Return HTML output
    return $output;
}


/**
 * Outputs DLM versions in changelog shortcode
 *
 * @param array $versions List of DLM version objects
 *
 * @return string/HTML
 */
function DLMCL_Shortcode_Display_versions($versions)
{
    // Initialize output string
    $output = '';

    // Iterate over DLM versions
    foreach ($versions as $version) {
        // Start version output
        $output .= '<div class="dlm-changelog-item" id="dlm-changelog-item-'.$version->id.'">'."\n";

        // Version Title
        $output .= '<h3 class="dlm-changelog-item-title">';
        $output .= $version->version;
        $output .= '</h3>'."\n";

        // Download Link
        $output .= '<p class="cl-dl"><a href="';
        $output .= $version->url;
        $output .= '" target="_blank">'.__('Download', 'dlm-changelog').'</a>';

        // Release Date
        $release = get_post($version->id);

        $output .= ' &ndash; Released ';
        $output .=  date('m/d/y', strtotime($release->post_date));
        $output .= '</p>'."\n";

        // Release Notes
        $output .= $release->post_content ."\n";

        // End version output
        $output .= '</div>'."\n";
    }

    // Return HTML output
    return $output;
}
