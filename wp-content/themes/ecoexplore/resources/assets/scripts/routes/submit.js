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

    // // Redirect to dashboard after form successfully submitted
    // document.addEventListener( 'wpcf7mailsent', function() {
    //   window.location.href = '/user/';
    // }, false );
  },
};
