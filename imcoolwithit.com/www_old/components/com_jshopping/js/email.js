jQuery(function(){
    // OnKeyDown Function
    var timer;

    jQuery("#email").keyup(function(){
        var email = jQuery(this);

        clearTimeout(timer);
        timer = setTimeout(function(){
            jQuery.ajax({
                url: '/components/com_jshopping/controllers/save_data/check_email.php',
                data: {email : email.val()},
                type: "POST",
                success: function(data){
                    if(data == 'success'){
                        jQuery('.form-content input[type=submit]').attr('disabled',false);
                        jQuery('.email-error').html('Username available');
                    } else {
                        jQuery('.form-content input[type=submit]').attr('disabled',true);
                        jQuery('.email-error').html('Username already in use');
                    }
                },
                error: function() {
                    jQuery('.form-content input[type=submit]').attr('disabled',true);
                    jQuery('.email-error').html('Unknown error');
                }
            });
        },1000);

    });

});
