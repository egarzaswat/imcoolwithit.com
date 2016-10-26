<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopTax extends JTable {    
    var $tax_id = null;
    var $tax_name = null;
    var $tax_value = null;
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_taxes', 'tax_id', $_db );
    }
    
    function getAllTaxes(){
        $db = JFactory::getDBO();                
        $query = "SELECT tax_id, tax_name, tax_value FROM `#__jshopping_taxes`";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getExtTaxes($tax_id = 0){
        $db = JFactory::getDBO();
        $where = "";
        if ($tax_id) $where = " where tax_id='".$tax_id."'";
        $query = "SELECT * FROM `#__jshopping_taxes_ext` ".$where;
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach($list as $k=>$v){
            $list[$k]->countries = unserialize($v->zones);
        }
        return $list;
    }

}
?>