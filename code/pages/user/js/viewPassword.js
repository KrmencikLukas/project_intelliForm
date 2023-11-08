$(document).ready(function() {
    $('.password-input-container').hover(function() {
        // Append the "Show Password" span to the input field
        var inputField = $(this).find('.password-input');
        var showPasswordSpan = inputField.next('.show-password');
        if (showPasswordSpan.length === 0) {
          inputField.after('<span class="show-password"></span>');
          showPasswordSpan = inputField.next('.show-password');
        }
        showPasswordSpan.show();
      }, function() {
        $(this).find('.show-password').hide();
      });
    
      $(document).on('click', '.show-password', function() {
        var inputField = $(this).siblings('.password-input');
        var isVisible = inputField.attr('type') === 'password';
    
        if (isVisible) {
          inputField.attr('type', 'text');
          $(this).css('background-image', 'url("../../../assets/img/icons/seen.png")');
        } else {
          inputField.attr('type', 'password');
          $(this).css('background-image', 'url("../../../assets/img/icons/unseen.png")');
        }
      });
  });