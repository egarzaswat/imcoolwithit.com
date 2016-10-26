<?php 
/**
* @version      4.8.0 10.02.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php 	
JHTML::_('behavior.tooltip');
$metadata = $this->meta_data;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=meta_data" method="post" name="adminForm" id="adminForm" enctype = "multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
    <tr>
        <td class="key">
            <?php echo 'Alias'; ?>
        </td>
        <td>
            <input type="text" class="inputbox" name="alias" value="<?php echo $metadata->alias?>" style="width:600px;" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo 'Title'; ?>
        </td>
        <td>
            <input type="text" class="inputbox" name="title" value="<?php echo $metadata->title?>" style="width:600px;" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo 'Keywords'; ?>
        </td>
        <td>
            <input type="text" class="inputbox" name="keywords" value="<?php echo $metadata->keywords?>" style="width:600px;" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo 'Description'; ?>
        </td>
        <td>
            <input type="text" class="inputbox" name="description" value="<?php echo $metadata->description?>" style="width:600px;" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo 'Header'; ?>
        </td>
        <td>
            <input type="text" class="inputbox" name="header" value="<?php echo $metadata->header?>" style="width:600px;" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo 'Content'; ?>
        </td>
        <td class="description_h">
            <?php
            $editor=JFactory::getEditor();
            print $editor->display('content',  $metadata->content, '600', '350', '75', '20' );
            ?>
        </td>
    </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="id" value="<?php echo $metadata->id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>