export default {
  init() {
    const flatpickr = require('flatpickr');

    // Add Date/Time picker
    flatpickr('#datetime', {
      inline: true,
      enableTime: true,
      time_24hr: false,
      dateFormat: "m/d/Y h:i",
      defaultDate: Date.now(),
      disableMobile: true,
    });

    // Prevent form submit when pressing enter in location search field
    $('#cf7_location_picker_postcode').on('keydown', function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
        $(this).next('input[type="button"]').click();
        return false;
      }
    });

    // Conditional fields -- show HotSpot dropdown or map depending on answer
    $('#choice input[type="radio"]').on('change', function() {
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

    // Add loading icon when submit button clicked and prevent double form submissions
    $(document).on('click', '.wpcf7-submit', function(e){
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
