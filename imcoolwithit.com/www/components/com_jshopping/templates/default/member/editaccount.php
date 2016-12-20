<?php
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$pathToJS = JURI::root().'components/com_jshopping/js/';
$document->addScript($pathToJS.'ga.js');
$document->addScript($pathToJS.'zipcode.js');
$document->addScript($pathToJS.'username.js');
$document->addScript($pathToJS.'js_crop_image/jquery.min.js');
?>

<?php if (!$this->registered) { ?>
<div style="height: 70px;"></div>
<?php } ?>

<div class="settings col-sm-10 col-xs-12 col-sm-offset-1">

    <div class="page-content">

        <h1 class="title"><?php print $this->title; ?></h1>

        <form class="form-content row" method="post" enctype="multipart/form-data">
            <div class="col-sm-6 col-xs-12">
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_USERNAME'); ?></span>
                    <input type="text" class="left-border-input" name="username" id="username" value="<?php print $this->user->u_name; ?>"
                           placeholder="Username" maxlength="15" required="required">
                    <span class="username-error error"></span>
                </div>
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_SEX'); ?></span>
                    <?php echo $this->sex_options; ?>
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
<!--                <div class="field-block">
                    <input type="password" class="left-border-input" name="password" value="<?php /*print $this->user->password; */?>" placeholder="Password"
                           required="required">
                    <span class="password-error"></span>
                </div>-->
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_BIRTHDAY'); ?></span>
                    <input type="number" class="left-border-input" name="birthday"
                           value="<?php print $this->user->birthday; ?>"
                           placeholder="Age" min="18" max="99" required="required">
                </div>
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_POSTAL_CODE'); ?></span>
                    <input type="text" class="left-border-input" name="zip" id="zip"
                           value="<?php echo $this->user->zip; ?>"
                           placeholder="Zip Code" pattern="[0-9]*" autocomplete="off" required="required">
                    <span class="zip-error error"></span>
                </div>
            </div>
            <div class="col-xs-12 settings-separator"></div>
            <div class="col-sm-6 col-xs-12">
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_LOOKING_FOR'); ?></span>
                    <?php echo $this->looking_for_options; ?>
                </div>
                <div  class="field-block">
                    <a href="<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_PHOTOS');?>" class="link-button" style="margin-left: 55px;">Upload Photos</a>
                </div>
            </div>
            <div class="col-sm-6 col-xs-12">
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_AGE'); ?></span>
                    <input type="number" class="ages-input" name="age_from" value="<?php print (($this->user->age_look_from == 0) ? '18' : $this->user->age_look_from); ?>"
                           placeholder="Age From" min="18" max="99" required="required">
                    <span class="ages-separator" > - </span>
                    <input type="number" class="ages-input" name="age_to" value="<?php print (($this->user->age_look_to == 0) ? '35' : $this->user->age_look_to); ?>"
                           placeholder="Age To" min="18" max="99" required="required">
                </div>
                <div class="field-block">
                    <span><?php print JText::_('SETTINGS_PAGE_DISTANCE'); ?></span>
                    <?php echo $this->distance_options; ?>
                </div>
            </div>
            <input type="hidden" name="email" id="email" value="<?php print $this->user->email ?>" class="input" disabled="disabled" style="display: none;"/>
            <input type="hidden" name="cityDisable" id="cityDisable" value="<?php print $this->user->city ?>" class="input" disabled="disabled" style="display: none;"/>
            <input type="hidden" name="city" id="city" value="<?php print $this->user->city ?>" class="input" style="display: none;"/>
            <input type="hidden" name="stateDisable" id="stateDisable" value="<?php print $this->user->state ?>" class="input" disabled="disabled" style="display: none;"/>
            <input type="hidden" name="state" id="state" value="<?php print $this->user->state ?>" class="input" style="display: none;"/>
            <input type="hidden" name="longitude" id="longitude" value="<?php echo $this->user->longitude; ?>" />
            <input type="hidden" name="latitude" id="latitude" value="<?php echo $this->user->latitude; ?>" />
            <div class="col-xs-12" style="text-align: center; margin-bottom: 15px">
                <input type="submit" class="link-button" value="<?php print JText::_('SETTINGS_PAGE_BUTTON'); ?>">
                <span class="submit-message"></span>
            </div>
        </form>

    </div>

