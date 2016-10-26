<?php
/**
* @version      4.10.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerAttributesGroups extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        checkAccessController("attributesgroups");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){        
        $db = JFactory::getDBO();
        $model = JSFactory::getModel("attributesGroups");
        $rows = $model->getList();
        
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->sidebar = JHtmlSidebar::render();
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAttributesGroups', array(&$view));
        $view->displayList();
    }
    
    function edit(){        
        $id = JRequest::getInt("id");
        $row = JSFactory::getTable('attributesgroup', 'jshop');
        $row->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;    
                
        $view = $this->getView("attributesgroups", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe($row, ENT_QUOTES);
        $view->assign('row', $row);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditAttributesGroups', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $row = JSFactory::getTable('attributesgroup', 'jshop');
        $post = JRequest::get("post");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSaveAttributesGroups', array(&$post));
        
        $row->bind($post);
        if (!$id){
            $row->ordering = null;
            $row->ordering = $row->getNextOrder();
        }        
        $row->store();
        
        $dispatcher->trigger('onAfterSaveAttributesGroups', array(&$row) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups&task=edit&id=".$row->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups");
        }
    }

    function remove(){
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = array();
        foreach ($cid as $key => $value) {            
            $query = "DELETE FROM `#__jshopping_attr_groups` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }    
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterRemoveAttributesGroups', array(&$cid));
        
        $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups", implode("</li><li>", $text));
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
    
    function order(){        
        $id = JRequest::getInt("id");
        $move = JRequest::getInt("move");        
        $row = JSFactory::getTable('attributesgroup', 'jshop');
        $row->load($id);
        $row->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups");
    }
    
    function saveorder(){
        $cid = JRequest::getVar('cid', array(), 'post', 'array');
        $order = JRequest::getVar('order', array(), 'post', 'array');        
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('attributesgroup', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }
        
        $table = JSFactory::getTable('attributesgroup', 'jshop');
        $table->ordering = null;
        $table->reorder();
                
        $this->setRedirect("index.php?option=com_jshopping&controller=attributesgroups");
    }
}