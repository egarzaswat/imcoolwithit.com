<?php
/**
* @version      2.9.0 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelSeo extends JModelLegacy{ 

    function getList(){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();         
        $query = "SELECT id, alias, `".$lang->get('title')."` as title, `".$lang->get('keyword')."` as keyword, `".$lang->get('description')."` as description FROM `#__jshopping_config_seo` ORDER BY ordering";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }      
}
?>