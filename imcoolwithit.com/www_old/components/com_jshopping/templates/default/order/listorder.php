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
<div class="jshop myorders_list" id="comjshop">

    <h1><?php print _JSHOP_MY_ORDERS ?></h1>
    
    <?php print $this->_tmp_html_before_user_order_list;?>
    
    <?php if (count($this->orders)) {?>
        <?php foreach ($this->orders as $order){?>
            <div class="myorders_block_info">
            
                <div class="order_number">
                    <b><?php print _JSHOP_ORDER_NUMBER ?>:</b>
                    <span><?php print $order->order_number?></span>
                </div>
                <div class="order_status">
                    <b><?php print _JSHOP_ORDER_STATUS ?>:</b>
                    <span><?php print $order->status_name?></span>
                </div>
                
                <div class="table_order_list">
                    <div class="row-fluid">
                        <div class="span6 users">
                            <div>
                                <b><?php print _JSHOP_ORDER_DATE ?>:</b>
                                <span><?php print formatdate($order->order_date, 0) ?></span>
                            </div>
                            <div>
                                <b><?php print _JSHOP_EMAIL_BILL_TO ?>:</b>
                                <span><?php print $order->f_name ?> <?php print $order->l_name ?></span>
                            </div>
                            <div>
                                <b><?php print _JSHOP_EMAIL_SHIP_TO ?>:</b>
                                <span><?php print $order->d_f_name ?> <?php print $order->d_l_name ?></span>
                            </div>
                            <?php print $this->_tmp_ext_user_info;?>
                        </div>
                        <div class="span3 products">
                            <div>
                                <b><?php print _JSHOP_PRODUCTS ?>:</b> 
                                <span><?php print $order->count_products ?></span>
                            </div>
                            <div>
                                <b></b> 
                                <span><?php print formatprice($order->order_total, $order->currency_code)?></span>
                                <?php print $order->_ext_price_html?>
                            </div>
                            <?php print $this->_tmp_ext_prod_info;?>
                        </div>
                        <div class="span3 buttons">
                            <a class="btn" href = "<?php print $order->order_href ?>"><?php print _JSHOP_DETAILS?></a> 
                            <?php print $this->_tmp_ext_but_info;?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <div class="myorders_total">
            <span class="name"><?php print _JSHOP_TOTAL?>:</span>
            <span class="price"><?php print formatprice($this->total, getMainCurrencyCode())?></span>
        </div>
        
    <?php }else{ ?>
        <div class="myorders_no_orders">
            <?php print _JSHOP_NO_ORDERS ?>
        </div>
    <?php } ?>
    
    <?php print $this->_tmp_html_after_user_order_list;?>
</div>