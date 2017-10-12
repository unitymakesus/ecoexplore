export default {
  init() {
    // Add password strength meter
    $('div[data-key=user_password]').append('<span id="password-strength">Strength indicator</span>');
  },
  finalize() {
    // Check password strength
    $('body').on('keyup', 'input[data-key=user_password], input[data-key=confirm_user_password]', function() {
      // Reset strength meter
      $('#password-strength').removeClass('short bad good strong');

      const pass1 = $('input[data-key=user_password]').val();
      const pass2 = $('input[data-key=confirm_user_password]').val();
      const blacklist = wp.passwordStrength.userInputBlacklist();

      // Calculate strength
      let strength = wp.passwordStrength.meter(pass1, blacklist, pass2);

      switch (strength) {
        case 2:
          $('#password-strength').addClass('bad').html('Weak');
          break;

        case 3:
          $('#password-strength').addClass('good').html('Medium');
          break;

        case 4:
          $('#password-strength').addClass('strong').html('Strong');
          break;

        case 5:
          $('#password-strength').addClass('short').html('Very weak');
          break;

        default:
          $('#password-strength').addClass('short').html('Very weak');
      }
    });
  },
};
