jQuery(document).ready(function($) {
  // Dropzone handling
  window.Dropzone.options.cf7dropzone = {
    url: function() {
      return $(this.element).closest('.wpcf7-form').attr('action');
    },
    url: '/submit-new-observation/#wpcf7-f36-p35-o1',
    method: 'post',
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 5,
    maxFiles: 5,
    maxFilesize: 10,
    acceptedFiles: 'image/*',
    addRemoveLinks: true,
    init: function() {
      dzClosure = this; // Makes sure that 'this' is understood inside the functions below.

      // for Dropzone to process the queue (instead of default form behavior):
      $(dzClosure.element).closest('.wpcf7-form').find('.wpcf7-submit').on('click', function(e) {

        // Prevent form from being submitted multiple times
        if( $('.loading-spinner').length ) {
          return false;
        } else {
          e.preventDefault();
          e.stopPropagation();
          $(this).after('<div class="loading-spinner"></div>');
          dzClosure.processQueue();
        }
      });

      //send all the form data along with the files:
      dzClosure.on("sendingmultiple", function(data, xhr, formData) {
        console.info('data', data);
        console.info('xhr', xhr);
        console.info('formData', formData);
        // formData.append("firstname", jQuery("#firstname").val());
        // formData.append("lastname", jQuery("#lastname").val());
      });
    }
  }
});
