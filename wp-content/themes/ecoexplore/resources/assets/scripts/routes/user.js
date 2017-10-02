export default {
  init() {
    // Set top of modal if admin bar is showing
    let modalTop = 0;
    if (document.body.classList.contains('admin-bar')) {
      modalTop = 32;
    }

    // Init materialize-css modals
    $('.modal').modal({
      endingTop: modalTop,
    });

    // Open modals on page load if there's a url param for it
    const params = new URLSearchParams(window.location.search);

    if (params.has('obs')) {
      let obs = params.get('obs');
      $('#' + obs).modal('open');
    }
  },
  finalize() {
    // Move modals to end of DOM and open on click
    $('.modal').appendTo($('body'));
  },
};
