<?php
/**
 * Plugin Name: DLM Changelog Add-on
 * Plugin URI: http://erinmorelli.com/wordpress/dlm-changelog
 * Description: An add-on for Mike Jolley's Dowload Monitor that adds version changelog functionlity.
 * Version: 0.1.2
 * Author: Erin Morelli
 * Author URI: http://erinmorelli.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
 
 
// Define plugin file paths
define('DLMCL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DLMCL_PLUGIN_URL', plugin_dir_url(__FILE__));

if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' );

// Initiate plugin files
function __dlmcl_plugin_load(){

    // Set path to DLM plugin file
    define( 'DLM_PLUGIN_DIR', ABSPATH . PLUGINDIR . '/download-monitor/' );
    $dlm_path = DLM_PLUGIN_DIR . 'download-monitor.php';

	// Detect if DLM is installed & load DLMCL files
	if ( file_exists( $dlm_path ) ) {
		
        // Included for get_plugin_data function
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        // Retrieve current DLM plugin version
        $dlm_info = get_plugin_data( $dlm_path, false);
		$dlm_version = floatval( $dlm_info['Version'] );

        // Check against this version
		$check_version = 1.2;
		
        // If correct version, include DLMCL files
		if ( $dlm_version >= $check_version ) {
			
            //load admin files only in admin
			if(is_admin())
				require_once(DLMCL_PLUGIN_DIR.'includes/dlmcl-admin.php');
        
			require_once(DLMCL_PLUGIN_DIR.'includes/dlmcl-shortcode.php');
			
			add_action( 'wp_enqueue_scripts', 'dlmcl_load_styles' );

        // Else return version error
		} else {
		
			add_action( 'admin_notices', 'dlmcl_version_notice' );

            function dlmcl_version_notice() {
            
              	echo '<div class="updated"><p><strong>DLM Changelog</strong> works with <strong>Download Monitor</strong> version 1.2.0 and higher. Please upgrade to the latest version.</p></div>';
              	
              	if ( isset( $_GET['activate'] ) )
                	unset( $_GET['activate'] );
             }
			
		}

	// Return an error
	} else {
		
		add_action( 'admin_init', 'dlmcl_error_deactivate' );
		add_action( 'admin_notices', 'dlmcl_error_notice' );
		
		function dlmcl_error_deactivate() {
        	deactivate_plugins( plugin_basename( __FILE__ ) );
        }

        function dlmcl_error_notice() {
            
       		echo '<div class="error"><p><strong>DLM Changelog</strong> requires the <a href="http://wordpress.org/plugins/download-monitor/" target="_blank"><strong>Download Monitor</strong></a> plugin to work. Please install and reactivate.</p></div>';
        	if ( isset( $_GET['activate'] ) )
            	unset( $_GET['activate'] );
            	
        }
		
	}
		
}

// Init plugin
add_action( 'plugins_loaded', '__dlmcl_plugin_load', 10 );


// Load plugin styles
function dlmcl_load_styles() {
	wp_register_style( 'dlmcl-shortcode', DLMCL_PLUGIN_URL.'assets/css/shortcode-style.css' );
	wp_enqueue_style( 'dlmcl-shortcode' );
}


// Activation setup
register_activation_hook(__FILE__, 'dlmcl_plugin_activation');

function dlmcl_plugin_activation() {
	// Check for new version
	$dlmcl_curr_version = '0.1.2';
	 
	if (!defined('DLMCL_VERSION_KEY')) {
		// Define new version option
    	define('DLMCL_VERSION_KEY', 'dlmcl_version');
    }
    
    if (!defined('DLMCL_VERSION_NUM')) {
    	// Add current version value
	    define('DLMCL_VERSION_NUM', $dlmcl_curr_version);	
	    add_option(DLMCL_VERSION_KEY, DLMCL_VERSION_NUM);  
    } 
    
    if (get_option(DLMCL_VERSION_KEY) != $dlmcl_curr_version) {
	    // Update the version value
	    update_option(DLMCL_VERSION_KEY, $dlmcl_curr_version);
	}
}

// Deactivation setup
register_deactivation_hook(__FILE__, 'dlmcl_plugin_deactivation');

function dlmcl_plugin_deactivation() {
	// Deactivation rules
}


// Uninstall setup
register_uninstall_hook(__FILE__, 'dlmcl_plugin_uninstall');

function dlmcl_plugin_uninstall() {  
	// Unregister JS & CSS
	wp_dequeue_script('dlmcl-load-posts');
	wp_dequeue_script('dmcl-inline-edit');
	wp_dequeue_style('dmcl-admin');
	wp_dequeue_style('dlmcl-shortcode');
}


?>