<?php 
/*
Copyright (c) 2013, Erin Morelli. 

This program is free software; you can redistribute it and/or 
modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation; either version 2 
of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
*
*
* DLM Changelog Admin Settings
*
*/


add_action( 'wp_ajax_dlmcl_update', 'dlmcl_update_action' );

function dlmcl_update_action() {
	// Grab jEditiable Post Variables
	$id = $_POST['id'];
	$value = $_POST['value'];
	
	// If there's post content
	if(!empty($value)) {
	    
	    // Declare new post attribues array
	    $new_post_content = array( 
	    	'ID' => $id,
	    	'post_content' => $value
	    	);
	
	    // Update the post into the database
	    wp_update_post( $new_post_content );
	    
	    // print updated content
	    print($value);
	    
	    // die before wp admin ajax dies
	    die();
	
	} else {
	    // If no post content, do nothing
	    die();
	}
}


add_action( 'admin_head', 'dlmcl_update_javascript' );

function dlmcl_update_javascript() {
?>
<script type="text/javascript" >
//Inline Editor
jQuery(document).ready(function(){
	jQuery('.editable').editable(ajaxurl, {
		indicator : '<?php _e('Saving', 'dlm-changelog'); ?>...',
		tooltip : '<?php _e('Click to edit notes', 'dlm-changelog'); ?>',
		type : 'textarea',
		submit : '<?php _e('Save', 'dlm-changelog'); ?>',
		cancel : '<?php _e('Cancel', 'dlm-changelog'); ?>',
		placeholder : "<?php _e('Click to add notes', 'dlm-changelog'); ?>",
		rows : 10,
		submitdata : {action: 'dlmcl_update'}
     });
});
</script>
<?php
}


function dlm_changelog_js() {
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'dmcl-inline-edit', DLMCL_PLUGIN_URL . 'assets/js/jquery.jeditable.js', array( 'jquery' ), '1.0', true );
	
	wp_enqueue_style( 'dmcl-admin', DLMCL_PLUGIN_URL .'assets/css/admin-style.css' );
}
add_action('admin_enqueue_scripts', 'dlm_changelog_js');


add_action('admin_menu', 'dlm_changelog_admin');

function dlm_changelog_admin() {
	add_submenu_page( 'edit.php?post_type=dlm_download', __( 'Changelogs', 'dlm-changelog' ), __( 'Changelogs', 'dlm-changelog' ), 'manage_options', 'download-monitor-changelogs', 'dlm_changelog_admin_page' );
}


function dlm_changelog_admin_page() {

	$get_downloads = get_posts( array(
		'post_type' => 'dlm_download',
		'post_status' => 'publish',
		'orderby' => 'title',
		'order' => 'ASC'
		) );

?>

<script type="text/javascript">
// Download Select
function change(){
    document.getElementById("dmcl_dl_form").submit();
}
</script>

<div class="wrap">
	
	<?php screen_icon(); ?><h2><?php _e('Changelogs', 'dlm-changelog'); ?></h2>
	
	<?php if( isset( $_POST['dmcl_dl_select'] ) ) $dlm_id = $_POST['dmcl_dl_select']; else $dlm_id = null;  ?>
	
	<form method="post" id="dmcl_dl_form" action="?post_type=dlm_download&page=download-monitor-changelogs">
	
		<?php echo wp_nonce_field( 'dmcl_dl_edit'); ?>
	
    	<input type="hidden" name="dmcl_dl_edit_submitted" value="yes" />
		
	    <table class="form-table">
	        <tbody>
	            <tr valign="top">
	                <th scope="row">
	                    <label for="dmcl_dl_select"><?php _e('Select Download: ', 'dlm-changelog');?></label>
	                </th>
	                <td>
	                    <select id="dmcl_dl_select" name="dmcl_dl_select" onchange="change()" />
	                    		<option value="null">-- <?php _e('Select Download', 'dlm-changelog'); ?> --</option>
							<?php foreach( $get_downloads as $download ) : ?>
								<option value="<?php echo $download->ID; ?>"<?php if( $dlm_id == $download->ID) echo " selected"; ?>><?php echo $download->post_title; ?></option>
							<?php endforeach; ?>
						</select>
	                </td>
	            </tr>
			</tbody>
	    </table>
	    <br />
		
	</form>
	
	
	<?php if( isset( $_POST['dmcl_dl_select'] ) && ( $dlm_id != 'null' ) ) : ?>
	
		<?php $dlm_post = new DLM_Download( $dlm_id );
			  $dlm_versions = $dlm_post->get_file_versions();
		?>
	
		<hr />
	
		<h2><?php $dlm_post->the_title() ;?><?php _e(' Changelog', 'dlm-changelog'); ?><a href="<?php echo get_edit_post_link( $dlm_id ); ?>#download-monitor-file" class="add-new-h2"><?php _e('Add Version', 'dlm-changelog'); ?></a></h2>

		<ul class="subsubsub">
			<li class="all"><?php _e('Display this changelog on your site using this shortcode: ', 'dlm-changelog'); ?><code>[dlm_changelog id="<?php echo $dlm_id; ?>"]</code><br />&nbsp;</li>
		</ul>
		
		<table class="widefat dlm-versions">
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
				<?php foreach( $dlm_versions as $dlm_version ) : 
					  $this_version = get_post( $dlm_version->id ); ?>
				<tr>
					<td><?php echo $dlm_version->id; ?></td>
		            <td><strong><?php echo $dlm_version->version; ?></strong></td>
		            <td class="editable" id="<?php echo $dlm_version->id; ?>"><?php echo $this_version->post_content; ?></td>
					<td><?php echo date('m/d/y', strtotime( $this_version->post_date ) ); ?></td>
		            <td><a href="<?php echo $dlm_version->url; ?>"><?php echo $dlm_version->filename; ?></a></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
	<?php endif; ?>
	
</div>
	
<?php 



}

?>