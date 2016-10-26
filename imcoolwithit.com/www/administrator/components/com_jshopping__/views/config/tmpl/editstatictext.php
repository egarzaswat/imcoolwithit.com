<?php 
/**
* @version      4.11.3 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$jshopConfig=JSFactory::getConfig();
JHTML::_('behavior.tooltip');
displaySubmenuConfigs('statictext');
$editor=JFactory::getEditor();
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="id" value="<?php print $this->row->id?>">

<div class="col100">
<fieldset class="adminform">
    <legend><?php if (defined("_JSHP_STPAGE_".$this->row->alias)) print constant("_JSHP_STPAGE_".$this->row->alias); else print $this->row->alias;?></legend>
<table class="admintable" width="100%">
<?php $pkey="etemplatevarstart";if ($this->$pkey){print $this->$pkey;}?>
<?php if (!$this->row->id){?>
<tr>
   <td class="key" style="width:220px;">
     <?php echo _JSHOP_ALIAS; ?>
   </td>
   <td>
     <input type="text" class="inputbox" name="alias" size="40" value="<?php echo $this->row->alias?>" />
   </td>
</tr>
<?php }
foreach($this->languages as $lang){
$field="text_".$lang->language;
?>
<tr>
   <td class="key" >
     <?php echo _JSHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
     <div style="font-size:10px;"><?php if (defined("_JSHP_STPAGE_INFO_".$this->row->alias)) print constant("_JSHP_STPAGE_INFO_".$this->row->alias);?></div>
   </td>
   <td>
     <?php print $editor->display( 'text'.$lang->id,  $this->row->$field , '100%', '350', '75', '20' ); ?>
   </td>
</tr>
<?php $pkey="etemplatevar".$lang->language;if ($this->$pkey){print $this->$pkey;}?>
<?php } ?>
<tr>
   <td class="key">
     <?php echo _JSHOP_USE_FOR_RETURN_POLICY; ?>
   </td>
   <td>
     <input type = "checkbox"  name = "use_for_return_policy" size="40" value = "1"  <?php if($this->row->use_for_return_policy) echo 'checked = "checked"';?> />
   </td>
</tr>    
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
</form>
</div>