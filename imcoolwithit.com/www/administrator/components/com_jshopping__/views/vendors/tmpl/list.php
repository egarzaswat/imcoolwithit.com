<?php
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$i = 0;
$rows = $this->rows;
$pageNav = $this->pageNav;
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=vendors">
<?php print $this->tmp_html_start?>

<div id="filter-bar" class="btn-toolbar">

    <?php print $this->tmp_html_filter?> 

    <div class="filter-search btn-group pull-left">
        <input type="text" id="text_search" name="text_search" placeholder="<?php print _JSHOP_SEARCH?>" value="<?php echo htmlspecialchars($this->text_search);?>" />
    </div>

    <div class="btn-group pull-left hidden-phone">
        <button class="btn hasTooltip" type="submit" title="<?php print _JSHOP_SEARCH?>">
            <i class="icon-search"></i>
        </button>
        <button class="btn hasTooltip" onclick="document.id('text_search').value='';this.form.submit();" type="button" title="<?php print _JSHOP_CLEAR?>">
            <i class="icon-remove"></i>
        </button>
    </div>
    
</div>
 
<table class="table table-striped" width="100%">
<thead>
<tr>
     <th width="20">
        #
     </th>
     <th width="20">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
     </th>
     <th width="150" align="left">
       <?php echo _JSHOP_USER_FIRSTNAME?>
     </th>
     <th width="150" align="left">
       <?php echo _JSHOP_USER_LASTNAME?>
     </th>
     <th align="left">
       <?php echo _JSHOP_STORE_NAME?>
     </th>
     <th width="150">
       <?php echo _JSHOP_EMAIL?>
     </th>
     <th width="60" class="center">
        <?php echo _JSHOP_DEFAULT;?>    
    </th>	 	      
     <th width="50" class="center">
        <?php echo _JSHOP_EDIT;?>
    </th>
     <th width="40" class="center">
        <?php echo _JSHOP_ID;?>
    </th>
</tr>
</thead> 
<?php 
$i=0; 
foreach($rows as $row){?>
<tr class="row<?php echo ($i%2);?>">
     <td align="center">
        <?php echo $pageNav->getRowOffset($i);?>
     </td>
     <td align="center">
        <?php echo JHtml::_('grid.id', $i, $row->id);?>
     </td>
     <td>
        <?php echo $row->f_name?>
     </td>
     <td>
        <?php echo $row->l_name;?>
     </td>
     <td>
        <?php echo $row->shop_name;?>
     </td>
     <td>
        <?php echo $row->email;?>
     </td>
     <td class="center">
     <?php if ($row->main==1) {?>
        <a class="btn btn-micro">
            <i class="icon-default"></i>
        </a>
     <?php }?>
     </td>
     <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=vendors&task=edit&id=<?php print $row->id?>'>
            <i class="icon-edit"></i>
        </a>
     </td>
     <td class="center">
        <?php print $row->id?>
     </td>
</tr>
<?php 
$i++;
}?>
<tfoot>
 <tr>   
    <td colspan="11">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
 </tr>
</tfoot>
</table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>