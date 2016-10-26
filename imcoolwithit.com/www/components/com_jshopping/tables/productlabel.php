<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProductLabel extends JTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_product_labels', 'id', $_db);
    }
    
	function getListLabels(){
		$lang = JSFactory::getLang();
		$db = JFactory::getDBO();
		$query = "SELECT id, image, `".$lang->get("name")."` as name FROM `#__jshopping_product_labels` ORDER BY name";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$rows = array();
		foreach($list as $row){
			$rows[$row->id] = $row;
		}
	return $rows;
    }
}
?>