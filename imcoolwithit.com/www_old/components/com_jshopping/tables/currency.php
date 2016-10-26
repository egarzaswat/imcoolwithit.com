<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopCurrency extends JTable {
    
	var $currency_id = null;
	var $currency_name = null;
    var $currency_code = null;
	var $currency_code_iso = null;
	var $currency_ordering = null;
	var $currency_value = null;
	var $currency_publish = null;
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_currencies', 'currency_id', $_db );
    }

	function getAllCurrencies($publish = 1) {
		$db = JFactory::getDBO(); 
		$query_where = ($publish)?("WHERE currency_publish = '1'"):("");
		$query = "SELECT currency_id, currency_name, currency_code, currency_code_iso, currency_value FROM `#__jshopping_currencies` $query_where ORDER BY currency_ordering";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
?>