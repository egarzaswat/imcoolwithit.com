<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$row=$this->product;
$lists=$this->lists;
$tax_value=$this->tax_value;
$jshopConfig=JSFactory::getConfig();
$currency = $this->currency;
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal', 'a.modal');

$post = JRequest::get('post');
$cat_id = $post["category_id"][0];

$category_id = ($cat_id) ? $cat_id : $_GET['category_id'];

$dispatcher=JDispatcher::getInstance();
?>
<div class="jshop_edit">
<script type="text/javascript">var lang_delete="<?php print _JSHOP_DELETE?>";</script>
<form action="index.php?option=com_jshopping&controller=partners" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" id="adminForm">
<ul class="nav nav-tabs">
    <?php if ($this->product->parent_id==0){?>
    <?php $i=0; foreach($this->languages as $lang){ $i++;?>
        <li <?php if ($i==1){?>class="active"<?php }?>>
            <a href="#<?php print $lang->lang.'-page'?>" data-toggle="tab">
                <?php echo _JSHOP_DESCRIPTION?><?php if ($this->multilang){?> (<?php print $lang->lang?>)<img class="tab_image" src="components/com_jshopping/images/flags/<?php print $lang->lang?>.gif" /><?php }?>
            </a>
        </li>
    <?php }?>
    <li><a href="#main-page" data-toggle="tab"><?php echo _JSHOP_INFO_PRODUCT;?></a></li>
    <?php }?>
    <?php if ($this->product->parent_id==0){
        $dispatcher->trigger('onDisplayProductEditTabsTab', array(&$row, &$lists, &$tax_value));
    }?>

    <?php if ($jshopConfig->admin_show_freeattributes && $this->product->parent_id==0){?>
        <li><a href="#product_freeattribute" data-toggle="tab"><?php echo _JSHOP_FREE_ATTRIBUTES;?></a></li>
    <?php }?>
    <li <?php if ($this->product->parent_id!=0){?>class="active"<?php }?>><a href="#product_images_tab" data-toggle="tab"><?php echo _JSHOP_PRODUCT_IMAGES;?></a></li>
<!--    --><?php //if ($jshopConfig->admin_show_product_video && $this->product->parent_id==0){?>
<!--        <li><a href="#product_videos" data-toggle="tab">--><?php //echo _JSHOP_PRODUCT_VIDEOS;?><!--</a></li>-->
<!--    --><?php //}?>
<!--    --><?php //if ($jshopConfig->admin_show_product_related && $this->product->parent_id==0){?>
<!--        <li><a href="#product_related" data-toggle="tab">--><?php //echo _JSHOP_PRODUCT_RELATED;?><!--</a></li>-->
<!--    --><?php //}?>
 <!--   <?php /*if ($jshopConfig->admin_show_product_files){*/?>
        <li><a href="#product_files" data-toggle="tab"><?php /*echo _JSHOP_FILES;*/?></a></li>
    <?php /*}*/?>
    <?php /*if ($jshopConfig->admin_show_product_extra_field && $this->product->parent_id==0){*/?>
        <li><a href="#product_extra_fields" data-toggle="tab"><?php /*echo _JSHOP_EXTRA_FIELDS;*/?></a></li>
    <?php /*}*/?>

    <?php /*if ($jshopConfig->admin_show_attributes && $this->product->parent_id==0/* && $category_id ==1){*/?>
        <li><a href="#attribs-page" data-toggle="tab"><?php /*echo _JSHOP_ATTRIBUTES;*/?></a></li>
    --><?php /*}*/?>

<!--    --><?php //if ($this->product->parent_id==0){
//       $dispatcher->trigger('onDisplayProductEditTabsEndTab', array(&$row, &$lists, &$tax_value));
//    }?>
</ul>
<div id="editdata-document" class="tab-content">
   <?php
   if ($this->product->parent_id==0){
       include(dirname(__FILE__)."/description.php");
       include(dirname(__FILE__)."/info.php");
   }
   if ($this->product->parent_id==0){
	   $pane = null;
	   $dispatcher->trigger('onDisplayProductEditTabs', array(&$pane, &$row, &$lists, &$tax_value, &$currency));
   }
   if ($jshopConfig->admin_show_attributes && $this->product->parent_id==0/* && $category_id ==1*/){
    include(dirname(__FILE__)."/attribute.php");
   }
   if ($jshopConfig->admin_show_freeattributes && $this->product->parent_id==0) {
    include(dirname(__FILE__)."/freeattribute.php");
   }
   include(dirname(__FILE__)."/images.php");
   if ($jshopConfig->admin_show_product_video && $this->product->parent_id==0) {
    include(dirname(__FILE__)."/videos.php");
   }
   if ($jshopConfig->admin_show_product_related && $this->product->parent_id==0) {
    include(dirname(__FILE__)."/related.php");
   }
   if ($jshopConfig->admin_show_product_files) {
    include(dirname(__FILE__)."/files.php");
   }
   if ($jshopConfig->admin_show_product_extra_field && $this->product->parent_id==0){
    include(dirname(__FILE__)."/extrafields.php");
   }
   if ($this->product->parent_id==0){
	   $pane = null;
       $dispatcher->trigger('onDisplayProductEditTabsEnd', array(&$pane, &$row, &$lists, &$tax_value, &$currency));
   }
   ?>
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="current_cat" value="<?php echo JRequest::getVar('current_cat', 0)?>" />
<input type="hidden" name="product_id" value="<?php echo $row->product_id?>" />
<input type="hidden" name="parent_id" value="<?php echo $row->parent_id?>" />
</form>
</div>

<script type="text/javascript">
var product_price_precision=<?php print intval($jshopConfig->product_price_precision)?>;
Joomla.submitbutton=function(task){
    if (task=='save' || task=='apply'){
        if (isEmpty($F_('product_width_image')) && isEmpty($F_('product_height_image'))){
           alert('<?php echo _JSHOP_WRITE_SIZE_BAD?>');
           return false;
        }
        <?php if ($this->product->parent_id==0){?>
        if ($_('category_id').selectedIndex == -1){
           alert('<?php echo _JSHOP_WRITE_SELECT_CATEGORY?>');
           return false;
        }
        <?php }?>
    }
    Joomla.submitform(task, document.getElementById('adminForm'));
}

function _showHideAddPrice(){
    $_('tr_add_price').style.display=($_('product_is_add_price').checked)  ? ('') : ('none');
}
<?php if ($this->product->parent_id==0){?>
    //showHideAddPrice();
<?php }?>
</script>