jQuery(function(){
    // OnKeyDown Function
    var timer;

    jQuery("#username").keyup(function(){
        var username = jQuery(this);
        var letters = /^[A-Za-z].[0-9A-Za-z_]+$/;

        clearTimeout(timer);
        timer = setTimeout(function(){

            if (username.val().length < 3){
                jQuery('.form-content input[type=submit]').attr('disabled',true);
                jQuery('.username-error').html('Username too short').addClass('error').removeClass('success');
            } else if(username.val().length > 15){
                jQuery('.form-content input[type=submit]').attr('disabled',true);
                jQuery('.username-error').html('Username too long').addClass('error').removeClass('success');
            } else if (username.val().indexOf(' ') != -1){
                jQuery('.form-content input[type=submit]').attr('disabled',true);
                jQuery('.username-error').html('Username can not contain spaces').addClass('error').removeClass('success');
            } else if (!username.val().match(letters)){
                jQuery('.form-content input[type=submit]').attr('disabled',true);
                jQuery('.username-error').html('Invalid Username').addClass('error').removeClass('success');
            } else {

                jQuery.ajax({
                    url: '/components/com_jshopping/controllers/save_data/check_username.php',
                    data: {username : username.val()},
                    type: "POST",
                    success: function(data){
                        if(data == 'success'){
                            jQuery('.form-content input[type=submit]').attr('disabled',false);
                            jQuery('.username-error').html('Username available').addClass('success').removeClass('error');
                        } else {
                            jQuery('.form-content input[type=submit]').attr('disabled',true);
                            jQuery('.username-error').html('Username already in use').addClass('error').removeClass('success');
                        }
                    },
                    error: function() {
                        jQuery('.form-content input[type=submit]').attr('disabled',true);
                        jQuery('.username-error').html('Unknown error').addClass('error').removeClass('success');
                    }
                });
            }
        },1000);

    });

});
