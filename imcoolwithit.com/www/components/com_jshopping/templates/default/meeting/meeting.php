<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-coupon col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::sprintf('LINCUP_TITLE', $this->sponsor_data['name']); ?></h1>

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
                <span class="lincup-coupon-title"><?php print $this->sponsor_data['title']; ?></span>
                <span class="lincup-coupon-description"><?php print $this->sponsor_data['description']; ?></span>
            </div>
        </div>

        <div class="lincup-coupon-tokens text-none-select col-sm-8 col-xs-12">
            <?php if ($this->isset_tokens_send) { ?>
                <div>
                    <span class="lincup"><?php print JText::_('LINCUP_FOR'); ?></span>
                    <span class="tokens-count"><?php print $this->submit_data['count_tokens']; ?></span>
                    <span><?php print JText::_('LINCUP_TOKENS'); ?></span>
                </div>
            <?php } else { ?>
                <span class="lincup-no-tokens"><?php print JText::_('NOT_ENOUGH_TOKENS'); ?></span>
                <a href="<?php print $this->link_earn_tokens; ?>"><?php print JText::_('NOT_ENOUGH_TOKENS_LINK'); ?></a>
            <?php } ?>
        </div>

        <div class="lincup-coupon-options text-none-select col-sm-4 col-xs-12">
            <?php if ($this->submit_data['friend'] == 0) { ?>
            <div class="options">
                <a href="<?php print $this->link_not_your_date; ?>" class="choose-date"><?php print JText::_('CHOOSE_DATE'); ?></a>
                <a href="<?php print $this->link_another_sponsor; ?>" class="another-offer"><?php print JText::_('ANOTHER_OFFER'); ?></a>
            </div>
            <?php } else { ?>
                <div class="send-invite">
                    <span><?php print JText::sprintf('SEND_INVITE', $this->user_data->name); ?></span>
                </div>
            <?php } ?>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.lincup-coupon-options .send-invite').click(function(){
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