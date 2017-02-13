<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="show-private col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('VIEW_PRIVATE_PHOTOS'); ?></h1>
            <div class="block-right">
                <div class="ph-border">
                    <img src="/templates/protostar/images/system/private_pictures.png">
                </div>
            </div>
        </div>

        <div class="page-top-info">
            <span><?php print JText::sprintf('VIEW_PRIVATE_PHOTOS_INFO', $this->count_tokens, ''); ?></span>
        </div>


        <div class="show-private-action">
            <div class="show-private-tokens text-none-select">
                <div class="tokens-image">
                    <img src="/templates/protostar/images/system/token.png">
                </div>
                <span class="tokens-text"><?php print JText::sprintf('VIEW_PRIVATE_PHOTOS_TOKENS', $this->count_tokens); ?></span>
            </div>

            <?php if ($this->permission == true) { ?>
                <input type="button" class="submit-button" value="<?php print JText::_('BUTTON_VIEW'); ?>"/>
            <?php } else { ?>
                <span class="no_tokens">
                    <?php print JText::_('USER_PAGE_NO_TOKENS'); ?><br>
                    <a href="<?php print $this->link_earn_tokens; ?>"><?php print JText::_('USER_PAGE_NO_TOKENS_LINK_TEXT'); ?></a>
                </span>
            <?php } ?>
        </div>
    </div>

</div>

<script type="text/javascript">

    jQuery('.submit-button').click(function(){
        jQuery(this).attr('disabled',true);

        var data_post = {
            'user_id'   : '<?php print $this->adv_id; ?>',
            'adv_email' : '<?php print $this->adv_email; ?>',
            'adv_name'  : '<?php print $this->adv_name; ?>'
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/show_private_pictures.php',
            data: data_post,
            success: function(){
                setTimeout(function () {
                    jQuery(location).attr('href','<?php print $this->link; ?>');
                }, 500);
            },
            error: function(data){
            }
        });
        return false;
    });
</script>