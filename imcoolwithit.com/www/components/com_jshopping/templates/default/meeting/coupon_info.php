<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-match col-sm-8 col-sm-offset-2 col-xs-12" style="margin-top: 45px;">

    <div class="page-popup row">

        <h1 class="title"><?php print JText::_('LINCUP_MATCH_TITLE'); ?></h1>
        <span class="close-page">X</span>

        <div class="lincup-match-info">
            <span class="text"><?php print $this->message; ?></span>
            <div class="col-sm-8 col-sm-offset-2">
                <div class="users-photo">
                    <div class="item">
                        <a href="<?php print $this->my_link; ?>">
                            <img class="user-image" src="<?php echo $this->my_photo; ?>"/>
                        </a>
                    </div>
                    <div class="item">
                        <a href="<?php print $this->user_link; ?>">
                            <img class="user-image" src="<?php echo $this->user_photo; ?>"/>
                        </a>
                    </div>
                </div>

                <div class="links">
                    <a href="<?php echo $this->message_link; ?>"><?php print JText::_('LINCUP_MATCH_MESSAGE'); ?></a>

                    <?php if($this->show_link){ ?>
                        <a href="<?php echo $this->confirm_link; ?>"><?php print JText::_('LINCUP_MATCH_INFO'); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function () {
        jQuery(location).attr('href', '<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MESSAGING_RECEIVED'); ?>');
    });
</script>