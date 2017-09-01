<?php
/**
 * UM Avatar Suggestions Api.
 *
 * @since   0.0.1
 * @package UM_Avatar_Suggestions
 */

/**
 * UM Avatar Suggestions Api.
 *
 * @since 0.0.1
 */
class UM_Avatar_Suggestions_Api {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   UM_Avatar_Suggestions
	 */
	protected $plugin = null;

	/**
	 * Post Type.
	 *
	 * @since 0.0.1
	 *
	 * @var string $plugin Post Type.
	 */
	public $post_type = 'um_avatar';

	/**
	 * Meta Kay.
	 *
	 * @since 0.0.1
	 *
	 * @var string $plugin Meta Key.
	 */
	public $meta_key = '_um_avatar_id';

	/**
	 * Transient cache key.
	 *
	 * @since 0.0.1
	 *
	 * @var  string $cache Key to save transient.
	 */
	protected $cache = 'um_avatar_list';

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  UM_Avatar_Suggestions $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {
		add_filter( 'um_user_photo_menu_edit', array( $this, 'avatar_picker_link' ), 12, 1 );
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'wp_footer', array( $this, 'add_modal_content' ) );
		add_action( 'save_post_' . $this->post_type, array( $this, 'cache_avatars' ), 12, 1 );
		add_action( 'wp_ajax_um_avatar_suggestions_ajax', array( $this, 'ajax' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		// Add the additional 1 to priority to bust um_get_avatar filter.
		add_filter('get_avatar', array( $this, 'um_suggestions_get_avatar' ), 999991, 5);
	}

	/**
	 * Perform AJAX actions.
	 *
	 * @author suiteplugins
	 *
	 * @since 1.0.0
	 *
	 * @return JSON Object
	 */
	public function ajax() {
		// Check if Nonce correct.
		check_ajax_referer( 'um-avatar-nonce', 'security' );

		// Check if correct do is set.
		if ( ! isset( $_POST['do'] ) ) {
			wp_send_json_error( array( 'message' => 'failed' ) );
		}

		$user_id = get_current_user_id();
		$results = array();
		// Switch between do actions.
		switch( $_POST['do'] ) {
			case 'set_avatar':
				$avatar_id = ! empty( $_POST['avatar_id'] ) ? $_POST['avatar_id'] : '';
				if ( 'default' === $avatar_id ) {
					delete_user_meta( $user_id, $this->meta_key );
				} else {
					if ( get_post_status( $avatar_id ) ) {
						$post_thumbnail_id = get_post_thumbnail_id( $avatar_id );
						$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, array( 96, 96 ) );
						if ( $image_attributes ) {
							$results['thumb'] = $image_attributes[0];
						}
						update_user_meta( $user_id, $this->meta_key, $avatar_id );
					}
				}
			break;
		}

		wp_send_json_success( $results );
	}
	public function avatar_picker_link( $items ) {

		if ( ! $this->is_enabled() ) {
			return $items;
		}
		// Bail early if no avatar found.
		if ( empty( $this->get_avatar_list() ) ) {
			return $items;
		}
	    $inserted = array( '<a href="#" class="um-manual-trigger" data-modal="um_avatar_picker">' . __( 'Pick an Avatar','um-avatar-suggestions' ) . '</a>' ); // Not necessarily an array

	    array_splice( $items, 1, 0, $inserted ); // splice in at position 3
	    return $items;
	}

	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Avatars', 'post type general name', 'um-avatar-suggestions' ),
			'singular_name'      => _x( 'Avatar', 'post type singular name', 'um-avatar-suggestions' ),
			'menu_name'          => _x( 'Avatars', 'admin menu', 'um-avatar-suggestions' ),
			'name_admin_bar'     => _x( 'Avatar', 'add new on admin bar', 'um-avatar-suggestions' ),
			'add_new'            => _x( 'Add New', 'avatar', 'um-avatar-suggestions' ),
			'add_new_item'       => __( 'Add New Avatar', 'um-avatar-suggestions' ),
			'new_item'           => __( 'New Avatar', 'um-avatar-suggestions' ),
			'edit_item'          => __( 'Edit Avatar', 'um-avatar-suggestions' ),
			'view_item'          => __( 'View Avatar', 'um-avatar-suggestions' ),
			'all_items'          => __( 'All Avatars', 'um-avatar-suggestions' ),
			'search_items'       => __( 'Search Avatars', 'um-avatar-suggestions' ),
			'parent_item_colon'  => __( 'Parent Avatars:', 'um-avatar-suggestions' ),
			'not_found'          => __( 'No avatars found.', 'um-avatar-suggestions' ),
			'not_found_in_trash' => __( 'No avatars found in Trash.', 'um-avatar-suggestions' )
		);

		$args = array(
			'labels'             => $labels,
            'description'        => __( 'Avatar Post Type.', 'um-avatar-suggestions' ),
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'avatar' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'thumbnail' )
		);

		register_post_type( $this->post_type, $args );
	}

	public function cache_avatars( $post_id = 0 ) {
		global $wpdb;

		// If this is just a revision then bail.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$avatar_query = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='%s' AND post_status = 'publish'", $this->post_type ) );

		$avatars      = array();

		if ( ! empty( $avatar_query ) ) {
			foreach ( $avatar_query as $post_id ) {
				$post_thumbnail_id = get_post_thumbnail_id( $post_id );
				if ( $post_thumbnail_id ) {
					$avatars[ $post_id ] = wp_get_attachment_thumb_url( $post_thumbnail_id, 'full' );
				}
			}
		}
		// Cache the avatars for a month.
		set_transient( $this->cache, $avatars, 4 * WEEK_IN_SECONDS );
	}

	/**
	 * Get Avatar List.
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_avatar_list() {
		global $wpdb;
		if ( false === ( $avatars = get_transient( $this->cache ) ) ) :

			$avatar_query = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='%s' AND post_status = 'publish' ", $this->post_type ) );

			$avatars      = array();

			if ( ! empty( $avatar_query ) ) {
				foreach ( $avatar_query as $post_id ) {
					$post_thumbnail_id = get_post_thumbnail_id( $post_id );
					if ( $post_thumbnail_id ) {
						$avatars[ $post_id ] = wp_get_attachment_thumb_url( $post_thumbnail_id, 'full' );
					}
				}
			}

			// Cache the avatars for a month.
			set_transient( $this->cache, $avatars, 4 * WEEK_IN_SECONDS );

		endif;

		return $avatars;
	}

	/**
	 * Get Modal.
	 */
	public function add_modal_content() {
		$avatars = $this->get_avatar_list();
		um_fetch_user( um_get_requested_user() );

			$content = um_convert_tags( um_get_option('profile_desc') );
			$user_id = um_user('ID');
			$url = um_user_profile_url();

			if ( um_profile('profile_photo') ) {
				$avatar = um_user_uploads_uri() . um_profile('profile_photo');
			} else {
				$avatar = um_get_default_avatar_uri();
			}

		um_reset_user();
		?>
		<style type="text/css">
			.um-avatar-suggestions-list {
				list-style: none;
				margin: 0px;
				padding: 0px;
			}
			.um-avatar-suggestions-list li{
				display: inline-block;
			}
			.um-avatar-suggestions-list li img{
				border-radius: 50%;
				width: 100px;
				height: 100px;
			}
		</style>
		<div id="um_avatar_picker" style="display:none">

			<div class="um-modal-header">
				<?php _e('Avatar Picker','um-avatar-suggestions'); ?>
			</div>

			<div class="um-modal-body">
				<ul class="um-avatar-suggestions-list">
					<li id="um_avatar_list_item_default">
						<a href="#" data-id="default"><img src="<?php echo esc_url( $avatar ); ?>" /></a>
						<div class="um-avatar-type"><?php _e( 'Default', 'um-avatar-suggestions' ); ?></div>
					</li>
					<?php if ( ! empty( $avatars ) ) : ?>
						<?php foreach( $avatars as $id => $src ) { ?>
							<li id="um_avatar_list_item_<?php echo absint( $id ); ?>">
								<a href="#" data-id="<?php echo absint( $id ); ?>"><img src="<?php echo esc_url( $src ); ?>" /></a>
								<div class="um-avatar-message">&nbsp;</div>
							</li>
						<?php } ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			jQuery( '.um-avatar-suggestions-list li a').on( 'click', function( event ) {
		   		event.preventDefault();
		   		var avatar_id = jQuery( this ).data( 'id' );
		   		jQuery( '#um_avatar_list_item_' + avatar_id ).find( '.um-avatar-message' ).text( '<?php echo __( 'Saving...', 'um-avatar-suggestions' ); ?>' );
		   		jQuery.ajax({
					type: 'post',
					url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					data: {
						'action': 'um_avatar_suggestions_ajax',
						'do': 'set_avatar',
						'avatar_id': avatar_id,
						'security': '<?php echo wp_create_nonce( 'um-avatar-nonce' ); ?>'
					},
		   			cache: false,
					success: function(response) {
						jQuery( '#um_avatar_list_item_' + avatar_id ).find( '.um-avatar-message' ).text( '<?php echo __( 'Updated', 'um-avatar-suggestions' ); ?>' );

						if ( response.data.thumb ) {
							jQuery( '.um-avatar-uploaded,.um-avatar-suggested' ).attr( 'src', response.data.thumb );
						}

						if ( 'default' === avatar_id ) {
							jQuery( '.um-avatar-uploaded,.um-avatar-suggested' ).attr( 'src', '<?php echo $this->get_user_uploaded_avatar(); ?>' );
						}

						setTimeout(function () {
							jQuery( '.um-avatar-message' ).html('&nbsp;');
							um_remove_modal();
						}, 1000 );
					}
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Get User Uploaded Avatar.
	 *
	 * @param  integer $user_id
	 * @param  integer $size
	 *
	 * @return string
	 */
	public function get_user_uploaded_avatar( $user_id = 0, $size = 96 ) {

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		um_fetch_user( $user_id );

		if ( um_profile('profile_photo') ) {
			$avatar = um_user_uploads_uri() . um_profile('profile_photo');
		} else {
			$avatar = um_get_default_avatar_uri();
		}

		return $avatar;
	}

	/**
	 * Get user UM suggestion avatar avatars.
	 * @param  string $avatar
	 * @param  string $id_or_email
	 * @param  string $size
	 * @param  string $avatar_class
	 * @param  string $default
	 * @param  string $alt
	 * @hooks  filter `get_avatar`
	 *
	 * @return string returns avatar in image html elements.
	 */
	function um_suggestions_get_avatar($avatar = '', $id_or_email='', $size = '96', $avatar_class = '', $default = '', $alt = '') {
		if ( is_numeric( $id_or_email ) ) {
			$user_id = (int) $id_or_email;
		} elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) ) {
			$user_id = $user->ID;
		} elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
			$user_id = (int) $id_or_email->user_id;
		}

		if ( empty( $user_id ) ) {
			return $avatar;
		}

		// Get suggested avatar.
		$picked_avatar = get_user_meta( $user_id, $this->meta_key, true );

		// Check if suggested avatar set and bail if not set.
		if ( ! $picked_avatar ) {
			return $avatar;
		}

		// Check if avatar still available in published list.
		$avatars = $this->get_avatar_list();

		// Get the post IDs from array keys.
		$avatars = array_keys( $avatars );

		// If no post IDs found or the picked avatar was unpublished by admin then bail.
		if ( empty( $avatars ) || ! in_array( $picked_avatar, $avatars, false ) ) {
			return $avatar;
		}

		// Get post thumbnail ID.
		$post_thumbnail_id = get_post_thumbnail_id( $picked_avatar );

		if ( ! $post_thumbnail_id ) {
			return $avatar;
		}

		// Get avatar URL.
		$thumb = wp_get_attachment_image_src( $post_thumbnail_id, array( $size, $size ) );

		$avatar_url = $thumb[0];

		um_fetch_user( $user_id );

		$image_alt = apply_filters("um_avatar_image_alternate_text",  um_user("display_name") );

		$avatar = '<img src="' . $avatar_url  .'" class="gravatar avatar avatar-' . $size . ' um-avatar-suggested um-avatar" width="' . $size . '" height="' . $size . '" alt="' . $image_alt . '" />';

		return $avatar;
	}

	/**
	 * Add Post Meta Box to Avatar Screen.
	 */
	public function add_meta_boxes() {
		// Add this metabox to every selected post
		add_meta_box(
			sprintf('wp_plugin_template_%s_section', $this->post_type ),
			sprintf('%s Guide', ucwords(str_replace("_", " ", $this->post_type ) ) ),
			array( $this, 'add_inner_meta_boxes'),
			$this->post_type
		);
	}

	public function add_inner_meta_boxes( $post ) {
		echo __( 'To create an avatar for the avatar modal, give the avatar a title to find it in the admin and add a featured image.<br />
				 The featured image will be used inside of the avatar picker as long as it is published. For best results, use an image that is in the shape of a square.' );
	} // END public function add_inner_meta_boxes($post)

	/**
	 * Wrapper function around cmb2_get_option
	 * @since  0.1.0
	 * @param  string $key     Options array key
	 * @param  mixed  $default Optional default value
	 * @return mixed           Option value
	 */
	public function get_option( $key = '', $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( $this->plugin->settings->key, $key, $default );
		}
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( $this->plugin->settings->key, $default );
		$val = $default;
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
		return $val;
	}

	public function is_enabled() {
		return ( 'on' === $this->get_option( 'enable' ) );
	}
}
