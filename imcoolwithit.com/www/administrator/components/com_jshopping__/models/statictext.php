<?php
/**
* @version      4.6.0 26.06.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelStaticText extends JModelLegacy{ 
    
    function getList($use_for_return_policy = 0){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $where = $use_for_return_policy?' WHERE use_for_return_policy=1 ':'';
        $query = "SELECT id, alias, use_for_return_policy FROM `#__jshopping_config_statictext` ".$where." ORDER BY id";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>