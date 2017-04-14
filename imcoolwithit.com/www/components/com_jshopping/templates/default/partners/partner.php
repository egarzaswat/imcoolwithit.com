<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-coupon partner-page col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('PARTNER_TITLE'); ?></h1>
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
                <img src="<?php print $this->partner['image']; ?>">
            </div>
            <div class="lincup-coupon-text col-sm-7 col-xs-12">
                <span class="lincup-coupon-title"><?php print $this->partner['title']; ?></span>
                <span class="lincup-coupon-description"><?php print $this->partner['description']; ?></span>
            </div>

            <div class="lincup-bottom">
                <div class="left">

                </div>
                <div class="right">
                    <?php if ($this->submit_data['friend'] == 0) { ?>
                        <a class="not-interested" onclick="addToCookies(<?php print $this->partner['product_id']; ?>)">
                            <img src="/templates/protostar/images/system/refuse_.png">
                            <span><?php print JText::_('NOT_INTERESTED_OFFER'); ?></span>
                        </a>
                        <a href="<?php print $this->link_another_sponsor; ?>">
                            <img src="/templates/protostar/images/system/mee_select_another.png">
                            <span><?php print JText::_('ANOTHER_OFFER'); ?></span>
                        </a>
                    <?php } else { ?>
                        <div class="send-invite">
                            <span><?php print JText::sprintf('SEND_INVITE', $this->user_data->name); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    function addToCookies(id){
        var msg = {
            'id' : id
        };
        console.debug(msg);
        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/add_to_cookies.php',
            data: msg,
            success: function(data) {
                if(data=='success'){
                    location.href= 'http://' + '<?php print $_SERVER['SERVER_NAME']; ?>'  +'/partners';
//                    jQuery(location).attr('href','/partners');
                } else {
                    console.log(data);
                }
            },
            error:  function(data){
                console.log(data);
            }
        });
    }
</script>