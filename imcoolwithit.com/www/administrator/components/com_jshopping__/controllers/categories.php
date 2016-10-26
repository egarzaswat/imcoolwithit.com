<?php
/**
* @version      4.10.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCategories extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        checkAccessController("categories");
        addSubmenu("categories");
    }
    
    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        
        $dispatcher = JDispatcher::getInstance();
        $jshopConfig = JSFactory::getConfig();
        $_categories = JSFactory::getModel("categories");
        
        $context = "jshopping.list.admin.category";
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
		$text_search = $mainframe->getUserStateFromRequest($context.'text_search', 'text_search', '');
		
		$filter = array("text_search" => $text_search);
		
        $categories = $_categories->getTreeAllCategories($filter, $filter_order, $filter_order_Dir);
        $total = count($categories);

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        
        $countproducts = $_categories->getAllCatCountProducts();
        
        $categories = array_slice($categories, $pagination->limitstart, $pagination->limit);
        $view = $this->getView("category", 'html');
        $view->setLayout("list");
        $view->assign('categories', $categories);
        $view->assign('countproducts', $countproducts);
        $view->assign('pagination', $pagination);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		$view->assign('text_search', $text_search);
        $view->sidebar = JHtmlSidebar::render();
        $dispatcher->trigger('onBeforeDisplayListCategoryView', array(&$view));
        $view->displayList();
    }
    
    function edit() {
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $cid = JRequest::getInt("category_id");
        
        $category = JSFactory::getTable("category","jshop");
        $category->load($cid);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $nofilter = array();
        JFilterOutput::objectHTMLSafe( $category, ENT_QUOTES, $nofilter);

        if ($cid) {
            $parentid = $category->category_parent_id;
            $rows = $this->_getAllCategoriesLevel($category->category_parent_id, $category->ordering);
        } else {
            $category->category_publish = 1;
            $parentid = JRequest::getInt("catid");
            $rows = $this->_getAllCategoriesLevel($parentid);
        }

        $lists['templates'] = getTemplates('category', $category->category_template);
        $lists['onelevel'] = $rows;    
        $parentTop = new stdClass();
        $parentTop->category_id = 0;
        $parentTop->name = _JSHOP_TOP_LEVEL;
        $categories = buildTreeCategory(0,1,0);
        array_unshift($categories, $parentTop);
        
        $lists['treecategories'] = JHTML::_('select.genericlist', $categories,'category_parent_id','class="inputbox" size="1" onchange = "changeCategory()"','category_id','name', $parentid);
        $lists['parentid'] = $parentid;

        $accessgroups = getAccessGroups();
        $lists['access'] = JHTML::_('select.genericlist', $accessgroups, 'access','class = "inputbox" size = "1"','id','title', $category->access);

        $view=$this->getView("category", 'html');
        $view->setLayout("edit");
        $view->assign('category', $category);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditCategories', array(&$view));
        $view->displayEdit();
    }
    
    function save(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        require_once ($jshopConfig->path.'lib/image.lib.php');
        require_once ($jshopConfig->path.'lib/uploadfile.class.php');
        
        $dispatcher = JDispatcher::getInstance();
        
        $_alias = JSFactory::getModel("alias"); 
        $db = JFactory::getDBO();
        
        $_categories = JSFactory::getModel("categories");
        $category = JSFactory::getTable("category","jshop");
        if (!$_POST["category_id"]){
            $_POST['category_add_date'] = getJsDate();
        }
        if (!isset($_POST['category_publish'])){
            $_POST['category_publish'] = 0;
        }
        
        $post = JRequest::get('post');
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        if ($post['category_parent_id']==$post['category_id']) $post['category_parent_id'] = 0;
        
        $dispatcher->trigger('onBeforeSaveCategory', array(&$post));

        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]=="") $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            $post['alias_'.$lang->language] = JApplication::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, $post['category_id'], 0)){
                $post['alias_'.$lang->language] = "";
                JError::raiseWarning("",_JSHOP_ERROR_ALIAS_ALREADY_EXIST);
            }
            $post['description_'.$lang->language] = JRequest::getVar('description'.$lang->id,'','post',"string", 2);
            $post['short_description_'.$lang->language] = JRequest::getVar('short_description_'.$lang->language,'','post',"string", 2);
        }
        
        if (!$category->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=categories");
            return 0;
        }
        $edit = $category->category_id;
        
        $upload_image = $_categories->uploadImage($post);
        if ($upload_image!=''){
            $category->category_image = $upload_image;
        }

        $this->_reorderCategory($category);
        
        $dispatcher->trigger('onBeforeStoreCategory', array(&$post, &$category));
        
        if (!$category->store()){
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=categories");
            return 0;
        }
        
        $dispatcher->trigger('onAfterSaveCategory', array(&$category, &$post));
        
        $success = ($edit)?(_JSHOP_CATEGORY_SUCC_UPDATE):(_JSHOP_CATEGORY_SUCC_ADDED);
        
        if ($this->getTask()=='apply'){
            $this->setRedirect('index.php?option=com_jshopping&controller=categories&task=edit&category_id='.$category->category_id, $success);
        }else{
            $this->setRedirect('index.php?option=com_jshopping&controller=categories', $success);
        }
    }
    
    function order(){
        $id = JRequest::getInt("id");
        $move = JRequest::getInt("move");
        $table = JSFactory::getTable('category', 'jshop');
        $table->load($id);
        $table->move($move, 'category_parent_id="'.$table->category_parent_id.'"');
        $this->setRedirect("index.php?option=com_jshopping&controller=categories");
    }
    
    function saveorder(){
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        $category_parent_id = JRequest::getInt("category_parent_id");
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('category', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = JSFactory::getTable('category', 'jshop');
        $table->ordering = null;
        $table->reorder('category_parent_id="'.$category_parent_id.'"');
                
        $this->setRedirect("index.php?option=com_jshopping&controller=categories");
    }

    function _getAllCategoriesLevel($parentId, $currentOrdering = 0){
        $jshopConfig = JSFactory::getConfig();
        $_categories = JSFactory::getModel("categories");
        $rows = $_categories->getSubCategories($parentId, "ordering");
        $first[] = JHTML::_('select.option', '0',_JSHOP_ORDERING_FIRST,'ordering','name');
        $rows = array_merge($first,$rows);
        $currentOrdering = (!$currentOrdering) ? ($rows[count($rows) - 1]->ordering) : ($currentOrdering);
        return (JHTML::_('select.genericlist', $rows,'ordering','class="inputbox" size="1"','ordering','name',$currentOrdering));
    }
    
    function _reorderCategory(&$category) {
        $db = JFactory::getDBO();
        $query = "UPDATE `#__jshopping_categories` SET `ordering` = ordering + 1
                    WHERE `category_parent_id` = '" . $category->category_parent_id . "' AND `ordering` > '" . $category->ordering . "'";
        $db->setQuery($query);
        $db->query();
        $category->ordering++;
    }
    
    function publish(){
        $this->publishCategory(1);
        $this->setRedirect('index.php?option=com_jshopping&controller=categories');
    }
    
    function unpublish(){
        $this->publishCategory(0);
        $this->setRedirect('index.php?option=com_jshopping&controller=categories');
    }
    
    function publishCategory($flag) {
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $cid = JRequest::getVar("cid");
        $dispatcher->trigger( 'onBeforePublishCategory', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__jshopping_categories` SET `category_publish` = '" . $db->escape($flag) . "' WHERE `category_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }

        $dispatcher->trigger( 'onAfterPublishCategory', array(&$cid, &$flag) );
    }
    
    function remove(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $text = array();
        $cid = JRequest::getVar("cid");
        $_categories = JSFactory::getModel("categories");
        
        $dispatcher->trigger('onBeforeRemoveCategory', array(&$cid));
        $allCatCountProducts = $_categories->getAllCatCountProducts();
        
        foreach($cid as $key=>$value){
            $category = JSFactory::getTable("category", "jshop");
            $category->load($value);
            $name_category = $category->getName();
            $childs = $category->getChildCategories();
            if ($allCatCountProducts[$value] || count($childs)){
                JError::raiseWarning("",sprintf(_JSHOP_CATEGORY_NO_DELETED, $name_category));
                continue;
            }
            $_categories->deleteCategory($value);
            @unlink($jshopConfig->image_category_path.'/'.$category->category_image);
            $text[]= sprintf(_JSHOP_CATEGORY_DELETED, $name_category);
        }
        
        $dispatcher->trigger( 'onAfterRemoveCategory', array(&$cid) );
        
        $this->setRedirect('index.php?option=com_jshopping&controller=categories', implode('</li><li>',$text));
    }
    
    function sorting_cats_html(){
        $catid = JRequest::getVar('catid');
        print $this->_getAllCategoriesLevel($catid);
    die();    
    }
    
    function delete_foto(){
        $catid = JRequest::getInt("catid");
        $jshopConfig = JSFactory::getConfig();
        $category = JSFactory::getTable("category", "jshop");
        $category->load($catid);
        @unlink($jshopConfig->image_category_path.'/'.$category->category_image);
        $category->category_image = "";
        $category->store();
    die();
    }
}
?>