</div>

<script type="text/javascript">
    jQuery('.form-content').find('input[type="submit"]').click(function( event ) {
        event.preventDefault();

        jQuery('.form-content').find('input[type="submit"]').attr('disabled', true);

        var msg = {
            'username' : jQuery('.form-content input[name="username"]').val(),
            'sex' : jQuery('.form-content select[name="sex_options"]').val(),
            'birthday' : jQuery('.form-content input[name="birthday"]').val(),
            'zip' : jQuery('.form-content input[name="zip"]').val(),
            'looking_for' : jQuery('.form-content select[name="looking_for_options"]').val(),
            'age_look_from' : jQuery('.form-content input[name="age_from"]').val(),
            'age_look_to' : jQuery('.form-content input[name="age_to"]').val(),
            'distance' : jQuery('.form-content select[name="distance_for_options"]').val(),
            'city' : jQuery('.form-content input[name="city"]').val(),
            'state' : jQuery('.form-content input[name="state"]').val(),
            'longitude' : jQuery('.form-content input[name="longitude"]').val(),
            'latitude' : jQuery('.form-content input[name="latitude"]').val()
        };

        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/save_account.php',
            data: msg,
            success: function(data) {
                if(data=='success'){
                    jQuery(location).attr('href','<?php print "http://" . $_SERVER["SERVER_NAME"] . "/" . JText::_("LINK_MY_ACCOUNT"); ?>');
                } else {
                    switch (data){
                        case 'error username': jQuery('.form-content input[name="username"]').css( "border-color", "red"); break;
                        case 'error sex': jQuery('.form-content select[name="sex_options"]').css( "border-color", "red"); break;
                        case 'error birthday': jQuery('.form-content input[name="birthday"]').css( "border-color", "red"); break;
                        case 'error zip': jQuery('.form-content input[name="zip"]').css( "border-color", "red"); break;
                        case 'error looking for': jQuery('.form-content select[name="looking_for_options"]').css( "border-color", "red"); break;
                        case 'error age from': jQuery('.form-content input[name="age_from"]').css( "border-color", "red"); break;
                        case 'error age to': jQuery('.form-content input[name="age_to"]').css( "border-color", "red"); break;
                        case 'error distance': jQuery('.form-content select[name="distance_for_options"]').css( "border-color", "red"); break;
                        default : console.log(data); break;
                    }
                    jQuery('.form-content').find('input[type="submit"]').addClass('error');
                    setTimeout(function () {
                        jQuery('.form-content').find('input[type="submit"]').attr('disabled', false).removeClass('error');
                        jQuery('.form-content select').css( "border-color", "rgb(57, 167, 223)");
                        jQuery('.form-content input').css( "border-color", "rgb(57, 167, 223)");
                    }, 3000);

/*                    jQuery('.form-content .submit-message').removeClass('success').addClass('error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut();
                    setTimeout(function () {
                        jQuery('.form-content').find('input[type="submit"]').attr('disabled', false);
                    }, 3000);*/
                }
            },
            error:  function(data){
                jQuery('.form-content').find('input[type="submit"]').addClass('error');
                setTimeout(function () {
                    jQuery('.form-content').find('input[type="submit"]').attr('disabled', false).removeClass('error');
                }, 3000);

/*                jQuery('.form-content .submit-message').removeClass('success').addClass('error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut();
                setTimeout(function () {
                    jQuery('.form-content').find('input[type="submit"]').attr('disabled', false);
                }, 3000);*/
            }
        });
    });
</script>