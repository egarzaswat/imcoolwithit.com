<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="refer-friend col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('REFER_FRIEND_TITLE'); ?></h1>

        <div class="refer-friend-info">
            <img src="<?php print $this->image; ?>">
            <span><?php print JText::_('REFER_FRIEND_INFO'); ?></span>
        </div>

        <?php if ($_GET['status'] == 'success') { ?>
            <div class="refer-friend-form">
                <div class="input-block">
                    <?php print JText::_('REFER_FRIEND_INFO_COMPLETED'); ?>
                </div>
            </div>
            <div class="refer-more-friends">
                <a href="<?php print $this->link_refer; ?>"><?php print JText::_('REFER_MORE_FRIEND_BUTTON'); ?></a>
            </div>
        <?php } else { ?>
            <form class="refer-friend-form">
                <div class="input-block">
                    <input id="email" type="text" name="email" placeholder="Enter email addresses"/>
                </div>
                <span class="loading"></span>
                <div class="submit-block">
                    <input class="submit-button" type="submit" value="<?php print JText::_('REFER_FRIEND_BUTTON'); ?>"/>
                    <span class="earn-tokens-tokens text-none-select">
                        <span class="tokens-count"><?php print $this->tokens_count; ?></span>
                        <span><?php print JText::sprintf('REFER_FRIEND_TOKEN', $this->tokens_count); ?></span>
                    </span>
                </div>
            </form>
        <?php } ?>

        <div class="page-footer"></div>

    </div>

</div>

<script type="text/javascript">

    jQuery('.refer-friend-form').submit(function(e){
        e.preventDefault();
        var data_post = {
            'emails': jQuery(".refer-friend-form #email").val()
        };

        jQuery('.refer-friend-form .submit-button').val('Sending');
        jQuery('.refer-friend-form .loading').show();

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_email_refer.php',

            data: data_post,
            success: function(data){

                if(data === "Success"){
                    jQuery(location).attr('href', '<?php print $this->link_refer . "?status=success"; ?>');
                } else {
                    console.debug(data);

//                    jQuery(location).attr('href', '<?php //print $this->link_error; ?>//');
                }

            },
            error: function(data){

            }
        });
        return false;
    });
</script>