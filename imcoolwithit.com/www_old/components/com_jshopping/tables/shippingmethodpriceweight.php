<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopShippingMethodPriceWeight extends JTable {
    
	var $sh_pr_weight_id = null;
	var $sh_pr_method_id = null;
	var $shipping_price = null;
	var $shipping_package_price = null;
	var $shipping_weight_to = null;
	var $shipping_weight_fron = null;
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_shipping_method_price_weight', 'sh_pr_weight_id', $_db );
    }
}
?>