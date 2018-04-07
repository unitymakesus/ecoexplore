<?php
class cf7_dropfiles_frontend {
	function __construct(){
		add_action("wp_enqueue_scripts",array($this,"add_lib"));
	}
	 function add_lib(){
            wp_enqueue_style( 'cf7-dropfiles', CT7_DROPFILES_PLUGIN_URL."frontend/css/cf7-dropfiles.css" );
            wp_enqueue_script( 'cf7-dropfiles', CT7_DROPFILES_PLUGIN_URL."frontend/js/dropfiles-cf7.js",array("jquery"),time() );
            wp_localize_script('cf7-dropfiles','cf7_dropfiles',array("url_plugin"=>CT7_DROPFILES_PLUGIN_URL,'ajax_url' => admin_url( 'admin-ajax.php' )));
    }
}
new cf7_dropfiles_frontend;