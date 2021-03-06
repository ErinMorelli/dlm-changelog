<?php
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
 * @package DLMCL\Admin
 */


/**
 * Saves all version post content to a meta value to prevent overrides
 *
 * @param int $version_id The WP post ID of the version being saved
 *
 * @return void
 */
function DLMCL_Admin_override($version_id)
{
    // We only care about DLM version posts
    if (get_post_type($version_id) != 'dlm_download_version') {
        return;
    }

    // Get current post and meta data
    $version = get_post($version_id);
    $saved_content = get_post_meta($version_id, DLMCL_POST_CONTENT, true);

    // Update our post meta if there's content
    if (!$saved_content || $version->post_content != '') {
        update_post_meta($version_id, DLMCL_POST_CONTENT, $version->post_content);
    }
}

// Add to save post hook
add_action('save_post', 'DLMCL_Admin_override');


/**
 * Saves changelog content to the database via AJAX call
 *
 * @return void
 */
function DLMCL_Admin_update()
{
    // Grab Post Variables
    $id = $_POST['post_id'];
    $content = $_POST['post_content'];

    // If there's post content
    if (!empty($content)) {
        // Declare new post attributes array
        $new_post_content = array(
            'ID'            => $id,
            'post_content'  => $content
        );

        // Update the post into the database
        wp_update_post($new_post_content);

        // Print updated content
        print($content);

        // Die before WP admin AJAX dies
        wp_die();
    } else {
        // If no post content, do nothing
        wp_die();
    }
}

// Add AJAX update hook
add_action('wp_ajax_dlmcl_save_post', 'DLMCL_Admin_update');


/**
 * Loads the DLMCL admin JS and CSS files
 *
 * @return void
 */
function DLMCL_Admin_scripts()
{
    // Load JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('tiny_mce', '//cdn.tinymce.com/4/tinymce.js', array('jquery'), '1.0', true);
    wp_enqueue_script('dlmcl-admin', DLMCL_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), '1.0', true);

    // Load CSS
    wp_enqueue_style('dlmcl-admin', DLMCL_PLUGIN_URL .'assets/css/admin.css');
}

// Loads scripts in WP admin
add_action('admin_enqueue_scripts', 'DLMCL_Admin_scripts');


/**
 * Add DLMCL page to the WP menu
 *
 * @return void
 */
function DLMCL_Admin_menu()
{
    add_submenu_page(
        'edit.php?post_type=dlm_download',
        __('Changelogs', 'dlm-changelog'),
        __('Changelogs', 'dlm-changelog'),
        'manage_downloads',
        'download-monitor-changelogs',
        'DLMCL_Admin_page'
    );
}

// Load settings page menu link
add_action('admin_menu', 'DLMCL_Admin_menu');


/**
 * Loads DLMCL page for users with valid permissions
 *
 * @return void
 */
