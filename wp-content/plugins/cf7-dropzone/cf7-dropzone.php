<?php
/**
 * Plugin Name: Contact Form 7 Dropzone
 * Plugin URI: https://www.unitymakes.us/
 * Description: Add Dropzone fields to Contact Form 7
 * Version: 1.0.0
 * Author: Alisa Herr
 * Author URI: https://www.unitymakes.us
 * License: GPL2
 */

// Require plugin assets
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_script( 'dropzone', plugin_dir_url( __FILE__ ) . 'scripts/dropzone.min.js', array('jquery'), '5.2.0', true );
  wp_enqueue_script( 'cf7-dropzone', plugin_dir_url( __FILE__ ) . 'scripts/cf7-dropzone.js', array(), '1.0', true );
  wp_enqueue_style( 'dropzone', plugin_dir_url( __FILE__ ) . 'styles/dropzone.css', array(), '5.2.0' );
});

// Create new dropzone field type
add_action( 'wpcf7_init', function() {
   wpcf7_add_form_tag('dropzone', 'cf7dropzone_form_tag_handler');
});

function cf7dropzone_form_tag_handler( $tag ) {
  $output = '<div class="dropzone" id="cf7dropzone"><div class="dz-message">Drop files here or click to upload. <span class="note">(You can choose to upload up to 5 photos at a time. Make sure each file size does not exceed 10 MB.)</span></div></div>';

  return $output;
}
