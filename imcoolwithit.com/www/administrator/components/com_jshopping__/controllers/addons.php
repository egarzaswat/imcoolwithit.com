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

class JshoppingControllerAddons extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );        
        checkAccessController("addons");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $db = JFactory::getDBO();
        $addons = JSFactory::getModel("addons");
        $rows = $addons->getList();
        $back64 = base64_encode("index.php?option=com_jshopping&controller=addons");
        foreach($rows as $k=>$v){
            if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/config.tmpl.php")){
                $rows[$k]->config_file_exist = 1;
            }else{
                $rows[$k]->config_file_exist = 0;
            }
            if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/info.tmpl.php")){
                $rows[$k]->info_file_exist = 1;
            }else{
                $rows[$k]->info_file_exist = 0;
            }
            if (file_exists(JPATH_COMPONENT_SITE."/addons/".$v->alias."/version.tmpl.php")){
                $rows[$k]->version_file_exist = 1;
            }else{
                $rows[$k]->version_file_exist = 0;
            }
        }

        $view=$this->getView("addons", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows); 
        $view->assign('back64', $back64);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAddons', array(&$view));		
        $view->displayList();
    }
    
    function edit(){
        $id = JRequest::getVar("id");
        
        $dispatcher = JDispatcher::getInstance();
        $row = JSFactory::getTable('addon', 'jshop');
        $row->load($id);
        $config_file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/config.tmpl.php";
        $config_file_exist = file_exists($config_file_patch);

        $view=$this->getView("addons", 'html');
        $view->setLayout("edit");
        $view->assign('row', $row);
        $view->assign('params', $row->getParams());
        $view->assign('config_file_patch', $config_file_patch);
        $view->assign('config_file_exist', $config_file_exist);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditAddons', array(&$view));
        $view->displayEdit();
    }
    
    function save(){
        $this->saveConfig('save');
    }
    
    function apply(){
        $this->saveConfig();
    }
    
    private function saveConfig($task = 'apply'){
        $post = JRequest::get('post');
        $row = JSFactory::getTable('addon', 'jshop');
        $params = $post['params'];
        if (!is_array($params)) $params = array();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSaveAddons', array(&$params, &$post, &$row));
        $row->bind($post);
        $row->setParams($params);
        $row->store();
		$dispatcher->trigger('onAfterSaveAddons', array(&$params, &$post, &$row));
		
        if ($task == 'save'){
            $this->setRedirect("index.php?option=com_jshopping&controller=addons");
        } else {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons&task=edit&id=".$post['id']);
        }
    }

    function remove(){
        $id = JRequest::getVar("id");
        $db = JFactory::getDBO();
        $text = '';
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeRemoveAddons', array(&$id) );

        $row = JSFactory::getTable('addon', 'jshop');
        $row->load($id);
        if ($row->uninstall){
            include(JPATH_ROOT.$row->uninstall);
        }
        $row->delete();
        $dispatcher->trigger( 'onAfterRemoveAddons', array(&$id, &$text) );        
        $this->setRedirect("index.php?option=com_jshopping&controller=addons", $text);
    }
    
    function info(){
        $id = JRequest::getVar("id");
        
        $dispatcher = JDispatcher::getInstance();
        $row = JSFactory::getTable('addon', 'jshop');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/info.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view=$this->getView("addons", 'html');
        $view->setLayout("info");
        $view->assign('row', $row);
        $view->assign('params', $row->getParams());
        $view->assign('file_patch', $file_patch);
        $view->assign('file_exist', $file_exist);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeInfoAddons', array(&$view));
        $view->displayInfo();
    }
    
    function version(){
        $id = JRequest::getVar("id");
        
        $dispatcher = JDispatcher::getInstance();
        $row = JSFactory::getTable('addon', 'jshop');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/version.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view=$this->getView("addons", 'html');
        $view->setLayout("info");
        $view->assign('row', $row);
        $view->assign('params', $row->getParams());
        $view->assign('file_patch', $file_patch);
        $view->assign('file_exist', $file_exist);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeVersionAddons', array(&$view));
        $view->displayVersion();
    }
    
}
?>