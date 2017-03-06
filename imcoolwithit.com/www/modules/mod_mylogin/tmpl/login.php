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
        box-sizing: content-box;
        display: block;
        font-size: 12px;
        height: 26px;
        line-height: 35px;
        margin: 0 10px 10px;
        max-width: 90%;
        padding: 6px 12px;
        width: 135px;
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
    }
    .form-signup-text{
        color: rgb(51, 51, 51);
        cursor: pointer;
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
        margin: 5px 20px;
    }
    .submit-button:hover{
        background: rgb(253, 197, 82)
    }
    @media (max-width: 449px){
        .form-signup-input{
            width: 175px;
        }
    }
    @media (max-width: 767px){
        .form-inline{
            text-align: center;
        }
        .form-signup-text{
            margin: 0 10px;
        }
    }
</style>

<?php if(!isset($_GET['u']) || !isset($_GET['h'])){ ?>
    <form class="form-inline form_login" action="" method="post" >
        <div class="form-input-container">
            <div class="form-group">
                <label class="form-signup-label" for="username_email">Username or Email</label>
                <input name="username_email" type="text" class="form-signup-input" id="username_email" style="width: 214px;">
            </div>
            <div class="form-group">
                <label class="form-signup-label" for="password">Password</label>
                <input name="password" type="password" class="form-signup-input" id="password"  style="width: 214px;">
                <span class="form-error login-error"></span>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="submit-button">Submit</button>
            <span class="form-signup-text forgot_show">Forgot Password? <span style="text-decoration: underline;">Click here to reset</span></span>
        </div>
    </form>

    <form style="display: none;" class="form-inline form_send_forgot" action="" method="post" >
        <div class="form-input-container">
            <div class="form-group">
                <label class="form-signup-label" for="email">Email</label>
                <input name="email" type="email" class="form-signup-input" id="email" style="width: 234px;">
                <input name="action_type" value="send_email" type="hidden">
                <span class="form-error email-error"></span>
            </div>
        </div>
        <span class="message"></span>
        <button type="submit" class="submit-button">Submit</button>
        <span class="form-signup-text login_show" style="display: none;">Log In</span>
    </form>



    <script type="text/javascript">
        jQuery('.forgot_show').click(function(){
            jQuery('.form_login').hide();
            jQuery('.forgot_show').hide();
            jQuery('.form_send_forgot').show();
            jQuery('.login_show').show();
        });
        jQuery('.login_show').click(function(){
            jQuery('.form_login').show();
            jQuery('.forgot_show').show();
            jQuery('.form_send_forgot').hide();
            jQuery('.login_show').hide();
        });

        jQuery('.form_login').submit(function(e){
            e.preventDefault();
            var data = jQuery('.form_login').serialize();

            jQuery.ajax({
                type: "POST",
                url: 'modules/mod_mylogin/ajax/login.php',
                data: data,
                success: function(html){
                    if(html == 'success'){
                        if (jQuery(window).width() <= '767'){
                            jQuery(location).attr('href', '/search?mobile=true');
                        } else {
                            jQuery(location).attr('href', '/search');
                        }
//                        jQuery(location).attr('href', '<?php //echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'); ?>//');
//                        jQuery(location).attr('href', '<?php //echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'); ?>//');
                    } else if (html == 'user does not exist') {
                        jQuery('.login-error').removeClass('form-success').addClass('form-error').html('Incorrect username or password');
                    } else {
                        console.log(html);
                    }
                },
                error: function(html){
                    console.log(html);
                }
            });
            return false;
        });

        jQuery("#username_email").keyup(function(){
            jQuery('.login-error').removeClass('form-error').addClass('form-success').html('');
        });
        jQuery("#password").keyup(function(){
            jQuery('.login-error').removeClass('form-error').addClass('form-success').html('');
        });

        var timer;

        jQuery("#email").keyup(function() {
            var email = jQuery(this);

            clearTimeout(timer);
            timer = setTimeout(function() {

                if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.val())) {
                    jQuery('.form_send_forgot .submit-button').attr('disabled', false);
                    jQuery('.email-error').removeClass('form-error').addClass('form-success').html('');
                } else {
                    jQuery('.form_send_forgot .submit-button').attr('disabled', true);
                    jQuery('.email-error').removeClass('form-success').addClass('form-error').html('Invalid email');
                }
            }, 1000);
        });

        jQuery('.form_send_forgot').submit(function(e){
            e.preventDefault();
            var data = jQuery('.form_send_forgot').serialize();

            jQuery.ajax({
                type: "POST",
                url: 'modules/mod_mylogin/ajax/forgot.php',
                data: data,
                success: function(html){
                    if(html == 'success'){
                        jQuery('.form_send_forgot .email-error').removeClass('form-error').addClass('form-success').html('Link has been sent! Please, check entered email address');
                    } else if (html == 'invalid email') {
                        jQuery('.form_send_forgot .email-error').removeClass('form-success').addClass('form-error').html('Invalid email');
                    } else if (html == 'unverified email') {
                        jQuery('.form_send_forgot .email-error').removeClass('form-success').addClass('form-error').html('Please, enter your verified email!');
                    } else {
                        jQuery('.form_send_forgot .email-error').removeClass('form-success').addClass('form-error').html('Sending error');
                        console.log(html);
                    }
                },
                error: function(html){
                    console.log(html);
                }
            });
            return false;
        });
    </script>
