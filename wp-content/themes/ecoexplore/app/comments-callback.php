<?php

namespace App;

/**
* Customize the display of comments
* @param  [type] $comment [description]
* @param  [type] $args    [description]
* @param  [type] $depth   [description]
* @return [type]          [description]
*/
function comments_callback($comment, $args, $depth) {
  if ( 'div' === $args['style'] ) {
    $tag       = 'div';
    $add_below = 'comment';
  } else {
    $tag       = 'li';
    $add_below = 'div-comment';
  }?>
  <<?php echo $tag; ?> <?php comment_class('collection-item avatar', empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>">
  <?php
    if ( $args['avatar_size'] != 0 ) {
      echo get_avatar( $comment, $args['avatar_size'] );
    }
  ?>

  <div class="comment-author vcard">
    <?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author() ); ?>
  </div>

  <?php comment_text(); ?>

  <div class="comment-meta commentmetadata">
    <?php
      printf(
        __('%1$s at %2$s'),
        get_comment_date(),
        get_comment_time()
      );
    ?>
  </div>

  <?php
}
