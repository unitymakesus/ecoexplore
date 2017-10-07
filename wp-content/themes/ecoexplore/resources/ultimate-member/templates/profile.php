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
						<div class="col m8">
							<h3>Notes from ecoEXPLORE</h3>

							<?php
								$announcements = new WP_Query([
									'post_type' => 'posts',
									'posts_per_page' => 2,
									'orderby' => 'date',
									'order' => 'DESC'
								]);

								// var_dump($announcements);

								$comments = get_comments([
									'post_author' => $user_id,
									'post_type' => 'observation',
									'orderby' => 'date',
									'order' => 'DESC'
								]);

								// var_dump($comments);

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

								// var_dump($season_observations);

								$inat_obs = App\get_observations($number, $username);
								// echo '<pre>';
								// print_r($inat_obs);
								// echo '</pre>';

							?>

							<div class="card">
								<div class="card-content">
									<h4 class="card-title">How to earn your <?php echo $this_season->posts[0]->post_title; ?> badge:</h4>

									<div class="progress">
										<div class="determinate" style="width: <?php echo ($season_observations->found_posts/4)*100; ?>%"></div>
									</div>

									<ul>
										<li>You have submitted <strong><?php echo $season_observations->found_posts; ?> of 3</strong> observations for the current field season. <a href="/submit-new-observation/">Submit an observation!</a></li>
										<li>Complete the <?php echo $this_season->posts[0]->post_title; ?> challenge:<br />
											<?php echo get_field('challenge', $season_id); ?>
										</li>
									</ul>
								</div>
							</div>

							<!-- Recent comments on my observations -->

							<!-- How to earn badge, progress towards badge -->

							<!-- Upcoming events -->

						</div>

						<div class="col m4">

							<!-- Badges, points, n stuff -->

							<?php if (!empty($points)) { ?>
								<h2><span class="points-val"><?php echo $points; ?></span> Points</h2>
							<?php } ?>

							<?php if (!empty($badges)) { ?>

									<h3>My Badges</h3>

									<div class="row block-grid up-s1 up-m2 up-l4">
										<?php foreach ($badges as $badge => $val) { ?>

											<div class="col badge">
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

							<h3>My Recent Observations</h3>

							<?php observations_loop(4, $username, 'has-modal horizontal'); ?>

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
