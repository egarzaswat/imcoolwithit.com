<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
displaySubmenuOptions("shippings");
$rows=$this->rows;
?>
<form action="index.php?option=com_jshopping&controller=shippingextprice" method="post" name="adminForm" id="adminForm">
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
    <th align="left" width="300">
      <?php echo _JSHOP_TITLE;?>
    </th>
    <th>
        <?php echo _JSHOP_DESCRIPTION;?>
    </th>
    <th>
      <?php echo _JSHOP_ORDERING;?>
    </th>
    <th width="30">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="50">
        <?php echo _JSHOP_CONFIG;?>
    </th>
    <th width="50">
        <?php echo _JSHOP_DELETE;?>
    </th>
    <th width="40">
        <?php echo _JSHOP_ID;?>
    </th>
  </tr>
</thead>  
<?php
$count=count($rows);
foreach($rows as $i=>$row){?>
<tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo JHtml::_('grid.id', $i, $row->id); ?>
   </td>
   <td>     
        <?php echo $row->name;?>     
   </td>
   <td>
        <?php echo $row->description;?>
   </td>
   <td class="order" style="width:80px;">
    <span><?php if ($i != 0) echo JHtml::_('jgrid.orderUp', $i, "orderup");?></span>
    <span><?php if ($i != $count - 1) echo JHtml::_('jgrid.orderDown', $i, "orderdown");?></span>
   </td>
   <td align="center">     
     <?php echo JHtml::_('jgrid.published', $row->published, $i);?>
   </td>
   <td align="center">
        <a href='index.php?option=com_jshopping&controller=shippingextprice&task=edit&id=<?php print $row->id;?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
    <a href='index.php?option=com_jshopping&controller=shippingextprice&task=remove&id=<?php print $row->id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>')"><img src='components/com_jshopping/images/publish_r.png'></a>
   </td>
   <td align="center">
    <?php print $row->id;?>
   </td>
  </tr>
<?php $i++; } ?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>