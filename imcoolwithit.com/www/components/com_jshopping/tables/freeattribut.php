<?php
/**
* @version      2.8.0 12.03.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class jshopFreeAttribut extends JTableAvto{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_free_attr', 'id', $_db );
    }
    
    function getName($id) {
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_free_attr` WHERE `id` = '".$db->escape($id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getAll() {
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, required, ordering FROM `#__jshopping_free_attr` ORDER BY `ordering`";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getAllNames(){
        $rows = array();
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $query = "SELECT id, `".$lang->get("name")."` as name FROM `#__jshopping_free_attr` ORDER BY `ordering`";
        $db->setQuery($query);
        $list = $db->loadObjectList();        
        foreach($list as $v){
            $rows[$v->id] = $v->name;
        }
        return $rows;
    }
}
?>