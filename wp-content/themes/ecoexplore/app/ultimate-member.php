<?php

// Include subscribers in list of authors for observations
add_filter( 'wp_dropdown_users_args', function( $query_args, $r ) {

  $query_args['who'] = '';
  return $query_args;

}, 10, 2);

// Replace avatar upload with avatar picker
add_filter('um_user_photo_menu_edit', __NAMESPACE__ . '\\eco_user_photo_menu');
add_filter('um_user_photo_menu_view', __NAMESPACE__ . '\\eco_user_photo_menu');

function eco_user_photo_menu($items) {
  if (current_user_can('administrator')) {
    $items = [
      '<a href="#" class="um-manual-trigger" data-parent=".um-profile-photo" data-child=".um-btn-auto-width">'.__('Upload photo','ultimate-member').'</a>',
      '<a href="#" class="um-manual-trigger" data-modal="um_avatar_picker">' . __( 'Pick an Avatar','um-avatar-suggestions' ) . '</a>',
      '<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
    ];
  } else {
    $items = [
      '<a href="#" class="um-manual-trigger" data-modal="um_avatar_picker">' . __( 'Pick an Avatar','um-avatar-suggestions' ) . '</a>',
      '<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
    ];
  }

  return $items;
}

// Profile tabs
add_filter('um_user_profile_tabs', function($tabs) {
  $tabs = [
    'main' => [
      'name' => 'Updates',
      'icon' => 'um-faicon-comment'
    ],
    'posts' => [
      'name' => 'Observations',
      'icon' => 'um-faicon-camera'
    ],
    'edit' => [
      'name' => 'Edit Profile',
      'icon' => 'um-faicon-pencil'
    ]
  ];

  return $tabs;
});

add_filter('um_profile_menu_link_edit', function($nav_link) {
	$nav_link = add_query_arg( [
    'profiletab' => 'edit',
    'um_action' => 'edit'
  ], $nav_link );

  return $nav_link;
});

// Add observation button to profile header
add_action('um_after_header_meta', function() {
  if ( !isset($_GET['um_action']) || $_GET['um_action'] !== 'edit' ) {
    echo '<a class="btn-submit-obs btn-primary" href="/submit-new-observation/"><i class="material-icons" aria-hidden="true">photo_camera</i> Submit Observation</a>';
  }
});

add_action('um_profile_before_header', function() {
  if ( isset( $_GET['notice'] ) && $_GET['notice'] == 'incomplete_access' && $_GET['profilttab'] !== 'edit' ) {
    wp_redirect('/user/?profiletab=edit&um_action=edit');
  	exit;
  }
});

// Let users edit header cover image without going to edit profile page
add_action('eco_um_profile_header_cover_area', function($args) {
	global $ultimatemember;

	if ( $args['cover_enabled'] == 1 ) {

		$default_cover = um_get_option('default_cover');

		$overlay = '<span class="um-cover-overlay">
			<span class="um-cover-overlay-s">
				<ins>
					<i class="um-faicon-picture-o"></i>
					<span class="um-cover-overlay-t">'.__('Change your cover photo','ultimate-member').'</span>
				</ins>
			</span>
		</span>';

	?>

		<div class="um-cover <?php if ( um_profile('cover_photo') || ( $default_cover && $default_cover['url'] ) ) echo 'has-cover'; ?>" data-user_id="<?php echo um_profile_id(); ?>" data-ratio="<?php echo $args['cover_ratio']; ?>">

			<?php do_action('um_cover_area_content', um_profile_id() ); ?>

			<?php

				if ( $ultimatemember->fields->editing ) {

					$items = array(
								'<a href="#" class="um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width">'.__('Change cover photo','ultimate-member').'</a>',
								'<a href="#" class="um-reset-cover-photo" data-user_id="'.um_profile_id().'">'.__('Remove','ultimate-member').'</a>',
								'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
					);

					echo $ultimatemember->menu->new_ui( 'bc', 'div.um-cover', 'click', $items );

				}
			?>

			<?php $ultimatemember->fields->add_hidden_field( 'cover_photo' ); ?>

			<div class="um-cover-e">

  			<?php echo $overlay; ?>

				<?php if ( um_profile('cover_photo') ) { ?>

				<?php

				if( $ultimatemember->mobile->isMobile() ){
					if ( $ultimatemember->mobile->isTablet() ) {
						echo um_user('cover_photo', 1000);
					} else {
						echo um_user('cover_photo', 300);
					}
				} else {
					echo um_user('cover_photo', 1000);
				}

				?>

				<?php } elseif ( $default_cover && $default_cover['url'] ) {

					$default_cover = $default_cover['url'];

					echo '<img src="'. $default_cover . '" alt="" />';

				} else {

					if ( !isset( $ultimatemember->user->cannot_edit ) ) { ?>

					<a href="#" class="um-cover-add um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width"><span class="um-cover-add-i"><i class="um-icon-plus um-tip-n" title="<?php _e('Upload a cover photo','ultimate-member'); ?>"></i></span></a>

				<?php }

				} ?>

			</div>

		</div>

		<?php

	}

}, 9, 1);
