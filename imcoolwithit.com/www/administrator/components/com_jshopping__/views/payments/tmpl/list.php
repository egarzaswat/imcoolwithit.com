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
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="payment_ordering";
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=payments" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped" width="70%">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
	  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="12%" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_CODE, 'payment_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="15%" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_ALIAS, 'payment_class', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <?php echo $this->tmp_extra_column_headers?>
    <th width = "15%" align = "left">
        <?php echo JHTML::_('grid.sort', _JSHOP_SCRIPT_NAME, 'scriptname', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="40" colspan="3">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'payment_ordering', $this->filter_order_Dir, $this->filter_order); ?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50" class="center">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="50" class="center">
    	<?php print _JSHOP_EDIT;?>
    </th>
    <th width="40" class="center">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'payment_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo JHtml::_('grid.id', $i, $row->payment_id);?>
   </td>
   <td>
     <a title="<?php echo _JSHOP_EDIT_PAYMENT;?>" href="index.php?option=com_jshopping&controller=payments&task=edit&payment_id=<?php echo $row->payment_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td>
     <?php echo $row->payment_code;?>
   </td>
   <td>
     <?php echo $row->payment_class;?>
   </td>
   <?php echo $row->tmp_extra_column_cells?>
   <td>
     <?php echo $row->scriptname;?>
   </td>
   <td align="right" width="20">
    <?php
      if ($i!=0 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=payments&task=order&id=' . $row->payment_id . '&order=up&number=' . $row->payment_ordering . '"><i class="icon-uparrow"></i></a>';
    ?>
   </td>
   <td align="left" width="20">
      <?php
        if ($i!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=payments&task=order&id=' . $row->payment_id . '&order=down&number=' . $row->payment_ordering . '"><i class="icon-downarrow"></i></a>';
      ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->payment_id;?>" value="<?php echo $row->payment_ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
   </td>
   <td class="center">
     <?php echo JHtml::_('jgrid.published', $row->payment_publish, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=payments&task=edit&payment_id=<?php print $row->payment_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
        <?php print $row->payment_id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
</table>
    
<?php if ($this->config->advert) include('payment_advert.php');?>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>