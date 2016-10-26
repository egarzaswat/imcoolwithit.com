<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProductAttribut extends JTableAvto {
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_attr', 'product_attr_id', $_db);
    }
    
    function check(){
        return 1;
    }
    
    function deleteAttributeForProduct(){
        $db = JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_attr` WHERE `product_id` = '".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $db->query();    
    }
    
    function deleteAttribute($id){
        $db = JFactory::getDBO();
        
        $this->load($id);
        if ($this->ext_attribute_product_id){
            $query = "DELETE FROM `#__jshopping_products` WHERE `product_id` = '".$db->escape($this->ext_attribute_product_id)."'";
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "DELETE FROM `#__jshopping_products_attr` WHERE `product_attr_id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        $db->query();
    }
}
?>