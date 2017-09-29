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


							?>

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

							<?php observations_loop('4', $username); ?>

						</div>
					</div>
				<?php } else { ?>
					<div class="row block-grid up-s1 up-m3 up-l4">

						<?php observations_loop('-1', $username); ?>

					</div>
				<?php } ?>

			</div>

		<?php if ( um_is_on_edit_profile() ) { ?>
			</form>
		<?php } ?>

	</div>

</div>
