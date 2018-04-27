export default {
  init() {
    const flatpickr = require('flatpickr');

    // Simplified validation
    function simple_validation($field) {
      let len = $field.val().length;

      if (len === 0) {
        $field.removeClass('valid');
        $field.addClass('invalid');
      }
      else {
        if ($field.is(':valid')) {
          $field.removeClass('invalid');
          $field.addClass('valid');
        }
        else {
          $field.removeClass('valid');
          $field.addClass('invalid');
        }
      }
    }

    // On-the-fly field validation
    $('#observation-form').on('blur', '.validate', function() {
      simple_validation($(this));

      let thisSection = $(this).closest('.form-section');
      let nValidate = thisSection.find('.validate').length;
      let nValid = thisSection.find('.validate.valid').length;

      if (nValidate == nValid) {
        thisSection.find('.btn-primary').removeClass('disabled');
      }
    });

    // Multi-page form pagination and progress functions
    $('#observation-form .form-buttons').on('click', 'a', function(e) {
      e.preventDefault();
      let thisSection = $(this).closest('.form-section');

      // Don't allow clicks on disabled buttons
      if ($(this).hasClass('disabled')) {

        // Check for invalid inputs
        thisSection.find('.validate').each(function() {
          simple_validation($(this));
        });

        return false;
      }

      // Next button handler
      if ($(this).attr('data-button-type') == "next") {
        let thisStep = Number(thisSection.attr('data-section-number'));
        let nextStepN = thisStep+1;
        let nextStepT = $('.form-progress .progress-step[data-step-current]').next().html();

        // Hide this section
        thisSection.addClass('hidden').attr('aria-hidden', 'true');

        // Show next section
        $('.form-section[data-section-number="' + nextStepN + '"]').removeClass('hidden').attr('aria-hidden', 'false');

        // Change progress step
        $('.form-progress').attr('aria-valuenow', nextStepN);
        $('.form-progress').attr('aria-valuetext', 'Step ' + nextStepN + ' of 3: ' + nextStepT);
        $('.form-progress').attr('aria-valuetext', 'Step ' + nextStepN + ' of 3: ' + nextStepT);
        $('.form-progress .progress-step[data-step-current]').removeAttr('data-step-current').attr('data-step-complete', '')
          .next().removeAttr('data-step-incomplete').attr('data-step-current', '');

      // Map search handler
      } else if ($(this).attr('data-button-type') == "map-search") {
        mapsearch();

      // Submit button handler
      // } else if ($(this).attr('data-button-type') == "submit") {
      //   $('form#ecosubmit').submit();
      }
    });

    // Progress step click functions
    $('.form-progress').on('click', '.progress-step[data-step-complete]', function() {
      let targetIndex = $(this).index();
      let thisSection = $('#observation-form .form-section[aria-hidden="false"]');
      let targetStepN = targetIndex+1;
      let targetStepT = $(this).html();

      // Hide this section
      thisSection.addClass('hidden').attr('aria-hidden', 'true');

      // Show target section
      $('.form-section[data-section-number="' + targetStepN + '"]').removeClass('hidden').attr('aria-hidden', 'false');

      // Change progress step
      $('.form-progress').attr('aria-valuenow', targetStepN);
      $('.form-progress').attr('aria-valuetext', 'Step ' + targetStepN + ' of 3: ' + targetStepT);
      $('.form-progress').attr('aria-valuetext', 'Step ' + targetStepN + ' of 3: ' + targetStepT);
      $('.form-progress .progress-step[data-step-current]').removeAttr('data-step-current').attr('data-step-incomplete', '');
      $(this).removeAttr('data-step-complete').attr('data-step-current', '');
    });

    // Conditional fields -- show HotSpot dropdown or map depending on answer
    $('#choice-wrap input[type="radio"]').on('change', function() {
      if (this.value == "Yes") {
        $('#county-wrap').addClass('active');
        $('#location-wrap').removeClass('active');
      } else if (this.value == "No") {
        $('#county-wrap').removeClass('active');
        $('#hotspot-wrap').removeClass('active');
        $('#location-wrap').addClass('active');
        // google.maps.event.trigger(map, 'resize');
      }
    });

    // Add Date/Time picker
    flatpickr('#datetime', {
      inline: true,
      enableTime: true,
      time_24hr: false,
      dateFormat: "m/d/Y h:i",
      defaultDate: Date.now(),
    });

    // Get HotSpots for the selected county
    $('select#county').on('change', function() {
      let val = $(this).val();
      const hotselect = $('select#hotspot');

      if (val.length) {
        jQuery.ajax({
          type: 'POST',
          // eslint-disable-next-line no-undef
          url: eco_ajax_vars.ajax_url,
          data: {
            action: 'obsform_county_hotspots',
            county: val,
          },
          dataType: 'json',
        }).done(function(response) {
          hotselect.empty();
          Object.keys(response).forEach(function(key) {
            hotselect.append('<option value="'+response[key]+'">'+response[key]+'</option>');
          });
          $('.hotspot-wrapper').removeClass('disabled');
        });
      }
    });

    // Activate submit button when HotSpot selected
    $('select#hotspot').on('change', function() {
      let val = $(this).val();

      if (val.length) {
        $('#btn-submit').removeClass('disabled');
      }
    })

    /* eslint-disable */
    // Google Map Picker
    const MAP_DIV_ELEMENT_ID = "google-map";
    const MAP_OPTIONS = {
      zoom: 7,
      center: new google.maps.LatLng(35.1501331,-79.8027368),
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

    function mapsearch() {
      var address = document.getElementById("map-search").value;
      var geocoder = new google.maps.Geocoder;
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

          map.setCenter(results[0].geometry.location);
          map.setZoom(15);

        } else {
          alert("Sorry! We couldn't find that location (error code " + status + ")");
        }
      });
    }

    function mapclicked(loc) {
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

      // Geocode pin location
      var geocoder = new google.maps.Geocoder;
      geocoder.geocode({'location': loc}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {

          // Find City, State, Country
          let i = 0;
          const len = results.length;
          for (i, len; i < len; i++) {
            if (results[i]['geometry']['location_type'] == "APPROXIMATE") {
              if ($.inArray("locality", results[i]['types']) !== -1 || $.inArray("postal_code", results[i]['types']) !== -1) {
                break;
              }
            }
          }

          if (results[i]) {
            $('#picker-address').val(results[i].formatted_address);
            $('#btn-submit').removeClass('disabled');
          } else {
            $('#picker-coords').val('');
            $('#picker-address').val('');
            $('#btn-submit').addClass('disabled');
            marker.setMap(null);
            marker = null;
          }
        } else {
          $('#picker-coords').val('');
          $('#picker-address').val('');
          $('#btn-submit').addClass('disabled');
          marker.setMap(null);
          marker = null;
          console.log('Geocoder failed due to: ' + status);
        }
      });
    }
    /* eslint-enable */

    // Add loading icon when submit button clicked and prevent double form submissions
    $('#btn-submit').on('click', function(e){
      e.preventDefault();

      // Don't allow clicks on disabled buttons
      if ($(this).hasClass('disabled')) {
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

      // Submit form with AJAX
      jQuery.ajax({
        type: 'POST',
        // eslint-disable-next-line no-undef
        url: eco_ajax_vars.ajax_url,
        data: {
          action: 'obsform_submit',
          form: $('#ecosubmit').serializeArray(),
        },
        dataType: 'json',
      }).done(function() {
        window.location = "/user/?profiletab=posts";
        return false;
      });
    });
  },
};
