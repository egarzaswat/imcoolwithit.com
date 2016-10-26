<?php
/**
* @version      4.1.0 20.11.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

class jshopCategory extends JTableAvto{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_categories', 'category_id', $_db);
        JPluginHelper::importPlugin('jshoppingproducts');
    }
    
    function getSubCategories($parentId, $order = 'id', $ordering = 'asc', $publish = 0) {
        $lang = JSFactory::getLang();
        $user = JFactory::getUser();
        $add_where = ($publish)?(" AND category_publish = '1' "):("");
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $add_where .=' AND access IN ('.$groups.')';
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('description')."` as description,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image FROM `#__jshopping_categories`
                   WHERE category_parent_id = '".$this->_db->escape($parentId)."' ".$add_where."
                   ORDER BY ".$orderby." ".$ordering;
        $this->_db->setQuery($query);
        $categories = $this->_db->loadObjectList();
        foreach($categories as $key=>$value){
            $categories[$key]->category_link = SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$categories[$key]->category_id, 1);
        }        
        return $categories;
    }

    function getName() {
        $lang = JSFactory::getLang();
        $name = $lang->get('name');
        return $this->$name;
    }
    
    function getDescription(){
        
        if (!$this->category_id){
            $this->getDescriptionMainPage();
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

    function getTreeChild() {
        $category_parent_id = $this->category_parent_id;
        $i = 0;
        $list_category = array();
        $list_category[$i] = new stdClass();
        $list_category[$i]->category_id = $this->category_id;
        $list_category[$i]->name = $this->name;
        $i++;
        while($category_parent_id) {
            $category = JSFactory::getTable('category', 'jshop');
            $category->load($category_parent_id);
            $list_category[$i] = new stdClass();
            $list_category[$i]->category_id = $category->category_id;
            $list_category[$i]->name = $category->getName();
            $category_parent_id = $category->category_parent_id;
            $i++;
        }
        $list_category = array_reverse($list_category);
        return $list_category;
    }

    function getAllCategories($publish = 1, $access = 1) {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
        if ($access){
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $where[] =' access IN ('.$groups.')';
        }
        $add_where = "";
        if (count($where)){
            $add_where = " where ".implode(" and ", $where);
        }
        $query = "SELECT category_id, category_parent_id FROM `#__jshopping_categories` ".$add_where." ORDER BY ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getChildCategories($order='id', $ordering='asc', $publish=1){
        return $this->getSubCategories($this->category_id, $order, $ordering, $publish);
    }

    function getSisterCategories($order, $ordering = 'asc', $publish = 1) {
        return $this->getSubCategories($this->category_parent_id, $order, $ordering, $publish);
    }

    function getTreeParentCategories($publish = 1, $access = 1){
        $user = JFactory::getUser();
        $cats_tree = array(); 
        $category_parent = $this->category_id;
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
        if ($access){
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $where[] =' access IN ('.$groups.')';
        }
        $add_where = "";
        if (count($where)){
            $add_where = "and ".implode(" and ", $where);
        }
        while($category_parent) {
            $cats_tree[] = $category_parent;
            $query = "SELECT category_parent_id FROM `#__jshopping_categories` WHERE category_id = '".$this->_db->escape($category_parent)."' ".$add_where;
            $this->_db->setQuery($query);
            $rows = $this->_db->loadObjectList();
            $category_parent = $rows[0]->category_parent_id;
        }
        return array_reverse($cats_tree);
    }

    function getProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0) {
        $jshopConfig = JSFactory::getConfig();        
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("category", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetProductList', array("category", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  $adv_from
                  WHERE pr_cat.category_id = '".$this->_db->escape($this->category_id)."' AND prod.product_publish = '1' ".$adv_query." ".$order_query;

        if ($limit){
            $this->_db->setQuery($query, $limitstart, $limit);
        }else{
            $this->_db->setQuery($query);
        }
        $products = $this->_db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }

    function getCountProducts($filters){
        $jshopConfig = JSFactory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "count(*)";
        $this->getBuildQueryListProduct("category", "count", $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryCountProductList', array("category", &$adv_result, &$adv_from, &$adv_query, &$filters) );

        $query = "SELECT $adv_result FROM `#__jshopping_products_to_categories` AS pr_cat
                  INNER JOIN `#__jshopping_products` AS prod ON pr_cat.product_id = prod.product_id
                  $adv_from 
                  WHERE pr_cat.category_id = '".$this->_db->escape($this->category_id)."' AND prod.product_publish = '1' ".$adv_query;
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }
    
    function getDescriptionMainPage(){
        $statictext = JSFactory::getTable("statictext","jshop");
        $row = $statictext->loadData("home");
        $this->description = $row->text;
        
        $seo = JSFactory::getTable("seo","jshop");
        $row = $seo->loadData("category");
        $this->meta_title = $row->title;
        $this->meta_keyword = $row->keyword;
        $this->meta_description = $row->description;
    }
    
    /**
    * get List Manufacturer for this category
    */
    function getManufacturers(){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        $adv_query = "";
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query .=' AND prod.access IN ('.$groups.')';
        if ($jshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if ($jshopConfig->manufacturer_sorting==2){
            $order = 'name';
        }else{
            $order = 'man.ordering';
        }
        $query = "SELECT distinct man.manufacturer_id as id, man.`".$lang->get('name')."` as name FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_manufacturers` as man on prod.product_manufacturer_id=man.manufacturer_id 
                  WHERE categ.category_id = '".$this->_db->escape($this->category_id)."' AND prod.product_publish = '1' AND prod.product_manufacturer_id!=0 ".$adv_query." order by ".$order;
        $this->_db->setQuery($query);
        $list = $this->_db->loadObjectList();
        return $list;
           
    }    
}
?>