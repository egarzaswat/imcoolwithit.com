<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="contact-us col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print $this->header; ?></h1>
        </div>

        <div class="contact-us-content">
            <span><b><?php print JText::_('CONTACT_US_INFO_1'); ?></b></span>
            <span><?php print JText::_('CONTACT_US_INFO_2'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_3'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_4'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_5'); ?></span>
        </div>

    </div>

</div>

<!--<div class="support row">

    <h1 class="col-xs-12"><?php /*print $this->header*/?></h1>

    <div class="col-sm-6 col-xs-12">
        <div class="photo">
            <img src="/images/content/support_photo.png">
        </div>
    </div>

    <form class="support_form col-sm-6 col-xs-12">
        <div class="xs_max_width">
            <input type="text" name="name" placeholder="<?php /*print JText::_('SUPPORT_FORM_NAME'); */?>">
            <input type="text" name="email" placeholder="<?php /*print JText::_('SUPPORT_FORM_EMAIL'); */?>">
            <input type="text" name="subject" placeholder="<?php /*print JText::_('SUPPORT_FORM_SUBJECT'); */?>">
            <textarea name="message" placeholder="<?php /*print JText::_('SUPPORT_FORM_MESSAGE'); */?>"></textarea>
            <span class="message"></span>
            <input class="button_action" type="submit" value="Send">
        </div>
    </form>

</div>-->

<!--<script type="text/javascript">
    jQuery('.support_form').submit(function(){
        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_email_message.php',
            data: jQuery(".support_form").serialize(),
            success: function(data){
                if(data === "Success"){
                    jQuery('.support .support_form .message').removeClass('error').addClass('success').html('Message Sent.');
                } else {
                    jQuery('.support .support_form .message').removeClass('success').addClass('error').html('Error! Please try again.');
                }

            },
            error: function(data){

            }
        });
        return false;
    });
</script>-->