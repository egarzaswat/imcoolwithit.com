<?php 
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
displaySubmenuOptions();
$rows = $this->rows;
$count = count ($rows);
$i = 0; 
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="A.attr_ordering";
?>

<select id="select_category">
    <option value="0" <?php if($_GET['category'] == null || !isset($_GET['category']) || $_GET['category'] == 0){ print 'selected="selected"'; } ?> ><?php print JText::_('JGLOBAL_ALL_CATEGORIES'); ?></option>
    <?php foreach ($this->categories as $key => $value) {
        if($value->category_id != 2){ ?>
            <option value="<?php print $value->category_id; ?>" <?php if((int)$_GET['category'] == $value->category_id){print 'selected="selected"'; } ?> ><?php print  $value->name; ?></option>
        <?php }
    } ?>
</select>

<form action="index.php?option=com_jshopping&controller=attributes" method="post" name="adminForm" id="adminForm">
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
    <th width="600" align="left">
      <?php echo JHTML::_('grid.sort', 'Questions', 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align="left">
      <?php echo _JSHOP_OPTIONS; ?>
    </th>
<!--    <th align = "left">-->
<!--        --><?php //echo JHTML::_('grid.sort', _JSHOP_GROUP, 'groupname', $this->filter_order_Dir, $this->filter_order);?>
<!--    </th>-->
    <th colspan="3" width="40">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'A.attr_ordering', $this->filter_order_Dir, $this->filter_order); ?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_EDIT; ?>
    </th>
    <th width="40" class="center">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'A.attr_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>
<?php $count=count($rows);foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i + 1;?>
   </td>
   <td>     
     <?php echo JHtml::_('grid.id', $i, $row->attr_id);?>
   </td>
   <td>
    <?php if (!$row->count_values) {?><img src="components/com_jshopping/images/disabled.png" alt="" /><?php }?>
     <a href="index.php?option=com_jshopping&controller=attributes&task=edit&attr_id=<?php echo $row->attr_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=attributesvalues&task=show&attr_id=<?php echo $row->attr_id?>"><?php echo _JSHOP_OPTIONS?></a>
     <?php echo $row->values;?>
   </td>
<!--   <td>-->
<!--    --><?php //print $row->groupname?>
<!--   </td>-->
   <td align="right" width="20">
   <?php
        if ($i != 0 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=attributes&task=order&id=' . $row->attr_id . '&order=up&number=' . $row->attr_ordering . '"><i class="icon-uparrow"></i></a>';
   ?>
   </td>
   <td align="left" width="20">
   <?php
        if ($i!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=attributes&task=order&id=' . $row->attr_id . '&order=down&number=' . $row->attr_ordering . '"><i class="icon-downarrow"></i></a>';
   ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->attr_id;?>" size="5" value="<?php echo $row->attr_ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=attributes&task=edit&attr_id=<?php print $row->attr_id;?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <?php print $row->attr_id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="<?php echo JRequest::getVar('task', 0)?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>

<script type="text/javascript">
    jQuery('#select_category').change(function () {
        jQuery(location).attr('href', '/administrator/index.php?option=com_jshopping&controller=attributes&category=' + jQuery(this).val());
    });
</script>