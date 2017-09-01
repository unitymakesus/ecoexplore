<?php
/**
 * Plugin Name: Location Module (Lite) for Contact Form 7
 * Plugin URI: http://wordpress.org/extend/plugins/cf7-location-module/
 * Description: Location Module (Lite) is a Contact Form 7 Plugin extension to let users search their location, adjust and send it.
 * Version: 1.0.11
 * Author: Nicola Bavaro
 * Author URI: https://www.nicolabavaro.it
 * License: GPLv2 or later
 * Text Domain: location-module-for-contact-form-7
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) or die( 'Ops!' );

/**
 * Check if Contact Form 7 is installed if not the plugin will be deactivated.
 */
if(!function_exists('cf7_location_module_admin_notices')){

    add_action('admin_notices', 'cf7_location_module_admin_notices');
    function cf7_location_module_admin_notices() {
        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
            echo "<div class='notice notice-error'><p>". __('Plugin Contact Form 7 not found or not activated. Please install and activate it to use the plugin "Location Module (Lite) for Contact Form 7"','location-module-for-contact-form-7') ." </p></div>";
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
        if (get_option('default_api_key','')=='') {
            echo "<div class='notice notice-warning is-dismissible'><p>". __('<strong>Location Module (Lite) for Contact Form 7</strong> use Google Maps, please get a valid Google Maps API KEY and fill the field on plugin setting page.','location-module-for-contact-form-7') ." </p></div>";
        }
    }
}

/**
 * Options INIT
 */
include_once plugin_dir_path(__FILE__).'/admin/cf7-location-module-options.php';

/**
 * Enqueue Plugin Script and Style
 */
if(!function_exists('cf7_location_module_load_scripts')){

    add_action( 'wp_enqueue_scripts', 'cf7_location_module_load_scripts' );
    function cf7_location_module_load_scripts() {

        $maps_api = get_option('default_api_key','');

        // JS
        wp_enqueue_script( 'maps-js','https://maps.google.com/maps/api/js?key='.$maps_api,array('jquery'),NULL);

        wp_enqueue_script( 'gmaps-js', plugins_url( 'js/gmaps.min.js', __FILE__ ),array('maps-js'));
        wp_enqueue_script( 'location-js', plugins_url( 'js/cf7-location-module.js', __FILE__ ),array('gmaps-js'),null,false);

        // CSS
        wp_enqueue_style( 'location-style',  plugins_url('css/cf7-location-module.min.css', __FILE__ ) );

        // PASSING THE OPTION TO JS
        // THX OTTO
        // http://ottopress.com/2010/passing-parameters-from-php-to-javascripts-in-plugins/
        $params = array(
            'deflat' => get_option('default_lat','41.9102415'),
            'deflng' => get_option('default_lng','12.3959126'),
            'defzoom' => get_option('default_zoom','10'),
            'def_err_msg'=>__('Error while geocoding your address. Please retry.','location-module-for-contact-form-7'),
            'mapsView' => get_option('mapsView','roadmap'),
        );

        wp_localize_script( 'location-js', 'CF7LM', $params );
    }
}

/**
 * Module Init for location and location*
 */
if(!function_exists('cf7_location_module_init')){

    add_action( 'wpcf7_init', 'cf7_location_module_init' , 20 );
    function cf7_location_module_init(){

        if(function_exists('wpcf7_add_form_tag')){
            /* Shortcode handler */
            wpcf7_add_form_tag( 'location', 'cf7_location_module_shortcode_handler', true );
            wpcf7_add_form_tag( 'location*', 'cf7_location_module_shortcode_handler', true );
            return;
        }

        if(function_exists('wpcf7_add_shortcode')){
            /* Shortcode handler */
            wpcf7_add_shortcode( 'location', 'cf7_location_module_shortcode_handler', true );
            wpcf7_add_shortcode( 'location*', 'cf7_location_module_shortcode_handler', true );
        }
    }
}

/**
 * Location Shortcode
 */
if(!function_exists('cf7_location_module_shortcode_handler')){
    function cf7_location_module_shortcode_handler( $tag ) {

        if(WPCF7_VERSION >= '4.6') {
            $tag = new WPCF7_FormTag($tag);
        }else{
            $tag = new WPCF7_Shortcode($tag);
        }

        if ( empty( $tag->name ) )
            return '';

        // get validation error from CF7
        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        // if there is an error i add this class
        if ( $validation_error )
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );

        $atts['id'] = 'cf7-geocode-address';

        $atts['type'] = 'text';

        // check if field is required
        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }
        // Validation
        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $value = (string) reset( $tag->values );

        if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        $value = $tag->get_default_option( $value );

        if ( ! is_string( $value ) ) {
            $value = json_encode( $value );
        }

        $value = wpcf7_get_hangover( $tag->name, $value );

        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts( $atts );

        $set_button_text = get_option('button_send_text');

        if($set_button_text==''){
            $set_button_text = __('SET','location-module-for-contact-form-7');
        }
        // If Show Reset Button is checked i show the reset button
        if(get_option('cf7_enable_reset','false')==true){
            $reset_bt = '<a id="cf7-geocode-reset">Reset</a>';
        }else{
            $reset_bt = '';
        }

        // http://hpneo.github.io/gmaps/examples/basic.html
        // REMEMBER VALIDATION OF CF7 WORKS ONLY WITH SPAN!!!!!!!
        $html = sprintf('
        <span id="cf7-location-address" class="wpcf7-form-control-wrap %1$s">
            <input %2$s value="" /><span id="cf7-geocode-buttons"><a id="geocode-link" class="cf7-loc-button">%3$s</a>%4$s</span>
        </span>

        <div id="cf7-location-map">
        </div>

        <input type="hidden" id="cf7-location-lat" name="cf7-location-lat" value="">
        <input type="hidden" id="cf7-location-lng" name="cf7-location-lng" value="">
        <input type="hidden" id="cf7-location-url" name="cf7-location-url" value="">
        ', sanitize_html_class( $tag->name ), $atts, $set_button_text, $reset_bt );

        return $html;
    }
}

