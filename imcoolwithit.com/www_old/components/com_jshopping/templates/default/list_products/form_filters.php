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
<form action="<?php print $this->action;?>" method="post" name="sort_count" id="sort_count" class="form-horizontal">
<div class="form_sort_count">
<?php if ($this->config->show_sort_product || $this->config->show_count_select_products) : ?>
<div class="block_sorting_count_to_page">
    <?php if ($this->config->show_sort_product) : ?>
        <div class="control-group box_products_sorting">
            <div class="control-label">
                <?php print _JSHOP_ORDER_BY.": "; ?>
            </div>
            <div class="controls">
                <?php echo $this->sorting?>
                <span class="icon-arrow">
                    <img src="<?php print $this->path_image_sorting_dir?>" alt="orderby" onclick="submitListProductFilterSortDirection()" />
                </span>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->config->show_count_select_products) : ?>
        <div class="control-group box_products_count_to_page">
            <div class="control-label">
                <?php print _JSHOP_DISPLAY_NUMBER.": "; ?>
            </div>
            <div class="controls">
                <?php echo $this->product_count?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($this->config->show_product_list_filters && $this->filter_show) : ?>

    <?php if ($this->config->show_sort_product || $this->config->show_count_select_products) : ?>
        <div class="margin_filter"></div>
    <?php endif; ?>
    
    <div class="jshop filters">
        <div class="box_cat_man">
            <?php if ($this->filter_show_category) : ?>
                <div class = "control-group box_category">
                    <div class = "control-label">
                        <?php print _JSHOP_CATEGORY.": "; ?>
                    </div>
                    <div class = "controls"><?php echo $this->categorys_sel?></div>
                </div>
            <?php endif; ?>
            <?php if ($this->filter_show_manufacturer) : ?>
                <div class="control-group box_manufacrurer">
                    <div class="control-label">
                        <?php print _JSHOP_MANUFACTURER.": "; ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->manufacuturers_sel; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php print $this->_tmp_ext_filter_box;?>
        </div>
        
        <?php if (getDisplayPriceShop()) : ?>
            <div class="filter_price">
                <label><?php print _JSHOP_PRICE?>: </label>
                <div class="control-group box_price_from">
                    <div class="control-label"><?php print _JSHOP_FROM?></div>
                    <div class="controls">
                        <span class="input-append">
                            <input type="text" class="input" name="fprice_from" id="price_from" size="7" value="<?php if ($this->filters['price_from']>0) print $this->filters['price_from']?>" />
                            <a class="btn"><?php print $this->config->currency_code?></a>
                        </span>
                    </div>

                </div>
                <div class="control-group box_price_to">
                    <div class="control-label"><?php print _JSHOP_TO?></div>
                    <div class="controls">
                        <span class="input-append">
                            <input type="text" class="input" name="fprice_to"  id="price_to" size="7" value="<?php if ($this->filters['price_to']>0) print $this->filters['price_to']?>" />
                            <a class="btn"><?php print $this->config->currency_code?></a>
                        </span>
                    </div>
                </div>
                <?php print $this->_tmp_ext_filter;?>
                <div class="control-group box_button">
                    <div class="controls">
                    <input type="button" class="btn button" value="<?php print _JSHOP_GO; ?>" onclick="submitListProductFilters();" />
                    <span class="clear_filter"><a href="#" onclick="clearProductListFilter();return false;"><?php print _JSHOP_CLEAR_FILTERS?></a></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<input type="hidden" name="orderby" id="orderby" value="<?php print $this->orderby?>" />
<input type="hidden" name="limitstart" value="0" />
</form>