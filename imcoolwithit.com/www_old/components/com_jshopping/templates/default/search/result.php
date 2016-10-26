<?php
defined('_JEXEC') or die('Restricted access');
$usersList = $this->usersList;
?>


<div class="col-sm-10 col-sm-offset-1 col-xs-12">
    <div class="user-invisible" <?php print ($this->user_visible == 1) ? ('') : ('style="line-height: 50px;"'); ?>>
        <?php print ($this->user_visible == 1) ? JText::_('SET_INVISIBLE') : JText::_('YOU_INVISIBLE'); ?>
    </div>
</div>

<div class="user-list col-sm-10 col-sm-offset-1 col-xs-12">
    <?php if (count($usersList) < 1) { ?>
        <div class="page-content no-records-found">
            <?php print JText::_('NO_USERS_FOUND'); ?>
        </div>
    <?php }
    foreach ($usersList as $key=>$user) { ?>
        <a class="user-item user-link" href="<?php echo $user['user_link']; ?>">
            <div class="user-item-top">
                <span class="title"><?php echo $user['name']; ?></span>
                <span class="localisation">
                    <i class="icon-location"></i>
                    <span><?php print JText::_('DISTANCE') . $user['distance'] . ' | ' . $user['sex']; ?></span>
                </span>
            </div>
            <div class="user-item-photo">
                <img src="<?php echo $user['photo']; ?>" />
            </div>
            <div class="user-item-bottom">
                <span class="last-visit"><?php echo JText::_('LAST_ONLINE'). $user['last_visit']; ?></span>
                <span class="info"><?php print JText::_('AGE'); ?> <span><?php print $user['age']; ?></span></span>
                <span class="info"><?php print JText::_('HEIGHT'); ?> <span><?php print $user['height']; ?></span></span>
                <span class="info"><?php print JText::_('BODY'); ?> <span><?php print $user['body']; ?></span></span>
                <span class="info"><?php print JText::_('STATUS'); ?> <span><?php print $user['status']; ?></span></span>
            </div>
        </a>
    <?php } ?>

    <div class="col-xs-12">
        <?php print $this->pagination; ?>
    </div>

</div>

<script type="text/javascript">
    jQuery('.set-invisible').click(function(){
        if (confirm("<?php print JText::_('CONFIRM_CLOCK_PROFILE'); ?>")) {
            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/set_invisible.php',
                success: function (data) {
                    if (data === 'success') {
                        jQuery('.user-invisible').html('<?php print JText::_('YOU_INVISIBLE'); ?>').css('line-height', '50px');
                    }
                },
                error: function (er) {

                }
            });
        }
    });




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
</script>