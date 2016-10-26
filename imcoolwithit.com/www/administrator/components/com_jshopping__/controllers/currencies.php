<?php
/**
* @version      4.10.2 03.11.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCurrencies extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("currencies");
        addSubmenu("other");
    }
        
    function display($cachable = false, $urlparams = false) {        
        $jshopConfig = JSFactory::getConfig();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.currencies";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "currency_ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');

        $current_currency = JSFactory::getTable('currency', 'jshop');
        $current_currency->load($jshopConfig->mainCurrency);
        if ($current_currency->currency_value!=1){
            JError::raiseWarning("",_JSHOP_ERROR_MAIN_CURRENCY_VALUE);    
        }
        
        $currencies = JSFactory::getModel("currencies");
        $rows = $currencies->getAllCurrencies(0, $filter_order, $filter_order_Dir);
        
        $view=$this->getView("currencies", 'html');
        $view->setLayout("list");        
        $view->assign('rows', $rows);        
        $view->assign('config', $jshopConfig);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayCourencies', array(&$view));
        $view->displayList();
    }
    
    function edit() {        
        $db = JFactory::getDBO();        
        $currency = JSFactory::getTable('currency', 'jshop');
        $currencies = JSFactory::getModel("currencies");
        $currency_id = JRequest::getInt('currency_id');
        $currency->load($currency_id);
        if ($currency->currency_value==0) $currency->currency_value = 1;
        $first[] = JHTML::_('select.option', '0',_JSHOP_ORDERING_FIRST,'currency_ordering','currency_name');
        $rows = array_merge($first, $currencies->getAllCurrencies() );
        $lists['order_currencies'] = JHTML::_('select.genericlist', $rows,'currency_ordering','class="inputbox" size="1"','currency_ordering','currency_name',$currency->currency_ordering);
        $edit = ($currency_id)?($edit = 1):($edit = 0);
        JFilterOutput::objectHTMLSafe( $currency, ENT_QUOTES);
        
        $view=$this->getView("currencies", 'html');
        $view->setLayout("edit");
        $view->assign('currency', $currency);        
        $view->assign('lists', $lists);        
        $view->assign('edit', $edit);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditCurrencies', array(&$view));        
        $view->displayEdit();
    }

    function save() {
        
        $dispatcher = JDispatcher::getInstance();        
        $currency_id = JRequest::getInt("currency_id");
        $apply = JRequest::getVar("apply");
        $currency = JSFactory::getTable('currency', 'jshop');
        $post = JRequest::get("post");
        $post['currency_value'] = saveAsPrice($post['currency_value']);
        $dispatcher->trigger( 'onBeforeSaveCurrencie', array(&$post) );
        if (!$currency->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
            return 0;
        }
        if ($currency->currency_value==0) $currency->currency_value = 1;

        $this->_reorderCurrency($currency);
        if (!$currency->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
            return 0;
        }
                
        $dispatcher->trigger( 'onAfterSaveCurrencie', array(&$currency) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=currencies&task=edit&currency_id=".$currency->currency_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
        }
                
    }

    function _reorderCurrency(&$currency) {
        $db = JFactory::getDBO();
        $query = "UPDATE `#__jshopping_currencies`
                    SET `currency_ordering` = currency_ordering + 1
                    WHERE `currency_ordering` > '" . $currency->currency_ordering . "'";
        $db->setQuery($query);
        $db->query();
        $currency->currency_ordering++;
    }

    function order() {
        $db = JFactory::getDBO();
        
        $order = JRequest::getVar("order");
        $cid = JRequest::getInt("id");
        $number = JRequest::getInt("number");
        
        $currency = JSFactory::getTable('currency', 'jshop'); 
        $currency->load($cid);
        switch ($order) {
            case 'up':
                $query = "SELECT a.currency_id, a.currency_ordering
                       FROM `#__jshopping_currencies` AS a
                       WHERE a.currency_ordering < '" . $number . "'
                       ORDER BY a.currency_ordering DESC
                       LIMIT 1";
                break;
            case 'down':
                $query = "SELECT a.currency_id, a.currency_ordering
                       FROM `#__jshopping_currencies` AS a
                       WHERE a.currency_ordering > '" . $number . "'
                       ORDER BY a.currency_ordering ASC
                       LIMIT 1";
        }

        $db->setQuery($query);
        $row = $db->loadObject();


        $query1 = "UPDATE `#__jshopping_currencies` AS a
                     SET a.currency_ordering = '" . $row->currency_ordering . "'
                     WHERE a.currency_id = '" . $cid . "'";
        $query2 = "UPDATE `#__jshopping_currencies` AS a
                     SET a.currency_ordering = '" . $number . "'
                     WHERE a.currency_id = '" . $row->currency_id . "'";
        $db->setQuery($query1);
        $db->query();
        $db->setQuery($query2);
        $db->query();
        
        $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
    }

    function remove() {
        $db = JFactory::getDBO();
        $text = '';
        $cid = JRequest::getVar("cid");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveCurrencie', array(&$cid) );
        
        $model = JSFactory::getModel("currencies");
        $resdelete = $model->deleteList($cid);
        
        if ($resdelete)
            $text = _JSHOP_CURRENCY_DELETED;
        else
            $text = _JSHOP_CURRENCY_ERROR_DELETED;
                
        $dispatcher->trigger( 'onAfterRemoveCurrencie', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=currencies", $text); 
    }
    
    function publish(){
        $this->publishCurrency(1);
    }
    
    function unpublish(){
        $this->publishCurrency(0);
    }

    function publishCurrency($flag) {
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishCurrencie', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__jshopping_currencies`
                       SET `currency_publish` = '" . $db->escape($flag) . "'
                       WHERE `currency_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        
        $dispatcher->trigger( 'onAfterPublishCurrencie', array(&$cid, &$flag) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
    }
    
    function setdefault(){
        $jshopConfig = JSFactory::getConfig();
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        if ($cid[0]){
            $config = new jshopConfig($db);
            $config->id = $jshopConfig->load_id;
            $config->mainCurrency = $cid[0];
            $config->store();
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=currencies");
    }
        
    
}

?>		