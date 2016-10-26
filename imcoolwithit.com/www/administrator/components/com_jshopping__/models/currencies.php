<?php
/**
* @version      4.10.2 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelCurrencies extends JModelLegacy{ 

    function getAllCurrencies($publish = 1, $order = null, $orderDir = null) {
        $db = JFactory::getDBO();
        $query_where = ($publish)?("WHERE currency_publish = '1'"):("");
        $ordering = 'currency_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `#__jshopping_currencies` $query_where ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function deleteList($list){
        $res = 1;
        foreach($list as $k=>$id){
            if (!$this->delete($id)){
                $res = 0;
            }
        }
        return $res;
    }
    
    function getCountProduct($id){
        $db = JFactory::getDBO();
        $query = "select count(*) from #__jshopping_products where currency_id=".intval($id);
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function delete($id, $check=1){
        if ($check){
            if ($this->getCountProduct($id)){
                return 0;
            }
        }
        $row = JSFactory::getTable('currency', 'jshop');
        return $row->delete($id);
    }
}
?>