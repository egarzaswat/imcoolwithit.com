<?php
/**
* @version      2.8.0 10.01.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelVendors extends JModelLegacy{
	
    function getNamesVendors() {
        $db = JFactory::getDBO(); 
        $query = "SELECT id, f_name, l_name FROM `#__jshopping_vendors` ORDER BY f_name, l_name DESC";
        $db->setQuery($query);
        return $db->loadObjectList();
    }    	

    function getAllVendors($limitstart, $limit, $text_search="") {
        $db = JFactory::getDBO();
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);            
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') ";            
        } 
        $query = "SELECT * FROM `#__jshopping_vendors` where 1 ".$where." ORDER BY id DESC";
        $db->setQuery($query, $limitstart, $limit);        
        return $db->loadObjectList();
    }

    function getCountAllVendors($text_search = "") {
        $db = JFactory::getDBO(); 
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);            
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') ";
        }
        $query = "SELECT COUNT(id) FROM `#__jshopping_vendors` where 1 ".$where." ORDER BY id DESC";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getAllVendorsNames($main_id_null = 0){
        $db = JFactory::getDBO();
        $query = "SELECT id, concat(f_name, ' ', l_name) as name, `main` FROM `#__jshopping_vendors` ORDER BY name";
        $db->setQuery($query);        
        $rows = $db->loadObjectList();
        if ($main_id_null){
            foreach($rows as $k=>$v){
                if ($v->main) $rows[$k]->id = 0;
            }
        }
        return $rows;
    }
    
    function getIdVendorForUserId($id){
        $db = JFactory::getDBO();
        $query = "SELECT id FROM `#__jshopping_vendors` where user_id='".$db->escape($id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
      
}
?>