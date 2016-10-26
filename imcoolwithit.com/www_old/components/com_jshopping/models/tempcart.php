<?php
/**
* @version      4.7.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopTempCart {
    
    var $savedays = 30;
    
    function __construct(){
        JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshopTempCart', array(&$this));
    }
    
    function insertTempCart($cart) {
        
        if ($cart->type_cart!='wishlist') return 0; //not save if type == cart
        
        $patch = "/";
        if (JURI::base(true) != "") $patch = JURI::base(true);
        
        $db = JFactory::getDBO();
        setcookie('jshopping_temp_cart', session_id() ,time() + 3600*24*$this->savedays, $patch);        
        $query = "DELETE FROM `#__jshopping_cart_temp` WHERE `id_cookie` = '".session_id()."' AND `type_cart`='".$cart->type_cart."'";
        $db->setQuery($query);
        $db->query();
        
		if (!count($cart->products)) return 0;
		
        $query = "INSERT INTO `#__jshopping_cart_temp` SET 
                    `id_cookie` = '" . session_id() . "', 
                    `cart` = '".$db->escape(serialize($cart->products))."',
                    `type_cart` = '".$cart->type_cart."' ";
        $db->setQuery($query);
        $db->query();
        return 1;
    }
        
    function getTempCart($id_cookie, $type_cart="cart") {
        
        $db = JFactory::getDBO();
        $query = "SELECT `cart` FROM `#__jshopping_cart_temp`
                  WHERE `id_cookie` = '" . $db->escape($id_cookie) . "' AND `type_cart`='".$type_cart."' LIMIT 0,1";
        $db->setQuery($query);
        $cart = $db->loadResult();
        if ($cart!="")        
            return (unserialize($cart));
        else
            return array();    
    }
}

?>