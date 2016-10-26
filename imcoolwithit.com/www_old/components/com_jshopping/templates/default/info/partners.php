<div class="partners row">
    <div class="partners-header">
        <h1 class="partner-header-title">Interested in being part of our community?</h1>
        <p class="partner-header-description">Cool With It&trade; partners with local venues and establishments in order to provide our members with new, fun and interesting places to meet/Linc Up and get to know each other. Our partners benefit from being part of Cool With It&trade; by consitent referrals from our site to their venues.</p>
        <p class="partner-header-description">If you feel your venue fits this description please fill in your contact info below and someone will get back to you within 24 hours.</p>
    </div>
    <form class="partners-form" id="partners_form">
        <div class="partners-form-inputs">
            <div>
                <label for="name"><?php print JText::_('SUPPORT_FORM_NAME'); ?></label>
                <input type="text" name="name">
            </div>
            <div>
                <label for="subject"><?php print JText::_('SUPPORT_FORM_SUBJECT'); ?></label>
                <input type="text" name="subject">
            </div>
            <div>
                <label for="email"><?php print JText::_('SUPPORT_FORM_EMAIL'); ?></label>
                <input type="text" name="email">
            </div>
        </div>
        <div class="partners-form-message">
            <div>
                <label for="message"><?php print JText::_('SUPPORT_FORM_MESSAGE'); ?></label>
                <textarea name="message"></textarea>
                <span class="message"></span>
            </div>
        </div>
        <div class="partners-form-submit">
            <input class="submit-button" type="submit" value="Submit">
        </div>
    </form>
</div>

<script type="text/javascript">
    jQuery('#partners_form').submit(function(){
        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_email_message.php',
            data: jQuery("#partners_form").serialize(),
            success: function(data){
                console.log(data);
                if(data === "Success"){
                    jQuery('.partners .partners-form .message').removeClass('error').addClass('success').html('Message Sent.');
                } else {
                    jQuery('.partners .partners-form .message').removeClass('success').addClass('error').html('Error! Please try again.');
                }
            },
            error: function(data){

            }
        });
        return false;
    });
</script>