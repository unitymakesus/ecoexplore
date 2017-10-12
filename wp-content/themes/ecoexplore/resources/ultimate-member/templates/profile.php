<?php
// This user's data
$user_id = um_profile_id();
$username = um_user('user_nicename');
$library = get_field('library', "user_$user_id");
$points = get_field('running_points', "user_$user_id");
$earned_badges = get_field('earned_badges', "user_$user_id");
$badges = [];

if (!empty($earned_badges)) {
	foreach ($earned_badges as $k => $badge) {
		$badges[$badge['badge']][] = $k;
	}
}

include('observations-loop.php');
?>

<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?> um-role-<?php echo um_user('role'); ?> ">

	<div class="um-form">

		<?php do_action('um_profile_before_header', $args ); ?>

		<?php if ( um_is_on_edit_profile() ) { ?>
			<form method="post" action="">
		<?php } ?>

			<?php do_action('eco_um_profile_header_cover_area', $args ); ?>

			<?php do_action('um_profile_header', $args ); ?>

			<?php do_action('um_profile_navbar', $args ); ?>
			<?php
				$nav = $ultimatemember->profile->active_tab;
				$subnav = ( get_query_var('subnav') ) ? get_query_var('subnav') : 'default';
			?>

			<div class="um-profile-body <?php echo $nav; ?> <?php echo $nav . '-' . $subnav; ?>">

				<?php if ($nav !== 'posts') { ?>
					<div class="row">
						<div class="col m7 updates">
							<?php
								$notes = array();

								$announcements = new WP_Query([
									'post_type' => 'post',
									'posts_per_page' => 10,
									'orderby' => 'date',
									'order' => 'DESC'
								]);
								$notes = array_merge($notes, $announcements->posts);

								// Recent comments on my observations
								$comments = get_comments([
									'post_author' => $user_id,
									'post_type' => 'observation',
									'orderby' => 'date',
									'order' => 'DESC'
								]);
								$notes = array_merge($notes, $comments);

								// Sort everything by date
								foreach ($notes as $key => $note) {
									if (get_class($note) == "WP_Comment") {
										$date[$key] = $note->comment_date;
									} else {
										$date[$key] = $note->post_date;
									}
								}
								array_multisort($date, SORT_DESC, $notes);

								// Show the last 10 things here!
								foreach (array_slice($notes, 0, 10) as $note) {
									if (get_class($note) == "WP_Post") {
										// Format for posts
										?>

										<div class="post card horizontal avatar">
											<div class="card-image">
												<?php echo get_avatar($note->post_author); ?>
											</div>

											<div class="card-content">
											  <div class="card-meta">
											    <span class="author"><?php echo get_user_meta($note->post_author, 'nickname', true); ?></span>
													<?php echo human_time_diff( strtotime($note->post_date), current_time('timestamp') ); ?> ago
												</div>

												<h3><?php echo $note->post_title; ?></h3>

												<?php echo apply_filters('the_content', $note->post_content); ?>
											</div>
										</div>

										<?php
									} elseif (get_class($note) == "WP_Comment") {
										// Format for comment
										$user = get_user_by('login', $note->comment_author);
										?>

										<div class="comment card horizontal">
											<a class="mega-link" href="<?php echo get_permalink($note->comment_post_ID); ?>"></a>
											
											<div class="card-image">
												<?php echo get_the_post_thumbnail($note->comment_post_ID, 'thumbnail'); ?>
											</div>

											<div class="card-content">
											  <div class="card-meta">
											    <?php echo $user->user_nicename; ?> commented
													<?php echo human_time_diff( strtotime($note->comment_date), current_time('timestamp') ); ?> ago
												</div>

												<h3><?php echo get_the_title($note->comment_post_ID); ?></h3>

											  <?php echo apply_filters('the_content', $note->comment_content); ?>
											</div>
									  </div>

										<?php
									}
								}
							?>

						</div>

						<div class="col m5">

							<!-- Badges, points, n stuff -->

							<?php if (!empty($points)) { ?>
								<h2><span class="points-val"><?php echo $points; ?></span> Points</h2>
							<?php } ?>

							<?php if (!empty($badges)) { ?>

									<h3>My Badges</h3>

									<div class="row block-grid up-s1 up-m2 up-l4 badges">
										<?php foreach ($badges as $badge => $val) { ?>

											<div class="col">
												<?php
													$season = get_page_by_title($badge, 'OBJECT', 'field-season');
													$badge = get_field('badge', $season->ID);
												?>

												<img class="alignleft badge" src="<?php echo $badge['sizes']['thumbnail']; ?>" alt="<?php echo get_the_title($season->ID); ?> Season Badge" />

												<?php
													if (count($val) > 1) {
														echo '<div class="btn-floating">' . count($val) . '</div>';
													}
												?>
											</div>

										<?php } ?>
									</div>

							<?php } ?>

							<?php
								// Get progress towards current badge!
								$now = date('Ymd', current_time('timestamp'));

	              $this_season = new WP_Query([
	                'post_type' => 'field-season',
	                'posts_per_page' => -1,
	                'meta_query' => [
	                  'relation' => 'AND',
	                  [
	                    'key' => 'start_date',
	                    'compare' => '<=',
	                    'value' => $now,
	                    'type' => 'NUMERIC'
	                  ], [
	                    'key' => 'end_date',
	                    'compare' => '>=',
	                    'value' => $now,
	                    'type' => 'NUMERIC'
	                  ]
	                ]
	              ]);

								$season_id = $this_season->posts[0]->ID;
								$after = get_field('start_date', $season_id);
								$before = get_field('end_date', $season_id);

								$season_observations = new WP_Query([
									'post_type' => 'observation',
									'posts_per_page' => -1,
									'post_author' => $user_id,
									'date_query' => [
										[
											'after' => [
												'year' => date('Y', $after),
												'month' => date('m', $after),
												'day' => date('j', $after)
											],
											'before' => [
												'year' => date('Y', $before),
												'month' => date('m', $before),
												'day' => date('j', $before)
											],
											'inclusive' => true,
										]
									],
									'meta_query' => [
										[
											'key' => 'field_season_observation',
											'compare' => '=',
											'value' => TRUE
										]
									]
								]);
							?>

							<h3>Earn Your <?php echo $this_season->posts[0]->post_title; ?> Badge:</h3>

							<div class="progress">
								<div class="determinate" style="width: <?php echo ($season_observations->found_posts/4)*100; ?>%"></div>
							</div>

							<ol class="badge-progress">
								<li>You have submitted <strong><?php echo $season_observations->found_posts; ?> of 3</strong> observations for the current field season. <a href="/submit-new-observation/">Submit an observation!</a></li>
								<li>Complete the <?php echo $this_season->posts[0]->post_title; ?> challenge:<br />
									<?php echo get_field('challenge', $season_id); ?>
								</li>
								<li>Save the date and come to the season ending event:
									<?php
										$featured = tribe_get_events([
											'posts_per_page' => 1,
											'eventDisplay' => 'upcoming',
											'featured' => true
										]);

										if (!empty($featured)) :
											foreach ($featured as $event) :
												$event_id = $event->ID;
												$ecats = wp_get_post_terms($event_id, TribeEvents::TAXONOMY);
												$venue = tribe_get_venue_details($event_id);

												foreach ($ecats as $ecat) {
													if ($ecat->slug != 'season-ending-event') {
														$season = $ecat->slug;
													}
												}
											?>

											<div class="event <?php echo $season; ?>">
			                  <div class="date">
			                    <span class="month"><?php echo date('M', strtotime($event->EventStartDate)); ?></span>
			                    <span class="day"><?php echo date('j', strtotime($event->EventStartDate)); ?></span>
			                  </div>
			                  <div class="title">
			                    <h4><a href="<?php echo get_the_permalink($event_id); ?>" class="event-title"><?php echo get_the_title($event_id); ?></a></h4>
													<div class="tribe-events-event-meta">
														<?php echo $venue['linked_name']; ?>
													</div>
													<a class="tribe-events-read-more" href="<?php echo get_the_permalink($event_id); ?>">Find out more &raquo;</a>
			                  </div>
			                </div>

											<?php
											endforeach;
										endif;
									?>
								</li>
							</ol>
						</div>
					</div>
				<?php } else { ?>
					<div class="row block-grid up-s1 up-m3 up-l4">

						<?php observations_loop(-1, $username, 'has-modal solid', 'col'); ?>

					</div>
				<?php } ?>

			</div>

		<?php if ( um_is_on_edit_profile() ) { ?>
			</form>
		<?php } ?>

	</div>

</div>
