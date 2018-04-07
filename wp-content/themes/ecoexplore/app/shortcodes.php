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

add_shortcode('observation_form', function ($atts, $content = null) {
  ob_start();
  ?>
  <ol class="form-progress" tabindex="0" role="progressbar" aria-valuemin="1" aria-valuemax="3" aria-valuenow="1" aria-valuetext="Step 1 of 3: Upload Image">
    <li class="progress-step" aria-hidden="true" data-step-current>Upload Image</li>
    <li class="progress-step" aria-hidden="true" data-step-incomplete>Description</li>
    <li class="progress-step" aria-hidden="true" data-step-incomplete>When &amp; Where</li>
  </ol>

  <div class="observation-form" id="observation-form">
    <section class="form-section fieldset" data-section-number="1" aria-hidden="false">
      <div class="form-row">
        <h3>Step 1</h3>
        <p class="step-description">Let's begin by uploading a photograph of your observation.</p>

        <div class="dz-wrapper">
          <?php echo do_shortcode('[wp-dropzone id="dropfile" max-file-size="10" remove-links="true" accepted-files="image/*" max-files="1" dom-id="dz-files" title="Drop image here or click to upload" callback="success: function(file, response) { jQuery(\'#btn-first-next-step\').removeClass(\'disabled\'); }"]'); ?>
        </div>

        <div class="form-buttons">
          <a class="btn-primary" data-button-type="next" id="btn-first-next-step" href="#">Next Step</a>
        </div>
      </div>
    </section>

    <form name="ecosubmit" id="ecosubmit">
      <input type="hidden" name="dz-files" id="dz-files" required="true" aria-required="true" />
      <section class="form-section hidden" data-section-number="2" aria-hidden="true">
        <div class="form-row">
          <h3>Step 2</h3>
          <p class="step-description">Now tell us all about your observation. Can you identify the species? What did you notice that was special about it?</p>

          <div class="input-field">
            <label for="identification">Species Identification <span aria-label="Required">*</span></label>
            <input type="text" name="identification" id="identification" required="true" aria-required="true">
          </div>

          <div class="input-field">
            <label for="description">Description of Observation (optional)</label>
            <textarea name="description" id="description" class="materialize-textarea"></textarea>
          </div>

          <div class="form-buttons">
            <a class="btn-primary" data-button-type="next" href="#">Next Step</a>
          </div>
        </div>
      </section>

      <section class="form-section hidden" data-section-number="3" aria-hidden="true">
        <div class="form-row">
          <h3>Step 3</h3>
          <p class="step-description">Finally, when and where did you make the observation?</p>

          <div class="input-wrapper">
            <label for="datetime">Date &amp; Time Observed <span aria-label="Required">*</span></label>
            <input type="text" name="datetime" class="flatpickr-input" id="datetime" aria-required="true" required="true" readonly="readonly">
          </div>

          <fieldset class="input-wrapper" id="choice-wrap">
            <legend>Did you snap this at a HotSpot?</legend>
            <div>
              <input type="radio" name="choice" id="choice-yes" value="yes">
              <label for="choice-yes">Yes</label>
            </div>
            <div>
              <input type="radio" name="choice" id="choice-no" value="no">
              <label for="choice-no">No</label>
            </div>
          </fieldset>

          <div class="input-wrapper" id="county-wrap">
            <label for="county">Choose County</label>
            <select name="county" id="county" class="browser-default">
							<option value="" selected>Select One</option>
              <?php
                // Add each county to the dropdown select options
                $hotspot_coords = get_field('hotspot_coordinates', 'options');
              	foreach ($hotspot_coords as $hsc) {
              		echo '<option value="' . $hsc['county'] . '">' . $hsc['county'] . '</option>';
              	}
              ?>
						</select>
          </div>

          <div class="input-wrapper" id="hotspot-wrap">
            <label for="hotspot">Choose HotSpot</label>
            <select name="hotspot" id="hotspot" class="browser-default">
							<option value="" selected>You must first select the county</option>
						</select>
          </div>

          <div class="input-wrapper" id="location-wrap">
            <label for="picker-address">Location of Observation</label>
            <input type="hidden" name="picker-address" id="picker-address">
            <p class="description">Click to drop a pin on the map.</p>
            <div id="google-map" style="height:400px;"></div>
          </div>

          <div class="form-buttons">
            <a class="btn-secondary" data-button-type="submit" href="#">Submit Observation</a>
          </div>
        </div>
      </section>
    </form>
  </div>
  <?php
  return ob_get_clean();
});
