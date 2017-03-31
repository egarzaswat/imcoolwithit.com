<style>
    .form-signup-label{
        color: rgb(66, 66, 66);
        display: block;
        font-size: 16px;
        margin: 0 10px;
        text-align: left;
    }
    .form-signup-input{
        border: 1px solid rgb(58, 166, 222);
        display: block;
        font-size: 12px;
        height: 35px;
        line-height: 35px;
        margin: 0 10px 10px;
        padding: 6px 12px;
        width: 175px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }
    .form-input-container{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        margin-top: 15px
    }
    .texts-form-eighteen{
        color: rgb(58, 166, 222);
        display: inline-block;
        font-size: 12px;
        margin-bottom: 5px;
        vertical-align: bottom;
    }
    .form-signup-text{
        color: rgb(51, 51, 51);
        display: block;
        font-size: 12px;
        font-weight: bold;
        margin-right: 20px;
    }
    .form-error{
        color: rgb(255, 0, 0);
        font-size: 13px;
        font-weight: bold;
        margin: 0 10px;
    }
    .form-success{
        color: rgb(58, 166, 222);
        font-size: 13px;
        font-weight: bold;
        margin: 0 10px;
    }
    .submit-button{
        /*background: rgb(243, 177, 62);*/
        height: 40px;
        margin: 5px 20px;
        width: 120px;
    }
    .submit-button:hover{
        /*background: rgb(253, 197, 82)*/
    }
    @media (max-width: 479px){
        .form-inline{
            text-align: center;
        }
        .form-signup-text{
            margin: 0 10px;
        }
        .home-sign-up .sign-up-box .sign-up-text h1{
            font-size: 24px;
        }
        .home-sign-up .sign-up-box .sign-up-text span{
            font-size: 18px;
        }
    }
    @media (max-width: 767px){
        .form-group{
            margin-bottom: 0;
        }
        .form-signup-input{
            margin-bottom: 0;
        }
        .home-sign-up .sign-up-box .sign-up-border #fb_login{
            margin: 10px;
        }
    }
</style>

<form class="form-inline form_sign_up" action="" method="post" >

    <div class="form-input-container">
        <div class="form-group">
            <label class="form-signup-label" for="age">Age<sup>*</sup></label>
            <input name="age" type="text" class="form-signup-input" id="age" placeholder="e.g. 27"
                   autocomplete="off">
            <span class="form-error age-error"></span>
        </div>
        <div class="form-group">
            <label class="form-signup-label" for="zip">Zip Code</label>
            <input name="zip" type="text" class="form-signup-input" id="zip" placeholder="e.g. 20001"
                   autocomplete="off">
            <span class="form-error zip-error"></span>
        </div>
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

    <input type="hidden" name="email" id="email" value="" class="input" disabled="disabled" style="display: none;" />
    <input type="hidden" name="cityDisable" id="cityDisable" value="" class="input" disabled="disabled" style="display: none;" />
    <input type="hidden" name="city" id="city" value="" class="input" style="display: none;" />
    <input type="hidden" name="stateDisable" id="stateDisable" value="" class="input" disabled="disabled" style="display: none;" />
    <input type="hidden" name="state" id="state" value="" class="input" style="display: none;" />
    <input type="hidden" name="longitude" id="longitude" value="" />
    <input type="hidden" name="latitude" id="latitude" value="" />

    <div class="actions">
        <span class="texts-form-eighteen">
            <span class="form-eighteen-older">Already a member? <a href="/login">Sign in here.</a></span><br>
            <span class="form-eighteen-older">*Must be 18 years or older to join</span>
        </span>
        <button type="submit" class="submit-button">Sign Up</button>
        <span class="form-signup-text">Rest assured, credit card numbers are never requested or taken.</span>
    </div>
</form>

<script type="text/javascript">
    var timer;
    jQuery('#age').keyup(function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            if(parseInt(jQuery('#age').val()) < 18){
                jQuery('.form_sign_up .submit-button').attr('disabled',true);
                jQuery('.age-error').removeClass('form-success').addClass('form-error').html('Invalid age');
            } else {
                jQuery('.form_sign_up .submit-button').attr('disabled',false);
                jQuery('.age-error').removeClass('form-error').addClass('form-success').html('Good!!!')
            }
        }, 1000);
    });

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

    jQuery('.form-content input').keyup(function(){
        jQuery('.form-content input[name="age"]').css( "border-color", "rgb(57, 167, 223)");
    });

    jQuery('.form_sign_up').submit(function(e){
        e.preventDefault();
        var data = jQuery('.form_sign_up').serialize();

        jQuery.ajax({
            type: "POST",
            url: 'modules/mod_mylogin/ajax/sign_up.php',
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