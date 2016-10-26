<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-invite col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('LINCUP_INVITE_TITLE'); ?></h1>

        <div class="lincup-invite-confirmation row">
            <div class="lincup-invite-date padding-null col-sm-5 col-xs-12">
                <a class="user-link" href="<?php print $this->data->user_data->link; ?>">
                    <img class="user-image" src="<?php print $this->data->user_data->photosite; ?>">
                </a>
                <span class="username"><?php print $this->data->user_data->u_name . ", " . $this->data->user_data->age; ?></span>
                <span class="localisation">
                    <?php print $this->data->user_data->city . ", " . $this->data->user_data->state . ", " . JText::sprintf('LINCUP_INVITE_MILES_AWAY', $this->data->user_data->distance); ?>
                    <span class="yellow">|</span>
                    <?php print $this->data->user_data->sex; ?>
                </span>
            </div>
            <div class="lincup-invite-buttons padding-null col-sm-7 col-xs-12">
                <span class="lincup-invite-info"><?php print JText::sprintf('LINCUP_INVITE_INFO', $this->data->user_data->u_name); ?></span>
                <span class="lincup-invite-in"><?php print JText::_('LINCUP_INVITE_IN'); ?></span>
                <span class="confirmation">
                    <?php if($this->isset_tokens_accept){ ?>
                        <input type="submit" class="submit-button accept" value="<?php print JText::_('LINCUP_INVITE_SUBMIT'); ?>">
                    <?php } else { ?>
                        <input type="submit" class="submit-button accept" value="<?php print JText::_('LINCUP_INVITE_SUBMIT'); ?>" disabled="disabled">
                    <?php } ?>
                    <input type="submit" class="submit-button refuse" value="<?php print JText::_('LINCUP_INVITE_CANCEL'); ?>">
                </span>
            </div>
        </div>

        <div class="lincup-coupon-info row">
            <div class="lincup-coupon-logo col-sm-5 col-xs-12">
                <img src="<?php print $this->data->sponsor_data['image']; ?>">
            </div>
            <div class="lincup-coupon-text col-sm-7 col-xs-12">
                <span class="lincup-coupon-over"><?php print JText::_('LINCUP_OVER'); ?></span>
                <span class="lincup-coupon-title"><?php print $this->data->sponsor_data['title']; ?></span>
                <span class="lincup-coupon-description"><?php print $this->data->sponsor_data['description']; ?></span>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.lincup-invite-buttons .confirmation .accept[type="submit"]').click(function(){
        jQuery(this).attr('disabled',true);
        var link='<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MEETING_COUPON_INFO') . '?meet=' . $this->meet; ?>';
        var data_post = {
            'meet'          : '<?php print $this->meet; ?>',
            'friend'        : '<?php print $this->friend; ?>',
            'accept'        : 1
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_and_rejected_meet_up.php',
            data: data_post,
            success: function(data){
                if(data==='confirmed'){
                    jQuery(location).attr('href',link);
                }
            },
            error: function(data){

            }
        });
        return false;
    });

    jQuery('.lincup-invite-buttons .confirmation .refuse[type="submit"]').click(function(){
        jQuery(this).attr('disabled',true);
        var link='<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED'); ?>';
        var data_post = {
            'meet'          : '<?php print $this->meet; ?>',
            'friend'        : '<?php print $this->friend; ?>',
            'accept'        : 0
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_and_rejected_meet_up.php',
            data: data_post,
            success: function(data){
                if(data==='confirmed'){
                    jQuery(location).attr('href',link);
                }
                jQuery(location).attr('href',link);
            },
            error: function(data){

            }
        });
        return false;
    });
</script>