<?php
/**
* @version      4.6.0 21.05.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class jshopStaticText extends JTableAvto{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_config_statictext', 'id', $_db );
    }
    
    function loadData($alias){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();         
        $query = "SELECT id, alias, `".$lang->get('text')."` as text FROM `#__jshopping_config_statictext` where alias='".$db->escape($alias)."'";
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    function loadDataByIds($list){
        if (!count($list)){
            return array();
        }
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();  
        $ids = implode(',', $list);
        $query = "SELECT id, alias, `".$lang->get('text')."` as text FROM `#__jshopping_config_statictext` where id IN (".$db->escape($ids).")";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getReturnPolicyForProducts($products){
        $productOption = JSFactory::getTable('productOption', 'jshop');
        $listrp = $productOption->getProductOptionList($products, 'return_policy');
        $listrp = array_unique($listrp);
        $tmp = $this->loadData('return_policy');
        $defidrp = intval($tmp->id);
        foreach($listrp as $k=>$v){
            if (!$v) $listrp[$k] = $defidrp;
        }
        return $this->loadDataByIds($listrp);
    }
    
}
?>