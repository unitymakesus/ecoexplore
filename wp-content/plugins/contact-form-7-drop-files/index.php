<?php
/*
Plugin Name: Contact Form 7 Drag and Drop FIles Upload  - Multiple Files Upload
Plugin URI: https://codecanyon.net/user/rednumber/portfolio
Description: Allows you to add powerful Drag & Drop or choose Multiple Files Uploading area to your Form. It automatic Attachments in the email, you don’t need to do anything!
Author: Rednumber
Version: 1.7
Author URI: https://codecanyon.net/user/rednumber/portfolio
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define( 'CT7_DROPFILES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT7_DROPFILES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CT7_DROPFILES_DOMAIN', "cf7_dropfiles" );
include_once(ABSPATH.'wp-admin/includes/plugin.php');
/*
* Include pib
*/
if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
    include CT7_DROPFILES_PLUGIN_PATH."backend/index.php";
    include CT7_DROPFILES_PLUGIN_PATH."frontend/index.php";
}
/*
* Check plugin contact form 7
*/
class cf7_dropfiles_init {
    function __construct(){
       add_action('admin_notices', array($this, 'on_admin_notices' ) );
       register_activation_hook(__FILE__, array($this,'remove_files_cf7_activation'));
       add_action('remove_files_dropfiles', array($this,'do_remove_files'));
       load_plugin_textdomain( 'cf7_dropfiles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    function on_admin_notices(){
        if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
            echo '<div class="error"><p>' . __('Plugin need active plugin Contact Form 7', CT7_DROPFILES_DOMAIN) . '</p></div>';
        }
    }
    function remove_files_cf7_activation(){
         if (! wp_next_scheduled ( 'remove_files_dropfiles' )) {
            wp_schedule_event(time(), 'daily', 'remove_files_dropfiles');
         }
    }
    function do_remove_files(){
        $uploads = wp_upload_dir();
        $upload_path = $uploads['baseurl'].'/cf7-uploads-custom/*';
        $files = glob($upload_path); // get all file names
            foreach($files as $file){ // iterate files
              if(@is_file($file))
                @unlink($file); // delete file
            }
    }
}
new cf7_dropfiles_init;


