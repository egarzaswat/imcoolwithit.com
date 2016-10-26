<?php
/**
* @version      3.3.0 20.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelShippingExtPrice extends JModelLegacy{
    function getList($active = 0){
        $db = JFactory::getDBO();
        $adv_query = "";
        if ($active==1){
            $adv_query = "where `published`='1'";
        }
        $query = "select * from `#__jshopping_shipping_ext_calc` ".$adv_query." order by `ordering`";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
    return $db->loadObjectList();
    }
}
?>