<?php
/**
* @version      4.10.0 03.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerProductFields extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("productfields");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.productfields";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "F.ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $group = $mainframe->getUserStateFromRequest($context.'group', 'group', 0, 'int');
        $text_search = $mainframe->getUserStateFromRequest($context.'text_search', 'text_search', '');
        
        $filter = array("group"=>$group, "text_search"=>$text_search);
		
        $_categories = JSFactory::getModel("categories");
        $listCats = $_categories->getAllList(1);
        
        $_productfields = JSFactory::getModel("productFields");
		$rows = $_productfields->getList(0, $filter_order, $filter_order_Dir, $filter);
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->printcat = _JSHOP_ALL;
            }else{
                $catsnames = array();
                $_cats = unserialize($v->cats);
                foreach($_cats as $cat_id){
                    $catsnames[] = $listCats[$cat_id];
                    $rows[$k]->printcat = implode(", ", $catsnames);
                }
            }
        }
        
        $_productfieldvalues = JSFactory::getModel("productFieldValues");
        $vals = $_productfieldvalues->getAllList(2);
    
        foreach($rows as $k=>$v){
            if (isset($vals[$v->id])){
                if (is_array($vals[$v->id])){
                    $rows[$k]->count_option = count($vals[$v->id]);
                }else{
                    $rows[$k]->count_option = 0;
                }
            }else{
                $rows[$k]->count_option = 0;
            }    
        }
		$lists = array();
        $_productfieldgroups = JSFactory::getModel("productFieldGroups");
        $groups = $_productfieldgroups->getList();
        $groups0 = array();
        $groups0[] = JHTML::_('select.option', 0, "- "._JSHOP_GROUP." -", 'id', 'name');        
        $lists['group'] = JHTML::_('select.genericlist', array_merge($groups0, $groups), 'group', 'class="chosen-select" onchange="document.adminForm.submit();"', 'id', 'name', $group);
        
        $types = array(_JSHOP_LIST, _JSHOP_TEXT);

        $view = $this->getView("product_fields", 'html');
        $view->setLayout("list");
		$view->assign('lists', $lists);
        $view->assign('rows', $rows);
        $view->assign('vals', $vals);
        $view->assign('types', $types);
		$view->assign('text_search', $text_search);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductField', array(&$view));
        $view->displayList();
    }
    
    function edit(){        
        $id = JRequest::getInt("id");
        $productfield = JSFactory::getTable('productField', 'jshop');
        $productfield->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $all = array();
        $all[] = JHTML::_('select.option', 1, _JSHOP_ALL, 'id','value');
        $all[] = JHTML::_('select.option', 0, _JSHOP_SELECTED, 'id','value');
        if (!isset($productfield->allcats)) $productfield->allcats = 1;
        $lists['allcats'] = JHTML::_('select.radiolist', $all, 'allcats','onclick="PFShowHideSelectCats()"','id','value', $productfield->allcats);
        
        $categories_selected = $productfield->getCategorys();
        $categories = buildTreeCategory(0,1,0);
        $lists['categories'] = JHTML::_('select.genericlist', $categories,'category_id[]','class="inputbox" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        
        $type = array();
        $type[] = JHTML::_('select.option', 0, _JSHOP_LIST, 'id', 'value');
        $type[] = JHTML::_('select.option', -1, _JSHOP_MULTI_LIST, 'id', 'value');
        $type[] = JHTML::_('select.option', 1, _JSHOP_TEXT, 'id', 'value');
        if (!isset($productfield->type)) $productfield->type = 0;
        if ($productfield->multilist) $productfield->type = -1;
        $lists['type'] = JHTML::_('select.radiolist', $type, 'type','','id','value', $productfield->type);
        
        $_productfieldgroups = JSFactory::getModel("productFieldGroups");
        $groups = $_productfieldgroups->getList();
        $groups0 = array();
        $groups0[] = JHTML::_('select.option', 0, "- - -", 'id', 'name');        
        $lists['group'] = JHTML::_('select.genericlist', array_merge($groups0, $groups),'group','class="inputbox"','id','name', $productfield->group);
                                                    
        $view = $this->getView("product_fields", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe($productfield, ENT_QUOTES);
        $view->assign('row', $productfield);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditProductFields', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $id = JRequest::getInt("id");
        $productfield = JSFactory::getTable('productField', 'jshop');        
        $post = JRequest::get("post");
        if ($post['type']==-1){
            $post['type'] = 0;
            $post['multilist'] = 1;
        }else{
            $post['multilist'] = 0;
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveProductField', array(&$post) );
                
        if (!$productfield->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
            return 0;
        }
        
        $categorys = $post['category_id'];
        if (!is_array($categorys)) $categorys = array();
        
        $productfield->setCategorys($categorys);
        
        if (!$id){
            $productfield->ordering = null;
            $productfield->ordering = $productfield->getNextOrder();            
        }

        if (!$productfield->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
            return 0; 
        }
        
        if (!$id){
            $query = "ALTER TABLE `#__jshopping_products` ADD `extra_field_".$productfield->id."` ".$jshopConfig->new_extra_field_type." NOT NULL";
            $db->setQuery($query);
            $db->query();
        }
        
        $dispatcher->trigger( 'onAfterSaveProductField', array(&$productfield) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=productfields&task=edit&id=".$productfield->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
        }
                        
    }

    function remove(){
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = array();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveProductField', array(&$cid) );
        foreach ($cid as $key => $value) {            
            $query = "DELETE FROM `#__jshopping_products_extra_fields` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }
            
            $query = "DELETE FROM `#__jshopping_products_extra_field_values` WHERE `field_id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();
            
            $query = "ALTER TABLE `#__jshopping_products` DROP `extra_field_".$value."`";
            $db->setQuery($query);
            $db->query();
                
        }
        $dispatcher->trigger( 'onAfterRemoveProductField', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields", implode("</li><li>",$text));
    }
    
    function order(){        
        $id = JRequest::getInt("id");
        $move = JRequest::getInt("move");        
        $productfield = JSFactory::getTable('productField', 'jshop');
        $productfield->load($id);
        $productfield->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
    function saveorder(){
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('productField', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = JSFactory::getTable('productField', 'jshop');
        $table->ordering = null;
        $table->reorder();        
                
        $this->setRedirect("index.php?option=com_jshopping&controller=productfields");
    }
    
    function addgroup(){
        $this->setRedirect("index.php?option=com_jshopping&controller=productfieldgroups");
    }
    
}
?>		