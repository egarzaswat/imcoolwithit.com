<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$count = count($this->reviews);
$i = 0;
JHtml::_('formbehavior.chosen', '.chosen-select');
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=reviews" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>

<div class="js-stools clearfix jshop_block_filter">
    
    <div class="js-stools-container-bar">
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
    
    <div class="clearfix"></div>
    
    <div class="js-stools-container-filters">

        <?php print $this->tmp_html_filter?>
        
        <div class="js-stools-field-filter">
            <?php echo $this->categories;?>
        </div>
        <div class="js-stools-field-filter">
            <?php echo $this->products_select;?>
        </div>
                
    </div>
    
</div>

<table class="table table-striped" >
<thead> 
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width = "200" align = "left">
        <?php echo JHTML::_('grid.sort', _JSHOP_NAME_PRODUCT, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_USER, 'pr_rew.user_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>        
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_EMAIL, 'pr_rew.user_email', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align = "left">
        <?php echo JHTML::_('grid.sort', _JSHOP_PRODUCT_REVIEW, 'pr_rew.review', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_REVIEW_MARK, 'pr_rew.mark', $this->filter_order_Dir, $this->filter_order); ?>
    </th> 
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_DATE, 'pr_rew.time', $this->filter_order_Dir, $this->filter_order); ?> 
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', 'IP', 'pr_rew.ip', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_PUBLISH;?>       
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_EDIT; ?>
    </th>
    <th width="50" class="center">
        <?php echo _JSHOP_DELETE; ?>
    </th>
    <th width="40" class="center">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'pr_rew.review_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead> 
<?php foreach ($this->reviews as $row){?>
<tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $this->pagination->getRowOffset($i);?>             
   </td>
   <td>         
     <?php echo JHtml::_('grid.id', $i, $row->review_id);?>
   </td>
   <td>
     <?php echo $row->name;?>
   </td>
   <td>
     <?php echo $row->user_name;?>
   </td> 
   <td>
     <?php echo $row->user_email;?>
   </td>     
   <td>
     <?php echo $row->review;?>
   </td> 
   <td>
     <?php echo $row->mark;?>
   </td> 
   <td>
     <?php echo $row->dateadd;?>
   </td>
   <td>
     <?php echo $row->ip;?>
   </td>
   <td class="center">
     <?php echo JHtml::_('jgrid.published', $row->publish, $i);?>
   </td> 
   <td class="center">
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=reviews&task=edit&cid[]=<?php print $row->review_id?>'>
        <i class="icon-edit"></i>
    </a>
   </td>
   <td class="center">
    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=reviews&task=remove&cid[]=<?php print $row->review_id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>')">
        <i class="icon-delete"></i>
    </a>
   </td>
   <td class="center">
    <?php print $row->review_id;?>
   </td>
</tr>
<?php
$i++;
}
?>
 <tfoot>
 <tr>
    <td colspan="13">
		<div class = "jshop_list_footer"><?php echo $this->pagination->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $this->pagination->getLimitBox(); ?></div>
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