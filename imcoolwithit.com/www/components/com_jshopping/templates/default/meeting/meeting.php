<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-coupon col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::sprintf('LINCUP_TITLE', $this->sponsor_data['name']); ?></h1>
        </div>

        <?php if ($this->submit_data['friend'] != 0) { ?>
            <div class="lincup-coupon-users">
                <span class="lincup-coupon-user">
                    <a class="user-photo" href="<?php print $this->my_data->link; ?>">
                        <img class="user-image" src="<?php print $this->my_data->photosite; ?>" />
                    </a>
                    <span><?php print $this->my_data->name . ', ' . $this->my_data->age; ?></span>
                </span>
                <span class="ampersand">&</span>
                <span class="lincup-coupon-user">
                    <a class="user-photo" href="<?php print $this->user_data->link; ?>">
                        <img class="user-image" src="<?php print $this->user_data->photosite; ?>" />
                    </a>
                    <span><?php print $this->user_data->name . ', ' . $this->user_data->age; ?></span>
                    <a class="not-your-date" href="<?php print $this->link_not_your_date; ?>"><?php print JText::_('NOT_YOUR_DATE'); ?></a>
                </span>
            </div>
        <?php } ?>

        <div class="lincup-coupon-info row">
            <div class="lincup-coupon-logo col-sm-5 col-xs-12">
                <img src="<?php print $this->sponsor_data['image']; ?>">
            </div>
            <div class="lincup-coupon-text col-sm-7 col-xs-12">
                <span class="lincup-coupon-over"><?php print JText::_('LINCUP_OVER'); ?></span>
<!--                <span class="lincup-coupon-title">--><?php //print $this->sponsor_data['title']; ?><!--</span>-->
                <span class="lincup-coupon-description"><?php print $this->sponsor_data['description']; ?></span>
            </div>


            <div class="lincup-bottom">
                <div class="left">
                    <div class="tokens-block <?php if (!$this->isset_tokens_send) { print 'wa'; }?>">
                        <img src="/templates/protostar/images/system/linkup_icon.png">
                        <?php if ($this->isset_tokens_send) { ?>
                            <span class="wa"><?php print $this->submit_data['count_tokens'] . ' ' . JText::_('LINCUP_TOKENS'); ?></span>
                        <?php } else { ?>
                            <span class="lincup-no-tokens"><?php print JText::_('NOT_ENOUGH_TOKENS'); ?></span>
                            <a href="<?php print $this->link_earn_tokens; ?>" style="text-decoration: underline;"><?php print JText::_('NOT_ENOUGH_TOKENS_LINK'); ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="right">
                    <a href="/meeting/verification?sponsor=<?php print $this->sponsor_data['product_id']; ?>">
                        <img src="/templates/protostar/images/system/redeem_now.png">
                        <span><?php print JText::_('REDEEM_NOW'); ?></span>
                    </a>
                    <?php if ($this->submit_data['friend'] == 0) { ?>
                        <a href="<?php print $this->link_not_your_date; ?>">
                            <img src="/templates/protostar/images/system/choose_date.png">
                            <span><?php print JText::_('CHOOSE_DATE'); ?></span>
                        </a>
                        <a href="<?php print $this->link_another_sponsor; ?>">
                            <img src="/templates/protostar/images/system/mee_select_another.png">
                            <span><?php print JText::_('ANOTHER_OFFER'); ?></span>
                        </a>
                    <?php } else { ?>
                        <div class="send-invite-to-user">
                            <span><?php print JText::sprintf('SEND_INVITE', $this->user_data->name); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.send-invite-to-user').click(function(){
        jQuery(this).parent().hide();
        var link='<?php print $this->submit_data['link']; ?>';
        var data_post = {
            'friend'        : <?php print $this->submit_data['friend']; ?>,
            'sponsor'       : <?php print $this->submit_data['sponsor']; ?>
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_meet_up.php',
            data: data_post,
            dataType: 'json',
            success: function(data){
                if(data.confirmed == 'confirmed'){
                    jQuery(location).attr('href',link+data.meet_up);
                } else { alert(data);}
            },
            error: function(data){
                alert(data);
            }
        });
        return false;
    });
</script>