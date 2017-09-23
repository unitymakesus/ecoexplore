<?php
function observations_loop($number, $username) {

  $recent_obs = new WP_Query([
    'post_type' => 'observation',
    'posts_per_page' => $number,
    'author_name' => $username
  ]);

  if ($recent_obs->have_posts()) {
    while ($recent_obs->have_posts()) {
      $recent_obs->the_post();
      $obs_time = get_field('observation_time');
      ?>

      <div class="observation card horizontal">
        <?php if (!empty($inat_id = get_field('inat_id'))) { ?>
          <a href="https://www.inaturalist.org/observations/<?php echo $inat_id; ?>" target="_blank" rel="noopener" class="mega-link" aria-hidden="true"></a>
        <?php } ?>

        <div class="card-image">
          <?php the_post_thumbnail('thumbnail'); ?>
        </div>

        <div class="card-stacked">
          <div class="card-content">
            <h3><?php the_title(); ?></h3>
            <ul>
              <li><i class="material-icons" aria-label="Where">location_on</i> Location</li>
              <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($obs_time)); ?></li>
            </ul>
          </div>

          <div class="card-action">
            <ul>
              <li><i class="material-icons" aria-label="Points">star_border</i> <?php the_field('points'); ?></li>
              <?php if (!empty($inat_id = get_field('inat_id'))) { ?>
                <li><a href="https://www.inaturalist.org/observations/<?php echo $inat_id; ?>" target="_blank" rel="noopener"><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>

      <?php
    }
  } else {
    echo $user_id;
    $observations = App\get_observations($number, $username);

    if (!empty($observations)) {
      foreach($observations as $o) {
        ?>

        <div class="observation card horizontal">
          <a href="<?php echo $o->uri; ?>" target="_blank" rel="noopener" class="mega-link" aria-hidden="true"></a>
          <div class="card-image">
            <img src="<?php echo $o->photos[0]->square_url; ?>" alt="" />
          </div>

          <div class="card-stacked">
            <div class="card-content">
              <h3><a href="<?php echo $o->uri; ?>" target="_blank" rel="noopener"><?php echo $o->species_guess ?></a></h3>
              <ul>
                <li><i class="material-icons" aria-label="Where">location_on</i> <?php echo $o->place_guess ?></li>
                <li><i class="material-icons" aria-label="When">access_time</i> <?php echo date("M j, Y", strtotime($o->created_at)); ?></li>
              </ul>
            </div>

            <div class="card-action">
              <ul>
                <li><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</li>
              </ul>
            </div>
          </div>
        </div>

        <?php
      }
    } else {
      ?>

      <p>You haven't submitted any observations yet.<br />
      <a href="/submit-new-observation/">Send us your first one now!</a></p>

      <?php
    }
  }

  wp_reset_postdata();

}
