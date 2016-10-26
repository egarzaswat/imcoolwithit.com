<?php 
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="jshop" id="comjshop">
    <?php if ($this->params->get('show_page_title') && $this->params->get('page_title')) : ?>    
        <div class="componentheading<?php print $this->params->get('pageclass_sfx');?>">
            <?php print $this->params->get('page_title')?>
        </div>
    <?php endif; ?>
    
    <?php if (count($this->rows)) : ?>
    <div class="jshop_list_manufacturer">
        <div class = "jshop">
            <?php foreach($this->rows as $k=>$row) : ?>
                <?php if ($k % $this->count_to_row == 0) : ?>
                    <div class = "row-fluid">
                <?php endif; ?>
                <div class = "span<?php echo (12 / $this->count_to_row); ?> jshop_categ vendor">
                    <div class = "span7 image">
                        <a class = "product_link" href = "<?php print $row->link?>">
                            <img class = "jshop_img" src = "<?php print $row->logo;?>" alt="<?php print htmlspecialchars($row->shop_name);?>" />
                        </a>                    
                    </div>
                    <div class = "span5">
                        <div class="vendor_name">
                            <a class = "product_link" href = "<?php print $row->link?>">
                                <?php print $row->shop_name?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php if ($k % $this->count_to_row == $this->count_to_row - 1) : ?>
                    <div class = "clearfix"></div>
                    </div>
                <?php endif; ?>
             <?php endforeach; ?>
             <?php if ($k % $this->count_to_row != $this->count_to_row - 1) : ?>
                <div class = "clearfix"></div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($this->display_pagination) : ?>
            <div class="jshop_pagination">
                <div class="pagination"><?php print $this->pagination?></div>
            </div>
        <?php endif;  ?>
    </div>
    <?php endif; ?>
</div>