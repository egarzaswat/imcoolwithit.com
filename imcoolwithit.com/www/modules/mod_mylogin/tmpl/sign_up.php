<form class="form-inline form_sign_up" action="" method="post" >

    <div class="form-input-container">
        <div class="form-group">
            <label class="form-signup-label" for="email">Email</label>
            <input name="email" type="email" class="form-signup-input" id="email" placeholder="e.g. example@url.com"
                   autocomplete="off">
            <span class="form-error email-error"></span>
        </div>
        <div class="form-group">
            <label class="form-signup-label" for="password">Password</label>
            <input name="password" type="password" class="form-signup-input" id="password" placeholder="enter"
                   autocomplete="off">
            <span class="form-error password-error"></span>
        </div>
    </div>

    <button type="submit" class="submit-button">Sign Up</button>
</form>




<script type="text/javascript">
    var timer;

    jQuery('#password').keyup(function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            if(jQuery('#password').val().length < 6){
                jQuery('.form_sign_up .submit-button').attr('disabled',true);
                jQuery('.password-error').removeClass('form-success').addClass('form-error').html('Password too short');
            } else {
                jQuery('.form_sign_up .submit-button').attr('disabled',false);
                jQuery('.password-error').removeClass('form-error').addClass('form-success').html('Good!!!')
            }
        }, 1000);
    });

    jQuery('.form_sign_up').submit(function(e){
        e.preventDefault();
        var data = jQuery('.form_sign_up').serialize();

        jQuery.ajax({
            type: "POST",
            url: 'modules/mod_mylogin/ajax/join_now.php',
            data: data,
            success: function(data){
                if (data == 'success') {
                    jQuery(location).attr('href', '<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'); ?>');
                } else {
                    switch (data) {
                        case 'error birthday': jQuery('.form-content input[name="age"]').css( "border-color", "red"); break;
                        case 'error zip': jQuery('.form-content input[name="zip"]').css( "border-color", "red"); break;
                        case 'error email': jQuery('.form-content input[name="email"]').css( "border-color", "red"); break;
                        case 'error password': jQuery('.form-content input[name="password"]').css( "border-color", "red"); break;
                        case 'exist': jQuery('.email-error').removeClass('form-success').addClass('form-error').html('Email already in use'); break;
                        default : console.log(data); break;
                    }
                }
            },
            error: function(data){
                console.log(data);
            }
        });
        return false;
    });
</script>