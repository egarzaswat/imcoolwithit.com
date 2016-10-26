<?php
/**
* @version      4.2.1 08.04.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class jshopProductField extends JTableAvto{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_extra_fields', 'id', $_db );
    }
    
    /**
    * set categorys
    * 
    * @param array $cats
    */
    function setCategorys($cats){
        $this->cats = serialize($cats);
    }
    
    /**
    * get gategoryd
    * 
    * @return array
    */    
    function getCategorys(){
        if ($this->cats!=""){
            return unserialize($this->cats);
        }else{
            return array();
        }
    }
    
    function getList($groupordering = 1){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $ordering = "F.ordering";
        if ($groupordering){
            $ordering = "G.ordering, F.ordering";
        } 
        $query = "SELECT F.id, F.`".$lang->get("name")."` as name, F.`".$lang->get("description")."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`".$lang->get("name")."` as groupname, multilist FROM `#__jshopping_products_extra_fields` as F left join `#__jshopping_products_extra_field_groups` as G on G.id=F.group order by ".$ordering;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $list = array();        
        foreach($rows as $k=>$v){
            $list[$v->id] = $v;
            if ($v->allcats){
                $list[$v->id]->cats = array();
            }else{
                $list[$v->id]->cats = unserialize($v->cats);
            }            
        }
        unset($rows);
        return $list;
    }
    
}

?>