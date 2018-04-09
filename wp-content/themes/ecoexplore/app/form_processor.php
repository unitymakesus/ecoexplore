<?php

namespace App;

/**
 * This file manages custom functions for forms
 */

// AJAX fetch the hotspots for dropdown
add_action('wp_ajax_obsform_county_hotspots', __NAMESPACE__ . '\\obsform_county_hotspots');
add_action('wp_ajax_nopriv_obsform_county_hotspots', __NAMESPACE__ . '\\obsform_county_hotspots');

function obsform_county_hotspots() {
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

// AJAX processing to create new observation post from submitted form
add_action('wp_ajax_obsform_submit', __NAMESPACE__ . '\\obsform_submit');

function obsform_submit() {
	error_log('Start form processing');

	$posted_data = array();
	foreach ($_POST['form'] as $post) {
		$posted_data[$post['name']] = $post['value'];
	}

	error_log(print_r($posted_data, true));

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
		echo json_encode(array('status' => 'error', 'message' => 'There was an error sending your observation. Please try again or contact <a href="mailto:ecoexplore@ncarboretum.org" target="_blank">ecoexplore@ncarboretum.org</a> for help.'));
	} else {
		// Keep processing
		// Set custom fields
		update_post_meta($post_id, 'observation_time', $posted_data['datetime']);
		update_post_meta($post_id, 'at_hotspot', $posted_data['choice']);
		update_post_meta($post_id, 'county', $posted_data['county']);
		update_post_meta($post_id, 'which_hotspot', $posted_data['hotspot']);
		update_post_meta($post_id, 'observation_location', $posted_data['picker-coords']);

		// Sanitize address
		if (!empty($posted_data['picker-address'])) {
			$address = $posted_data['picker-address'];
			$address = preg_replace('/[0-9]+/', '', $address);
			$address = str_replace(', USA', '', $address);
			$address = str_replace(', US', '', $address);
			$address = str_replace('US-', '', $address);
			update_post_meta($post_id, 'city_state', $posted_data['picker-address']);
		}

		// Attach image as post thumbnail
		global $wpdb;
		$dz_file_rel = str_replace('https://files.ecoexplore.net', '', $posted_data['dz-files']);
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%%%s';", $dz_file_rel));
		error_log(print_r($attachment, true));
		set_post_thumbnail($post_id, $attachment[0]);
		error_log('Image has been added to WP and set to observation post');

		// Clear transients
		delete_transient( 'notes_' . $args['post_author'] );

		echo json_encode(array('status' => 'success', 'ID' => $post_id));
	}

	die();
}
