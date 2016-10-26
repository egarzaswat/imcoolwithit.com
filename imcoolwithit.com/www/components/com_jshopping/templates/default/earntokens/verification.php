<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="verification col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('VERIFICATION_TITLE'); ?></h1>

        <span class="close-page"></span>

        <div class="verification-info">
            <span><?php print JText::_('VERIFICATION_INFO'); ?></span>
            <a href="<?php print $this->link_verify_email; ?>"><?php print JText::_('VERIFICATION_BUTTON'); ?></a>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EARN_TOKENS'); ?>');
    });
</script>
