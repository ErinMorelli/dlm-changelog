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
* DLM Changelog Inline Edit Update Function
*
*/


// Grab jEditiable Post Variables

$path = $_POST['path'];
$id = $_POST['id'];
$value = $_POST['value'];


//Include WP Functions

define('WP_USE_THEMES', false);
require_once($path.'/wp-load.php');


// If there's post content
if(!empty($value)) {
    
    // Declare new post attribues array
    $new_post_content = array( 
    	'ID' => $id,
    	'post_content' => $value
    	);

    // Update the post into the database
    wp_update_post( $new_post_content );
    
    // Echo updated content
    echo $value;


} else {
    // If no post content, do nothing
    return;
}

?>