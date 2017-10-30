<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
  wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);

  if (is_page('register') || is_page('register-group')) {
    wp_enqueue_script( 'password-strength-meter' );
  }

  // Prevent conflicting map JS from loading on events page
  if (!is_page('submit-new-observation')) {
    wp_dequeue_script('cf7-mapjs');
  }

  if (!is_page('locations')) {
    wp_dequeue_script('google-maps-builder-gmaps');
    wp_dequeue_script('google-maps-builder-clusterer');
    wp_dequeue_script('google-maps-builder-infowindows');
    wp_dequeue_script('google-maps-builder-maps-icons');
    wp_dequeue_script('google-maps-builder-plugin-script');
    wp_dequeue_script('google-maps-builder-plugin-script-pro');
  }
}, 100);

/**
 * Make sure the Map scripts show up on event pages
 */
remove_filters_with_method_name('wp_print_scripts', 'check_for_multiple_google_maps_api_calls', 10);

/**
 * Remove avatar picker from all pages except user edit page
 */
add_action('init', function() {
  if (!isset($_GET['um_action']) && $_GET['um_action'] !== 'edit' ) {
    remove_filters_with_method_name('wp_footer', 'add_modal_content', 10);
  }
});


/**
 * Change WordPress email sender name and email
 */
add_filter( 'wp_mail_from_name', function( $original_email_from ) {
  return 'ecoEXPLORE';
});

add_filter('wp_mail_from', function($original_email_from) {
  return 'ecoexplore@ncarboretum.org';
});


/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'logged_in' => __('Logged In', 'sage'),
        'logged_out' => __('Logged Out', 'sage'),
        'footer_left' => __('Footer Left Nav', 'sage'),
        'footer_right' => __('Footer Right Nav', 'sage')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    // add_editor_style(asset_path('styles/main.css'));

    /**
     * Enable logo uploader in customizer
     */
     add_image_size('ecoexplore-logo', 175, 67, false);
     add_image_size('ecoexplore-logo-2x', 350, 134, false);
     add_theme_support('custom-logo', array(
       'size' => 'ecoexplore-logo-2x'
     ));

     /**
      * Add image sizes
      */
     add_image_size('event-square', 400, 400, true);
     add_image_size('event-landscape', 600, 400, true);

}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ];
    register_sidebar([
        'name'          => __('Primary', 'sage'),
        'id'            => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name'          => __('Footer', 'sage'),
        'id'            => 'sidebar-footer'
    ] + $config);
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });
});
