<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-redeem col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">
        <div class="redeem-ready"><span><?php print JText::_('LINCUP_REDEEM_INFO'); ?></span></div>

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
    jQuery('.lincup-redeem .lincup-coupon-redeem .submit-button').click(function(){
        jQuery(this).attr('disabled', true);

        var data_post = {
            'code' : jQuery('.redeem-code').val(),
            'sponsor' : '<?php print $this->sponsor_data['product_id']; ?>'
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/check_sponsor.php',
            data: data_post,
            success: function(data){
                if(data == 'success'){
                    alert('Success!');
                    jQuery('.lincup-redeem .lincup-coupon-redeem .submit-button').removeAttr('disabled');
                } else {
                    alert('Error!');
                    jQuery('.lincup-redeem .lincup-coupon-redeem .submit-button').removeAttr('disabled');
                }
            },
            error: function(data){

            }
        });
        return false;
    });
</script>