<?php
/**
* @version      4.10.0 25.11.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerTaxes extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("taxes");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false) {
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.taxes";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "tax_name", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $taxes = JSFactory::getModel("taxes");
        $rows = $taxes->getAllTaxes($filter_order, $filter_order_Dir);
        
        $view = $this->getView("taxes", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render(); 
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayTaxes', array(&$view));
        $view->displayList();
    }
    
    function edit() {
        $tax_id = JRequest::getInt("tax_id");        
        $tax = JSFactory::getTable('tax', 'jshop');
        $tax->load($tax_id);
        $edit = ($tax_id)?($edit = 1):($edit = 0);
                
        $view=$this->getView("taxes", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe( $tax, ENT_QUOTES);
        $view->assign('tax', $tax); 
        $view->assign('edit', $edit);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditTaxes', array(&$view)); 
        $view->displayEdit();
    }

    function save(){    
        $tax_id = JRequest::getInt("tax_id");
        $tax = JSFactory::getTable('tax', 'jshop');
        $post = JRequest::get("post"); 
        $post['tax_value'] = saveAsPrice($post['tax_value']);
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveTax', array(&$tax) );

        
        if (!$tax->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=taxes");
            return 0;
        }

        if (!$tax->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=taxes");
            return 0; 
        }
        
        $dispatcher->trigger( 'onAfterSaveTax', array(&$tax) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=taxes&task=edit&tax_id=".$tax->tax_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=taxes");
        }
                        
    }

    function remove() {
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = '';
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveTax', array(&$cid) );

        foreach ($cid as $key => $value) {
            $tax = JSFactory::getTable('tax', 'jshop');
            $tax->load($value);
            $query2 = "SELECT pr.product_id
                       FROM `#__jshopping_products` AS pr
                       WHERE pr.product_tax_id = '" . $db->escape($value) . "'";
            $db->setQuery($query2);
            $res = $db->query();
            if($db->getNumRows($res)) {
                $text .= sprintf(_JSHOP_TAX_NO_DELETED, $tax->tax_name)."<br>";
                continue;
            }
            $query = "DELETE FROM `#__jshopping_taxes` WHERE `tax_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            if ($db->query()){
                $text .= sprintf(_JSHOP_TAX_DELETED,$tax->tax_name)."<br>";
            }
            $query = "DELETE FROM `#__jshopping_taxes_ext` WHERE `tax_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();            
        }
        
        $dispatcher->trigger( 'onAfterRemoveTax', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=taxes", $text);
    }
    
}
?>