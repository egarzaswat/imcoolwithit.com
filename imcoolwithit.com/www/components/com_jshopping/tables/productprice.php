<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProductPrice extends JTable{
    
	var $price_id = null;
	var $product_id = null;
	var $discount = null;
	var $product_quantity_start = null;
	var $product_quantity_finish = null;
	
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_products_prices', 'price_id', $_db );
    }
    
    function getAddPrices($product_id){        
        $db = JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_products_prices` WHERE product_id = '".$db->escape($product_id)."' ORDER BY product_quantity_start DESC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
}
?>