<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
include_once(JPATH_COMPONENT_SITE."/shippings/shippingext.php");

class jshopShippingExt extends JTableAvto{

    function __construct(&$_db){
        parent::__construct('#__jshopping_shipping_ext_calc', 'id', $_db);
    }
    
    function loadFromAlias($alias){
        $db = JFactory::getDBO();
        $query = "SELECT id FROM `#__jshopping_shipping_ext_calc` WHERE `alias`='".$db->escape($alias)."'";
        extract(js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = $db->loadResult();
        return $this->load($id);
    }
    
    function load($id = null, $reset = true){
        $return = parent::load($id, $reset);
        $jshopConfig = JSFactory::getConfig();
        $path = $jshopConfig->path."shippings";
        $extname = $this->alias;
        $filepatch = $path."/".$extname."/".$extname.".php";
        if (file_exists($filepatch)){
            include_once($filepatch);
            $this->exec = new $extname();
        }else{
            JError::raiseWarning("","Load ShippingExt ".$extname." error.");
        }
        
        return $return;
    }
    
    function getList($active = 0){
        $db = JFactory::getDBO();
        $adv_query = "";
        if ($active==1){
            $adv_query = "where `published`='1'";
        }
        $query = "select * from `#__jshopping_shipping_ext_calc` ".$adv_query." order by `ordering`";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function setShippingMethod($data){
        $this->shipping_method = serialize($data);
    }
    
    function getShippingMethod(){
        if ($this->shipping_method=="") return array();
        return unserialize($this->shipping_method);
    }
    
    function setParams($data){
        $this->params = serialize($data);
    }
    
    function getParams(){        
        if ($this->params=="") return array();
        return unserialize($this->params);
    }
}
?>