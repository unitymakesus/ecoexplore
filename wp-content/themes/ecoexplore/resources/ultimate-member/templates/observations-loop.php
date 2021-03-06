<?php

namespace App;

function observations_loop($number, $username, $format = null, $wrapper = null) {
  $page = (!empty($_GET['pg']) ? $_GET['pg'] : 1);

  $recent_obs = new \WP_Query([
    'post_type' => 'observation',
    'posts_per_page' => $number,
    'author_name' => $username,
    'post_status' => ['publish', 'pending'],
    'posts_per_page' => 12,
    'paged' => $page
  ]);

  $inat_ids = array();

  if ($recent_obs->have_posts()) {
    while ($recent_obs->have_posts()) {
      $recent_obs->the_post();
      $obs_time = get_field('observation_time');
      $points = get_field('points');
      $inat_id = get_field('inat_id');
      $inat_ids[] = $inat_id;

      $at_hotspot = get_field('at_hotspot');
      if (is_array($at_hotspot)) {
        $at_hotspot = $at_hotspot[0];
      }

      // Was this at a hotspot?
      if ($at_hotspot == "Yes") {
        $location = get_field('which_hotspot');
      } else {
        $location = get_field('city_state');
      }

      $status = '';
      if (get_post_status() == 'pending') {
        $status = 'pending';
        $slug = 'pending' . get_the_ID();
      } else {
        $slug = get_post_field('post_name', get_the_ID());
      }

      if (!empty($wrapper)) {
        echo '<div class="' . $wrapper . '">';
      }
      ?>

      <div class="observation card <?php echo $format; ?> <?php echo $status; ?>">
        <?php if (stristr($format, 'has-modal') !== FALSE) { ?>
          <a href="#obs<?php echo $slug; ?>" class="mega-link modal-trigger" aria-hidden="true"></a>
        <?php } else { ?>
          <?php if (get_field('add_to_inaturalist') == 1 && !empty($inat_id)) { ?>
            <a href="https://www.inaturalist.org/observations/<?php echo $inat_id; ?>" target="_blank" rel="noopener" class="mega-link" aria-hidden="true"></a>
          <?php } ?>
        <?php } ?>

        <?php
          if (stristr($format, 'horizontal') !== FALSE) {
            ?>
              <div class="card-image">
                <?php the_post_thumbnail('thumbnail'); ?>
              </div>
            <?php
          } else {
            ?>
              <div class="card-image square" style="background-image: url('<?php echo get_the_post_thumbnail_url($recent_obs->ID, 'medium'); ?>')"></div>
            <?php
          }
        ?>

        <div class="card-stacked">
          <div class="card-content">
            <h3><?php the_title(); ?></h3>
            <ul>
              <?php if (!empty($location)) { ?>
                <li><i class="material-icons" aria-label="Where">location_on</i> <?php echo $location; ?></li>
              <?php } ?>
              <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($obs_time)); ?></li>
            </ul>
          </div>

          <div class="card-action">
            <?php if ($status == 'pending') { ?>
              <ul>
                <li><i class="material-icons" aria-hidden="true">hourglass_empty</i> This observation is pending review!</li>
              </ul>
            <?php } else { ?>
              <ul>
                <li><i class="material-icons" aria-label="Points">star_border</i> <?php echo $points; ?> Point<?php if ($points > 1) { echo 's'; }; ?></li>
                <?php if (get_field('add_to_inaturalist') == 1 && !empty($inat_id = get_field('inat_id'))) { ?>
                  <li><a href="https://www.inaturalist.org/observations/<?php echo $inat_id; ?>" target="_blank" rel="noopener"><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</a></li>
                <?php } ?>
              </ul>
            <?php } ?>
          </div>

        </div>
      </div>

      <div class="modal" id="obs<?php echo $slug; ?>">
        <div class="modal-content">
          <img src="<?php echo get_the_post_thumbnail_url($recent_obs->ID, 'medium'); ?>" alt=""
            srcset="<?php echo get_the_post_thumbnail_url($recent_obs->ID, 'large'); ?> 768w" />

          <h3><?php the_title(); ?></h3>

          <ul class="obs-meta">
            <?php if (!empty($location)) { ?>
              <li><i class="material-icons" aria-label="Where">location_on</i> <?php echo $location; ?></li>
            <?php } ?>
            <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($obs_time)); ?></li>
            <?php if ($status == 'pending') { ?>
              <li><i class="material-icons" aria-hidden="true">hourglass_empty</i> This observation is pending review!</li>
            <?php } else { ?>
              <li><i class="material-icons" aria-label="Points">star_border</i> <?php echo $points; ?> Point<?php if ($points > 1) { echo 's'; }; ?></li>
            <?php } ?>
          </ul>

          <ul class="collection">
            <?php
              if ($status !== 'pending') {
                wp_list_comments(
                  [
                    'style' => 'ul',
                    'type' => 'comment',
                    'callback' => __NAMESPACE__ . '\\comments_callback'
                  ], get_comments([
                    'post_id' => get_the_ID(),
                    'number' => -1
                  ])
                );
              }

              $content = trim(str_replace("&nbsp;","",strip_tags(get_the_content())));
              if (!empty($content)) {
                ?>
                  <li class="comment collection-item avatar">
                    <?php echo get_avatar(um_profile_id()); ?>
                    <div class="comment-author vcard">
                      <cite class="fn"><?php echo $username; ?></cite>
                      <span class="says">says:</span>
                    </div>
                    <?php the_content(); ?>
                    <div class="comment-meta commentmetadata"><?php echo the_time('F j, Y \a\t  j:i a'); ?></div>
                  </li>
                <?php
              } ?>
            </ul>

            <?php if ($status !== 'pending') {
              comment_form([
                'logged_in_as' => '',
                'title_reply' => ''
              ]);
            }
          ?>
        </div>
        <div class="modal-footer">
          <?php if (get_field('add_to_inaturalist') == 1 && !empty($inat_id = get_field('inat_id'))) { ?>
            <a href="https://www.inaturalist.org/observations/<?php echo $inat_id; ?>" target="_blank" rel="noopener" class="modal-action modal-close btn-secondary"><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</a>
          <?php } ?>
        </div>
      </div>

      <?php
      if (!empty($wrapper)) {
        echo '</div>';
      }
    }
    ?>

    <nav class="pagination" role="navigation">
      <h2 class="screen-reader-text">Posts navigation</h2>
      <div class="nav-links">
        <?php
        echo paginate_links( array(
          'base' => '%_%',
          'format' => '?pg=%#%',
          'current' => max( 1, $page ),
          'total' => $recent_obs->max_num_pages,
          'before_page_number' => '<span class="screen-reader-text">Page </span>',
          'prev_text' => '&laquo; Previous <span class="screen-reader-text">page</span>',
          'next_text' => 'Next <span class="screen-reader-text">page</span> &raquo;',
        ) );
        ?>
      </div>
    </nav>
    <?php
  }

  wp_reset_postdata();

  if (max(1, $page) == $recent_obs->max_num_pages) {

    // Show the observations from iNaturalist
    $observations = namespace\get_observations($number, $username);

    if (!empty($observations)) {
      foreach($observations as $o) {
        if (!in_array($o->id, $inat_ids)) {
          if (!empty($wrapper)) {
            echo '<div class="' . $wrapper . '">';
          }
          ?>

          <div class="observation card <?php echo $format; ?>">
            <?php if (stristr($format, 'has-modal') !== FALSE) { ?>
              <a href="#obs<?php echo $o->id; ?>" class="mega-link modal-trigger" aria-hidden="true"></a>
            <?php } else { ?>
              <a href="<?php echo $o->uri; ?>" target="_blank" rel="noopener" class="mega-link" aria-hidden="true"></a>
            <?php } ?>

            <?php
              if (stristr($format, 'horizontal') !== FALSE) {
                ?>
                  <div class="card-image">
                    <img src="<?php echo $o->photos[0]->square_url; ?>" alt="" />
                  </div>
                <?php
              } else {
                ?>
                  <div class="card-image square" style="background-image: url('<?php echo $o->photos[0]->medium_url; ?>')"></div>
                <?php
              }
            ?>

            <div class="card-stacked">
              <div class="card-content">
                <h3><?php echo $o->species_guess; ?></h3>
                <ul>
                  <li><i class="material-icons" aria-label="Where">location_on</i> <?php echo $o->place_guess; ?></li>
                  <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($o->created_at)); ?></li>
                </ul>
              </div>

              <div class="card-action">
                <ul>
                  <li><a href="<?php echo $o->uri; ?>" target="_blank" rel="noopener"><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="modal" id="obs<?php echo $o->id; ?>">
            <div class="modal-content">
              <img src="<?php echo $o->photos[0]->medium_url; ?>" alt=""
                srcset="<?php echo $o->photos[0]->large_url; ?>768w" />
              <h3><?php echo $o->species_guess; ?></h3>
              <ul class="obs-meta">
                <li><i class="material-icons" aria-label="Where">location_on</i> <?php echo $o->place_guess; ?></li>
                <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($o->created_at)); ?></li>
              </ul>
            </div>
            <div class="modal-footer">
              <a href="<?php echo $o->uri; ?>" target="_blank" rel="noopener" class="modal-action modal-close btn-secondary"><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</a>
            </div>
          </div>

          <?php
          if (!empty($wrapper)) {
            echo '</div>';
          }
        }
      }
    }
  }

  if (empty($observations) && $recent_obs->found_posts == 0) {
    ?>

    <div class="col-centered">
      <p class="center">You haven't submitted any observations yet.<br />
      <a href="/submit-new-observation/">Send us your first one now!</a></p>
    </div>

    <?php
  }
}
