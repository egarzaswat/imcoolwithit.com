<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class jshopAttributesGroup extends JTableAvto{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_attr_groups', 'id', $_db );
    }
    
    function getList(){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang(); 
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering FROM `#__jshopping_attr_groups` order by ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}