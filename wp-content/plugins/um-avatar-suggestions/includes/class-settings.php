<?php
/**
 * UM Avatar Suggestions Settings.
 *
 * @since   0.0.1
 * @package UM_Avatar_Suggestions
 */
 if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
 	require_once( 'EDD_SL_Plugin_Updater.php' );
 }
require_once dirname( __FILE__ ) . '/../vendor/cmb2/init.php';

/**
 * UM Avatar Suggestions Settings class.
 *
 * @since 0.0.1
 */
class UM_Avatar_Suggestions_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    UM_Avatar_Suggestions
	 * @since  0.0.1
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	public $key = 'um_avatar_suggestions_settings';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $metabox_id = 'um_avatar_suggestions_settings_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

    /**
	 * License key
	 * @var string
	 */
	public $um_license_key = 'um_avatar_suggestions_license_key';

	/**
	 * License Status
	 * @var string
	 */
	public $um_license_status = 'um_avatar_suggestions_license_status';

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

		// Set our title.
		$this->title = esc_attr__( 'Settings', 'um-avatar-suggestions' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

		// Hook in our actions to the admin.
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );

        //License
		add_action( 'admin_init',  array( $this, 'plugin_updater' ), 0 );
		add_action( 'admin_init',  array( $this, 'register_license_option' ) );
		add_action( 'admin_init',  array( $this, 'activate_license' ) );
		add_action( 'admin_init',  array( $this, 'deactivate_license' ) );
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  0.0.1
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since  0.0.1
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page(
			'edit.php?post_type=um_avatar',
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since  0.0.1
	 */
	public function admin_page_display() {
        $active_tab = 'general';
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		}
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=general' ); ?>" class="nav-tab <?php echo 'general' == $active_tab  ? 'nav-tab-active' : ''; ?>"><?php _e( 'General', 'um-learndash' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=license' ); ?>" class="nav-tab <?php echo 'license' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'License', 'um-learndash' ); ?></a>
			</h2>
            <?php
			if ( 'license' == $active_tab ) {
				echo '<form method="post" action="options.php">';
				$this->license_fields();
				submit_button( __( 'Update License', 'um-learndash' ), 'primary','submit', true );
				echo '</form>';
			} else {
				cmb2_metabox_form( $this->metabox_id, $this->key );
			} // end if/else
			?>
		</div>
		<?php
	}

	/**
	 * Add custom fields to the options page.
	 *
	 * @since  0.0.1
	 */
	public function add_options_page_metabox() {

		// Add our CMB2 metabox.
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		) );

		// Add your fields here.
		$cmb->add_field( array(
			'name'    => __( 'Enable Avatar Suggestions', 'um-avatar-suggestions' ),
			'desc'    => __( 'If checked, users will be able to see the avatar picker.', 'um-avatar-suggestions' ),
			'id'      => 'enable', // No prefix needed.
			'type'    => 'checkbox',
		) );
	}

    /**
	 * License Fields setup.
	 *
	 * @since 1.0.0
	 */
	public function license_fields() {
		$license 	= get_option( $this->um_license_key );
		$status 	= get_option( $this->um_license_status );
		settings_fields( $this->um_license_key . '_field' );
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'License Key', 'um-avatar-suggestions' ); ?>
					</th>
					<td>
						<input id="um_license_key" name="<?php echo esc_attr( $this->um_license_key ); ?>"  type="text"  class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
						<label class="description" for="um_license_key"><?php _e( 'Enter your license key', 'um-avatar-suggestions'  ); ?></label>
					</td>
				</tr>
				<?php if ( false !== $license ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'Activate License' ); ?>
						</th>
						<td>
							<?php if ( false !== $status   && 'valid' == $status ) { ?>
								<span style="color:green;line-height: 25px;"><?php _e( 'active', 'um-avatar-suggestions'  ); ?></span>
								<?php wp_nonce_field( 'um_avatar_suggestions_license_nonce', 'um_avatar_suggestions_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_avatar_suggestions_license_deactivate" value="<?php _e( 'Deactivate License', 'um-avatar-suggestions'  ); ?>"/>
							<?php } else {
								wp_nonce_field( 'um_avatar_suggestions_license_nonce', 'um_avatar_suggestions_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_avatar_suggestions_license_activate" value="<?php _e( 'Activate License', 'um-avatar-suggestions'  ); ?>"/>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}

	public function plugin_updater() {
		// retrieve our license key from the DB
		$license_key = trim( get_option( $this->um_license_key ) );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( UM_AVATAR_SUGGESTIONS_STORE_URL, UM_AVATAR_SUGGESTIONS_PATH, array(
				'version' 	=> UM_AVATAR_SUGGESTIONS_VERSION, // current version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_name' => UM_AVATAR_SUGGESTIONS_ITEM_NAME, 	// name of this plugin
				'author' 	=> 'SuitePlugins', // author of this plugin
			)
		);
	}

	public function register_license_option() {
		register_setting( $this->um_license_key . '_field', $this->um_license_key, array( $this, 'sanitize_license' ) );
	}
	public function sanitize_license( $new ) {
		$old = get_option( $this->um_license_key );
		if ( $old && $old != $new ) {
			delete_option( $this->um_license_status ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	public function activate_license() {
		// listen for our activate button to be clicked
		if ( isset( $_POST['um_avatar_suggestions_license_activate'] ) ) {
			// run a quick security check
			if ( ! check_admin_referer( 'um_avatar_suggestions_license_nonce', 'um_avatar_suggestions_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'	=> 'activate_license',
				'license' 		=> $license,
				'item_name' 	=> urlencode( UM_AVATAR_SUGGESTIONS_ITEM_NAME ), // the name of our product in EDD
				'url'	   	    => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_AVATAR_SUGGESTIONS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			update_option( $this->um_license_status, $license_data->license );

		}
	}


	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_avatar_suggestions_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'um_avatar_suggestions_license_nonce', 'um_avatar_suggestions_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'	=> 'deactivate_license',
				'license' 		=> $license,
				'item_name' 	=> urlencode( UM_AVATAR_SUGGESTIONS_ITEM_NAME ), // the name of our product in EDD
				'url'	   	=> home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_AVATAR_SUGGESTIONS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( 'deactivated' == $license_data->license ) {
				delete_option( $this->um_license_status );
			}
		}
	}
}
