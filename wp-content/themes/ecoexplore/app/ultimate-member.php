<?php

// Replace avatar upload with avatar picker

add_filter('um_user_photo_menu_view', function($items) {
  $items = [
    '<a href="#" class="um-manual-trigger" data-modal="um_avatar_picker">' . __( 'Pick an Avatar','um-avatar-suggestions' ) . '</a>',
    '<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
  ];

  return $items;
});


// Profile tabs\

add_filter('um_user_profile_tabs', function($tabs) {
  $tabs = [
    'main' => [
      'name' => 'Updates',
      'icon' => 'um-faicon-pencil'
    ],
    'posts' => [
      'name' => 'Observations',
      'icon' => 'um-faicon-camera'
    ]
  ];

  return $tabs;
});


// Add observation button to profile header

add_action('um_after_header_meta', function() {
  echo '<a class="btn-submit-obs waves-effect waves-light btn-primary"><i class="material-icons left" aria-hidden="true">photo_camera</i> Submit Observation</a>';
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

				// if ( $ultimatemember->fields->editing ) {

					$items = array(
								'<a href="#" class="um-manual-trigger" data-parent=".um-cover" data-child=".um-btn-auto-width">'.__('Change cover photo','ultimate-member').'</a>',
								'<a href="#" class="um-reset-cover-photo" data-user_id="'.um_profile_id().'">'.__('Remove','ultimate-member').'</a>',
								'<a href="#" class="um-dropdown-hide">'.__('Cancel','ultimate-member').'</a>',
					);

					echo $ultimatemember->menu->new_ui( 'bc', 'div.um-cover', 'click', $items );

				// }
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

      <!-- <a class="btn-primary"><i class="material-icons left" aria-hidden="true">photo_camera</i> Submit Observation</a> -->

		</div>

		<?php

	}

}, 9, 1);
