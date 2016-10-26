<?php
/**
* @version      4.10.2 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<fieldset class="uploadform">
<legend><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
<form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table>
<tr>
	<td width="140">
	    <label for="install_package"><?php echo _JSHOP_UPDATE_PACKAGE_FILE; ?>:</label>
	</td>
	<td>
	    <input class="input_box" id="install_package" name="install_package" type="file" size="57" />
        <input type="hidden" name="installtype" value="package" />
	    <input class="button btn" type="submit" value="<?php echo _JSHOP_UPDATE_PACKAGE_UPLOAD; ?>" />
	</td>
</tr>
<?php $pkey="etemplatevar1";if ($this->$pkey){print $this->$pkey;}?>
</table>
</form>
</fieldset>   

<form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
<fieldset class="uploadform">
<legend><?php echo _JSHOP_UPDATE_UPLOAD_FROM_URL_PACKAGE_FILE; ?></legend>
<table>
<tr>
    <td width="140">
        <label for="install_url"><?php echo _JSHOP_UPDATE_UPLOAD_FROM_URL_PACKAGE_FILE; ?>:</label>
    </td>
    <td>
        <input style="margin:0px;" class="input_box" id="install_url" name="install_url" type="text" value="http://" size="57" />
        <input type="hidden" name="installtype" value="url" />
        <input class="button btn" type="submit" value="<?php echo _JSHOP_UPDATE_PACKAGE_UPLOAD; ?>" />
    </td>
</tr>
<?php $pkey="etemplatevar2";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
<?php print $this->tmp_html_end?>
</form>
</div>