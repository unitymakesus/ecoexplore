<?php

namespace App;

/**
 * This file manages custom functions for forms
 */

// Create a new observation post from submitted form
add_filter( 'wpcf7_before_send_mail', function( $form ) {
	if ( '36' == $form->id ) {
    $instance = \WPCF7_Submission::get_instance();
    $posted_data = $instance->get_posted_data();
    $uploaded_files = $instance->uploaded_files();

		$args = array(
			'post_type' => 'observation',
			'post_title' => wp_strip_all_tags($posted_data['identification']),
      'post_content' => '',
			'post_author' => get_current_user_id(),
      'post_status' => 'pending'
			// 'post_date' => strtotime($posted_data['datetime']),
		);

		$post_id = wp_insert_post($args, $wp_error);

		if (!is_wp_error($post_id)){
			if (isset($posted_data['location'])) {
        // Set custom fields
				update_post_meta($post_id, 'observation_location', $posted_data['location']);
        update_post_meta($post_id, 'observation_time', $posted_data['datetime']);
			}

      if (isset($posted_data['photo']) && isset($uploaded_files['photo'])) {
        $photo_name = $posted_data['photo'];
        $photo_content = file_get_contents($uploaded_files['photo']);

        // Put photo in uploads directory
        $upload = wp_upload_bits($photo_name, '', $photo_content);

        // Set up params to add to media library
        $filename= $upload['file'];
        $parent_post_id = $post_id;
        $filetype = wp_check_filetype( basename( $filename ), null );
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
        	'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
        	'post_mime_type' => $filetype['type'],
        	'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
        	'post_content'   => '',
        	'post_status'    => 'inherit'
        );

        // Insert to media library
        $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

        // Generate the metadata for the attachment, and update the database record.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Set post thumbnail to uploaded photo
        set_post_thumbnail( $parent_post_id, $attach_id );
      }
		}

		return $form;
	}
});
