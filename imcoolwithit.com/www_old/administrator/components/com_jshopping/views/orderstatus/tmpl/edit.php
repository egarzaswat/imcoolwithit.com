<?php 
/**
* @version      4.9.0 22.10.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$row=$this->order_status; 
$edit=$this->edit; 
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=orderstatus" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
   <?php
   foreach($this->languages as $lang){
   $field="name_".$lang->language;
   ?>
   <tr>
     <td class="key">
       <?php echo _JSHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
     </td>
     <td>
       <input type="text" class="inputbox" id="<?php print $field;?>" name="<?php print $field;?>" value="<?php echo $row->$field;?>" />
     </td>
   </tr>
   <?php }?>
   <tr>
     <td class="key">
       <?php echo _JSHOP_CODE;?>*
     </td>
     <td>
       <input type="text" class="inputbox" id="status_code" name="status_code" value="<?php echo $row->status_code;?>" />
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>
 
<input type="hidden" name="task" value="<?php echo JRequest::getVar('task')?>" />
<input type="hidden" name="edit" value="<?php echo $edit;?>" />
<?php if ($edit) {?>
  <input type="hidden" name="status_id" value="<?php echo $row->status_id?>" />
<?php }?>
<?php print $this->tmp_html_end?>
</form>
</div>