<?php
/**
* @version      4.1.0 23.09.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelDeliveryTimes extends JModelLegacy{    

    function getDeliveryTimes($order = null, $orderDir = null){
        $db = JFactory::getDBO();    
        $lang = JSFactory::getLang();    
        
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, `".$lang->get('name')."` as name FROM `#__jshopping_delivery_times` ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getCountDeliveryTimes() {
        $db = JFactory::getDBO();         
        $query = "SELECT count(id) FROM `#__jshopping_delivery_times`";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
}
?>