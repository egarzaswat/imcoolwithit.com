<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="show-private col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('VIEW_PRIVATE_PHOTOS'); ?></h1>



        <?php if($this->isFriend){ ?>
            <span class="show-private-info"><?php print JText::sprintf('VIEW_PRIVATE_PHOTOS_INFO', $this->count_tokens, ''); ?></span>

            <div class="show-private-action">
                <span class="show-private-tokens text-none-select">
                    <span class="tokens-count"><?php print $this->count_tokens; ?></span>
                </span>

                <?php if ($this->permission == true) { ?>
                    <input type="button" class="submit-button" value="<?php print JText::_('BUTTON_VIEW'); ?>"/>
                <?php } else { ?>
                    <span class="no_tokens">
                    <?php print JText::_('USER_PAGE_NO_TOKENS'); ?><br>
                    <a href="<?php print $this->link_earn_tokens; ?>"><?php print JText::_('USER_PAGE_NO_TOKENS_LINK_TEXT'); ?></a>
                </span>
                <?php } ?>

                <span class="show-private-tokens text-none-select" style="background: none;"></span>
            </div>
        <?php } else { ?>
            <span class="show-private-info"><?php print JText::_('NOT_RFIENDS_TO_PRIVATE'); ?></span>
        <?php } ?>

        <span class="page-footer"></span>

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