<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$rows = $this->rows;
$pageNav = $this->pageNav;
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
JHtml::_('formbehavior.chosen', '.chosen-select');
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=countries" method="post" name="adminForm" id="adminForm">

<?php print $this->tmp_html_start?>

<div class="js-stools clearfix jshop_block_filter">
    <div class="js-stools-container-filters">
        <?php print $this->tmp_html_filter?>
        <div class="js-stools-field-filter">
            <label><?php print _JSHOP_SHOW?>:</label>
            <div class="control"><?php print $this->filter;?></div>
        </div>
    </div>
</div>

<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width="10">
      #
    </th>
    <th width="20">
	  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_COUNTRY, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
	<th width="90">
      <?php echo JHTML::_('grid.sort', _JSHOP_CODE, 'country_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="90">
      <?php echo JHTML::_('grid.sort', _JSHOP_CODE . '2', 'country_code_2', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th colspan="3" width="40">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
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
	<th width="50" class="center">
      <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'country_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>
<?php
$count=count($rows);
foreach($rows as $row){
?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->country_id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=countries&task=edit&country_id=<?php echo $row->country_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td align="center">
     <?php echo $row->country_code;?>
   </td>
   <td align="center">
     <?php echo $row->country_code_2;?>
   </td>
   <td align="right" width="20">
    <?php
      if ($i != 0 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=countries&task=order&id=' . $row->country_id . '&order=up&number=' . $row->ordering . '"><i class="icon-uparrow"></i></a>';
    ?>
   </td>
   <td align="left" width="20">
      <?php
        if ($i != $count - 1 && $saveOrder) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=countries&task=order&id=' . $row->country_id . '&order=down&number=' . $row->ordering . '"><i class="icon-downarrow"></i></a>';
      ?>
   </td>
   <td class="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->country_id;?>"  size="5" value="<?php echo $row->ordering; ?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />    
   </td>
   <td class="center">     
     <?php echo JHtml::_('jgrid.published', $row->country_publish, $i);?>
   </td>
	<td class="center">
		<a class="btn btn-micro" href='index.php?option=com_jshopping&controller=countries&task=edit&country_id=<?php print $row->country_id;?>'>
            <i class="icon-edit"></i>
        </a>
	</td>
	<td class="center">
     <?php echo $row->country_id;?>
   </td>
  </tr>
<?php
$i++;  
}
?>
<tfoot>
<tr>
	<td colspan="11">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
</tr>
</tfoot>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>