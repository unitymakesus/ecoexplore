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
        $('#hotspot-wrap').addClass('active');
        $('#location-wrap').removeClass('active');
      } else if (this.value == "No") {
        $('#hotspot-wrap').removeClass('active');
        $('#location-wrap').addClass('active');
        // google.maps.event.trigger(map, 'resize');
      }
    });

    // // Redirect to dashboard after form successfully submitted
    // document.addEventListener( 'wpcf7mailsent', function() {
    //   window.location.href = '/user/';
    // }, false );
  },
};
