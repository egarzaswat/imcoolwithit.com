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
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=<?php print $this->question_id?>" method="post" name="adminForm" id="adminForm" enctype = "multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
    <tr>
       <td class="key">
         <?php echo 'Answer'; ?> *
       </td>
       <td>
           <input type="text" class="inputbox" name="answer" value="<?php echo $this->answer->answer?>" style="width:600px;" />
       </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo _JSHOP_SHOW_FOR_CATEGORY;?>*
     </td>
     <td>
       <?php echo $this->negative;?>
     </td>
   </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="hidemainmenu" value="0"/>
    <input type="hidden" name="id_question" value="<?php print $this->question_id ?>"/>
    <input type="hidden" name="id" value="<?php echo $this->answer->id ?>"/>
<?php print $this->tmp_html_end?>
</form>
</div>