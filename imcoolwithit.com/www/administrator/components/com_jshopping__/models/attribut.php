<?php
/**
* @version      4.11.0 04.06.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class JshoppingModelAttribut extends JModelLegacy {
    
    public function getNameAttribut($attr_id) {
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr` WHERE attr_id = '".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    public function getAllAttributes($result = 0, $categorys = null, $order = null, $orderDir = null){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        $ordering = "A.attr_ordering asc";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT A.attr_id, A.`".$lang->get("name")."` as name, A.attr_type, A.attr_ordering, A.independent, A.allcats, A.cats, G.`".$lang->get("name")."` as groupname
                  FROM `#__jshopping_attr` as A left join `#__jshopping_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
                
        if (is_array($categorys) && count($categorys)){
            foreach($list as $k=>$v){
                if (!$v->allcats){
                    if ($v->cats!=""){
                        $cats = unserialize($v->cats);
                    }else{
                        $cats = array();
                    }
                    $enable = 0;
                    foreach($categorys as $cid){
                        if (in_array($cid, $cats)) $enable = 1;
                    }
                    if (!$enable){
                        unset($list[$k]);
                    }
                }
            } 
        }
        
        if ($result==0){
            return $list;
        }
        if ($result==1){
            $attributes_format1 = array();
            foreach($list as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($result==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($list as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }
	
	public function delete($id){
		$this->deleteAttribute($id);
		$this->deleteAttributeValues($id);
		$this->deleteProductAttribute($id);
	}
	
	public function deleteAttribute($id){
		$db = JFactory::getDBO();		
		$query = "DELETE FROM `#__jshopping_attr` WHERE `attr_id`='".$db->escape($id)."'";
		$db->setQuery($query);
		$db->query();
	}
	
	public function deleteAttributeValues($id){
		$db = JFactory::getDBO();
		
		$attr_values = $this->getListAttributeValues($id);
		foreach($attr_values as $attr_val){
			if ($attr_val->image){
				@unlink(JSFactory::getConfig()->image_attributes_path."/".$attr_val->image);
			}
		}
		
		$query = "delete from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		$db->query();
	}
	
	public function getListAttributeValues($id){
		$db = JFactory::getDBO();
		$query = "select * from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function deleteProductAttribute($id){
		$this->deleteProductAttributeDependent($id);
		$this->deleteProductAttributeNotDependent($id);
	}
	
	public function deleteProductAttributeDependent($id){
		$db = JFactory::getDBO();
		$query="ALTER TABLE `#__jshopping_products_attr` DROP `attr_".(int)$id."`";
		$db->setQuery($query);
		$db->query();
	}
	
	public function deleteProductAttributeNotDependent($id){
		$db = JFactory::getDBO();
		$query = "delete from `#__jshopping_products_attr2` where `attr_id` = '".$db->escape($id)."' ";
		$db->setQuery($query);
		$db->query();
	}
    
}