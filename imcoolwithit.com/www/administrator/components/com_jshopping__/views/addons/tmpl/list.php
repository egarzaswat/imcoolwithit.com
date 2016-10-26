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
$count=count($rows);
$i=0;
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=addons" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width="10">#</th>
    <th align="left">
      <?php echo _JSHOP_TITLE?>
    </th>
    <th width="120">
        <?php echo _JSHOP_VERSION?>
    </th>
    <th width="60" class="center">
        <?php echo _JSHOP_DESCRIPTION?>
    </th>
    <th width="60" class="center">
        <?php echo _JSHOP_KEY?>
    </th>
    <th width="60" class="center">
        <?php echo _JSHOP_CONFIG?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_DELETE?>
    </th>
    <th width="40" class="center">
        <?php echo _JSHOP_ID?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo $row->name;?>
   </td>
   <td>
    <?php echo $row->version;?>
    <?php if ($row->version_file_exist){?>
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=addons&task=version&id=<?php print $row->id?>'><img src='components/com_jshopping/images/jshop_info_s.png'></a>
    <?php }?>
   </td>
   <td class="center">
   <?php if ($row->info_file_exist){?>
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=addons&task=info&id=<?php print $row->id?>'><img src='components/com_jshopping/images/jshop_info_s.png'></a>
   <?php }?>
   </td>
   <td class="center">
   <?php if ($row->usekey){?>
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=licensekeyaddon&alias=<?php print $row->alias?>&back=<?php print $this->back64?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   <?php }?>
   </td>
   <td class="center">
   <?php if ($row->config_file_exist){?>
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=addons&task=edit&id=<?php print $row->id?>'>
            <i class="icon-edit"></i>
        </a>
    <?php }?>
   </td>
   <td class="center">
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=addons&task=remove&id=<?php print $row->id?>' onclick="return confirm('<?php print _JSHOP_DELETE_ALL_DATA?>')">
        <i class="icon-delete"></i>
    </a>
   </td>
   <td class="center">
    <?php print $row->id;?>
   </td>
  </tr>
<?php $i++;}?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>