<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="refer-friend col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('REFER_FRIEND_TITLE'); ?></h1>
            <div class="block-right">
                <img src="<?php print $this->image; ?>">
            </div>
        </div>

        <div class="earn-tokens-info">
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
                    <span class="earn-tokens">
                        <span class="tokens-icon">
                            <img src="/templates/protostar/images/system/token.png">
                        </span>
                        <span><?php print JText::sprintf('REFER_FRIEND_TOKEN', $this->tokens_count); ?></span>
                    </span>
                </div>
            </form>
        <?php } ?>

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