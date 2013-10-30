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
* DLM Changelog Shortcode 
*
*/


function dlm_changelog($atts) {
   extract(shortcode_atts(array(
      'id' => '0', // download id
      'show' => '0' // number of downloads to show
   ), $atts));
   
   return dlm_changelog_display($id, $show);
}
add_shortcode('dlm_changelog', 'dlm_changelog');


function dlm_changelog_display($id, $show = '5') {
	$args = array('id' => $id, 'show' => $show);
	return dlm_changelog_output ($args);
}


function dlm_changelog_output($dlm_files) {

	wp_enqueue_script( 'dlmcl-load-posts', DLMCL_PLUGIN_URL .'assets/js/changelog.js', array('jquery'), '1.0', true );
	
	$id = $dlm_files['id'];
	$show = floatval($dlm_files['show']);
	
	$output = '';
	
	$download = new DLM_Download( $id );
	
	if ( $download->exists() ) {
	
		$downloads = $download->get_file_versions();
	
		if ( $show != 0 ) {
			
			$version_count = count($downloads);
			$max_pages = ceil( $version_count / $show );
			$pages = array_chunk( $downloads, $show );
			
			if ( isset($_GET['more']) )
				$pgkey = (int)$_GET['more'];
			else
				$pgkey = 0;	
			$next_page = '?more='.($pgkey+1);
			
			wp_localize_script(
	 			'dlmcl-load-posts',
	 			'dlmcl_load',
	 			array(
	 				'startPage' => $pgkey,
	 				'maxPages' => $max_pages,
	 				'nextLink' => $next_page,
	 				'moreText' => __('Show More', 'dlm-changelog'),
	 				'loadText' => __('Loading More', 'dlm-changelog')
	 			)
	 		);
	
			$output .= '<div id="dlm-changelog-list">'."\n";
			
				$output .= dlm_changelog_show_page( $pages[$pgkey] );
				
				$output .= '<noscript><p id="dlm-changelog-load-posts"><a href="'.$next_page.'" class="dlmcl-button">'.__('Show More', 'dlm-changelog').'</a></p></noscript>'."\n";
				
			$output .= '</div>'."\n";
		
		} 
		else {
			
			$output .= '<div class="dlm-changelog-list">'."\n";
			
				$output .= dlm_changelog_show_page( $downloads );
				
			$output .= '</div>'."\n";
			
		}
			
	}
	
	return $output;
	
}

function dlm_changelog_show_page($dlm_versions) {
		
	$output = '';
	
	foreach( $dlm_versions as $version ) {
		
		$output .= '<div class="dlm-changelog-item" id="dlm-changelog-item-'.$version->id.'">'."\n";
		
		// Version Title
		$output .= '<h3>';
		$output .= $version->version;
		$output .= '</h3>'."\n";
		
		// Download Link
		$output .= '<p class="cl-dl"><a href="';
		$output .= $version->url;
		$output .= '" target="_blank">'.__('Download', 'dlm-changelog').'</a>';
		
		// Release Date
		$release = get_post($version->id);
		
		$output .= ' &ndash; Released ';
		$output .=  date('m/d/y', strtotime( $release->post_date ) );
		$output .= '</p>'."\n";
		
		// Release Notes
		$output .= $release->post_content ."\n";
		
		$output .= '</div>'."\n";
	
	}
	
	return $output;
	
}
