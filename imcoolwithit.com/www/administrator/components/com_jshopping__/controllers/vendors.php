<?php
/**
* @version      4.10.0 12.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerVendors extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("vendors");
        addSubmenu("other");
    }	

    function display($cachable = false, $urlparams = false){       
        $mainframe = JFactory::getApplication();
                
        $context = "jshopping.list.admin.vendors";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 'text_search', '' );
        
        $vendors = JSFactory::getModel("vendors");
        
        $total = $vendors->getCountAllVendors($text_search);		
		        
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);        
        $rows = $vendors->getAllVendors($pageNav->limitstart, $pageNav->limit, $text_search);
        
        $view=$this->getView("vendors", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);        
        $view->assign('pageNav', $pageNav);
        $view->assign('text_search', $text_search);
        $view->sidebar = JHtmlSidebar::render();     
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayVendors', array(&$view));		
        $view->displayList();
    }
    
    function edit(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $id = JRequest::getInt("id");
        $vendor = JSFactory::getTable('vendor', 'jshop');
        $vendor->load($id);
        if (!$id){
            $vendor->publish = 1;
        }
        $_countries = JSFactory::getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $lists['country'] = JHTML::_('select.genericlist', $countries,'country','class = "inputbox" size = "1"','country_id','name', $vendor->country);
        
        $nofilter = array();
        JFilterOutput::objectHTMLSafe( $vendor, ENT_QUOTES, $nofilter);
        
        $view=$this->getView("vendors", 'html');
        $view->setLayout("edit");
        $view->assign('vendor', $vendor);  
        $view->assign('lists', $lists);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditVendors', array(&$view));
        $view->displayEdit();        
    }
    
    function save(){
        $apply = JRequest::getVar("apply");
        $vendor = JSFactory::getTable('vendor', 'jshop');
        
        $dispatcher = JDispatcher::getInstance();

        $id = JRequest::getInt("id");
        $vendor->load($id);
        
        if (!isset($_POST['publish'])){
            $_POST['publish'] = 0;
        }
        $post = JRequest::get("post");
        $dispatcher->trigger('onBeforeSaveVendor', array(&$post) );
        $vendor->bind($post);
        
        JSFactory::loadLanguageFile();

        if (!$vendor->check()) {            
            JError::raiseWarning("", $vendor->getError());
            $this->setRedirect("index.php?option=com_jshopping&controller=vendors&task=edit&id=".$vendor->id);
            return 0;
        }
        
        if (!$vendor->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=vendors&task=edit&id=".$vendor->id);
            return 0;
        }
        
        $dispatcher->trigger( 'onAfterSaveVendor', array(&$vendor) );
        
        if ($this->getTask()=='apply'){        
            $this->setRedirect("index.php?option=com_jshopping&controller=vendors&task=edit&id=".$vendor->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=vendors");
        }
    }

    function remove(){
        $mainframe = JFactory::getApplication();
        $vendor = JSFactory::getTable('vendor', 'jshop');
        $cid = JRequest::getVar( 'cid', array(), '', 'array' );
        $db = JFactory::getDBO(); 
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeRemoveVendor', array(&$cid));
        foreach($cid as $id){
            $query = "select count(*) from `#__jshopping_products` where `vendor_id`=".intval($id);
            $db->setQuery($query);
            $cp = $db->loadResult();
			if (!$cp){
                $query = "delete from `#__jshopping_vendors` where id='".$db->escape($id)."' and main=0";
                $db->setQuery($query);
                $db->query();
            }else{
                $vendor->load($id);
                JError::raiseWarning("",sprintf(_JSHOP_ITEM_ALREADY_USE, $vendor->f_name." ".$vendor->l_name));
            }
        }
        $dispatcher->trigger('onAfterRemoveVendor', array(&$cid));
        
        $this->setRedirect("index.php?option=com_jshopping&controller=vendors");
    }    
    
}
?>