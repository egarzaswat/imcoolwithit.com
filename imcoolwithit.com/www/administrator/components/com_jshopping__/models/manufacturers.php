<?php
/**
* @version      3.12.0 10.11.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelManufacturers extends JModelLegacy{

    function getAllManufacturers($publish=0, $order=null, $orderDir=null){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang(); 
        $query_where = ($publish)?(" WHERE manufacturer_publish = '1'"):("");  
        $queryorder = '';        
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, ordering, `".$lang->get('name')."` as name FROM `#__jshopping_manufacturers` $query_where ".$queryorder;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getList(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->getAllManufacturers(0, $morder, 'asc');
    } 
      
}
?>