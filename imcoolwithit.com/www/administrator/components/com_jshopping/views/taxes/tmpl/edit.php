<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$row=$this->tax;
$edit=$this->edit;
JHTML::_('behavior.tooltip');
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=taxes" method="post"name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table width="100%" class="admintable">
   <tr>
     <td class="key" style="width:250px;">
       <?php echo _JSHOP_TITLE;?>*
     </td>
     <td>
       <input type="text" class="inputbox" id="tax_name" name="tax_name" value="<?php echo $row->tax_name;?>" />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo _JSHOP_VALUE;?>*
     </td>
     <td>
       <input type="text" class="inputbox" id="tax_value" name="tax_value" value="<?php echo $row->tax_value;?>" /> %
       <?php echo JHTML::tooltip(_JSHOP_VALUE_TAX_INFO);?>
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
<input type="hidden" name="tax_id" value="<?php echo $row->tax_id?>" />
<?php }?>
<?php print $this->tmp_html_end?>
</form>
<script type="text/javascript">
Joomla.submitbutton=function(task){
     if (task == 'save' || task == 'apply'){
         var taxValue=$F_('tax_value');
         if (isNaN(taxValue)){
           alert ('<?php echo _JSHOP_WRITE_TAX_NO_VALID;?>');
           return 0;
         } else if (taxValue < 0 || taxValue >= 100){
           alert ('<?php echo _JSHOP_WRITE_TAX_BIG_LESS;?>');
           return 0;
         }
     }
     Joomla.submitform(task);
 }
</script>
</div>