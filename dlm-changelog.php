<?php
/**
 * Plugin Name: DLM Changelog Add-on
 * Plugin URI: https://www.erinmorelli.com/projects/dlm-changelog/
 * Description: An add-on for Mike Jolley's Download Monitor that adds version changelog functionality.
 * Version: 1.2.1
 * Author: Erin Morelli
 * Author URI: https://erinmorelli.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dlm-changelog
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013-2021, Erin Morelli.
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
 * @package DLMCL\Plugin
 */


// Define plugin file paths
define('DLMCL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DLMCL_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('PLUGINDIR')) {
    define('PLUGINDIR', 'wp-content/plugins');
}

// Set post meta name
define('DLMCL_POST_CONTENT', '_dlmcl_post_content');


/**
 * Load DLM Changelog plugin files
 *
 * @return void
 */
function DLMCL_Plugin_load()
{
    // Check for new version
    $dlmcl_curr_version = '1.2.1';

    // Define new version option
    if (!defined('DLMCL_VERSION_KEY')) {
        define('DLMCL_VERSION_KEY', 'dlmcl_version');
    }

    // Add current version value
    if (!defined('DLMCL_VERSION_NUM')) {

        define('DLMCL_VERSION_NUM', $dlmcl_curr_version);
        add_option(DLMCL_VERSION_KEY, DLMCL_VERSION_NUM);
    }

    // Update the version value
    if (get_option(DLMCL_VERSION_KEY) != $dlmcl_curr_version) {
        update_option(DLMCL_VERSION_KEY, $dlmcl_curr_version);
    }

    // Load plugins function
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Set DLM plugin path
    define('DLM_PLUGIN_DIR', ABSPATH . PLUGINDIR . '/download-monitor/');
    $dlm_path = DLM_PLUGIN_DIR . 'download-monitor.php';

    // Set plugin name
    $dlm_name = 'download-monitor/download-monitor.php';

    // Check to see if DLM is installed
    $dlm_installed = array_key_exists($dlm_name, get_plugins());

    // Check to see if DLM is active
    $dlm_active = in_array($dlm_name, get_option('active_plugins'));

    // Detect if DLM is installed & load DLMCL files
    if ($dlm_installed && $dlm_active) {
        // Retrieve current DLM plugin version
        $dlm_info = get_plugin_data($dlm_path, false);
        $dlm_version = floatval($dlm_info['Version']);

        // Check against this version
        $check_version = 1.2;
        $legacy_version = 4.0;

        // Check for legacy version
        define('DLMCL_USE_LEGACY', ($dlm_version < $legacy_version));

        // If correct version, include DLMCL files
        if ($dlm_version >= $check_version) {

            // Load admin files only in admin
            if (is_admin()) {
                include_once DLMCL_PLUGIN_DIR . 'includes/dlmcl-admin.php';
            }

            // Load shortcode
            include_once DLMCL_PLUGIN_DIR . 'includes/dlmcl-shortcode.php';

            // Load plugin styles
            add_action('wp_enqueue_scripts', 'DLMCL_Plugin_styles');
        } else {
            // Display incorrect version error
            add_action('admin_notices', 'DLMCL_Plugin_Error_upgrade');

            // Deactivate plugin on error
            add_action('admin_init', 'DLMCL_Plugin_Error_deactivate');
        }
    } elseif ($dlm_installed && !$dlm_active) {
        // Display incorrect version error
        add_action('admin_notices', 'DLMCL_Plugin_Error_inactive');

        // Deactivate plugin on error
        add_action('admin_init', 'DLMCL_Plugin_Error_deactivate');
    } else {
        // Display DLM required error
        add_action('admin_notices', 'DLMCL_Plugin_Error_required');

        // Deactivate plugin on error
        add_action('admin_init', 'DLMCL_Plugin_Error_deactivate');
    }

    // Plugin localization
    load_plugin_textdomain('dlm-changelog', false, plugin_basename(dirname(__FILE__)).'/languages');
}

