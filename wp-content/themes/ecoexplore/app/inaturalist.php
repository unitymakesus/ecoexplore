<?php

namespace App;

/**
 * This file manages the iNaturalist API integration
 *
 */

// Add options page to keep track of iNaturalist users
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'iNaturalist Settings',
    'menu_title' => 'iNaturalist Settings',
    'menu_slug' => 'inaturalist-settings',
    'capability' => 'edit_posts',
    'redirect' => false
  ]);
}


// This is a utility function that we ran once to get access tokens for each of the ecoEXPLORE accounts
// It won't be needed again unless there are more accounts added
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

// Uncomment this section to get access tokens for any newly added accounts.
// Then load any page on the website. Comment this section out when done.
// add_action('init', function() {
//   inat_get_access_tokens();
// });
