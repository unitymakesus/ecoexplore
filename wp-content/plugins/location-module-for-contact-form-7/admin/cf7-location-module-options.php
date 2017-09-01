<?php
/**
 *  Location Module (LITE) for Contact Form 7
 *
 *  Option Init, Validate, Page
 */

defined( 'ABSPATH' ) or die( 'Ops!' );

// create custom plugin settings menu
if(!function_exists('cf7_location_module_create_menu')){
    add_action('admin_menu', 'cf7_location_module_create_menu');

    function cf7_location_module_create_menu() {

        //create new sub menu
        add_options_page('Location Module (LITE) for Contact Form 7', 'Location Module for CF7', 'administrator','cf7-location-module-settings', 'cf7_location_module_settings_page' , plugins_url('/images/icon.png', __FILE__) );

        //call register settings function
        add_action( 'admin_init', 'register_cf7_location_module_settings' );
    }
}

if(!function_exists('register_cf7_location_module_settings')){
    function register_cf7_location_module_settings() {
        //register our settings
        register_setting( 'cf7_location_module-settings-group', 'default_lat','lat_validation' );           // Default Latitude
        register_setting( 'cf7_location_module-settings-group', 'default_lng','lng_validation' );           // Default Longitude
        register_setting( 'cf7_location_module-settings-group', 'default_zoom','zoom_validation' );         // Default Zoom
        register_setting( 'cf7_location_module-settings-group', 'default_api_key','maps_api_validation' );  // Default API KEY Google Maps
        register_setting( 'cf7_location_module-settings-group', 'mapsView','mapType_validation' );          // Map Type
        register_setting( 'cf7_location_module-settings-group', 'cf7_enable_reset','checkbox_validation' );    // Map Type
    }
}
// Validate Latitude input
if(!function_exists('lat_validation')){
    function lat_validation($input) {
        if (preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $input)){

            return $input;
            }

        add_settings_error( '', '', __('Latitude Invalid','location-module-for-contact-form-7'), 'error' );

        return '';
    }
}
// Validate longitude input
if(!function_exists('lng_validation')){
    function lng_validation($input){

        if (preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $input)){

            return $input;
        }

        add_settings_error( '', '', __('Longitude Invalid','location-module-for-contact-form-7'), 'error' );
        return '';
    }
}

// Validate zoom level
if(!function_exists('zoom_validation')){
    function zoom_validation($input){

        if(('0' <= $input) && ($input <= '15') ){

            return $input;
        }
        add_settings_error( '', '', __('Zoom Invalid, must be between 1 and 15','location-module-for-contact-form-7'), 'error' );
        return '1';
    }
}

// Validate Google Maps API Key
if(!function_exists('maps_api_validation')){
    function maps_api_validation($input){

        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬-]/', $input)){
            add_settings_error( '', '', __('API KEY INVALID','location-module-for-contact-form-7'), 'error' );
            return '';
        }else{
            return $input;
        }
    }
}
// Validate Latitude input
if(!function_exists('mapType_validation')){
    function mapType_validation($input) {
        if ($input =='roadmap' ){
            return $input;
        }elseif($input =='satellite'){
            return $input;
        }elseif($input =='hybrid'){
            return $input;
        }elseif($input =='terrain'){
            return $input;
        }

        add_settings_error( '', '', __('Map Type Invalid','location-module-for-contact-form-7'), 'error' );

        return '';
    }
}

if(!function_exists('checkbox_validation')){
    function checkbox_validation($input){
        return $input;
    }
}

if(!function_exists('cf7_location_module_settings_page')){
    function cf7_location_module_settings_page() {
        ?>
        <div class="wrap">
            <h2><strong>Location Module</strong>(LITE) for Contact Form 7</h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'cf7_location_module-settings-group' ); ?>
                <?php do_settings_sections( 'cf7_location_module-settings-group' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('Start Location','location-module-for-contact-form-7'); ?><br><?php echo __('Latitude / Longitude','location-module-for-contact-form-7'); ?></th>
                        <td><input type="text" name="default_lat" value="<?php echo esc_attr( get_option('default_lat','41.9102415') ); ?>" />
                            <input type="text" name="default_lng" value="<?php echo esc_attr( get_option('default_lng','12.3959126') ); ?>" />
                        </td>
                        <span><?php echo __('You can use this tool to get the coordinates for your start location','location-module-for-contact-form-7'); ?>&nbsp;<?php echo __('<a href="http://itouchmap.com/latlong.html" target="_blank" alt="itouchmap.com">itouchmap</a>','location-module-for-contact-form-7'); ?></span>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo __('Start Zoom Level','location-module-for-contact-form-7'); ?></th>
                        <td><input type="text" name="default_zoom" value="<?php echo esc_attr( get_option('default_zoom','10') ); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Google Maps API key:</th>
                        <td><input type="text" name="default_api_key" value="<?php echo esc_attr( get_option('default_api_key','') ); ?>" />To get an API Key please <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">check this</a></td>
                    </tr>
                    <tr>
                        <th scope="row">Google Maps View</th>
                        <td>
                            <select name="mapsView" type="text">
                                <option value="roadmap" <?php if(get_option('mapsView','roadmap') == 'roadmap'){echo 'selected="selected"';}?>>Roads </option>roadmap (default), satellite, hybrid and terrain.
                                <option value="satellite" <?php if(get_option('mapsView','roadmap') == 'satellite'){echo 'selected="selected"';}?>>Satellite </option>
                                <option value="hybrid" <?php if(get_option('mapsView','roadmap') == 'hybrid'){echo 'selected="selected"';}?>>Hybrid </option>
                                <option value="terrain" <?php if(get_option('mapsView','roadmap') == 'terrain'){echo 'selected="selected"';}?>>Terrain</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enable Reset Button</th>
                        <td>
                            <input type="checkbox" name="cf7_enable_reset" id="cf7_enable_reset" <?php checked('true', get_option('cf7_enable_reset'));?> value="true"><?php echo __('Show Reset button','location-module-for-contact-form-7'); ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
    <?php }
}
