<?php

namespace App;

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    // Add postMessage support
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->selective_refresh->add_partial('blogname', [
        'selector' => '.brand',
        'render_callback' => function () {
            bloginfo('name');
        }
    ]);
});

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});



/**
 * Admin dashboard widget displaying Observations Pending Review
 */
add_action( 'wp_dashboard_setup', function() {
  // Add widget to dashboard
  wp_add_dashboard_widget(
    'obs_pending_review',
    'Observations Pending Review',
    __NAMESPACE__ . '\\obs_pending_review_widget'
  );

  // Force widget to top of dashboard
 	global $wp_meta_boxes;  // Globalize the metaboxes array, this holds all the widgets for wp-admin
 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];  // Get the regular dashboard widgets array
 	$pending_widget_backup = array( 'obs_pending_review' => $normal_dashboard['obs_pending_review'] );  // Backup and delete our new dashboard widget from the end of the array
 	unset( $normal_dashboard['obs_pending_review'] );
 	$sorted_dashboard = array_merge( $pending_widget_backup, $normal_dashboard ); // Merge the two arrays together so our widget is at the beginning
 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;  // Save the sorted array back into the original metaboxes
});

// Display table of observations pending review
function obs_pending_review_widget() {
  $pendings_query = new \WP_Query( array(
    'post_type' => 'observation',
    'post_status' => 'pending',
    'posts_per_page' => -1,
    'orderby' => 'ID', // sort by order created, regardless of date
    'order' => 'DESC'
  ) );
  $pendings =& $pendings_query->posts;
  ?>

  <table class="wp-list-table widefat striped obs-pending">
    <thead>
      <tr>
        <th scope="col" id="title" class="manage-column column-title column-primary">Title</th>
        <th scope="col" id="author" class="manage-column column-author">Author</th>
        <td scope="col" id="date" class="manage-column column-date">Date</th>
      </tr>
    </thead>

    <tbody>
      <?php
        if ( !empty($pendings) ) {
          foreach ($pendings as $pending) {
            ?>
              <tr>
                <td class="title column-title column-primary">
                  <a href="<?php echo get_edit_post_link( $pending->ID, 'display' ); ?>" class="row-title">
                    <?php echo $pending->post_title; ?>
                  </a>
                </td>
                <td class="author column-author">
                  <?php echo get_the_author_meta('nicename', $pending->post_author); ?>
                </td>
                <td class="date column-date">
                  Last Modified
                  <br />
                  <abbr title="<?php echo $pending->post_modified; ?>"><?php echo date('Y/m/d', strtotime($pending->post_modified)); ?></abbr>
                </td>
              </tr>
            <?php
          }
        }
      ?>
    </tbody>
  </table>
<?php
}


/**
 * Add large meta box to show observation image in edit page
 */
function observation_image_meta_box() {
  add_meta_box(
      'image-notice',
      __( 'Observation Image' ),
      __NAMESPACE__.'\\observation_image_meta_box_callback',
      'observation',
      'normal',
      'high',
      null
  );
}

add_action( 'add_meta_boxes', __NAMESPACE__.'\\observation_image_meta_box' );

function observation_image_meta_box_callback( $post ) {
  $value = get_the_post_thumbnail_url( $post->ID, 'large', true );
  echo '<img style="max-width:100%; height:auto;" id="image_notice" name="image_notice" src="'.$value.'"></img>';
}


/**
 * Add sortable column to users list showing points
 */
function eco_user_table($column) {
  $column['running_points'] = 'Running Points';
  $column['total_points'] = 'Total Points';
  return $column;
}
add_filter('manage_users_columns', __NAMESPACE__.'\\eco_user_table');

function eco_sortable_columns($columns) {
  $columns['running_points'] = 'running_points';
  $columns['total_points'] = 'total_points';
  return $columns;
}
add_filter('manage_users_sortable_columns', __NAMESPACE__.'\\eco_sortable_columns');

function eco_add_points_user_table($val, $column, $user_id) {
  switch ($column) {
    case 'running_points':
      return get_field('running_points', "user_$user_id");
      break;
    case 'total_points':
      return get_field('total_points', "user_$user_id");
      break;
    default:
  }

  return $val;
}
add_action('manage_users_custom_column', __NAMESPACE__.'\\eco_add_points_user_table', 10, 3);

function eco_points_user_table_order($query) {
  global $wpdb, $current_screen;

  if ( 'users' != $current_screen->id ) {
    return;
  }

  $orderby = $query->get('orderby');
  $order = $query->get('order');

  if ($orderby == 'running_points' || $orderby == 'total_points') {
    $query->query_from .= " INNER JOIN {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key = '$orderby')";
    $query->query_orderby = " ORDER BY CAST(m1.meta_value AS unsigned) $order";
  }
}
add_action('pre_user_query', __NAMESPACE__.'\\eco_points_user_table_order');

function eco_admin_thumb_column($columns) {
  $columns['thumbnail'] = 'Thumbnail';
  return $columns;
}
add_filter('manage_observation_posts_columns', __NAMESPACE__.'\\eco_admin_thumb_column');

function eco_admin_thumb_column_view($column, $post_id) {
  switch ($column) {
    case 'thumbnail':
      echo get_the_post_thumbnail($post_id, 'thumbnail');
      break;
    default:
  }
}
add_action('manage_observation_posts_custom_column', __NAMESPACE__.'\\eco_admin_thumb_column_view', 10, 2);

?>
