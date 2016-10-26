<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class jshopAttribut extends JTableAvto{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_attr', 'attr_id', $_db );
    }

    function getName($attr_id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr` WHERE attr_id = '".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllAttributes($groupordering = 1){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        $ordering = "A.attr_ordering";
        if ($groupordering){
            $ordering = "G.ordering, A.attr_ordering";
        }        
        $query = "SELECT A.attr_id, A.`".$lang->get("name")."` as name, A.`".$lang->get("description")."` as description, A.attr_type, A.independent, A.allcats, A.cats, A.attr_ordering, G.`".$lang->get("name")."` as groupname
                  FROM `#__jshopping_attr` as A left join `#__jshopping_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->cats = array();
            }else{
                $rows[$k]->cats = unserialize($v->cats);
            }
        }
    return $rows;
    }
    
    function getTypeAttribut($attr_id){
        $db = JFactory::getDBO();
        $query = "select attr_type from #__jshopping_attr where `attr_id`='".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCategorys($cats){
        $this->cats = serialize($cats);
    }
      
    function getCategorys(){
        if ($this->cats!=""){
            return unserialize($this->cats);
        }else{
            return array();
        }
    }

}
?>