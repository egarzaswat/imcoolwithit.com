<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="messages col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row ">
        <div class="page-content-top content-top-col padding-null col-xs-12">
            <h1><?php print $this->title; ?></h1>
            <div class="top-info">
                <span class="received active"><?php echo JText::_('MESSAGE_INBOX_RECEIVED_BUTTON'); ?></span>
                <a class="sent" href="<?php print $this->link_sent; ?>"><?php echo JText::_('MESSAGE_INBOX_SEND_BUTTON'); ?></a>
                <div class="tokens-filter">
                    <?php print JText::_('TOKENS_WORD'); ?>
                    <input type="checkbox" name="tokens-filter" id="tokens-filter" <?php if ($this->ft) { print 'checked="checked"';} ?> >
                    <label for="tokens-filter"></label>
                </div>
            </div>
        </div>



        <div class="messages-list padding-null col-xs-12">
            <?php if (count($this->inbox_list) > 0) {
                foreach ($this->inbox_list as $key => $value) { ?>
                    <div class="messages-list-item row <?php if ($value['read'] != 1) { print 'is_new'; }?>">
                        <div class="messages-list-user">
                            <a href="<?php print $value['user_link']; ?>">
                                <img class="user-image" src="<?php print $value['photo']; ?>">
                            </a>
                        </div>
                        <div class="messages-list-info">
                            <?php if (isset($value['sr_tokens']) && $value['sr_tokens'] == true) { ?>
                                <span class="tokens-icon"></span>
                            <?php } ?>
                            <span class="username">
                                <?php print $value['name'];
                                if ($value['distance']!== false) {
                                    print ', ' . $value['distance'] . " " . JText::_('MILES');
                                }
                                ?>
                            </span>
                            <span class="date"><?php print $value['date']; ?></span>

                            <?php if ($value['message_expires']!= false) { ?>
                                <span class="message"><?php print $value['message']; ?></span>
                                <span class="info"><?php print $value['message_expires']; ?></span>
                                <?php if ($value['button']!= null) { ?>
                                    <a class="action" href="<?php print $value['button']['link']; ?>"><?php print $value['button']['name']; ?></a>
                                <?php } ?>
                            <?php } else { ?>
                                <span class="message info"><?php print $value['message']; ?></span>
                                <?php if ($value['button']!= null) { ?>
                                    <a class="action" href="<?php print $value['button']['link']; ?>"><?php print $value['button']['name']; ?></a>
                                <?php } ?>
                            <?php } ?>

                            <span>
                                <?php if ($value['accept'] && $value['accept']!= null) { ?>
                                    <div data-user="<?php print $value['accept']['value']; ?>" class="action accept" ><?php print $value['accept']['name']; ?></div>
                                <?php } ?>
                                <?php if ($value['decline'] && $value['decline']!= null) { ?>
                                    <div data-user="<?php print $value['decline']['value']; ?>" class="action decline" ><?php print $value['decline']['name']; ?></div>
                                <?php } ?>
                            </span>

                            <?php if ($value['block'] != 0) { ?>
                                <span class="deleted"><?php print JText::_('USER_IS_DELETED'); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="no-records-found"><?php print JText::_('NO_MESSAGES_FOUND'); ?></div>
            <?php } ?>
        </div>

        <?php print $this->pagination; ?>

    </div>

</div>

<script type="text/javascript">
    jQuery('#tokens-filter').change(function() {
        if(jQuery(this).is(':checked')){
            jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED') . '?tokens=true'; ?>');
        } else {
            jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED'); ?>');
        }
    });

    jQuery('.messages-list .messages-list-item .messages-list-info .decline').click(function(){
        jQuery(this).attr('disabled',true);
        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_rejected.php',
            data: data_post,
            success: function(data){
                if(data == "success"){
                    jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'); ?>');
                } else {

                }
            },
            error: function(data){

            }
        });
        return false;
    });

    jQuery('.messages-list .messages-list-item .messages-list-info .accept').click(function(){

        //jQuery('.full-user-page .external-links-top .accept-token .token').removeClass('token').addClass('sending-token');
        //jQuery('.full-user-page .external-links-top .accept-token .token').addClass('token-animation');
        var link='<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ACCEPT'); ?>?user=' + this.getAttribute('data-user');
        var referer_email='<?php print $email_ref; ?>';

        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_to_friends.php',
            data: data_post,
            success: function(data){
                if(referer_email != ''){
                    sendEmailReferrer(referer_email);
                }
                //setTimeout(function () {
                jQuery(location).attr('href', link);
                //}, 3000);
            },
            error: function(data){
                console.log(data);
            }
        });
        return false;
    });
</script>