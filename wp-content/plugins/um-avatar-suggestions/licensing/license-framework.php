<?php
/**
 * SuitePlugins License Framework Beta.
 */
class SP_License_Framework {

    /**
     * Version
     *
     * @var string $version
     *
     * @since 0.0.1
     */
    public $version;

    /**
     * Plugins with licenses.
     */
    public $products = array();

    /**
     * Store URL
     */
    public $store_url = 'https://suiteplugins.com';
    /**
     * Constructor.
     *
     * @since 0.0.1
     */
    public function __construct() {
        $this->products = apply_filters( 'suiteplugins_license_plugins', array() );
        $this->hooks();
    }

    public function hooks() {
        //License
		add_action( 'admin_init',  array( $this, 'plugin_updater' ), 0 );
		add_action( 'admin_init',  array( $this, 'register_license_option' ) );
		add_action( 'admin_init',  array( $this, 'activate_license' ) );
		add_action( 'admin_init',  array( $this, 'deactivate_license' ) );
    }

    /**
     * Register Menu
     */
    public function register_menu() {

    }

    /**
     * Check if product is valid.
     *
     * @param  array  $plugin Product Data.
     *
     * @return boolean Return a boolean if the product is missing a required key and value.
     */
    public function is_product_valid( $product = array() ) {
        // Validation keys.
        $keys = array( 'product_name', 'product_url', 'product_version', 'product_path', 'product_author', 'licence_key' );

        // Set default validation variable.
        $valid = true;

        foreach ( $keys as $key ) {
            if ( empty( $product[ $key ] ) {
                $valid = false;
                break;
            }
        }

        // Return false if product invalid.
        if ( false === $valid ) {
            return false;
        }

        return true;
    }
    /**
     * Get validated products.
     *
     * @return array.
     */
    public function get_products() {
        $index    = 0;
        $products = array();
        if ( ! empty( $this->products ) ) {
            foreach( $this->products as $product ) {
                // Product is not validated then bump it from the list.
                if ( ! $this->is_product_valid( $product ) ) {
                    unset( $this->products[ $index ] );
                    $index++;
                }
            }
        }

        return $products;
    }

    /**
     * Plugin Updater.
     *
     * Checks if plugin needs updating.
     *
     * @since 0.0.1
     */
    public function plugin_updater() {
        $products = $this->get_products();
        if ( ! empty( $products ) ) {
            foreach( $products as $product ) {

                // retrieve our license key from the DB.
        		$license_key = trim( get_option( $product['licence_key'] ) );

        		// setup the updater.
        		$edd_updater = new EDD_SL_Plugin_Updater( $product['product_url'], $product['product_path'], array(
        				'version' 	=> $product['product_version'], // current version number.
        				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB).
        				'item_name' => $product['product_name'], 	// name of this plugin.
        				'author' 	=> 'SuitePlugins', // author of this plugin.
        			)
        		);
            }
        }
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

	/**
	 * Activate the license.
	 *
	 * @since 0.0.1
	 */
	public function activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_gallery_pro_license_activate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

            $products = $this->get_products();
            if ( ! empty( $products ) ) {

                foreach( $products as $product ) {

                    // retrieve the license from the database.
        			$license = trim( get_option( $product['licence_key'] ) );

        			// data to send in our API request.
        			$api_params = array(
        				'edd_action'	=> 'activate_license',
        				'license' 		=> $license,
        				'item_name' 	=> urlencode( $product['product_name'] ), // the name of our product in EDD
        				'url'	   		=> home_url(),
        			);

        			// Call the custom API.
        			$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        			// make sure the response came back okay.
        			if ( is_wp_error( $response ) ) {
        				continue;
        			}

        			// decode the license data
        			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

        			// $license_data->license will be either "valid" or "invalid"
        			update_option( $product['licence_key'] . '_license_status', $license_data->license );
                }
            }
		}
	}


	/**
	 * Deactivate licenses
	 *
	 * @since 0.0.1
	 */
	function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_gallery_pro_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'um_gallery_pro_license_nonce', 'um_gallery_pro_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

            $products = $this->get_products();
            if ( ! empty( $products ) ) {

                foreach( $products as $product ) {
        			// retrieve the license from the database
        			$license = trim( get_option( $product['licence_key'] ) );

        			// data to send in our API request
        			$api_params = array(
        				'edd_action'	=> 'deactivate_license',
        				'license' 		=> $license,
        				'item_name' 	=> urlencode( $product['product_name'] ), // the name of our product in EDD
        				'url'	   	    => home_url(),
        			);

        			// Call the custom API.
        			$response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        			// make sure the response came back okay
        			if ( is_wp_error( $response ) ) {
        				return false;
        			}

        			// decode the license data
        			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

        			// $license_data->license will be either "deactivated" or "failed"
        			if ( 'deactivated' == $license_data->license ) {
        				delete_option( $product['licence_key'] . '_license_status' );
        			}
                }
            }
		}
	}


}
