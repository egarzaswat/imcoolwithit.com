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
$count=count($rows);
$i=0;
$saveOrder=$this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=manufacturers" method="post" name="adminForm" id="adminForm">
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
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th colspan="3" width="40">
      <?php echo JHTML::_( 'grid.sort', _JSHOP_ORDERING, 'ordering', $this->filter_order_Dir, $this->filter_order);?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50" class="center">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_EDIT;?>
    </th>
    <th width="40" class="center">
        <?php echo JHTML::_( 'grid.sort', _JSHOP_ID, 'manufacturer_id', $this->filter_order_Dir, $this->filter_order);?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->manufacturer_id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=manufacturers&task=edit&man_id=<?php echo $row->manufacturer_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td align="right" width="20">
    <?php
        if ($i != 0 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=manufacturers&task=order&id='.$row->manufacturer_id.'&move=-1"><i class="icon-uparrow"></i></a>';
    ?>
   </td>
   <td align="left" width="20">
    <?php
        if ($i != $count - 1 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=manufacturers&task=order&id='.$row->manufacturer_id.'&move=1"><i class="icon-downarrow"></i></a>';
    ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->manufacturer_id;?>" value="<?php echo $row->ordering; ?>" class="inputordering" />
   </td>
   <td class="center">
     <?php echo JHtml::_('jgrid.published', $row->manufacturer_publish, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=manufacturers&task=edit&man_id=<?php print $row->manufacturer_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <?php print $row->manufacturer_id;?>
   </td>
   </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>