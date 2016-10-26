<?php
/**
* @version      3.9.0 25.07.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerExtTaxes extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("exttaxes");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = JSFactory::getConfig();
        $back_tax_id = JRequest::getInt("back_tax_id");
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.exttaxes";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ET.id", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $taxes = JSFactory::getModel("taxes");
        $rows = $taxes->getExtTaxes($back_tax_id, $filter_order, $filter_order_Dir);
        
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

        $view = $this->getView("taxes_ext", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows); 
        $view->assign('back_tax_id', $back_tax_id);
        $view->assign('config', $jshopConfig);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforedisplayExtTax', array(&$view)); 
        $view->displayList();
    }

    function edit(){
        $jshopConfig = JSFactory::getConfig();
        $back_tax_id = JRequest::getInt("back_tax_id");
        $id = JRequest::getInt("id");
        
        $tax = JSFactory::getTable('taxExt', 'jshop');
        $tax->load($id);
        
        if (!$tax->tax_id && $back_tax_id){
            $tax->tax_id = $back_tax_id;
        }

        $list_c = $tax->getZones();
        $zone_countries = array();
        foreach($list_c as $v){
            $obj = new stdClass();
            $obj->country_id = $v;
            $zone_countries[] = $obj;
        }

        $taxes = JSFactory::getModel("taxes");
        $all_taxes = $taxes->getAllTaxes();
        $list_tax = array();
        foreach ($all_taxes as $_tax) {
            $list_tax[] = JHTML::_('select.option', $_tax->tax_id,$_tax->tax_name, 'tax_id', 'tax_name');
        }
        $lists['taxes'] = JHTML::_('select.genericlist', $list_tax, 'tax_id', '', 'tax_id', 'tax_name', $tax->tax_id);
        
        $countries = JSFactory::getModel("countries");
        $lists['countries'] = JHTML::_('select.genericlist', $countries->getAllCountries(0), 'countries_id[]', 'size = "10", multiple = "multiple"', 'country_id', 'name', $zone_countries);        

        $view = $this->getView("taxes_ext", 'html');
        $view->setLayout("edit");
        JFilterOutput::objectHTMLSafe($tax, ENT_QUOTES);
        $view->assign('tax', $tax);
        $view->assign('back_tax_id', $back_tax_id);
        $view->assign('lists', $lists);
        $view->assign('config', $jshopConfig);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditExtTax', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $back_tax_id = JRequest::getInt("back_tax_id");
        $id = JRequest::getInt("id");
        $tax = JSFactory::getTable('taxExt', 'jshop');
        $post = JRequest::get("post"); 
        $post['tax'] = saveAsPrice($post['tax']);
        $post['firma_tax'] = saveAsPrice($post['firma_tax']);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeSaveExtTax', array(&$post) );
        
        if (!$tax->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=exttaxes&back_tax_id=".$back_tax_id);
            return 0;
        }
        $tax->setZones($post['countries_id']);

        if (!$tax->store()){
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=exttaxes&back_tax_id=".$back_tax_id);
            return 0; 
        }
        
        updateCountExtTaxRule();
        
        $dispatcher->trigger( 'onAfterSaveExtTax', array(&$tax) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=exttaxes&task=edit&id=".$tax->id."&back_tax_id=".$back_tax_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=exttaxes&back_tax_id=".$back_tax_id);
        }
    }

    function remove(){
        $back_tax_id = JRequest::getInt("back_tax_id");
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = array();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveExtTax', array(&$cid) );

        foreach ($cid as $key => $value) {
            $query = "DELETE FROM `#__jshopping_taxes_ext` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            if ($db->query()){
                $text[] = _JSHOP_ITEM_DELETED;
            }
        }
        
        updateCountExtTaxRule();
        
        $dispatcher->trigger( 'onAfterRemoveExtTax', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=exttaxes&back_tax_id=".$back_tax_id, implode("</li><li>",$text));
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=taxes");
    }
    
}
?>