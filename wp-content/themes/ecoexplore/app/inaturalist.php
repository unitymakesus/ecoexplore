<?php

namespace App;

// This file manages the iNaturalist API integration

/**
 * Add options page to keep track of iNaturalist users
 *
 */

if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'iNaturalist Settings',
    'menu_title' => 'iNaturalist Settings',
    'menu_slug' => 'inaturalist-settings',
    'capability' => 'edit_posts',
    'redirect' => false
  ]);
}


/**
 * [post_observation description]
 * @return [type] [description]
 */
function post_observation($library) {
  // Get list of iNat accounts
  $inat_users = get_field('inaturalist_accounts', 'option');

  // Find this user's library's account
  foreach ($inat_users as $key => $user) {
    if ($user['library_account_map'] == $library) {
      $inat_key = $key;
    }
  }
}


/**
 * On save of any observation, gotta run these functions
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
add_action('save_post_observation', function($post_id, $post, $update) {

  // Only run this on updates, not on the initial post creation
  if ($update == false)
    return;

  error_log('still doing inat stuff...');

  $status = get_post_status($post_id);
  $inat_push = $_POST['acf']['field_5aa9a8cbcf244'];
  $inat_id = get_post_meta($post_id, 'inat_id', true);
  $user_id = $_POST['post_author'];
  $user = get_userdata($user_id);

  // Do user points calculations
  $points_old = get_post_meta($post_id, 'points', true);  // Old value
  $points_new = $_POST['acf']['field_59a880cd6c1ac'];     // New value

  if ($points_old !== $points_new) {
    // Get user points
    $running_points = get_user_meta($user_id, 'running_points', true);
    $total_points = get_user_meta($user_id, 'total_points', true);

    // Get difference in points for this observation
    $points_change = $points_new - $points_old;

    // Calculate new points values
    $running_points_new = $running_points + $points_change;
    $total_points_new = $total_points + $points_change;

    // Save new points total for this user
    $test1 = update_user_meta($user_id, 'running_points', $running_points_new);
    $test2 = update_user_meta($user_id, 'total_points', $total_points_new);
  }

  // If this was not at a Hotspot and the coords haven't been geocoded,
  // let's try using the coordinates to get town, state
  if (($_POST['acf']['field_59d8e80c867f5'] == "Yes") && (empty($_POST['acf']['field_59caa3fa222d5']))) {
    $google_api_url = 'https://maps.googleapis.com/maps/api/geocode/json';
    $geocode_api_key = 'AIzaSyD5IF_rp6nUrCw6ficzMBgFApZtucUfjdk';

    // Separate latitude and longitude
    $coords = $_POST['acf']['field_59a75086b34b4'];
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
      $address = str_replace(', US', '', $address);
      $address = str_replace('US-', '', $address);

      // Save new meta data
      $_POST['acf']['field_59caa3fa222d5'] = $address;
      update_post_meta($post_id, 'city_state', $address);
      update_post_meta($post_id, '_city_state', 'field_59caa3fa222d5');

    } else {
      $error = new \WP_Error('geocode-error', 'There was an error with the reverse geocode.', var_dump($geocode_results));
      echo $error->get_error_message();
      exit;
    }
  }

  // Check if it needs to go to iNaturalist
  if ($update == true && $status == 'publish' && $inat_push == true && empty($inat_id)) {

    $inat_base_url = get_field('inat_base_url', 'option');
    $library = get_user_meta($user_id, 'library', false);

    // Find this user's library's account
    $inat_key = '13'; // Default to ecoEXPLORE account
    $inat_users = get_field('inaturalist_accounts', 'option');
    foreach ($inat_users as $key => $iuser) {
      if ($iuser['library_account_map'] == $library) {
        $inat_key = $key;
      }
    }

    $auth = $inat_users[$inat_key]['access_token'];

    // Was this at a HotSpot?
    if ($_POST['acf']['field_59d8e80c867f5'] == "Yes") {
      $hotspot_coords = get_field('hotspot_coordinates', 'option');
      $county = $_POST['acf']['field_5aa98106cedd9'];
      $hotspot = $_POST['acf']['field_59d8e86f867f6'];

      // Get the coordinates for this HotSpot
      foreach ($hotspot_coords as $ckey => $hsc) {
        if ($hsc['county'] == $county) {
          $county_key = $ckey;
          foreach ($hsc['hotspots'] as $hskey => $hs) {
            if ($hs['hotspot_name'] == $hotspot) {
              $coord_key = $hskey;
            }
          }
        }
      }
      $coords = $hotspot_coords[$county_key]['hotspots'][$coord_key]['coordinates'];
      $geoprivacy = "open";
    } else {
      // Use the coords provided by user in submission
      $coords = $_POST['acf']['field_59a75086b34b4'];
      $geoprivacy = "obscured";
    }

    // Separate latitude and longitude
    preg_match("/\((.*?),/", $coords, $lat, PREG_OFFSET_CAPTURE, 0);
    preg_match("/, (.*?)\)/", $coords, $long, PREG_OFFSET_CAPTURE, 0);

    // Post observation data
    $payload = [
      'method' => 'POST',
      'headers' => [
        'Authorization' => "Bearer $auth",
        "Content-Type" => "multipart/form-data"
      ],
      'body' => [
        'observation' => [
          'species_guess' => $_POST['post_title'],
          'description' => 'ecoEXPLORE Username: ' . $user->display_name,
          'observed_on_string' => $_POST['acf']['field_59a7511ab34b5'],
          'latitude' => $lat[1][0],
          'longitude' => $long[1][0],
          'location_is_exact' => false,
          'geoprivacy' => $geoprivacy
        ]
      ]
    ];
    // error_log(print_r($payload, true));
    // var_dump($payload);
    // exit;
    $post_obs = wp_remote_post($inat_base_url . '/observations.json', $payload);

    // error_log(print_r($post_obs, true));

    // If the POST is a success
    if ($post_obs['response']['code'] == '200') {

      // Get the returned JSON object
      $response_json = $post_obs['body'];
      $response = json_decode($response_json);
      $inat_id = $response[0]->id;

      // Save new meta data
      update_post_meta($post_id, 'inat_id', $inat_id);
      update_post_meta($post_id, 'inat_data', $response_json);

      // Gather data for photo
      $photo_id = $_POST['_thumbnail_id'];
      $photo_path = get_attached_file($photo_id);
      $photo_type = get_post_mime_type($photo_id);

      // Set up multipart form data
      $boundary = wp_generate_password( 24 );
      $body = '';
      $body .= '--' . $boundary . "\r\n";
      $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($photo_path) . "\"\r\n";
      $body .= 'Content-Type: ' . $photo_type . "\r\n\r\n";
      $body .= file_get_contents($photo_path) . "\r\n";
      $body .= '--' . $boundary . "\r\n";
      $body .= 'Content-Disposition: form-data; name="observation_photo[observation_id]"' . "\r\n";
      $body .= 'Content-Type: application/json' . "\r\n\r\n";
      // $body .= json_encode(['observation_photo' => ['observation_id' => $inat_id]]) . "\r\n";
      $body .= $inat_id . "\r\n";
      $body .= '--' . $boundary . '--' . "\r\n";

      // Post photo to iNaturalist
      $photo_payload = [
        'method' => 'POST',
        'timeout' => 10,
        'headers' => [
          'Authorization' => "Bearer $auth",
          'Content-Type' => "multipart/form-data; boundary=$boundary"
        ],
        'body' => $body
      ];

      // error_log(print_r($photo_payload, true));

      $post_photo = wp_remote_post($inat_base_url . '/observation_photos', $photo_payload);

      // Add observation to ecoEXPLORE project
      $project_payload = [
        'method' => 'POST',
        'headers' => [
          'Authorization' => "Bearer $auth"
        ],
        'body' => [
          'project_observation' => [
            'observation_id' => $inat_id,
            'project_id' => '6488'
          ]
        ]
      ];
      $post_project_observation = wp_remote_post($inat_base_url . '/project_observations', $project_payload);

    } else {
      $error = new \WP_Error('inat-error', 'There was an error posting to iNaturalist.', var_dump($post_obs));
      echo $error->get_error_message();
      exit;
    }
  }

}, 10, 3);


/**
 * Get observations from iNaturalist API
 * @param  string $username
 * @return object the observations from the iNat API. False if error or none.
 */
