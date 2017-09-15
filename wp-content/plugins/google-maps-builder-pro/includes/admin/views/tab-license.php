<?php
/**
 * License
 *
 * @package   Google_Maps_Builder
 * @author    Devin Walker <devin@wordimpress.com>
 * @license   GPL-2.0+
 * @link      http://wordimpress.com
 * @copyright 2016 WordImpress, Devin Walker
 */
?>

<div class="container">
	<div class="row">
		<div class="col-md-10">

			<h3><?php _e( 'Maps Builder Pro License', 'google-maps-builder' ); ?></h3>

			<p><?php echo sprintf( __( 'Please activate your license for Maps Builder Pro to receive plugin updates and support. Need a license? Click %1$shere%2$s to purchase a license.', 'google-maps-builder' ), '<a href="https://wordimpress.com/plugins/maps-builder-pro?utm_source=MBF&utm_medium=BANNER&utm_content=SETTINGS&utm_campaign=MBF%20Settings" target="_blank">', '</a>' ); ?></p>

			<?php cmb2_metabox_form( $license_fields, $key ); ?>

		</div>
		<div class="col-md-2"></div>
	</div>
</div>
