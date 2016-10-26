<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopManufacturer extends JTableAvto{

    function __construct(&$_db){
        parent::__construct('#__jshopping_manufacturers', 'manufacturer_id', $_db);
        JPluginHelper::importPlugin('jshoppingproducts');
    }

	function getAllManufacturers($publish = 0, $order = "ordering", $dir ="asc" ) {
		$lang = JSFactory::getLang();
		$db = JFactory::getDBO();
        if ($order=="id") $orderby = "manufacturer_id";
        if ($order=="name") $orderby = "name";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering"; 
		$query_where = ($publish)?("WHERE manufacturer_publish = '1'"):("");
		$query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, `".$lang->get('name')."` as name, `".$lang->get('description')."` as description,  `".$lang->get('short_description')."` as short_description
				  FROM `#__jshopping_manufacturers` $query_where ORDER BY ".$orderby." ".$dir;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		foreach($list as $key=>$value){
            $list[$key]->link = SEFLink('index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id='.$list[$key]->manufacturer_id);
        }		
		return $list;
	}
    
    function getList(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->getAllManufacturers(1, $morder, 'asc');
    }
	
	function getName() {
        $lang = JSFactory::getLang();
        $name = $lang->get('name');
        return $this->$name;
    }
    
    function getDescription(){
        
        if (!$this->manufacturer_id){            
            return 1; 
        }
        
        $lang = JSFactory::getLang();
        $name = $lang->get('name');        
        $description = $lang->get('description');
        $short_description = $lang->get('short_description');
        $meta_title = $lang->get('meta_title');
        $meta_keyword = $lang->get('meta_keyword');
        $meta_description = $lang->get('meta_description');
        
        $this->name = $this->$name;
        $this->description = $this->$description;
        $this->short_description = $this->$short_description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
    }
	
	function getProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0){
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("manufacturer", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("manufacturer", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id                  
                  $adv_from
                  WHERE prod.product_manufacturer_id = '".$this->manufacturer_id."' AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        if ($limit){
            $this->_db->setQuery($query, $limitstart, $limit);
        }else{
            $this->_db->setQuery($query);
        }
        $products = $this->_db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }    
	
	function getCountProducts($filters) {
		$jshopConfig = JSFactory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "";
        $this->getBuildQueryListProduct("manufacturer", "count", $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryCountProductList', array("manufacturer", &$adv_result, &$adv_from, &$adv_query, &$filters) );
        
		$db = JFactory::getDBO(); 
		$query = "SELECT COUNT(distinct prod.product_id) FROM `#__jshopping_products` as prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_manufacturer_id = '".$this->manufacturer_id."' AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
		$db->setQuery($query);
		return $db->loadResult();
	}
    
    /**
    * get List category
    */
    function getCategorys(){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        $adv_query = "";
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        if ($jshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        $query = "SELECT distinct cat.category_id as id, cat.`".$lang->get('name')."` as name FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_categories` as cat on cat.category_id=categ.category_id
                  WHERE prod.product_publish = '1' AND prod.product_manufacturer_id='".$this->_db->escape($this->manufacturer_id)."' AND cat.category_publish='1' ".$adv_query." order by name";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObjectList();        
        return $list;
           
    } 
    
}
?>