function get_observations($number = 4, $username = '') {
  // delete_transient('inat_obs_' . $username . $number);
  // Use WP transients for caching
  if ( false === ( $observations = get_transient( 'inat_obs_' . $username . $number ) ) ) {
    // iNaturalist API stuff
    $inat_base_url = get_field('inat_base_url', 'option');

    $params = [
      'per_page' => $number
    ];
    $args = [];

    // Set search query for a specific username
    if (!empty($username)) {
      $params['q'] = $username;
    }

    // Use WordPress's built in HTTP GET method
    $inat_url = add_query_arg($params, $inat_base_url . '/observations/project/ecoexplore.json');
    $observations = wp_remote_get($inat_url, $args);

    // Check the response code
  	$response_code = wp_remote_retrieve_response_code( $observations );

    if ( 200 != $response_code || ! empty( $response_message ) ) {
      // This didn't work
      $observations = false;
    } else {
      // Get the returned JSON object
      $response_json = $observations['body'];
      $observations = json_decode($response_json);

      set_transient( 'inat_obs_' . $username . $number, $observations, 1 * HOUR_IN_SECONDS );
    }
  }

  return $observations;
}


/**
 * This is a utility function that we ran once to get access tokens for each of the ecoEXPLORE accounts
 * It won't be needed again unless there are more accounts added
 */
function inat_get_access_tokens() {
  $inat_base_url = get_field('inat_base_url', 'option');
  $inat_app_id = get_field('inat_app_id', 'option');
  $inat_secret = get_field('inat_app_secret', 'option');

  // Get list of users
  $inat_users = get_field('inaturalist_accounts', 'option');

  foreach ($inat_users as &$user) {
    // Set up posted data for API request
    $payload = [
      'method' => 'POST',
      'body' => [
        'client_id' => $inat_app_id,
        'client_secret' => $inat_secret,
        'grant_type' => 'password',
        'username' => $user['login'],
        'password' => $user['password']
      ]
    ];

    // Use WordPress's built in HTTP POST method
    $authentication = wp_remote_post($inat_base_url . '/oauth/token', $payload);

    // If the POST is a success
    if ($authentication['response']['code'] == '200') {
      // Get the returned JSON object
      $response_json = $authentication['body'];
      $response = json_decode($response_json);

      // Set the access token for this user
      $access_token = $response->access_token;
      $user['access_token'] = $access_token;
    } else {
      var_dump($authentication);
    }
  }

  // Save access tokens for all users
  update_field('inaturalist_accounts', $inat_users, 'option');
}


/**
 * Uncomment this section to get access tokens for any newly added accounts.
 * Then load any page on the website. Comment out this section out when done.
 */
// add_action('init', function() {
//   inat_get_access_tokens();
// });
