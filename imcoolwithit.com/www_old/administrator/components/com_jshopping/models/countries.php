<?php
/**
* @version      4.1.0 05.07.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelCountries extends JModelLegacy{
    
    /**
    * get list country
    * 
    * @param int $publish (0-all, 1-publish, 2-unpublish)
    * @param int $limitstart
    * @param int $limit
    * @param int $orderConfig use order config
    * @return array
    */
    function getAllCountries($publish = 1, $limitstart = null, $limit = null, $orderConfig = 1, $order = null, $orderDir = null){
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
                
        if ($publish == 0) {
            $where = " ";
        } else {
            if ($publish == 1) {
                $where = (" WHERE country_publish = '1' ");
            } else {
                if ($publish == 2) {
                    $where = (" WHERE country_publish = '0' ");
                }
            }
        }
        $ordering = "ordering";
        if ($orderConfig && $jshopConfig->sorting_country_in_alphabet) $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $lang = JSFactory::getLang();
        $query = "SELECT country_id, country_publish, ordering, country_code, country_code_2, `".$lang->get("name")."` as name FROM `#__jshopping_countries` ".$where." ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    /**
    * get count country
    * @return int
    */
    function getCountAllCountries() {
        $db = JFactory::getDBO(); 
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries`";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    /**
    * get count county
    * @param int $publish
    * @return int
    */
    function getCountPublishCountries($publish = 1) {
        $db = JFactory::getDBO(); 
        $query = "SELECT COUNT(country_id) FROM `#__jshopping_countries` WHERE country_publish = '".intval($publish)."'";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
      
}

?>