function DLMCL_Admin_page()
{
    // Get all published DLM objects
    $get_downloads = get_posts(
        array(
            'post_type'     => 'dlm_download',
            'post_status'   => 'publish',
            'orderby'       => 'title',
            'order'         => 'ASC',
            'numberposts'   => -1,
        )
    );

    // Set DLM download post ID
    if (isset($_POST['dlmcl-select-download'])) {
        $dlm_id = $_POST['dlmcl-select-download'];
    } else {
        $dlm_id = null;
    }
?>
<div class="wrap">

    <h2><?php _e('Changelogs', 'dlm-changelog'); ?></h2>

    <form method="post" id="dlmcl-select-form" action="?post_type=dlm_download&amp;page=download-monitor-changelogs">

        <?php echo wp_nonce_field('dlmcl-select-edit'); ?>

        <input type="hidden" name="dmcl-select-submitted" value="yes" />

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="dlmcl-select-download"><?php _e('Select Download', 'dlm-changelog');?>:</label>
                    </th>
                    <td>
                        <select id="dlmcl-select-download" name="dlmcl-select-download">
                            <option value="null">-- <?php _e('Select Download', 'dlm-changelog'); ?> --</option>
<?php foreach ($get_downloads as $download) : ?>
                            <option data-slug="<?php echo $download->post_name; ?>" value="<?php echo $download->ID; ?>"<?php echo ($dlm_id == $download->ID ? ' selected' : ''); ?>><?php echo $download->post_title; ?></option>
<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <br />

    </form>

    <?php if (isset($_POST['dlmcl-select-download']) && ($dlm_id != 'null')) : ?>

        <?php
            if (DLMCL_USE_LEGACY) {
                $dlm_post = new DLM_Download($dlm_id);
                $dlm_versions = $dlm_post->get_file_versions();
            } else {
                $dlm_post = download_monitor()->service('download_repository')->retrieve_single($dlm_id);
                $dlm_versions = $dlm_post->get_versions();
            }
        ?>

        <hr />

        <h2>&ldquo;<?php $dlm_post->the_title(); ?>&rdquo; <?php _e('Changelog', 'dlm-changelog'); ?> <a href="<?php echo get_edit_post_link($dlm_id); ?>#download-monitor-file" class="add-new-h2"><?php _e('Add Version', 'dlm-changelog'); ?></a></h2>

        <ul class="subsubsub">
            <li class="all"><?php _e('Display this changelog on your site using this shortcode', 'dlm-changelog'); ?>: <code>[dlm_changelog id="<?php echo $dlm_id; ?>"]</code><br />&nbsp;</li>
        </ul>

        <table class="widefat dlmcl-versions">
            <thead>
                <tr>
                    <th width="10%"><?php _e('ID', 'dlm-changelog'); ?></th>
                    <th width="10%"><?php _e('Version', 'dlm-changelog'); ?></th>
                    <th width="55%"><?php _e('Notes', 'dlm-changelog'); ?></th>
                    <th width="10%"><?php _e('Date', 'dlm-changelog'); ?></th>
                    <th width="15%"><?php _e('Files', 'dlm-changelog'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <th><?php _e('ID', 'dlm-changelog'); ?></th>
                <th><?php _e('Version', 'dlm-changelog'); ?></th>
                <th><?php _e('Notes', 'dlm-changelog'); ?></th>
                <th><?php _e('Date', 'dlm-changelog'); ?></th>
                <th><?php _e('Files', 'dlm-changelog'); ?></th>
            </tfoot>
            <tbody>
                <?php foreach ($dlm_versions as $dlm_version) :
                        $this_version_id = DLMCL_USE_LEGACY ? $dlm_version->id : $dlm_version->get_id();
                        $this_version = get_post($this_version_id);
                        $this_version_filename = DLMCL_USE_LEGACY ? $dlm_version->filename : $dlm_version->get_filename();
                        $this_version_version = DLMCL_USE_LEGACY ? $dlm_version->version : $dlm_version->get_version();
                        $this_version_url = DLMCL_USE_LEGACY ? $dlm_version->url : $dlm_version->get_url(); ?>
                <tr>
                    <td><?php echo $this_version_id; ?></td>
                    <td><strong><?php echo $this_version_version; ?></strong></td>
                    <td class="dlmcl-editable-cell">
                        <div
                            class="dlmcl-editable" id="dlmcl-editable-<?php echo $this_version_id; ?>"
                            title="<?php _e('Click to edit version notes', 'dlm-changelog'); ?>"
                            data-id="<?php echo $this_version_id; ?>"
                            data-placeholder="<?php _e('Click to add version notes', 'dlm-changelog'); ?>"
                        ><?php echo DLMCL_Plugin_Version_content($this_version_id); ?></div>
                    </td>
                    <td><?php echo date('m/d/y', strtotime($this_version->post_date)); ?></td>
                    <td><a href="<?php echo $this_version_url; ?>"><?php echo $this_version_filename; ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<?php
}
