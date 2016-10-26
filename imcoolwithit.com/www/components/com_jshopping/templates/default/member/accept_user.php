<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="token-match col-sm-8 col-sm-offset-2 col-xs-12" style="margin-top: 45px;">

    <div class="page-popup row">
        <h1 class="title"><?php print JText::_('INTERESTING_IN_EACH_OTHER'); ?></h1>
        <span class="close-page">X</span>

        <div class="token-match-info">
            <span class="text"><?php print JText::sprintf('TOKEN_ACCEPTED_YOU', $this->user_name); ?></span>
            <div class="col-sm-8 col-sm-offset-2">
                <div class="users-photo">
                    <div class="item">
                        <a href="<?php print $this->user_link; ?>"><img src="<?php echo $this->user_photo; ?>"></a>
                    </div>
                    <div class="item">
                        <a href="<?php print $this->my_link; ?>"><img src="<?php echo $this->my_photo; ?>"></a>
                    </div>
                </div>

                <div class="links">
                    <a href="<?php echo $this->message_link; ?>"><?php print JText::_('SEND_A_MESSAGE'); ?></a>
                    <a href="<?php echo $this->profile_link; ?>"><?php print JText::_('VIEW_PROFILE'); ?></a>
                    <a href="<?php echo $this->lincup_link; ?>"><?php print JText::_('LINCUP_REQUEST'); ?></a>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_FRIENDS'); ?>');
    });
</script>