// Initial plugin load
add_action('plugins_loaded', 'DLMCL_Plugin_load', 10);


/**
 * Displays DLM upgrade notification
 *
 * @return void
 */
function DLMCL_Plugin_Error_upgrade()
{
    echo '<div class="updated"><p>';
    printf(
        __('%s works with %s version 1.2.0 and higher. Please upgrade to the latest version.', 'dlm-changelog'),
        sprintf(
            '<strong>%s</strong>',
            __('DLM Changelog', 'dlm-changelog')
        ),
        sprintf(
            '<strong>%s</strong>',
            __('Download Monitor', 'dlm-changelog')
        )
    );
    echo '</p></div>';

    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }
}


/**
 * Displays DLM inactive notification
 *
 * @return void
 */
function DLMCL_Plugin_Error_inactive()
{
    echo '<div class="error"><p>';
    printf(
        __('%s requires %s to be activated in order to work.', 'dlm-changelog'),
        sprintf(
            '<strong>%s</strong>',
            __('DLM Changelog', 'dlm-changelog')
        ),
        sprintf(
            '<strong>%s</strong>',
            __('Download Monitor', 'dlm-changelog')
        )
    );
    echo '</p></div>';

    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }
}


/**
 * Displays DLM required notice
 *
 * @return void
 */
function DLMCL_Plugin_Error_required()
{
    echo '<div class="error"><p>';
    printf(
        __('%s requires the %s plugin to work. Please install and reactivate.', 'dlm-changelog'),
        sprintf(
            '<strong>%s</strong>',
            __('DLM Changelog', 'dlm-changelog')
        ),
        sprintf(
            '<a href="http://wordpress.org/plugins/download-monitor/" target="_blank"><strong>%s</strong></a>',
            __('Download Monitor', 'dlm-changelog')
        )
    );
    echo '</p></div>';

    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }

}


/**
 * Deactivates the plugin
 *
 * @return void
 */
function DLMCL_Plugin_Error_deactivate()
{
    deactivate_plugins(plugin_basename(__FILE__));
}


/**
 * Loads plugin styles
 *
 * @return void
 */
function DLMCL_Plugin_styles()
{
    wp_register_style('dlmcl-shortcode', DLMCL_PLUGIN_URL.'assets/css/shortcode.css');
    wp_enqueue_style('dlmcl-shortcode');
}


/**
 * Plugin activation setup
 *
 * @return void
 */
function DLMCL_Plugin_activate()
{
    // Activation rules
    return;
}

// Set activation hook
register_activation_hook(__FILE__, 'DLMCL_Plugin_activate');


/**
 * Plugin deactivation setup
 *
 * @return void
 */
function DLMCL_Plugin_deactivate()
{
    // Deactivation rules
    return;
}

// Set deactivation hook
register_deactivation_hook(__FILE__, 'DLMCL_Plugin_deactivate');


/**
 * Plugin uninstallation setup
 *
 * @return void
 */
function DLMCL_Plugin_uninstall()
{
    // Unregister JS
    wp_dequeue_script('dlmcl-load-posts');
    wp_dequeue_script('dlmcl-inline-edit');

    // Unregister CSS
    wp_dequeue_style('dlmcl-admin');
    wp_dequeue_style('dlmcl-shortcode');

    // Remove database settings
    delete_option(DLMCL_VERSION_KEY);
}

// Set uninstall hook
register_uninstall_hook(__FILE__, 'DLMCL_Plugin_uninstall');


/**
 * Get post content for a given download version
 *
 * @param int $version_id The WP post ID for the version
 *
 * @return string The HTML-formatted content for the version
 */
function DLMCL_Plugin_Version_content($version_id)
{
    $version = get_post($version_id);

    if ($version->post_content == '') {
        return get_post_meta($version_id, DLMCL_POST_CONTENT, true);
    }

    return $version->post_content;
}
