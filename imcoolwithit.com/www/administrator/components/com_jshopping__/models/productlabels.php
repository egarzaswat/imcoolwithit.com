<?php
/**
* @version      4.3.0 24.07.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelProductLabels extends JModelLegacy{    

    function getList($order = null, $orderDir = null){
        $db = JFactory::getDBO();
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
		$lang = JSFactory::getLang();
        $query = "SELECT id, image, `".$lang->get("name")."` as name FROM `#__jshopping_product_labels` ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>