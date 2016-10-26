<?php
/**
* @version      4.11.7 22.12.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die();
jimport('joomla.application.component.controller');

class JshoppingControllerCoupons extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("coupons");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.coupons";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "C.coupon_code", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $jshopConfig = JSFactory::getConfig();  	        		
        
        $coupons = JSFactory::getModel("coupons");
        $total = $coupons->getCountCoupons();
        
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);        
        $rows = $coupons->getAllCoupons($pageNav->limitstart, $pageNav->limit, $filter_order, $filter_order_Dir);
        
        $currency = JSFactory::getTable('currency', 'jshop');
        $currency->load($jshopConfig->mainCurrency);
                        
		$view=$this->getView("coupons", 'html');
        $view->setLayout("list");		
        $view->assign('rows', $rows);        
        $view->assign('currency', $currency->currency_code);
        $view->assign('pageNav', $pageNav);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();   
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayCoupons', array(&$view));		
		$view->displayList(); 
    }
    
    function edit() {
        $coupon_id = JRequest::getInt('coupon_id');
        $coupon = JSFactory::getTable('coupon', 'jshop'); 
        $coupon->load($coupon_id);
        $edit = ($coupon_id)?($edit = 1):($edit = 0);
        $arr_type_coupon = array();
		$arr_type_coupon[0] = new StdClass();
        $arr_type_coupon[0]->coupon_type = 0;
        $arr_type_coupon[0]->coupon_value = _JSHOP_COUPON_PERCENT;

		$arr_type_coupon[1] = new StdClass();
        $arr_type_coupon[1]->coupon_type = 1;
        $arr_type_coupon[1]->coupon_value = _JSHOP_COUPON_ABS_VALUE;
        
        if (!$coupon_id){
          $coupon->coupon_type = 0;  
          $coupon->finished_after_used = 1;
          $coupon->for_user_id = 0;
        }
        $currency_code = getMainCurrencyCode();

        $lists['coupon_type'] = JHTML::_('select.radiolist', $arr_type_coupon, 'coupon_type', 'onchange="changeCouponType()"', 'coupon_type', 'coupon_value', $coupon->coupon_type);

        $_tax = JSFactory::getModel("taxes");
        $all_taxes = $_tax->getAllTaxes();
        $list_tax = array();        
        foreach ($all_taxes as $tax) {
            $list_tax[] = JHTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
        }
        $lists['tax'] = JHTML::_('select.genericlist', $list_tax, 'tax_id', 'class = "inputbox" size = "1" ', 'tax_id', 'tax_name', $coupon->tax_id);        
        
        $view=$this->getView("coupons", 'html');
        $view->setLayout("edit");        
        $view->assign('coupon', $coupon);        
        $view->assign('lists', $lists);        
        $view->assign('edit', $edit);
        $view->assign('currency_code', $currency_code);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditCoupons', array(&$view));
        $view->displayEdit();
    }
    
    function save() {        

        $coupon_id = JRequest::getInt("coupon_id");        
        $coupon = JSFactory::getTable('coupon', 'jshop');
        
        
        $dispatcher = JDispatcher::getInstance();        
        
        $post = JRequest::get("post");        
        $post['coupon_code'] = JRequest::getVar("coupon_code");
        $post['coupon_publish'] = JRequest::getInt("coupon_publish");
        $post['finished_after_used'] = JRequest::getInt("finished_after_used");
        $post['coupon_value'] = saveAsPrice($post['coupon_value']);
        
        $dispatcher->trigger( 'onBeforeSaveCoupon', array(&$post) );
        
        if (!$post['coupon_code']){
            JError::raiseWarning("",_JSHOP_ERROR_COUPON_CODE);
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=".$coupon->coupon_id);
            return 0;
        }
        
        if ($post['coupon_value']<0 || ($post['coupon_value']>100 && $post['coupon_type']==0)){
            JError::raiseWarning("",_JSHOP_ERROR_COUPON_VALUE);
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=".$coupon->coupon_id);    
            return 0;
        }        
        
        if(!$coupon->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons");
            return 0;
        }
        
        if ($coupon->getExistCode()){
            JError::raiseWarning("",_JSHOP_ERROR_COUPON_EXIST);
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons");
            return 0;
        }

        if (!$coupon->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons");
            return 0;
        }
                
        $dispatcher->trigger( 'onAfterSaveCoupon', array(&$coupon) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=".$coupon->coupon_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=coupons");
        }
                
    }
    
    function remove() {
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $text = '';
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveCoupon', array(&$cid) );

        foreach ($cid as $key => $value) {
            $query = "DELETE FROM `#__jshopping_coupons` WHERE `coupon_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        
        $dispatcher->trigger( 'onAfterRemoveCoupon', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=coupons", _JSHOP_COUPON_DELETED);
    }
    
    function publish(){
        $this->publishCoupon(1);
    }
    
    function unpublish(){
        $this->publishCoupon(0);
    }

    function publishCoupon($flag) {
        $db = JFactory::getDBO();
        $cid = JRequest::getVar("cid");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishCoupon', array(&$cid,&$flag) );
        
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__jshopping_coupons`
                       SET `coupon_publish` = '" . $db->escape($flag) . "'
                       WHERE `coupon_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        $dispatcher->trigger( 'onAfterPublishCoupon', array(&$cid,&$flag) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=coupons");
    }
        
}