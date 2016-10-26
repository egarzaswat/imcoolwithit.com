<?php
/**
* @version      4.10.0 31.05.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelUsers extends JModelLegacy{    

    function getAllUsers($limitstart, $limit, $text_search="", $order = null, $orderDir = null) {
        $db = JFactory::getDBO();
        $where = "";
        $queryorder = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."') ";
        }
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT U.number, U.u_name, U.f_name, U.l_name, U.email, U.user_id, UM.block, UG.usergroup_name FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id
                 left join #__jshopping_usergroups as UG on UG.usergroup_id=U.usergroup_id
                 where 1 ".$where." ".$queryorder;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getCountAllUsers($text_search="") {
        $db = JFactory::getDBO(); 
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."') ";
        }
        $query = "SELECT COUNT(U.user_id) FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id where 1 ".$where;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getUsers(){
        $db = JFactory::getDBO();
        $query = "SELECT U.`user_id`, IF (concat(U.`f_name`,U.`l_name`)='', U.firma_name, concat(U.`f_name`,' ',U.`l_name`)) as `name`
                  FROM `#__jshopping_users` as U INNER JOIN `#__users` AS UM ON U.user_id=UM.id
                  ORDER BY name";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>