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
  // If new observation, let's geocode the coordinates to get town, state
  if ($update == false) {

  }
  // If updating observation, check if it needs to go to iNaturalist
  if ($update == true) {
    $inat_push = $_POST['acf']['field_59c27c4ddb770'];

    // If iNat push is true...
    if ($inat_push == true && empty($_POST['acf']['field_59c2cdc4b2634'])) {
      $inat_base_url = get_field('inat_base_url', 'option');
      $user_id = $_POST['post_author'];
      $library = get_user_meta($user_id, 'library', false);
      // THIS IS JUST FOR TESTING
      $library = 'test';

      // Get list of iNat accounts
      $inat_users = get_field('inaturalist_accounts', 'option');

      // Find this user's library's account
      foreach ($inat_users as $key => $user) {
        if ($user['library_account_map'] == $library) {
          $inat_key = $key;
        }
      }

      $auth = $inat_users[$inat_key]['access_token'];
      // var_dump($auth);

      // Get latitude and longitude separated
      $coords = $_POST['acf']['field_59a75086b34b4'];
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
            'observed_on_string' => $_POST['acf']['field_59a7511ab34b5'],
            'latitude' => $lat[1][0],
            'longitude' => $long[1][0],
            'location_is_exact' => false
          ]
        ]
      ];
      $post_obs = wp_remote_post($inat_base_url . '/observations.json', $payload);

      // If the POST is a success
      if ($post_obs['response']['code'] == '200') {
        // Get the returned JSON object
        $response_json = $post_obs['body'];
        $response = json_decode($response_json);
        $inat_id = $response[0]->id;

        // Save new meta data
        $_POST['acf']['field_59c2cdc4b2634'] = $inat_id;
        update_post_meta($post_id, 'inat_id', $inat_id);
        update_post_meta($post_id, '_inat_id', 'field_59c2cdc4b2634');
        update_post_meta($post_id, 'inat_data', $response_json);

      } else {
        var_dump($post_obs);
      }
    }


      $inat_base_url = get_field('inat_base_url', 'option');
      $user_id = $_POST['post_author'];
      $library = get_user_meta($user_id, 'library', false);
      // THIS IS JUST FOR TESTING
      $library = 'test';

      // Get list of iNat accounts
      $inat_users = get_field('inaturalist_accounts', 'option');

      // Find this user's library's account
      foreach ($inat_users as $key => $user) {
        if ($user['library_account_map'] == $library) {
          $inat_key = $key;
        }
      }

      $auth = $inat_users[$inat_key]['access_token'];

            $inat_id = '8014211';
            $photo_id = $_POST['_thumbnail_id'];
            $photo_path = get_attached_file($photo_id);
            $photo_type = get_post_mime_type($photo_id);

            /// AAARG
            function scaled_image_path($attachment_id, $size = 'thumbnail') {
              $file = get_attached_file($attachment_id, true);
              if (empty($size) || $size === 'full') {
                // for the original size get_attached_file is fine
                return realpath($file);
              }
              if (! wp_attachment_is_image($attachment_id) ) {
                return false; // the id is not referring to a media
              }
              $info = image_get_intermediate_size($attachment_id, $size);
              if (!is_array($info) || ! isset($info['file'])) {
                return false; // probably a bad size argument
              }

              return realpath(str_replace(wp_basename($file), $info['file'], $file));
            }

            $photo_sized = scaled_image_path($photo_id, 'thumbnail');

            var_dump($photo_sized);
            // $photo_file = @fopen($photo_path, 'r');
            // var_dump($photo_file);
            $photo_size = filesize($photo_sized);
            var_dump($photo_size);
            // $photo_data = fread($photo_file, $photo_size);

            $boundary = wp_generate_password( 24 );

            // $file = '@' . $photo_sized . ';filename=' . basename($photo_sized) . ';type=' . get_post_mime_type($photo_id);

            $body = '';
            $body .= '--' . $boundary . "\r\n";
            $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($photo_sized) . "\"\r\n";
            $body .= 'Content-Type: ' . $photo_type . "\r\n\r\n";
            $body .= file_get_contents($photo_sized) . "\r\n";
            $body .= '--' . $boundary . "\r\n";
            // $body .= 'Content-Disposition: form-data; name="observation_photo"' . "\r\n";
            $body .= 'Content-Type: application/json' . "\r\n\r\n";
            $body .= json_encode(['observation_photo' => ['observation_id' => $inat_id]]) . "\r\n";
            $body .= '--' . $boundary . '--' . "\r\n";

            // Post photo to observation
            $photo_payload = [
              'method' => 'POST',
              'timeout' => 10,
              'headers' => [
                'Authorization' => "Bearer $auth",
                'Content-Type' => "multipart/form-data; boundary=$boundary"
                // 'Content-Type' => "multipart/form-data;"
              ],
              'body' => $body
              // 'body' => [
              //   'observation_photo' => [
              //     'observation_id' => $inat_id
              //   ],
                // 'file' => ''
                // 'file' => $file
                // 'file' => base64_encode(file_get_contents($photo_sized))
                // 'file' => wp_get_attachment_url($_POST['_thumbnail_id']),
                // 'file' => base64_encode(file_get_contents($photo_path)),
              // ]
            ];
            // var_dump($photo_payload);
            echo '<pre>';
            // print_r($photo_payload);
            print_r($body);
            echo '</pre>';
            // echo "<pre>$body</pre>";
            $post_photo = wp_remote_post($inat_base_url . '/observation_photos', $photo_payload);
            // $post_photo = wp_remote_post('https://requestb.in/1gdsso01', $photo_payload);
            // $post_photo = wp_remote_post('http://posttestserver.com/post.php', $photo_payload);
            var_dump($post_photo);
  }
  exit;
}, 10, 3);


/**
 * Get observations from iNaturalist API
 * @param  string $username
 * @return object the observations from the iNat API. False if error or none.
 */
function get_observations($username = '') {
  // iNaturalist API stuff
  $inat_base_url = get_field('inat_base_url', 'option');

  $params = [
    'per_page' => 4
  ];
  $args = [];

  // Set search query for a specific username
  if (!empty($username)) {
    $params['q'] = $username;
  }

  // Use WordPress's built in HTTP GET method
  $inat_url = add_query_arg($params, $inat_base_url . '/observations/project/ecoexplore.json');
  $observations = wp_remote_get($inat_url, $args);

  // If the POST is a success
  if ($observations['response']['code'] == '200') {
    // Get the returned JSON object
    $response_json = $observations['body'];
    $response = json_decode($response_json);

    return $response;
  }

  return false;
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
