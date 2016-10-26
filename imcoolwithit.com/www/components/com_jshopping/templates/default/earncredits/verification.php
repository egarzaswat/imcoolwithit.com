<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="add-friends col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1">

    <div class="page-popup row">

        <h1 class="title"><?php print JText::_('VERIFICATION_TITLE'); ?></h1>

        <span class="close-page">X</span>

        <span class="token-sent  page-info">
            <span><?php print JText::_('VERIFICATION_INFO'); ?></span>
            <a href="<?php print $this->link_verify_email; ?>"><?php print JText::_('VERIFICATION_BUTTON'); ?></a>
        </span>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print $link; ?>');
    });
</script>