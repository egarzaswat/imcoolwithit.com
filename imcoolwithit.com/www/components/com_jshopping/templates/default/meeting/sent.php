<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-sent col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
    <div class="page-popup row">
        <h1 class="title"><?php print JText::_('LINCUP_SENT'); ?></h1>
        <span class="close-page">X</span>
        <span class="lincup-sent-info page-info"><?php print JText::sprintf('LINCUP_SENT_INFO', $this->username); ?></span>
    </div>
</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS'); ?>');
    });
</script>