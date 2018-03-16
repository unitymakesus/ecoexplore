<?php

namespace App;

/**
 * This file manages custom functions for forms
 */

// Dynamically populate the counties dropdown
add_filter('wpcf7_dynamic_select_counties', function($choices, $args=array()) {
	$hotspot_coords = get_field('hotspot_coordinates', 'options');
	$choices = array(
		'Select One' => ''
	);

	// Add each county to the dropdown select options
	foreach ($hotspot_coords as $hsc) {
		$choices[$hsc['county']] = $hsc['county'];
	}

	return $choices;
});

// Dynamically populate the hotspots dropdown based on selected county
add_filter('wpcf7_dynamic_select_hotspots', function($choices, $args=array()) {
	$choices = array(
		'You must first select the county' => ''
	);
	return $choices;
});

// AJAX processing to populate the hotspots dropdown
add_action('wp_ajax_cf7_county_hotspots', __NAMESPACE__ . '\\cf7_county_hotspots');
add_action('wp_ajax_nopriv_cf7_county_hotspots', __NAMESPACE__ . '\\cf7_county_hotspots');

function cf7_county_hotspots() {
	$county = $_POST['county'];

	$hotspot_coords = get_field('hotspot_coordinates', 'options');
	$choices[] = 'Select One';

	foreach ($hotspot_coords as $hsc) {
		if ($hsc['county'] == $county) {
			foreach ($hsc['hotspots'] as $hs) {
				$choices[] = $hs['hotspot_name'];
			}
		}
	}

	echo json_encode($choices);
	die();
}


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

		if (is_wp_error($post_id)) {
			// Output error
			error_log(print_r($post_id, true));
		} else {
			// Set custom fields
			update_post_meta($post_id, 'observation_time', $posted_data['datetime']);
			update_post_meta($post_id, 'at_hotspot', $posted_data['choice']);
			update_post_meta($post_id, 'observation_location', $posted_data['location']);
			update_post_meta($post_id, 'county', $posted_data['county']);
			update_post_meta($post_id, 'which_hotspot', $posted_data['hotspot']);

error_log(print_r($posted_data, true));
error_log(print_r($uploaded_files, true));

			// Process photo
      if (isset($posted_data['photo']) && isset($uploaded_files['photo'])) {
        $photo_name = $posted_data['photo'];
				$photo_file = $uploaded_files['photo'];

				// Copy file to uploads directory
				$wp_upload_dir = wp_upload_dir();
				$new_filename = $post_id . '_' . $photo_name;
				$new_filepath = $wp_upload_dir['path'] . '/' . $new_filename;
				copy($photo_file, $new_filepath);

        // Set up params to add to media library
        $filetype = wp_check_filetype( $new_filename, null );
error_log(print_r($filetype, true));
        $attachment = array(
        	'guid'           => $wp_upload_dir['url'] . '/' . $new_filename,
        	'post_mime_type' => $filetype['type'],
        	'post_title'     => preg_replace( '/\.[^.]+$/', '', $new_filename ),
        	'post_content'   => '',
        	'post_status'    => 'inherit'
        );
error_log(print_r($attachment, true));

        // Insert to media library
        $attach_id = wp_insert_attachment( $attachment, $new_filepath, $post_id );

        // Generate the metadata for the attachment
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

				// Generate image sizes for default image sizes
				$sizes = get_intermediate_image_sizes();
				global $_wp_additional_image_sizes;
				foreach ( get_intermediate_image_sizes() as $_size ) {
					if ( in_array( $_size, array('thumbnail', 'medium', 'large') ) ) {
						$size['width']  = get_option( "{$_size}_size_w" );
						$size['height'] = get_option( "{$_size}_size_h" );
						$size['crop']   = (bool) get_option( "{$_size}_crop" );
						$intermediate_size = image_make_intermediate_size($new_filepath, $size['width'], $size['height'], $size['crop']);;
						$attach_data['sizes'][$_size] = $intermediate_size;
					}
				}

				// Update the database record
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Set post thumbnail to uploaded photo
        set_post_thumbnail( $post_id, $attach_id );
      }

			// Map pin location geocoding
			if (!empty($posted_data['location'])) {
				$google_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';
				$geocode_api_key = 'AIzaSyD5IF_rp6nUrCw6ficzMBgFApZtucUfjdk';

		    // Separate latitude and longitude
		    $coords = $posted_data['location'];
		    preg_match("/\((.*?),/", $coords, $lat, PREG_OFFSET_CAPTURE, 0);
		    preg_match("/, (.*?)\)/", $coords, $lng, PREG_OFFSET_CAPTURE, 0);

				// Set up API url with parameters
		    $params = [
		      'latlng' => round($lat[1][0], 6) . ',' . round($lng[1][0], 6),
		      'location_type' => 'APPROXIMATE',
		      'result_type' => 'political',
					'key' => $geocode_api_key
		    ];

		    $reverse_geocode_url = add_query_arg($params, $google_api_url);
		    $geocode_results = wp_remote_get($reverse_geocode_url, []);

		    if ($geocode_results['response']['code'] == '200') {
		      $response_body = json_decode($geocode_results['body']);

		      // Get the address and remove USA
		      $address = $response_body->results[0]->formatted_address;
		      $address = str_replace(', USA', '', $address);
		      $address = str_replace(', US', '', $address);
		      $address = str_replace('US-', '', $address);

					// Set custom fields
		      update_post_meta($post_id, 'city_state', $address);
		    }
			}
		}

		// Clear transients
		delete_transient( 'notes_' . $args['post_author'] );

		return $form;
	}
});
