<?php
/**
* @version      4.10.4 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();

class JshoppingModelCategories extends JModelLegacy{
    
    function getAllList($display=0){
        $db = JFactory::getDBO();        
        $lang = JSFactory::getLang();
        if (isset($order) && $order=="id") $orderby = "`category_id`";
        if (isset($order) && $order=="name") $orderby = "`".$lang->get('name')."`";
        if (isset($order) && $order=="ordering") $orderby = "ordering";
        if (isset($orderby) && !$orderby) $orderby = "ordering";
        $query = "SELECT `".$lang->get('name')."` as name, category_id FROM `#__jshopping_categories` ORDER BY ordering";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);        
        $list = $db->loadObjectList();
        if ($display==1){
            $rows = array();
            foreach($list as $k=>$v){
                $rows[$v->category_id] = $v->name;    
            }
            unset($list);
            $list = $rows;
        }
        return $list;
    }
    
    function getSubCategories($parentId, $order = 'id', $ordering = 'asc') {
        $db = JFactory::getDBO();        
        $lang = JSFactory::getLang();
        if ($order=="id") $orderby = "`category_id`";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image FROM `#__jshopping_categories`
                   WHERE category_parent_id = '".$db->escape($parentId)."'
                   ORDER BY ".$orderby." ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);        
        return $db->loadObjectList();
    }
    
    function getAllCatCountSubCat() {
        $db = JFactory::getDBO();        
        $query = "SELECT C.category_id, count(C.category_id) as k FROM `#__jshopping_categories` as C
                   inner join  `#__jshopping_categories` as SC on C.category_id=SC.category_parent_id
                   group by C.category_id";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $rows = array();
        foreach($list as $row){
            $rows[$row->category_id] = $row->k;
        }        
        return $rows;
    }
    
    function getAllCatCountProducts(){
        $db = JFactory::getDBO();    
        $query = "SELECT category_id, count(product_id) as k FROM `#__jshopping_products_to_categories` group by category_id";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $rows = array();
        foreach($list as $row){
            $rows[$row->category_id] = $row->k;
        }        
        return $rows;
    }
    
    function deleteCategory($category_id){
        $db = JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_categories` WHERE `category_id` = '" . $db->escape($category_id) . "'";
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->query();
    }
    
    function getTreeAllCategories($filter = array(), $order = null, $orderDir = null) {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();

        $query = "SELECT ordering, category_id, category_parent_id, `".$lang->get('name')."` as name, `".$lang->get('short_description')."` as short_description, `".$lang->get('description')."` as description, category_publish, category_image FROM `#__jshopping_categories`
                  ORDER BY category_parent_id, ". $this->_allCategoriesOrder($order, $orderDir);
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
		$tree = new treeObjectList($db->loadObjectList(), array(
			'parent' => 'category_parent_id',
			'id' => 'category_id',
			'is_select' => 0
		));
		$categories = $tree->getList();

		foreach($categories as $key=>$category){
			$category->isPrev = 0; 
			$category->isNext = 0;
			if (isset($categories[$key-1]) && $category->category_parent_id == $categories[$key-1]->category_parent_id){
				$category->isPrev = 1;
			}
			if (isset($categories[$key+1]) && $category->category_parent_id == $categories[$key+1]->category_parent_id){
				$category->isNext = 1;
			}
		}

        if (count($categories)){
			if (isset($filter['text_search']) && !empty($filter['text_search'])){
                $originalCategories = $categories;
                $filter['text_search'] = strtolower($filter['text_search']);

                foreach($categories as $key => $category){
                    if (strpos(strtolower($category->name), $filter['text_search']) === false && 
						strpos(strtolower($category->short_description), $filter['text_search']) === false && 
						strpos(strtolower($category->description), $filter['text_search']) === false){
                        unset($categories[$key]);
                    }
                }

                if (count($categories)){
                    foreach($categories as $key => $category){
                        $categories[$key]->name = "<span class = 'jshop_green'>".$categories[$key]->name."</span>"; 
                        $category_parent_id = $category->category_parent_id;
                        $i = 0;
                        while ($category_parent_id || $i < 1000) {
                            foreach ($originalCategories as $originalKey => $originalCategory){
                                if ($originalCategory->category_id == $category_parent_id){
                                    $categories[$originalKey] = $originalCategory;
                                    $category_parent_id = $originalCategory->category_parent_id;
                                    break;
                                }
                            }
                            $i++;
                        }
                    }
                    
                    ksort($categories);
                }
            }
		
            foreach($categories as $key=>$category){
                $category->space = ''; 
                for ($i = 0; $i < $category->level; $i++){
                    $category->space .= '<span class = "gi">|—</span>';
                }
            }
        }
		
        return $categories;
    }
   
    function _allCategoriesOrder($order = null, $orderDir = null){
        $lang = JSFactory::getLang();
        if ($order && $orderDir){
            $fields = array("name" => "`".$lang->get('name')."`", "id" => "`category_id`", "description" => "`".$lang->get('description')."`", "ordering" => "`ordering`");
            if (strtolower($orderDir) != "asc") $orderDir = "desc";
            if (!$fields[$order]) return "`ordering` ".$orderDir;
            extract(js_add_trigger(get_defined_vars(), "before"));
            return $fields[$order]." ".$orderDir;
        }else{
            return "`ordering` asc";
        }
    }
    
    function uploadImage($post){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = JDispatcher::getInstance();
        
        $upload = new UploadFile($_FILES['category_image']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($jshopConfig->image_category_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            $name = $upload->getName();
            $dispatcher->trigger('onAfterUploadCategoryImage', array(&$post, &$name));
            if ($post['old_image'] && $name!=$post['old_image']){
                @unlink($jshopConfig->image_category_path."/".$post['old_image']);
            }
            @chmod($jshopConfig->image_category_path."/".$name, 0777);
            
            if ($post['size_im_category'] < 3){
                if($post['size_im_category'] == 1){
                    $category_width_image = $jshopConfig->image_category_width; 
                    $category_height_image = $jshopConfig->image_category_height;
                }else{
                    $category_width_image = JRequest::getInt('category_width_image'); 
                    $category_height_image = JRequest::getInt('category_height_image');
                }

                $path_full = $jshopConfig->image_category_path."/".$name;
                $path_thumb = $jshopConfig->image_category_path."/".$name;
                if ($category_width_image || $category_height_image){
                    if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)) {
                        JError::raiseWarning("",_JSHOP_ERROR_CREATE_THUMBAIL);
                        saveToLog("error.log", "SaveCategory - Error create thumbail");
                    }
                }
                @chmod($jshopConfig->image_category_path."/".$name, 0777);
            }
            $category_image = $name;
            $dispatcher->trigger('onAfterSaveCategoryImage', array(&$post, &$category_image, &$path_full, &$path_thumb));
        }else{
            $category_image = '';
            if ($upload->getError() != 4){
                JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_IMAGE);
                saveToLog("error.log", "SaveCategory - Error upload image. code: ".$upload->getError());
            }
        }
        return $category_image;
    }
    
}
?>