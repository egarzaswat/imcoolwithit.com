<?php
/**
* @version      4.10.0 03.11.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerConfigDisplayPrice extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("configdisplayprice");
        addSubmenu("config");        
    }
    
    function display($cachable = false, $urlparams = false){
        $db = JFactory::getDBO();
        $_configdisplayprice = JSFactory::getModel("configDisplayPrice");
        $rows = $_configdisplayprice->getList();
        
        $countries = JSFactory::getModel("countries");
        $list = $countries->getAllCountries(0);    
        $countries_name = array();
        foreach($list as $v){
            $countries_name[$v->country_id] = $v->name;
        }
        
        foreach($rows as $k=>$v){
            $list = unserialize($v->zones);
            
            foreach($list as $k2=>$v2){
                $list[$k2] = $countries_name[$v2];
            }
            if (count($list) > 10){
                $tmp = array_slice($list, 0, 10);
                $rows[$k]->countries = implode(", ", $tmp)."...";
            }else{
                $rows[$k]->countries = implode(", ", $list);
            }
        }
        
        $typedisplay = array(0=>_JSHOP_PRODUCT_BRUTTO_PRICE, 1=>_JSHOP_PRODUCT_NETTO_PRICE);
        
        $view = $this->getView("config_display_price", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('typedisplay', $typedisplay);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayConfigDisplayPrice', array(&$view)); 
        $view->displayList();
    }
    
    function edit() {        
        $id = JRequest::getInt("id");
        
        $configdisplayprice = JSFactory::getTable('configDisplayPrice', 'jshop');
        $configdisplayprice->load($id);
        
        $list_c = $configdisplayprice->getZones();
        $zone_countries = array();        
        foreach($list_c as $v){
            $obj = new stdClass();
            $obj->country_id = $v;
            $zone_countries[] = $obj;
        }        
        
        $display_price_list = array();
        $display_price_list[] = JHTML::_('select.option', 0, _JSHOP_PRODUCT_BRUTTO_PRICE, 'id', 'name');
        $display_price_list[] = JHTML::_('select.option', 1, _JSHOP_PRODUCT_NETTO_PRICE, 'id', 'name');
        
        $lists['display_price'] = JHTML::_('select.genericlist', $display_price_list, 'display_price', '', 'id', 'name', $configdisplayprice->display_price);
        $lists['display_price_firma'] = JHTML::_('select.genericlist', $display_price_list, 'display_price_firma', '', 'id', 'name', $configdisplayprice->display_price_firma);
        
        $countries = JSFactory::getModel("countries");
        $lists['countries'] = JHTML::_('select.genericlist', $countries->getAllCountries(0), 'countries_id[]', 'size = "10", multiple = "multiple"', 'country_id', 'name', $zone_countries);        
        
        JFilterOutput::objectHTMLSafe($configdisplayprice, ENT_QUOTES);

        $view = $this->getView("config_display_price", 'html');
        $view->setLayout("edit");
        $view->assign('row', $configdisplayprice);
        $view->assign('lists', $lists);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditConfigDisplayPrice', array(&$view));
        $view->displayEdit();
    }

    function save(){        
        $id = JRequest::getInt("id");
        $configdisplayprice = JSFactory::getTable('configDisplayPrice', 'jshop');        
        $post = JRequest::get("post");
        
        
        $dispatcher = JDispatcher::getInstance();
        
        $dispatcher->trigger( 'onBeforeSaveConfigDisplayPrice', array(&$post) );
                
        if (!$post['countries_id']){
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice&task=edit&id=".$post['id']);
            return 0;
        }
        
        if (!$configdisplayprice->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice");
            return 0;
        }
        $configdisplayprice->setZones($post['countries_id']);

        if (!$configdisplayprice->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice");
            return 0; 
        }
        
        updateCountConfigDisplayPrice();
        
        $dispatcher->trigger( 'onAftetSaveConfigDisplayPrice', array(&$configdisplayprice) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice&task=edit&id=".$configdisplayprice->id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice");
        }
                        
    }

    function remove(){
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        
        $dispatcher->trigger( 'onBeforeDeleteConfigDisplayPrice', array(&$cid) );
        $text = array();
        foreach ($cid as $key => $value) {            
            $query = "DELETE FROM `#__jshopping_config_display_prices` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }    
        }
        
        updateCountConfigDisplayPrice();
        
        $dispatcher->trigger( 'onAfterDeleteConfigDisplayPrice', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=configdisplayprice", implode("</li><li>",$text));
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=config&task=general");
    }
    
}
?>		