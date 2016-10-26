<?php 
/**
* @version      4.10.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$rows = $this->rows;
$count = count($rows);
$i = 0; 
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions("attributes");?>
<form action="index.php?option=com_jshopping&controller=attributesgroups" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
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
      <?php echo _JSHOP_TITLE; ?>
    </th>
    <th colspan="3" width="40">
      <?php echo _JSHOP_ORDERING; ?>
      <a href="javascript:saveorder(<?php echo ($count-1);?>, 'saveorder')" title="Save Order" class="saveorder"></a>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_EDIT; ?>
    </th>
    <th width="40" class="center">
        <?php echo _JSHOP_ID; ?>
    </th>
  </tr>
</thead>
<?php foreach ($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i + 1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=attributesgroups&task=edit&id=<?php echo $row->id;?>"><?php echo $row->name;?></a>
   </td>
   <td align="right" width="20">
    <?php
        if ($i!=0) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=attributesgroups&task=order&id='.$row->id.'&move=-1"><i class="icon-uparrow"></i></a>';
    ?>
   </td>
   <td align="left" width="20">
    <?php
        if ($i!=$count-1) echo '<a class="btn btn-micro" href="index.php?option=com_jshopping&controller=attributesgroups&task=order&id='.$row->id.'&move=1"><i class="icon-downarrow"></i></a>';
    ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id = "ord<?php echo $row->id;?>"  size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="inputordering" style="text-align: center" />
   </td>
   <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=attributesgroups&task=edit&id=<?php print $row->id;?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <?php print $row->id;?>
   </td>
  </tr>
<?php
$i++;
}?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>