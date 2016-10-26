<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php
$rows=$this->rows;
$i=0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="currency_ordering";
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions("currencies");;?>
<form action="index.php?option=com_jshopping&controller=currencies" method="post" name="adminForm" id="adminForm">
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
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'currency_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="60" class="center">
        <?php echo _JSHOP_DEFAULT;?>    
    </th>
    <th width="100">
        <?php echo JHTML::_('grid.sort', _JSHOP_VALUE_CURRENCY, 'currency_value', $this->filter_order_Dir, $this->filter_order); ?> 
    </th>    
    <th colspan="3" width="80">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'currency_ordering', $this->filter_order_Dir, $this->filter_order); ?>
    </th>    
    <th width="30" class="center">
      <?php echo _JSHOP_PUBLISH; ?>
    </th>
    <th width="50" class="center">
        <?php print _JSHOP_EDIT; ?>
    </th>
    <th width="40" class="center">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'currency_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php
 $count=count($rows);
 foreach($rows as $row){
  ?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->currency_id);?>
   </td>
   <td>
     <a title="<?php echo _JSHOP_EDIT_CURRENCY;?>" href="index.php?option=com_jshopping&controller=currencies&task=edit&currency_id=<?php echo $row->currency_id; ?>"><?php echo $row->currency_name;?></a>
   </td>
   <td class="center">
     <?php if ($this->config->mainCurrency==$row->currency_id) {?>
        <a class="btn btn-micro">            
            <i class="icon-default"></i>
        </a>
     <?php }?>
   </td>
   <td align="center">
       <?php echo $row->currency_value;?>
   </td>
   <td align="right" width="20">
    <?php
      if ($i != 0 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=currencies&task=order&id=' . $row->currency_id . '&order=up&number=' . $row->currency_ordering . '"><i class="icon-uparrow"></i></a>';
    ?>
   </td>
   <td align="left" width="20">
      <?php
        if ($i != $count - 1 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=currencies&task=order&id=' . $row->currency_id . '&order=down&number=' . $row->currency_ordering . '"><i class="icon-downarrow"></i></a>';
      ?>
   </td>
   <td align = "center" width = "10">
        <input type="text" size="5" value="<?php echo $row->currency_ordering;?>" <?php echo 'disabled'?> class="inputordering" />
    </td>
   <td class="center">     
     <?php echo JHtml::_('jgrid.published', $row->currency_publish, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=currencies&task=edit&currency_id=<?php print $row->currency_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
        <?php print $row->currency_id;?>
   </td>
  </tr>
<?php
$i++; 
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="<?php echo JRequest::getVar('task')?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>