<?php
/*
Plugin Name: Simple Thumbnail Generator
Description: Generates custom thumbnails and saves them in a custom folder upon image upload.
Version: 1.0
Author: test
*/

function simple_generate_thumbnail($attachment_id) {
 
    $upload_dir = wp_upload_dir();
    
 
    $thumb_dir_home = $upload_dir['basedir'] . '/thumbs/homepage/';
    $thumb_dir_list = $upload_dir['basedir'] . '/thumbs/listing/';
    

    if (!file_exists($thumb_dir_home) ) {
        wp_mkdir_p($thumb_dir_home);
    }
    if (!file_exists($thumb_dir_list) ) {
        wp_mkdir_p($thumb_dir_list);
    }
    

    $file_path = get_attached_file($attachment_id);
    
  
    if ($file_path && file_is_valid_image($file_path)) {
    
        $image = wp_get_image_editor($file_path);
        $image2 = wp_get_image_editor($file_path);
        
        if (!is_wp_error($image)) {
            // Resize the image
            $image->resize(500, 500, true);
            $image2->resize(300, 300, true);
            
           
            $filename = basename($file_path);
            $thumb_filename =$filename;
            
           
            $image->save($thumb_dir_home . $thumb_filename);
            $image2->save($thumb_dir_list . $thumb_filename);
            
          
            $attachment_meta = wp_get_attachment_metadata($attachment_id);
            $attachment_meta['custom_thumb'] = 'custom-thumbs/' . $thumb_filename;
            wp_update_attachment_metadata($attachment_id, $attachment_meta);
        }
    }
}


add_action('add_attachment', 'simple_generate_thumbnail');
//add_filter('wp_handle_upload', 'simple_generate_thumbnail');


/****************************************/

function custom_add_image_sizes() {
    add_image_size('custom-thumb', 300, 300, true);
}
add_action('after_setup_theme', 'custom_add_image_sizes');

// Step 2: Generate thumbnail for each selected image and save it in a custom folder
function custom_save_multiple_thumbnails($metadata, $attachment_id) {
    $upload_dir = wp_upload_dir();
    $thumb_dir = $upload_dir['basedir'] . '/thumbnails/';

    // Ensure the custom folder exists; create it if not
    if (!file_exists($thumb_dir)) {
        wp_mkdir_p($thumb_dir);
    }

    // Handle only the 'custom-thumb' size
    if (isset($metadata['sizes']['custom-thumb'])) {
        $size_data = $metadata['sizes']['custom-thumb'];
        $original_file = $upload_dir['basedir'] . '/' . dirname($metadata['file']) . '/' . $size_data['file'];
        $new_file = $thumb_dir . basename($size_data['file']);

        // Copy the thumbnail to the custom folder
        if (file_exists($original_file)) {
            copy($original_file, $new_file);
            // Update the metadata to reflect the new location of the thumbnail
            $metadata['sizes']['custom-thumb']['file'] = 'thumbnails/' . basename($size_data['file']);
        }
    }

    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'custom_save_multiple_thumbnails', 10, 2);



function custom_handle_upload($file) {
    $attachment_id = wp_insert_attachment(array(
        'post_mime_type' => $file['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($file['file'])),
        'post_content' => '',
        'post_status' => 'inherit'
    ), $file['file']);

    if (!is_wp_error($attachment_id)) {
       
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $file['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

     
        custom_save_multiple_thumbnails($attachment_data, $attachment_id);
    }

    return $file;
}
add_filter('wp_handle_upload', 'custom_handle_upload');


function custom_regenerate_all_thumbnails() {
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);

    foreach ($query->posts as $image) {
        $attachment_id = $image->ID;
        $file_path = get_attached_file($attachment_id);
        
        if ($file_path && file_exists($file_path)) {
            $metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $metadata);
            custom_save_multiple_thumbnails($metadata, $attachment_id);
        }
    }
}


function custom_add_admin_menu() {
    add_management_page('Regenerate Thumbnails', 'Regenerate Thumbnails', 'manage_options', 'regenerate-thumbnails', 'custom_regenerate_thumbnails_page');
}
add_action('admin_menu', 'custom_add_admin_menu');

function custom_regenerate_thumbnails_page() {
    echo '<div class="wrap">';
    echo '<h1>Regenerate Thumbnails</h1>';
    echo '<p>Click the button below to regenerate thumbnails for all existing images.</p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="regenerate_thumbnails" value="1">';
    submit_button('Regenerate Thumbnails');
    echo '</form>';
    echo '</div>';

    if (isset($_POST['regenerate_thumbnails'])) {
        custom_regenerate_all_thumbnails();
        echo '<div class="updated"><p>Thumbnails have been regenerated.</p></div>';
    }
}