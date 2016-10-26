<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class jshopAddon extends JTable{
    
    var $id = null;
    var $alias = null;
    var $key = null;
    var $version = null;
    var $params = null;
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_addons', 'id', $_db);
    }
    
    function setParams($params){
        $this->params = serialize($params);
    }
        
    function getParams(){
        if ($this->params!=""){
            return unserialize($this->params);
        }else{
            return array();
        }
    }
    
    function loadAlias($alias){
        $query = "select `id` from #__jshopping_addons where `alias`='".$this->_db->escape($alias)."'";
        $this->_db->setQuery($query);
        $id = $this->_db->loadResult();
        $this->load($id);
        $this->alias = $alias;
    }
    
    function getKeyForAlias($alias){
        $query = "select `key` from #__jshopping_addons where `alias`='".$this->_db->escape($alias)."'";
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }
	
	function installJoomlaExtension($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT extension_id FROM `#__extensions` WHERE element='".$db->escape($data['element'])."' AND folder='".$db->escape($data['folder'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('extension', 'JTable');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
	
	function installJoomlaModule($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__modules` WHERE module='".$db->escape($data['module'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('module', 'JTable');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            if (!$exid){
                $db->setQuery('INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ('.$extension->id.', 0)');
                $db->query();
            }
            return 1;
        }else{
            return 0;
        }
    }
    
    function installShipping($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('shippingExt', 'jshop');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(ordering) FROM `#__jshopping_shipping_ext_calc`";
            $db->setQuery($query);
            $extension->ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
	
	function installShippingMethod($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_shipping_method` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('shippingMethod', 'jshop');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(ordering) FROM `#__jshopping_shipping_method`";
            $db->setQuery($query);
            $extension->ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    
    function installPayment($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT payment_id FROM `#__jshopping_payment_method` WHERE `payment_class`='".$db->escape($data['payment_class'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('paymentMethod', 'jshop');
        if ($exid){
            $extension->load($exid);
        }
        if (!$exid){
            $query = "SELECT MAX(payment_ordering) FROM `#__jshopping_payment_method`";
            $db->setQuery($query);
            $extension->payment_ordering = $db->loadResult() + 1;
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    function installImportExport($data, $installexist = 0){
        $db = JFactory::getDbo();
        $db->setQuery("SELECT id FROM `#__jshopping_import_export` WHERE `alias`='".$db->escape($data['alias'])."'");
        $exid = (int)$db->loadResult();
        if ($exid && !$installexist){
            return -1;
        }
        $extension = JSFactory::getTable('importExport', 'jshop');
        if ($exid){
            $extension->load($exid);
        }
        $extension->bind($data);
        if ($extension->check()){
            $extension->store();
            return 1;
        }else{
            return 0;
        }
    }
    
    function addFieldTable($table, $field, $type){
        $db = JFactory::getDBO();
        $listfields = $db->getTableColumns($table);
        if (!isset($listfields[$field])){
            $query = "ALTER TABLE ".$db->quoteName($table)." ADD ".$db->quoteName($field)." ".$type;
            $db->setQuery($query);
            $db->query();
        }
    }
}
?>