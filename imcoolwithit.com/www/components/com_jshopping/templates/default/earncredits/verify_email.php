<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="verify-email col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('VERIFY_EMAIL_TITLE'); ?></h1>
            <div class="block-right">
                <img src="<?php print $this->image; ?>">
            </div>
        </div>

        <div class="earn-tokens-info">
            <span><?php print JText::_('VERIFY_EMAIL_INFO'); ?></span>
        </div>

        <div class="verify-email-info">
            <?php if ($this->active) { ?>
                <div class="verify-email-form">
                    <div class="input-block">
                        <span><?php print JText::_('VERIFIED_EMAIL'); ?></span>
                    </div>
                </div>
            <?php } else { ?>
                <form class="verify-email-form">
                    <div class="input-block">
                        <input id="email" type="text" name="email" value="<?php print $this->email; ?>" placeholder="Enter email address"/>
                    </div>
                    <div class="submit-block">
                        <input class="submit-button" type="submit" value="<?php print JText::_('REFER_FRIEND_BUTTON'); ?>"/>
                    <span class="earn-tokens text-none-select">
                        <span class="tokens-icon">
                            <img src="/templates/protostar/images/system/token.png">
                        </span>
                        <span><?php print JText::sprintf('VERIFY_EMAIL_TOKEN', $this->tokens_count); ?></span>
                    </span>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>

</div>

<script type="text/javascript">

    jQuery('.verify-email-form').submit(function(){

        var data_post = {
            'email': jQuery(".verify-email-form #email").val()
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_email_verify.php',
            data: data_post,
            success: function(data){

                if(data === "Success"){
                    jQuery(location).attr('href', '<?php print $this->link_verification; ?>');
                } else {
                    jQuery(location).attr('href', '<?php print $this->link_error; ?>');
                }

            },
            error: function(data){

            }
        });
        return false;
    });
</script>