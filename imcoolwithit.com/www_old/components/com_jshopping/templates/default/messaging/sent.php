<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="messages col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">

        <div class="title col-xs-12"><?php print $this->title; ?></div>

        <div class="messages-sent-received col-xs-12">
            <span class="sent active"><?php print JText::_('MESSAGE_INBOX_SEND_BUTTON'); ?></span>
            <a class="received" href="<?php print $this->link_received; ?>"><?php print JText::_('MESSAGE_INBOX_RECEIVED_BUTTON'); ?></a>
            <div class="tokens-filter">
                <?php print JText::_('TOKENS_WORD'); ?>
                <input type="checkbox" name="tokens-filter" id="tokens-filter" <?php if ($this->ft) { print 'checked="checked"';} ?> >
                <label for="tokens-filter"></label>
            </div>
        </div>

        <div class="messages-list padding-null col-xs-12">
            <?php if (count($this->inbox_list) > 0) {
                foreach ($this->inbox_list as $key => $value) { ?>
                    <div class="messages-list-item row">
                        <div class="messages-list-user">
                            <a href="<?php print $value['user_link']; ?>">
                                <?php if ($value['read'] != 1) { ?>
                                    <div class="new"></div>
                                <?php } ?>
                                <img class="user-image" src="<?php print $value['photo']; ?>">
                            </a>
                        </div>
                        <div class="messages-list-info">
                            <?php if (isset($value['sr_tokens']) && $value['sr_tokens'] == true) { ?>
                                <span class="tokens-icon"></span>
                            <?php } ?>
                            <span class="username"><?php print $value['name'];
                                if ($value['distance'] !== false) {
                                    print ', ' . $value['distance'] . " " . JText::_('MILES');
                                }
                                ?>
                            </span>
                            <span class="date"><?php print $value['date']; ?></span>

                            <?php if ($value['message_expires'] != false) { ?>
                                <span class="message"><?php print $value['message']; ?></span>
                                <span class="info"><?php print $value['message_expires']; ?></span>
                                <?php if ($value['button'] != null) { ?>
                                    <a class="action" href="<?php print $value['button']['link']; ?>"><?php print $value['button']['name']; ?></a>
                                <?php }
                            } else { ?>
                                <span class="message info"><?php print $value['message']; ?></span>
                                <?php if ($value['button'] != null) { ?>
                                    <a class="action" href="<?php print $value['button']['link']; ?>"><?php print $value['button']['name']; ?></a>
                                <?php }
                            } ?>
                            <?php if ($value['block'] != 0) { ?>
                                <span class="deleted"><?php print JText::_('USER_IS_DELETED'); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="no-records-found">
                    <?php print JText::_('NO_MESSAGES_FOUND'); ?>
                </div>
            <?php } ?>
        </div>

        <?php print $this->pagination; ?>

    </div>

</div>

<script type="text/javascript">
    jQuery('#tokens-filter').change(function() {
        if(jQuery(this).is(':checked')){
            jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_SENT') . '?tokens=true'; ?>');
        } else {
            jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_SENT'); ?>');
        }
    });
</script>
