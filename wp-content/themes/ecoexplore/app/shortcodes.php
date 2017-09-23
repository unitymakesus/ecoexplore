<?php

namespace App;

/**
 * Custom calendar events feed Shortcode
 */
add_shortcode('row', function ($atts, $content = null) {
  extract( shortcode_atts( array(
    'color' => 'white',
  ), $atts) );

  ob_start();
  ?>

  <div class="row">
    <?php echo do_shortcode($content); ?>
  </div>

  <?php

  // Return output
  return ob_get_clean();
});

add_shortcode('col', function ($atts, $content = null) {
  extract( shortcode_atts( array(
    'width' => 's12',
  ), $atts) );

  ob_start();
  ?>

  <div class="col <?php echo $width; ?>">
    <?php echo do_shortcode($content); ?>
  </div>

  <?php

  // Return output
  return ob_get_clean();
});
