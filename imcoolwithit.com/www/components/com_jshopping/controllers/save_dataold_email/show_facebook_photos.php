<?php
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../../'));
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();

$conf = new JConfig();
$modelUser = JSFactory::getModel('user', 'jshop');
$count_images = $modelUser->getImagesCount(JSFactory::getUser()->user_id);
$available_images = $conf->limit_upload_photo - $count_images;

$data = file_get_contents('https://graph.facebook.com/' . $_POST['album_id'] . '/photos?access_token=' . $_SESSION['fb_1618118001736353_access_token']);
$data_decoded = json_decode($data, true); ?>
<div class="title fb-title"><?php print JText::_('EDIT_ACCOUNT_FB_PHOTOS'); ?></div>
<div class="title fb-title-limit" style="display: none"><?php print JText::_('EDIT_ACCOUNT_FB_LIMIT'); ?></div>
<form class="fb-content col-xs-12">
    <?php foreach ($data_decoded['data'] as $photo) { ?>
        <div class="fb-album">
            <div class="thumbnail">
                <input id="<?php print $photo['id']; ?>" type="checkbox" name="fb_photos[]" value="<?php print $photo['source']; ?>"
                       style="display: none;"/>
                <label for="<?php print $photo['id']; ?>" style="display: inline-block;">
                    <span id="<?php print $photo['id'] . '-cover'; ?>" class="image-style" style="background:url(<?php print $photo['source']; ?>) center center no-repeat;
                        background-size: cover;">
                        <input type="button" class="visible-xs-block visible-sm-block set-avatar"
                               name="<?php print $photo['source']; ?>" value="Select Pic"/>
                    </span>
                    <img id="<?php print $photo['id'] . '-checked'; ?>" class="image-checked"
                         src="/templates/maintemplate/images/system/check.png">
                </label>
            </div>
        </div>
    <?php } ?>
</form>
<div class="col-xs-12 padding-null">
    <input class="close-photos col-xs-6" type="button" value="Back to albums"/>
    <input class="upload-photos col-xs-6" type="submit" value="Upload"/>
</div>

<script type="text/javascript">

    var countChecked = function () {

        var image_full = document.getElementById(this.id + '-cover');
        var image_check = document.getElementById(this.id + '-checked');

        if(jQuery('#' + this.id).is(":checked")){
            image_full.style.borderColor = '#37a9e4';
            image_full.style.opacity = '0.8';
            image_check.style.display = 'block';
        } else {
            image_full.style.borderColor = '';
            image_full.style.opacity = '';
            image_check.style.display = 'none';
        }

        var available_images = '<?php print $available_images; ?>';
        var checked_images = jQuery('input[type=checkbox]:checked').length;

        if (checked_images < available_images) {
            jQuery('input[type=checkbox]:not(:checked)').attr('disabled', false);
            jQuery('.fb-title').show();
            jQuery('.fb-title-limit').hide();
        }
        if (checked_images == available_images) {
            jQuery('input[type=checkbox]:not(:checked)').attr('disabled', true);
            jQuery('.fb-title').hide();
            jQuery('.fb-title-limit').show();
        }
    };

    jQuery('input[type=checkbox]').on('click', countChecked);

    jQuery('.close-photos').click(function(){

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/show_facebook_albums.php',
            success: function (data) {
                jQuery('#hide-fb-content').show();
                jQuery('#show-fb-img').html(data).show();
                var img_div=jQuery('.page-content .fb-album .image-style');
                jQuery(img_div).css('height', jQuery(img_div).width()+'px');
            },
            error: function (html) {
            }
        });
    });

    jQuery('.upload-photos').click(function(){

        jQuery('.upload-photos').attr('disabled', true);

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/save_facebook_photos.php',
            data: jQuery('.fb-content').serialize(),
            success: function () {
                window.location.reload();
            },
            error: function (html) {

            }
        });

    });

    jQuery('.image-style .set-avatar').click(function(){

        var path_to_photo = '<?php print $conf->path_user_image_medium; ?>';

        var data = new FormData();
        data.append('path', path_to_photo);
        data.append('user', '<?php print JSFactory::getUser()->u_name; ?>');
        data.append('image', this.name);

        jQuery.ajax({
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/upload_photo_from_facebook.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                jQuery('.hidden_block .image_crop img').attr("src", path_to_photo + data);
                initJcrop();
                jQuery('.hidden_block').show();
            },
            error: function (data) {
                jQuery('.form_image').html(data);
            }
        });
    });

</script>