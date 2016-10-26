<?php
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../../'));
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JFactory::getApplication('site')->initialise();

$albums = file_get_contents('https://graph.facebook.com/' . $_SESSION['fb_1618118001736353_user_id'] . '/albums?access_token=' . $_SESSION['fb_1618118001736353_access_token']);
$albums_decoded = json_decode($albums, true); ?>
<div class="title col-xs-12"><?php print JText::_('EDIT_ACCOUNT_FB_ALBUMS'); ?></div>
<div class="fb-content col-xs-12">
    <?php foreach ($albums_decoded['data'] as $album) {
        $album_cover = 'https://graph.facebook.com/' . $album['id'] . '/picture?access_token=' . $_SESSION['fb_1618118001736353_access_token']; ?>
        <div class="fb-album">
            <div class="thumbnail">
                <div id="<?php print $album['id']; ?>" class="image-style show-album"
                     style="background:url(<?php print $album_cover; ?>) center center no-repeat;
                         background-size: cover; "></div>
                <div class="caption">
                    <span><?php print $album['name'] ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<input class="close-albums col-xs-12" type="button" value="Close"/>

<script type="text/javascript">

    jQuery('.close-albums').click(function(){
        jQuery('#hide-fb-content').hide();
        jQuery('#show-fb-img').hide();
    });

    jQuery('.fb-album .show-album').click(function () {

        var data = {
            album_id: this.id
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/show_facebook_photos.php',
            data: data,
            success: function (data) {
                jQuery('#hide-fb-content').show();
                var img_div=jQuery('.page-content .fb-album .image-style');
                jQuery(img_div).css('height', jQuery(img_div).width()+'px');
                jQuery('#show-fb-img').html(data).show();
            },
            error: function (html) {
            }
        });

    });

</script>