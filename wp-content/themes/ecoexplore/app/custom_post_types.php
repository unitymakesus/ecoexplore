<?php

namespace App;

/**
 * This file adds custom post types to the theme.
 */

add_action( 'init', function() {
	register_post_type( 'observation',
		array('labels' => array(
				'name' => 'Observations',
				'singular_name' => 'Observation',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Observation',
				'edit' => 'Edit',
				'edit_item' => 'Edit Observation',
				'new_item' => 'New Observation',
				'view_item' => 'View Observation',
				'search_items' => 'Search Observations',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-camera',
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array( 'title', 'thumbnail', 'custom-fields', 'author', 'comments'),
			'public' => true,
			'has_archive' => true,
			'rewrite' => true,
			'query_var' => true
		)
	);

	register_post_type( 'prize',
		array('labels' => array(
				'name' => 'Prizes',
				'singular_name' => 'Prize',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Prize',
				'edit' => 'Edit',
				'edit_item' => 'Edit Prize',
				'new_item' => 'New Prize',
				'view_item' => 'View Prize',
				'search_items' => 'Search Prizes',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-products',
			'capability_type' => 'page',
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail'),
			'public' => true,
			'has_archive' => true,
			'rewrite' => true,
			'query_var' => true
		)
	);

	// register_post_type( 'badge',
	// 	array('labels' => array(
	// 			'name' => 'Badges',
	// 			'singular_name' => 'Badge',
	// 			'add_new' => 'Add New',
	// 			'add_new_item' => 'Add New Badge',
	// 			'edit' => 'Edit',
	// 			'edit_item' => 'Edit Badge',
	// 			'new_item' => 'New Badge',
	// 			'view_item' => 'View Badge',
	// 			'search_items' => 'Search Badges',
	// 			'not_found' =>  'Nothing found in the Database.',
	// 			'not_found_in_trash' => 'Nothing found in Trash',
	// 			'parent_item_colon' => ''
	// 		), /* end of arrays */
	// 		'exclude_from_search' => false,
	// 		'publicly_queryable' => true,
	// 		'show_ui' => true,
	// 		'show_in_nav_menus' => false,
	// 		'menu_position' => 8,
	// 		'menu_icon' => 'dashicons-awards',
	// 		'capability_type' => 'page',
	// 		'hierarchical' => true,
	// 		'supports' => array( 'title', 'editor', 'thumbnail'),
	// 		'public' => true,
	// 		'has_archive' => true,
	// 		'rewrite' => true,
	// 		'query_var' => true
	// 	)
	// );

	register_post_type( 'field-season',
		array('labels' => array(
				'name' => 'Field Seasons',
				'singular_name' => 'Field Season',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Field Season',
				'edit' => 'Edit',
				'edit_item' => 'Edit Field Season',
				'new_item' => 'New Field Season',
				'view_item' => 'View Field Season',
				'search_items' => 'Search Field Seasons',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-admin-site',
			'capability_type' => 'page',
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail'),
			'public' => true,
			'has_archive' => false,
			'rewrite' => true,
			'query_var' => true
		)
	);

	register_post_type( 'scientist',
		array('labels' => array(
				'name' => 'Scientists',
				'singular_name' => 'Scientist',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Scientist',
				'edit' => 'Edit',
				'edit_item' => 'Edit Scientist',
				'new_item' => 'New Scientist',
				'view_item' => 'View Scientist',
				'search_items' => 'Search Scientists',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			), /* end of arrays */
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-nametag',
			'capability_type' => 'page',
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail'),
			'public' => true,
			'has_archive' => false,
			'rewrite' => true,
			'query_var' => true
		)
	);

	register_taxonomy(
		'availability',
		'prize',
		array(
			'label' => __( 'Availability' ),
			'rewrite' => false,
			'hierarchical' => true,
		)
	);

	register_taxonomy(
		'limit',
		'prize',
		array(
			'label' => __( 'Limit' ),
			'rewrite' => false,
			'hierarchical' => true,
		)
	);
});

// Turn on comments by default for observations
add_filter( 'wp_insert_post_data', function( $data ) {
  if( $data['post_type'] == 'observation' ) {
    $data['comment_status'] = 'open';
  }

  return $data;
});
