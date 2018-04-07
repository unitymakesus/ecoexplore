export default {
  init() {
    const flatpickr = require('flatpickr');

    // Multi-page form pagination and progress functions
    $('#observation-form .form-buttons').on('click', 'a', function(e) {
      e.preventDefault();

      // Don't allow clicks on disabled buttons
      if ($(this).hasClass('disabled')) {
        return false;
      }

      if ($(this).attr('data-button-type') == "next") {
        let thisSection = $(this).closest('.form-section');
        let thisStep = Number(thisSection.attr('data-section-number'));
        let nextStepN = thisStep+1;
        let nextStepT = $('.form-progress .progress-step[data-step-current]').next();

        // Hide this section
        thisSection.addClass('hidden').attr('aria-hidden', 'true');

        // Show next section
        $('.form-section[data-section-number="' + nextStepN + '"]').removeClass('hidden').attr('aria-hidden', 'false');

        // Change progress step
        $('.form-progress').attr('aria-valuenow', nextStepN);
        $('.form-progress').attr('aria-valuetext', 'Step ' + nextStepN + ' of 3: ' + nextStepT.html());
        $('.form-progress').attr('aria-valuetext', 'Step ' + nextStepN + ' of 3: ' + nextStepT.html());
        $('.form-progress .progress-step[data-step-current]').removeAttr('data-step-current').attr('data-step-complete', '')
          .next().removeAttr('data-step-incomplete').attr('data-step-current', '');
      } else if ($(this).attr('data-button-type') == "submit") {
        $('form#ecosubmit').submit();
      }
    });

    // Conditional fields -- show HotSpot dropdown or map depending on answer
    $('#choice-wrap input[type="radio"]').on('change', function() {
      if (this.value == "yes") {
        $('#county-wrap').addClass('active');
        $('#location-wrap').removeClass('active');
      } else if (this.value == "no") {
        $('#county-wrap').removeClass('active');
        $('#hotspot-wrap').removeClass('active');
        $('#location-wrap').addClass('active');
        // google.maps.event.trigger(map, 'resize');
      }
    });

    $('select#county').on('change', function() {
      let val = $(this).val();
      const hotselect = $('select#hotspot');

      if (val.length) {
        jQuery.ajax({
          type: 'POST',
          // eslint-disable-next-line no-undef
          url: eco_ajax_vars.ajax_url,
          data: {
            action: 'cf7_county_hotspots',
            county: val,
          },
          dataType: 'json',
        }).done(function(response) {
          console.log(response);
          hotselect.empty();
          Object.keys(response).forEach(function(key) {
            hotselect.append('<option value="'+response[key]+'">'+response[key]+'</option>');
          });
          $('#hotspot-wrap').addClass('active');
        });
      }
    });

    // Add Date/Time picker
    flatpickr('#datetime', {
      inline: true,
      enableTime: true,
      time_24hr: false,
      dateFormat: "m/d/Y h:i",
      defaultDate: Date.now(),
      disableMobile: true,
    });

    /* eslint-disable */
    // Google Map Picker
    const MAP_DIV_ELEMENT_ID = "google-map";
    const MAP_OPTIONS = {
      zoom: 8,
      center: new google.maps.LatLng(35.595058, -82.551487),
      streetViewControl: false,
      disableDefaultUI: false,
      fullscreenControl: false,
      panControl:true,
      rotateControl:true,
      scaleControl:true,
      overviewMapControl:true,
      mapTypeId: 'roadmap',
      zoomControl: true,
      zoomControlOptions: { position: google.maps.ControlPosition.TL, style: google.maps.ZoomControlStyle.SMALL },
      mapTypeControl: false,
    };

    // The google map variable
    let map = null;

    // The marker variable, when it is null no marker has been added
    let marker = null;

    map = new google.maps.Map(document.getElementById(MAP_DIV_ELEMENT_ID), MAP_OPTIONS);

    google.maps.event.addListener(map, 'click', function(event) {
      mapclicked(event.latLng);
    });

    function mapclicked(loc) {
      var geocoder = new google.maps.Geocoder;
      geocoder.geocode({'location': loc}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            $('#picker-address').val(results[1].formatted_address);
          } else {
            window.alert('No results found');
          }
        } else {
          window.alert('Geocoder failed due to: ' + status);
        }
      });

      var latlng = loc.toString();
      $('#picker-coords').val(latlng);

      // Place marker
      if (marker != null) {
        marker.setPosition(loc);
      } else {
        marker = new google.maps.Marker({
          map: map,
          position: loc,
        });
      }
    }
    /* eslint-enable */

    // Add loading icon when submit button clicked and prevent double form submissions
    $(document).on('click', '.wpcf7-submit', function(e){

      // Prevent form submit until image is uploaded
      if ($('#dropzone-files').val() == null || $('#dropzone-files').val() == '') {
        alert('Please wait for image to finish processing');
        e.preventDefault();
        return false;
      }

      // If loader already added to DOM, don't submit form
      if( $('.loading-spinner').length ) {
        e.preventDefault();
        return false;
      } else {
        // Add loader to DOM
        $(this).after('<div class="loading-spinner"></div>');
      }
    });
  },
};
