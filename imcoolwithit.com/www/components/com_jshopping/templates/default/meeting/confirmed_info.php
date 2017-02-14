<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-confirmed col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1" style="margin-top: 45px;">

    <div class="page-popup row">
        <h1 class="title"><?php print JText::_('LINCUP_CONFIRMED_TITLE'); ?></h1>
        <span class="close-page">X</span>

        <div class="lincup-confirmed-info">
            <span class="text"><?php print JText::_('LINCUP_CONFIRMED_TEXT'); ?></span>
            <div class="bottom-bl">
                <span class="tokens-icon">
                    <img src="/templates/protostar/images/system/token.png">
                </span>
                <span><?php print JText::sprintf('LINCUP_CONFIRMED_TOKENS', $this->tokens); ?></span>
            </div>



<!--            <span class="lincup-tokens text-none-select">-->
<!--                <span class="tokens-count">--><?php //print $this->tokens; ?><!--</span>-->
<!--                <span class="tokens-info">--><?php //print JText::sprintf('LINCUP_CONFIRMED_TOKENS', $this->tokens); ?><!--</span>-->
<!--            </span>-->
        </div>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS'); ?>');
    });
</script>