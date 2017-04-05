<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-redeem col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::sprintf('LINCUP_REDEEM_TITLE', $this->sponsor_data['name_' . JSFactory::getLang()->lang]); ?></h1>
        </div>

        <div class="lincup-coupon-users">
            <span class="lincup-coupon-user">
                <a class="user-photo" href="<?php print $this->my_data->link; ?>">
                    <img class="user-image" src="<?php print $this->my_data->photosite; ?>"/>
                </a>
                <span><?php print $this->my_data->name . ', ' . $this->my_data->age; ?></span>
            </span>
            <span class="ampersand">&</span>
            <span class="lincup-coupon-user">
                <a class="user-photo" href="<?php print $this->user_data->link; ?>">
                    <img class="user-image" src="<?php print $this->user_data->photosite; ?>"/>
                </a>
                <span><?php print $this->user_data->name . ', ' . $this->user_data->age; ?></span>
            </span>
        </div>

        <div class="redeem-ready"><span><?php print JText::_('LINCUP_REDEEM_INFO_'); ?></span></div>

        <div class="lincup-coupon-info row">
            <div class="lincup-coupon-logo col-sm-4 col-xs-12">
                <img src="<?php print $this->sponsor_data['image']; ?>">
            </div>
            <div class="lincup-coupon-text col-sm-8 col-xs-12">
                <span class="lincup-coupon-over"><?php print JText::_('LINCUP_OVER'); ?></span>
<!--                <span class="lincup-coupon-title">--><?php //print $this->sponsor_data['title_' . JSFactory::getLang()->lang]; ?><!--</span>-->
                <span class="lincup-coupon-description"><?php print $this->sponsor_data['short_description_' . JSFactory::getLang()->lang]; ?></span>
            </div>
        </div>

        <div class="lincup-coupon-redeem row">
            <input type="text" class="redeem-code" name="code" placeholder="<?php print JText::_('LINCUP_REDEEM_ENTER_CODE'); ?>"/>
            <input type="button" class="submit-button" value="<?php print JText::_('LINCUP_REDEEM_SUBMIT'); ?>"/>
        </div>

    </div>

</div>

<script type="text/javascript">
    var tmp = false;
    function sendEmail(){
        var data_post = {
            'sponsor'   : "<?php print $this->sponsor_data['name_' . JSFactory::getLang()->lang]; ?>"
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_message_occurred_meet_up.php',
            data: data_post,
            success: function(data){
                tmp = true;
            },
            error: function(data){
                tmp = true;
            }
        });
    }

    jQuery('.lincup-redeem .lincup-coupon-redeem .submit-button').click(function(){

        jQuery(this).attr('disabled', true);

        var link='<?php print $this->link_info; ?>';
        var data_post = {
            'code' : jQuery('.redeem-code').val(),
            'meet' : '<?php print $this->meet; ?>'
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/confirm_meeting.php',
            data: data_post,
            success: function(data){
                if(data == 'confirmed'){
                    sendEmail();
                    setTimeout(function () {
                        jQuery(location).attr('href', link);
                    }, 2000);
                } else {
                    jQuery('.redeem-code').val('<?php print JText::_('LINCUP_REDEEM_INCORRECT_CODE'); ?>');
                    setTimeout(function () {
                        jQuery('.redeem-code').val('');
                        jQuery('.lincup-redeem .lincup-coupon-redeem .submit-button').attr('disabled', false);
                    }, 2000);
                }
            },
            error: function(data){

            }
        });
        return false;
    });
</script>