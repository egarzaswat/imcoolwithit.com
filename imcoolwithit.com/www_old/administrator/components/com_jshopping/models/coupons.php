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

class JshoppingModelCoupons extends JModelLegacy{    

    function getAllCoupons($limitstart, $limit, $order = null, $orderDir = null) {
        $db = JFactory::getDBO(); 
        $queryorder = 'ORDER BY C.used, C.coupon_id desc';
        if ($order && $orderDir){
            $queryorder = "ORDER BY ".$order." ".$orderDir;
        }
        $query = "SELECT C.*, U.f_name, U.l_name  FROM `#__jshopping_coupons` as C left join #__jshopping_users as U on C.for_user_id=U.user_id ".$queryorder;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }
    
    function getCountCoupons(){
        $db = JFactory::getDBO(); 
        $query = "SELECT count(*) FROM `#__jshopping_coupons`";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();   
    }    
}
?>