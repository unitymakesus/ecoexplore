<?php
// This user's data
$user_id = um_profile_id();
$username = um_user('user_login');
$library = get_field('library', $user_id);
?>

<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?> um-role-<?php echo um_user('role'); ?> ">

	<div class="um-form">

		<?php do_action('um_profile_before_header', $args ); ?>

		<?php if ( um_is_on_edit_profile() ) { ?>
			<form method="post" action="">
		<?php } ?>

			<?php do_action('um_profile_header_cover_area', $args ); ?>

			<?php do_action('um_profile_header', $args ); ?>

			<?php do_action('um_profile_navbar', $args ); ?>

			<?php
				$nav = $ultimatemember->profile->active_tab;
				$subnav = ( get_query_var('subnav') ) ? get_query_var('subnav') : 'default';
			?>

			<div class="um-profile-body <?php echo $nav; ?> <?php echo $nav . '-' . $subnav; ?>">
				<div class="row">
					<div class="col m8">
						<h2>Notes from ecoEXPLORE</h2>

						<!-- Recent comments on my observations -->

						<!-- How to earn badge, progress towards badge -->

						<!-- Upcoming events -->

					</div>

					<div class="col m4">
						<a class="waves-effect waves-light btn-large"><i class="material-icons left" aria-hidden="true">photo_camera</i> Submit New Observation</a>

						<h2>My recent observations</h2>

						<!-- Recent observations -->
						<?php
							$recent_obs = new WP_Query([
								'post_type' => 'observation',
								'posts_per_page' => 4,
								'author' => $user_id
							]);
						?>

						<?php if ($recent_obs->have_posts()) { ?>
							<?php while ($recent_obs->have_posts()) { ?>
								<?php $recent_obs->the_post(); ?>

								<div class="card horizontal">
									<div class="card-image">
										<?php the_post_thumbnail('thumbnail'); ?>
									</div>

									<div class="card-stacked">
										<div class="card-content">
											<h3><?php the_title(); ?></h3>
											<p>spotted by <?php the_author(); ?></p>
											<ul>
												<li><i class="material-icons" aria-label="Where">location_on</i> Location</li>
												<li><i class="material-icons" aria-label="When">access_time</i> Time</li>
											</ul>
										</div>

										<div class="card-action">
											<ul>
												<li><i class="material-icons" aria-label="Points">star_border</i> Points</li>
												<li><i class="material-icons" aira-hidden="true">cloud_upload</i> See on iNaturalist</li>
											</ul>
										</div>
									</div>
								</div>

							<?php } ?>
						<?php } else { ?>

							<?php $observations = App\get_observations('jman'); ?>
							<?php //print_r($observations); ?>

							<?php if (!empty($observations)) { ?>
								<?php foreach($observations as $o) { ?>

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

								<?php } ?>
							<?php } else { ?>

								<p>You haven't submitted any observations yet.<br />
								<a href="/submit-new-observation/">Send us your first one now!</a></p>

							<?php } ?>
						<?php } ?>

						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			</div>

		<?php if ( um_is_on_edit_profile() ) { ?>
			</form>
		<?php } ?>

	</div>

</div>