/**
 * CF7 Location Module Validation filter
 * http://contactform7.com/2015/03/28/custom-validation/
 */
if(!function_exists('cf7_location_module_validation_filter')){

    add_filter( 'wpcf7_validate_location', 'cf7_location_module_validation_filter', 20, 2 );
    add_filter( 'wpcf7_validate_location*', 'cf7_location_module_validation_filter', 20, 2 );
    function cf7_location_module_validation_filter( $result, $tag ) {

        // Backward Comp
        if(WPCF7_VERSION >= '4.6') {
            $tag = new WPCF7_FormTag($tag);
        }else{
            $tag = new WPCF7_Shortcode($tag);
        }

        $type = $tag->type;

        $name = $tag->name;

        // Get POST Value
        $value = isset( $_POST[$name] ) ? (string) $_POST[$name] : '';
        $posted_lat = isset( $_POST['cf7-location-lat'] ) ? (string) $_POST['cf7-location-lat'] : '';
        $posted_lng = isset( $_POST['cf7-location-lng'] ) ? (string) $_POST['cf7-location-lng'] : '';
        $posted_url =  isset( $_POST['cf7-location-url'] )? (string) $_POST['cf7-location-url'] : '';

        // Check if required field
        if ( $tag->is_required() && '' == $value ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }
        // If HTML tags are found invalidate the form
        if($value !=strip_tags($value)){
            $result->invalidate( $tag, __('No HTML tags are allowed','location-module-for-contact-form-7') );
        }

        // If latitude and longitude are empty
        if ($posted_lat == ''&&$posted_lng=='') {
            if($tag->is_required()){
                $result->invalidate($tag, __('No Location set, enter your address then press "SET" to get the location.', 'location-module-for-contact-form-7'));
            }
        }
        return $result;
    }
}

/**
 * CF7 Location Module Tag generator
 */
add_action( 'wpcf7_admin_init', 'cf7_location_module_tag_generator', 45 );

function cf7_location_module_tag_generator() {
    if (class_exists('WPCF7_TagGenerator')) {
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'location', __( 'Location Module', 'location-module-for-contact-form-7' ), 'cf7_location_module_panel' );
    } else if (function_exists('wpcf7_add_tag_generator')) {
        wpcf7_add_tag_generator( 'location', __( 'Location Module', 'location-module-for-contact-form-7' ),	'cf7_location_module_panel', 'cf7_location_module_panel' );
    }
}

add_filter('wpcf7_collect_mail_tags', 'cf7_location_module_tag');
function cf7_location_module_panel($contact_form, $args = '') {
    $args = wp_parse_args( $args, array() );

    $description = __( "Generate a form tag for an address text field with a map below it that show the geocoded position of given address. For more details, see %s.", 'location-module-for-contact-form-7' );
    $desc_link = wpcf7_link( __( 'https://wordpress.org/plugins/location-module-for-contact-form-7', 'location-module-for-contact-form-7' ), __( 'the plugin page on WordPress.org', 'location-module-for-contact-form-7' ), array('target' => '_blank' ) );

    include_once plugin_dir_path(__FILE__).'/admin/cf7-location-module-admin.php';

}

/**
 * Add a all-fields option to the Mail tab's merge tags
 * @since 2.0
 * @param array $mailtags
 * @return mailtags
 */
function cf7_location_module_tag( $mailtags = array() ) {

    $mailtags[] = 'cf7-location-lng';
    $mailtags[] = 'cf7-location-lat';
    $mailtags[] = 'cf7-location-url';

    return $mailtags;
}

/**
 * Add Support Link
 *
 * CODEX: https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
 */
add_filter( 'plugin_action_links', 'cf7_location_module_add_settings_link', 10, 5 );
function cf7_location_module_add_settings_link( $actions, $plugin_file )
{
    static $plugin;

    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $donate_link = array(
            'settings' => '<a href='. admin_url( "options-general.php?page=cf7-location-module-settings" ) .' target="_self">'. __('Settings','location-module-for-contact-form-7') .'</a>',
            'donate'=>'<a href="http://www.nicolabavaro.it/donate/" target="_blank">'. __('Support This Plugin','location-module-for-contact-form-7') .'</a>'
        );
        $actions = array_merge($donate_link, $actions);

    }
    return $actions;
}
