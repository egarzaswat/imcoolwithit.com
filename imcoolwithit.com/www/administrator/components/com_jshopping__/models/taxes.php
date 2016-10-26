<?php
/**
* @version      4.1.0 25.11.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelTaxes extends JModelLegacy{ 

    function getAllTaxes($order = null, $orderDir = null) {
        $db = JFactory::getDBO(); 
        $ordering = 'tax_name';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `#__jshopping_taxes` ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getExtTaxes($tax_id = 0, $order = null, $orderDir = null) {
        $db = JFactory::getDBO();
        $where = "";
        if ($tax_id) $where = " where ET.tax_id='".$tax_id."'";
        $ordering = 'ET.id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT ET.*, T.tax_name FROM `#__jshopping_taxes_ext` as ET left join #__jshopping_taxes as T on T.tax_id=ET.tax_id ".$where." ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
      
}

?>