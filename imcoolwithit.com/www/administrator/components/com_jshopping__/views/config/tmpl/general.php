<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$jshopConfig = JSFactory::getConfig();
$lists = $this->lists;
JHtml::_('bootstrap.tooltip');
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuConfigs('general');?>
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="1">

<div class="col100 jshop_edit">
<fieldset class="adminform">
    <legend><?php echo _JSHOP_GENERAL_PARAMETERS ?></legend>
<table class="admintable table-striped">
<tr>
    <td class="key" style="width:220px">
        <?php echo _JSHOP_EMAIL_ADMIN;?>
    </td>
    <td>
        <input type="text" name="contact_email" class="inputbox" value="<?php echo $jshopConfig->contact_email;?>" />
        <?php echo JHTML::tooltip(_JSHOP_EMAIL_ADMIN_INFO);?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_DEFAULT_LANGUAGE;?>
    </td>
    <td>
        <?php echo $lists['languages']; ?>
        <?php echo JHTML::tooltip(_JSHOP_INFO_DEFAULT_LANGUAGE);?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_TEMPLATE;?>
    </td>
    <td>
        <?php echo $lists['template'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_DISPLAY_PRICE_ADMIN;?>
    </td>
    <td>
        <?php echo $lists['display_price_admin']; ?>        
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_DISPLAY_PRICE_FRONT;?>
    </td>
    <td>
        <?php echo $lists['display_price_front']; ?> 
        <a class="btn btn-small btn-primary" href="index.php?option=com_jshopping&controller=configdisplayprice"><?php print _JSHOP_EXTENDED_CONFIG;?></a>        
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_USE_SSL?>
    </td>
    <td>
        <input type="checkbox" name="use_ssl"  value="1" <?php if ($jshopConfig->use_ssl) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_SAVE_INFO_TO_LOG?>
    </td>
    <td>
        <input type="checkbox" name="savelog" id="savelog" value="1" <?php if ($jshopConfig->savelog) echo 'checked="checked"';?> onclick="if (!jQuery('#savelog').attr('checked')) jQuery('#savelogpaymentdata').attr('checked',false);" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_SAVE_PAYMENTINFO_TO_LOG?>
    </td>
    <td>
        <input type="checkbox" name="savelogpaymentdata" id="savelogpaymentdata" value="1" <?php if ($jshopConfig->savelogpaymentdata) echo 'checked="checked"';?> onclick="if (!jQuery('#savelog').attr('checked')) this.checked=false;" />
        <?php echo JHTML::tooltip(_JSHOP_SAVE_PAYMENTINFO_TO_LOG_INFO);?>
    </td>
</tr>
<tr>
     <td class="key">
       <?php echo _JSHOP_STORE_DATE_FORMAT;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="store_date_format" value="<?php echo $jshopConfig->store_date_format?>" />
     </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_SECURITYKEY?>
    </td>
    <td>
        <input type="text" name="securitykey" size="50" value="<?php print $jshopConfig->securitykey;?>" />
        <?php echo JHTML::tooltip(_JSHOP_INFO_SECURITYKEY);?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _JSHOP_LICENSEKEY?>
    </td>
    <td>
        <input type="text" name="licensekod" size="50" value="<?php print $jshopConfig->licensekod;?>" />
        <a href="http://www.webdesigner-profi.de/joomla-webdesign/joomla-shop/forum/posts/22/373.html" target="_blank"><?php echo JHTML::tooltip(_JSHOP_INFO_LICENSEKEY);?></a>
    </td>
</tr>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
	
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
</form>
</div>