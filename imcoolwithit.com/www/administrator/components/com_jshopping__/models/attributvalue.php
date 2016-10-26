<?php
/**
* @version      4.11.0 31.07.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class JshoppingModelAttributValue extends JModelLegacy {
    
    function getNameValue($value_id) {
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr_values` WHERE value_id = '".$db->escape($value_id)."'";
        $db->setQuery($query);        
        return $db->loadResult();
    }

    function getAllValues($attr_id, $order = null, $orderDir = null) {
        $db = JFactory::getDBO(); 
        $lang = JSFactory::getLang();
        $ordering = 'value_ordering, value_id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering FROM `#__jshopping_attr_values` where attr_id='".$attr_id."' ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    /**
    * get All Atribute value
    * @param $resulttype (0 - ObjectList, 1 - array {id->name}, 2 - array(id->object) )
    * 
    * @param mixed $resulttype
    */
    function getAllAttributeValues($resulttype=0){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT value_id, image, `".$lang->get("name")."` as name, attr_id, value_ordering FROM `#__jshopping_attr_values` ORDER BY value_ordering, value_id";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $attribs = $db->loadObjectList();

        if ($resulttype==2){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v;    
            }
            return $rows;
        }elseif ($resulttype==1){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v->name;    
            }
            return $rows;
        }else{
            return $attribs;
        }        
    }
	
	public function delete($id){
		$this->deleteImage($id);
		$this->deleteProductAttributeValue($id);
		$this->deleteAttributeValue($id);		
	}
	
	public function deleteAttributeValue($id){
		$db = JFactory::getDBO();
		$query = "DELETE FROM `#__jshopping_attr_values` WHERE `value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->query();
	}
	
	public function deleteImage($id){
		$image = $this->getImage($id);
		if ($image){
			@unlink(JSFactory::getConfig()->image_attributes_path."/".$image);
		}
	}
	
	public function getImage($id){
		$db = JFactory::getDBO();
		$query = "SELECT image FROM `#__jshopping_attr_values` WHERE value_id ='".$db->escape($id)."'";
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function deleteProductAttributeValue($id){
		$this->deleteProductAttributeValueDependent($id);
		$this->deleteProductAttributeValueNotDependent($id);
	}
	
	public function deleteProductAttributeValueDependent($id){
		$db = JFactory::getDBO();
		$attributValue = JSFactory::getTable('attributValue');
		$attributValue->load($id);
		$attr_id = $attributValue->attr_id;
		if ($attr_id){
			$field = 'attr_'.(int)$attr_id;
			$query = "update `#__jshopping_products_attr` set `".$field."`='' where `".$field."`='".$db->escape($id)."'";
			$db->setQuery($query);
			$db->query();
		}
	}
	
	public function deleteProductAttributeValueNotDependent($id){
		$db = JFactory::getDBO();
		$query = "delete from `#__jshopping_products_attr2` where `attr_value_id` = '".$db->escape($id)."'";
		$db->setQuery($query);
		$db->query();
	}
    
}