<?php
    defined('_JEXEC') or die('Restricted access');
    $conf = new JConfig();
	
	if (@isset($_REQUEST['usr']))
	{ $u_name = $_REQUEST['usr']; }
	else { $u_name = 'User'; }

    if (@isset($_REQUEST['id']))
    { $link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_FULL_USER_PAGE') . '?user=' . $_REQUEST['id']; }
    else { $link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST');; }
?>

<div class="add-friends col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1">

    <div class="page-popup row">

        <h1 class="title"><?php print JText::_('TOKEN_SENT'); ?></h1>

        <span class="close-page">X</span>

        <span class="text"><?php print JText::sprintf('TOKEN_SENT_INFO', $u_name, $conf->day_expires_add_to_friends); ?></span>

    </div>

</div>

<script type="text/javascript">
    jQuery('.close-page').click(function(){
        jQuery(location).attr('href','<?php print $link; ?>');
    });
</script>