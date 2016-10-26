<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$rows=$this->rows;
$i=0;
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuConfigs('general');?>
<form action="index.php?option=com_jshopping&controller=configdisplayprice" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th>
        <?php echo _JSHOP_COUNTRY; ?>
    </th>
    <th width="100">
        <?php echo _JSHOP_DISPLAY_PRICE; ?>
    </th>
    <th width="160">
        <?php echo _JSHOP_DISPLAY_PRICE_FOR_FIRMA; ?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_EDIT; ?>
    </th>
    <th width="40" class="center">
        <?php echo _JSHOP_ID; ?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
    <?php echo $row->countries;?>
   </td>
   <td>
    <?php echo $this->typedisplay[$row->display_price];?>
   </td>
   <td>
    <?php echo $this->typedisplay[$row->display_price_firma];?>
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=configdisplayprice&task=edit&id=<?php print $row->id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
        <?php print $row->id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>