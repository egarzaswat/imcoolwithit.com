<?php
/**
* @version      4.1.0 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelUsergroups extends JModelLegacy{ 

    function getAllUsergroups($order = null, $orderDir = null){
        $ordering = "usergroup_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        
        $db = JFactory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_usergroups` ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function resetDefaultUsergroup(){
        $db = JFactory::getDBO(); 
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        $db->setQuery($query);
        $usergroup_default = $db->loadResult();
        $query = "UPDATE `#__jshopping_usergroups` SET `usergroup_is_default` = '0'";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->query();
    }

    function setDefaultUsergroup($usergroup_id){
        $db = JFactory::getDBO(); 
        $query = "UPDATE `#__jshopping_usergroups` SET `usergroup_is_default` = '1' WHERE `usergroup_id`= '".$db->escape($usergroup_id)."'";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->query();
    }

    function getDefaultUsergroup(){
        $db = JFactory::getDBO(); 
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }
}
?>