jQuery(function () {
    // OnKeyDown Function
    var timer;

    jQuery("#email").keyup(function() {
        var email = jQuery(this);

        clearTimeout(timer);
        timer = setTimeout(function() {

            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.val())) {
                jQuery.ajax({
                    url: '/components/com_jshopping/controllers/save_data/check_email.php',
                    data: {email: email.val()},
                    type: "POST",
                    success: function (data) {
                        if (data == 'success') {
                            jQuery('.form_sign_up .submit-button').attr('disabled', false);
                            jQuery('.email-error').removeClass('form-error').addClass('form-success').html('Email available');
                        } else {
                            jQuery('.form_sign_up .submit-button').attr('disabled', true);
                            jQuery('.email-error').removeClass('form-success').addClass('form-error').html('Email already in use');
                        }
                    },
                    error: function () {
                        jQuery('.form_sign_up .submit-button').attr('disabled', true);
                        jQuery('.email-error').removeClass('form-success').addClass('form-error').html('Unknown error');
                    }
                });
            } else {
                jQuery('.form_sign_up .submit-button').attr('disabled', true);
                jQuery('.email-error').removeClass('form-success').addClass('form-error').html('Invalid email');
            }
        }, 1000);
    });
});