<?php } else { ?>
    <form class="form-inline update_password" action="" method="post" >
        <div class="form-input-container">
            <div class="form-group">
                <label class="form-signup-label" for="password1">New Password</label>
                <input name="password1" type="password" class="form-signup-input" id="password1" style="width: 214px;">
                <span class="form-error password-error"></span>
            </div>
            <div class="form-group">
                <label class="form-signup-label" for="password2">Confirm Password</label>
                <input name="password2" type="password" class="form-signup-input" id="password2" style="width: 214px;">
                <span class="form-error confirmation-error"></span>
            </div>
        </div>
        <input name="action_type" value="update_password" type="hidden">
        <input name="user" value="<?php print $_GET['u']; ?>" type="hidden">
        <input name="hash" value="<?php print $_GET['h']; ?>" type="hidden">
        <button type="submit" class="submit-button">Submit</button>
    </form>

    <script type="text/javascript">

        var timer;

        jQuery('#password1').keyup(function(){
            jQuery('.confirmation-error').html('');
            clearTimeout(timer);
            timer = setTimeout(function(){
                if(jQuery('#password1').val().length < 6){
                    jQuery('.password-error').removeClass('form-success').addClass('form-error').html('Password too short');
                } else {
                    jQuery('.password-error').removeClass('form-error').addClass('form-success').html('');
                }
            }, 1000);
        });

        jQuery('#password2').keyup(function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                if(jQuery('#password1').val() != jQuery('#password2').val()){
                    jQuery('.confirmation-error').removeClass('form-success').addClass('form-error').html('Confirmation does not match');
                } else {
                    if(jQuery('#password1').val().length < 6){
                        jQuery('.password-error').removeClass('form-success').addClass('form-error').html('Password too short');
                    } else {
                        jQuery('.confirmation-error').removeClass('form-error').addClass('form-success').html('Confirmation match');
                    }
                }
            }, 1000);
        });

        jQuery('.update_password').submit(function(e){
            e.preventDefault();
            var data = jQuery('.update_password').serialize();

            jQuery.ajax({
                type: "POST",
                url: 'modules/mod_mylogin/ajax/forgot.php',
                data: data,
                success: function(html){
                    if(html == 'success'){
                        jQuery(location).attr('href', '<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_LOGIN'); ?>');
                    } else if (html == 'password error') {
                        jQuery('.confirmation-error').removeClass('form-success').addClass('form-error').html('Confirmation does not match');
                    } else if (html == 'hash error') {
                        jQuery('.confirmation-error').removeClass('form-success').addClass('form-error').html('Fail');
                    } else {
                        console.log(html);
                    }
                },
                error: function(html){
                    console.log(html);
                }
            });
            return false;
        });
    </script>
<?php } ?>