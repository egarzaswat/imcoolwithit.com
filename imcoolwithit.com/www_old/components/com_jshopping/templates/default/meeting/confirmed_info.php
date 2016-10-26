<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-confirmed col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('LINCUP_CONFIRMED_TITLE'); ?></h1>

        <span class="close-page"></span>

        <div class="lincup-confirmed-info">
            <span><?php print JText::_('LINCUP_CONFIRMED_TEXT'); ?></span>
            <span class="lincup-tokens text-none-select">
                <span class="tokens-count"><?php print $this->tokens; ?></span>
                <span><?php print JText::sprintf('LINCUP_CONFIRMED_TOKENS', $this->tokens); ?></span>
            </span>
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS'); ?>');
    });
</script>