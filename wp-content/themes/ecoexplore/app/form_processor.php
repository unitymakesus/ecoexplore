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
      'post_content' => wp_strip_all_tags($posted_data['description']),
			'post_author' => get_current_user_id(),
      'post_status' => 'pending'
		);

		$post_id = wp_insert_post($args, $wp_error);

		if (!is_wp_error($post_id)){
			if (isset($posted_data['location'])) {
			  // Let's geocode the coordinates to get town, state
		    $google_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';
		    $geocode_api_key = 'AIzaSyD5IF_rp6nUrCw6ficzMBgFApZtucUfjdk';

		    // Separate latitude and longitude
		    $coords = $posted_data['location'];
		    preg_match("/\((.*?),/", $coords, $lat, PREG_OFFSET_CAPTURE, 0);
		    preg_match("/, (.*?)\)/", $coords, $lng, PREG_OFFSET_CAPTURE, 0);

		    $params = [
		      'latlng' => round($lat[1][0], 6) . ',' . round($lng[1][0], 6),
		      'location_type' => 'APPROXIMATE',
		      'result_type' => 'political',
		      'key' => $geocode_api_key
		    ];
		    $args = [];

		    $reverse_geocode_url = add_query_arg($params, $google_api_url);
		    $geocode_results = wp_remote_get($reverse_geocode_url, $args);

		    if ($geocode_results['response']['code'] == '200') {
		      $response_body = json_decode($geocode_results['body']);

		      // Get the address and remove USA
		      $address = $response_body->results[0]->formatted_address;
		      $address = str_replace(', USA', '', $address);

					// Set custom fields
		      update_post_meta($post_id, 'city_state', $address);
					update_post_meta($post_id, 'observation_location', $coords);
					update_post_meta($post_id, 'observation_time', $posted_data['datetime']);
					update_post_meta($post_id, 'at_hotspot', $posted_data['choice']);
					update_post_meta($post_id, 'which_hotspot', $posted_data['hotspot']);
		    }
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
