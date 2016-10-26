<?php
/**
* @version      4.8.0 22.10.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerOrders extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask('add', 'edit' );
        checkAccessController("orders");
        addSubmenu("orders");
        JPluginHelper::importPlugin('jshoppingorder');
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = JSFactory::getConfig();
        $mainframe = JFactory::getApplication();        
        $context = "jshopping.list.admin.orders";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $id_vendor_cuser = getIdVendorForCUser();
        $client_id = JRequest::getInt('client_id',0);
        
        $status_id = $mainframe->getUserStateFromRequest( $context.'status_id', 'status_id', 0 );
        $year = $mainframe->getUserStateFromRequest( $context.'year', 'year', 0 );
        $month = $mainframe->getUserStateFromRequest( $context.'month', 'month', 0 );
        $day = $mainframe->getUserStateFromRequest( $context.'day', 'day', 0 );
        $notfinished = $mainframe->getUserStateFromRequest( $context.'notfinished', 'notfinished', 0 );
        $text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 'text_search', '' );
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "order_number", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "desc", 'cmd');
        
        $filter = array("status_id"=>$status_id, 'user_id'=>$client_id, "year"=>$year, "month"=>$month, "day"=>$day, "text_search"=>$text_search, 'notfinished'=>$notfinished);
        
        if ($id_vendor_cuser){            
            $filter["vendor_id"] = $id_vendor_cuser;
        }
        
        $orders = JSFactory::getModel("orders");
        
        $total = $orders->getCountAllOrders($filter);        
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);
        
        $_list_order_status = $orders->getAllOrderStatus();
        $list_order_status = array();
        foreach($_list_order_status as $v){
            $list_order_status[$v->status_id] = $v->name;
        }
        $rows = $orders->getAllOrders($pageNav->limitstart, $pageNav->limit, $filter, $filter_order, $filter_order_Dir);
        $lists['status_orders'] = $_list_order_status;
        $_list_status0[] = JHTML::_('select.option', 0, _JSHOP_ALL_ORDERS, 'status_id', 'name');
        $_list_status = $lists['status_orders'];
        $_list_status = array_merge($_list_status0, $_list_status);
        $lists['changestatus'] = JHTML::_('select.genericlist', $_list_status,'status_id','style = "width: 170px;" ','status_id','name', $status_id );
        $nf_option = array();
        $nf_option[] = JHTML::_('select.option', 0, _JSHOP_HIDE, 'id', 'name');
        $nf_option[] = JHTML::_('select.option', 1, _JSHOP_SHOW, 'id', 'name');
        $lists['notfinished'] = JHTML::_('select.genericlist', $nf_option, 'notfinished','style = "width: 100px;" ','id','name', $notfinished );
        
        $firstYear = $orders->getMinYear(); 
        $y_option = array();
        $y_option[] = JHTML::_('select.option', 0, " - - - ", 'id', 'name');
        for($y=$firstYear;$y<=date("Y");$y++){
            $y_option[] = JHTML::_('select.option', $y, $y, 'id', 'name');
        }        
        $lists['year'] = JHTML::_('select.genericlist', $y_option, 'year', 'style = "width: 80px;" ', 'id', 'name', $year);
        
        $y_option = array();
        $y_option[] = JHTML::_('select.option', 0, " - - ", 'id', 'name');
        for($y=1;$y<=12;$y++){
            if ($y<10) $y_month = "0".$y; else $y_month = $y;
            $y_option[] = JHTML::_('select.option', $y_month, $y_month, 'id', 'name');
        }        
        $lists['month'] = JHTML::_('select.genericlist', $y_option, 'month', 'style = "width: 80px;" ', 'id', 'name', $month);
        
        $y_option = array();
        $y_option[] = JHTML::_('select.option', 0, " - - ", 'id', 'name');
        for($y=1;$y<=31;$y++){
            if ($y<10) $y_day = "0".$y; else $y_day = $y;
            $y_option[] = JHTML::_('select.option', $y_day, $y_day, 'id', 'name');
        }        
        $lists['day'] = JHTML::_('select.genericlist', $y_option, 'day', 'style = "width: 80px;" ', 'id', 'name', $day);
		
		$payments = JSFactory::getModel("payments");
        $payments_list = $payments->getListNamePaymens(0);
        
        $shippings = JSFactory::getModel("shippings");
        $shippings_list = $shippings->getListNameShippings(0);
        
        $show_vendor = $jshopConfig->admin_show_vendors;
        if ($id_vendor_cuser) $show_vendor = 0;
        $display_info_only_my_order = 0;
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            $display_info_only_my_order = 1; 
        }
        
        $total = 0;
        foreach($rows as $k=>$row){
            if ($row->vendor_id>0){
                $vendor_name = $row->v_fname." ".$row->v_name;
            }else{
                $vendor_name = "-";
            }
            $rows[$k]->vendor_name = $vendor_name;
            
            $display_info_order = 1;
            if ($display_info_only_my_order && $id_vendor_cuser!=$row->vendor_id) $display_info_order = 0;
            $rows[$k]->display_info_order = $display_info_order;
            
            $blocked = 0;
            if (orderBlocked($row) || !$display_info_order) $blocked = 1;
            $rows[$k]->blocked = $blocked;
			
			$rows[$k]->payment_name = $payments_list[$row->payment_method_id];
            $rows[$k]->shipping_name = $shippings_list[$row->shipping_method_id];
			
            $total += $row->order_total / $row->currency_exchange;
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayListOrderAdmin', array(&$rows));
		
		$view=$this->getView("orders", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows); 
        $view->assign('lists', $lists); 
        $view->assign('pageNav', $pageNav); 
        $view->assign('text_search', $text_search); 
        $view->assign('filter', $filter);        
        $view->assign('show_vendor', $show_vendor);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('list_order_status', $list_order_status);
        $view->assign('client_id', $client_id);
        $view->assign('total', $total);
        $view->_tmp_order_list_html_end = '';
        $dispatcher->trigger('onBeforeShowOrderListView', array(&$view));
		$view->displayList(); 
    }
    
    function show(){
        $order_id = JRequest::getInt("order_id");
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $orders = JSFactory::getModel("orders");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $orderstatus = JSFactory::getTable('orderStatus', 'jshop');
        $orderstatus->load($order->order_status);
        $name = $lang->get("name");    
        $order->status_name = $orderstatus->$name;
        
        $id_vendor_cuser = getIdVendorForCUser();
        
        $shipping_method =JSFactory::getTable('shippingMethod', 'jshop');
        $shipping_method->load($order->shipping_method_id);
        
        $name = $lang->get("name");
        $order->shipping_info = $shipping_method->$name;
        
        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
        $order->payment_name = $pm_method->$name;
        
        $order_items = $order->getAllItems();
        if ($jshopConfig->admin_show_vendors){
            $tmp_order_vendors = $order->getVendors();
            $order_vendors = array();
            foreach($tmp_order_vendors as $v){
                $order_vendors[$v->id] = $v;
            }
        }

        $order->weight = $order->getWeightItems();
        $order_history = $order->getHistory();
        $lists['status'] = JHTML::_('select.genericlist', $orders->getAllOrderStatus(),'order_status','class = "inputbox" size = "1" id = "order_status"','status_id','name', $order->order_status);        
        
        $country = JSFactory::getTable('country', 'jshop');
        $country->load($order->country);
        $field_country_name = $lang->get("name");
        $order->country = $country->$field_country_name;
        
        $d_country = JSFactory::getTable('country', 'jshop');
        $d_country->load($order->d_country);
        $field_country_name = $lang->get("name");
        $order->d_country = $d_country->$field_country_name;
        
        $order->title = $jshopConfig->user_field_title[$order->title];
        $order->d_title = $jshopConfig->user_field_title[$order->d_title];
		
		$order->birthday = getDisplayDate($order->birthday, $jshopConfig->field_birthday_format);
        $order->d_birthday = getDisplayDate($order->d_birthday, $jshopConfig->field_birthday_format);
        
        $jshopConfig->user_field_client_type[0]="";
        $order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];
        
        $order->order_tax_list = $order->getTaxExt();
        
        if ($order->coupon_id){
            $coupon = JSFactory::getTable('coupon', 'jshop'); 
            $coupon->load($order->coupon_id);
            $order->coupon_code = $coupon->coupon_code;
        }
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        
        $display_info_only_product = 0;
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) $display_info_only_product = 1; 
        }
        
        $display_block_change_order_status = $order->order_created;        
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) $display_block_change_order_status = 0;
            foreach($order_items as $k=>$v){
                if ($v->vendor_id!=$id_vendor_cuser){
                    unset($order_items[$k]);
                }
            }
        }
        
        $order->delivery_time_name = '';
        $order->delivery_date_f = '';
        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = JSFactory::getAllDeliveryTime();
            $order->delivery_time_name = $deliverytimes[$order->delivery_times_id];
            if ($order->delivery_time_name==""){
                $order->delivery_time_name = $order->delivery_time;
            }
        }
        if ($jshopConfig->show_delivery_date && !datenull($order->delivery_date)){
            $order->delivery_date_f = formatdate($order->delivery_date);
        }
		
		$stat_download = $order->getFilesStatDownloads(1);
        
        if ($jshopConfig->order_display_new_digital_products){
            $product = JSFactory::getTable('product', 'jshop');
            foreach($order_items as $k=>$v){
                $product->product_id = $v->product_id;
                $product->setAttributeActive(unserialize($v->attributes));
                $files = $product->getSaleFiles();
                $order_items[$k]->files = serialize($files);
            }
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayOrderAdmin', array(&$order, &$order_items, &$order_history) );        
        
        $print = JRequest::getInt("print");
        
        $view=$this->getView("orders", 'html');
        $view->setLayout("show");
        $view->assign('config', $jshopConfig); 
        $view->assign('order', $order); 
        $view->assign('order_history', $order_history); 
        $view->assign('order_items', $order_items); 
        $view->assign('lists', $lists); 
        $view->assign('print', $print);
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('display_info_only_product', $display_info_only_product);
        $view->assign('current_vendor_id', $id_vendor_cuser);
        $view->assign('display_block_change_order_status', $display_block_change_order_status);
        $view->_tmp_ext_discount = '';
        $view->_tmp_ext_shipping_package = '';
		$view->assign('stat_download', $stat_download);
        if ($jshopConfig->admin_show_vendors){ 
            $view->assign('order_vendors', $order_vendors);
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeShowOrder', array(&$view));
        $view->displayShow();
    }

    function printOrder(){
        JRequest::setVar("print", 1);
        $this->show();
    }
    
    function update_one_status(){
        $this->_updateStatus(JRequest::getVar('order_id'),JRequest::getVar('order_status'),JRequest::getVar('status_id'),JRequest::getVar('notify',0),JRequest::getVar('comments',''),JRequest::getVar('include',''),1);
    }
    
    function update_status(){
        $this->_updateStatus(JRequest::getVar('order_id'),JRequest::getVar('order_status'),JRequest::getVar('status_id'),JRequest::getVar('notify',0),JRequest::getVar('comments',''),JRequest::getVar('include',''),0);        
    }    
    
    function _updateStatus($order_id, $order_status, $status_id, $notify, $comments, $include, $view_order){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $client_id = JRequest::getInt('client_id',0);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeChangeOrderStatusAdmin', array(&$order_id, &$order_status, &$status_id, &$notify, &$comments, &$include, &$view_order));
        
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);        
        
        JSFactory::loadLanguageFile($order->getLang());
        $prev_order_status = $order->order_status;
        $order->order_status = $order_status;
        $order->order_m_date = getJsDate();
        $order->store();
        
        $vendorinfo = $order->getVendorInfo();
        
        if ($jshopConfig->order_stock_removed_only_paid_status){
            $product_stock_removed = (in_array($order_status, $jshopConfig->payment_status_enable_download_sale_file));
        }else{
            $product_stock_removed = (!in_array($order_status, $jshopConfig->payment_status_return_product_in_stock));
        }
        
        if (!$product_stock_removed && $order->product_stock_removed==1){
            $order->changeProductQTYinStock("+");            
        }
        
        if ($product_stock_removed && $order->product_stock_removed==0){
            $order->changeProductQTYinStock("-");            
        }
        
        $order_history = JSFactory::getTable('orderHistory', 'jshop');
        $order_history->order_id = $order_id;
        $order_history->order_status_id = $order_status;
        $order_history->status_date_added = getJsDate();
        $order_history->customer_notify = $notify;
        $order_history->comments = $comments;
        $order_history->store();

        if ($jshopConfig->admin_show_vendors){
            $listVendors = $order->getVendors();
        }else{
            $listVendors = array();
        }

        $vendors_send_message = ($jshopConfig->vendor_order_message_type==1 || ($order->vendor_type==1 && $jshopConfig->vendor_order_message_type==2));
        $vendor_send_order = ($jshopConfig->vendor_order_message_type==2 && $order->vendor_type == 0 && $order->vendor_id);
        if ($jshopConfig->vendor_order_message_type==3) $vendor_send_order = 1;
        $admin_send_order = 1;
        if ($jshopConfig->admin_not_send_email_order_vendor_order && $vendor_send_order && count($listVendors)) $admin_send_order = 0;

        $lang = JSFactory::getLang($order->getLang());
        $new_status = JSFactory::getTable('orderStatus', 'jshop'); 
        $new_status->load($order_status);
        $comments = ($include)?($comments):('');
        $name = $lang->get('name');
        
        $shop_item_id = getShopMainPageItemid();
        $juri = JURI::getInstance();
        $liveurlhost = $juri->toString( array("scheme",'host', 'port'));
        $app = JApplication::getInstance('site');
        $router = $app->getRouter();
        $uri = $router->build('index.php?option=com_jshopping&controller=user&task=order&order_id='.$order_id."&Itemid=".$shop_item_id);
        $url = $uri->toString();
        $order_details_url = $liveurlhost.str_replace('/administrator', '', $url);
        if ($order->user_id==-1){
            $order_details_url = '';
        }

        $mailfrom = $mainframe->getCfg('mailfrom');
        $fromname = $mainframe->getCfg('fromname');
        
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');
        $checkout = JSFactory::getModel('checkout', 'jshop');
        $message = $checkout->getMessageChangeStatusOrder($order, $new_status->$name, $vendorinfo, $order_details_url, $comments);

        //message client
        if ($notify){
			$ishtml = false;
            $subject = sprintf(_JSHOP_ORDER_STATUS_CHANGE_SUBJECT, $order->order_number);
			$dispatcher->trigger('onBeforeSendClientMailOrderStatus', array(&$message, &$order, &$comments, &$new_status, &$vendorinfo, &$order_details_url, &$ishtml, &$mailfrom, &$fromname, &$subject));
            $mailer = JFactory::getMailer();
            $mailer->setSender(array($mailfrom, $fromname));
            $mailer->addRecipient($order->email);
            $mailer->setSubject($subject);
            $mailer->setBody($message);
            $mailer->isHTML($ishtml);
            $send = $mailer->Send();
        }
        
        //message vendors
        if ($vendors_send_message || $vendor_send_order){
            $subject = sprintf(_JSHOP_ORDER_STATUS_CHANGE_SUBJECT, $order->order_number);
            foreach($listVendors as $k=>$datavendor){
				$ishtml = false;
				$dispatcher->trigger('onBeforeSendVendorMailOrderStatus', array(&$message, &$order, &$comments, &$new_status, &$vendorinfo, &$order_details_url, &$ishtml, &$mailfrom, &$fromname, &$subject, &$datavendor));
                $mailer = JFactory::getMailer();
                $mailer->setSender(array($mailfrom, $fromname));
                $mailer->addRecipient($datavendor->email);
                $mailer->setSubject($subject);
                $mailer->setBody($message);
                $mailer->isHTML($ishtml);
                $send = $mailer->Send();
            }
        }

        JSFactory::loadAdminLanguageFile();
        
        $dispatcher->trigger( 'onAfterChangeOrderStatusAdmin', array(&$order_id, &$order_status, &$status_id, &$notify, &$comments, &$include, &$view_order) );
        
        if ($view_order)
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id, _JSHOP_ORDER_STATUS_CHANGED);
        else
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id, _JSHOP_ORDER_STATUS_CHANGED);
    }
    
    function finish(){
		$jshopConfig = JSFactory::getConfig();
        $order_id = JRequest::getInt("order_id");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $order->order_created = 1;
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeAdminFinishOrder', array(&$order));
        $order->store();
        
        JSFactory::loadLanguageFile($order->getLang());
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');
        $checkout = JSFactory::getModel('checkout', 'jshop');
        if ($jshopConfig->send_order_email){
            $checkout->sendOrderEmail($order_id, 1);
        }
        
        JSFactory::loadAdminLanguageFile();
        $this->setRedirect("index.php?option=com_jshopping&controller=orders", _JSHOP_ORDER_FINISHED);
    }

    function remove(){
        $client_id = JRequest::getInt('client_id',0);
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $tmp = array();
        
        $dispatcher = JDispatcher::getInstance();
        
        $dispatcher->trigger( 'onBeforeRemoveOrder', array(&$cid) );
        
        if (count($cid)){
            foreach ($cid as $key=>$value){
                $query = "DELETE FROM `#__jshopping_orders` WHERE `order_id` = '" . $db->escape($value) . "'";
                $db->setQuery($query);
                if ($db->query()){
                    $query = "DELETE FROM `#__jshopping_order_item` WHERE `order_id` = '" . $db->escape($value) . "'";
                    $db->setQuery($query);
                    $db->query();
                    $query = "DELETE FROM `#__jshopping_order_history` WHERE `order_id` = '" . $db->escape($value) . "'";
                    $db->setQuery($query);
                    $db->query();
                    $tmp[] = $value;
                }
            }
                        
            $dispatcher->trigger( 'onAfterRemoveOrder', array(&$cid) );
        }
        if (count($tmp)){
            $text = sprintf(_JSHOP_ORDER_DELETED_ID, implode(",",$tmp));
        }else{
            $text = "";
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id, $text);
    }
    
    function edit(){
        $mainframe = JFactory::getApplication();
        $order_id = JRequest::getVar("order_id");
        $client_id = JRequest::getInt('client_id',0);
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $orders = JSFactory::getModel("orders");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $name = $lang->get("name");
        
        $id_vendor_cuser = getIdVendorForCUser();
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) {
                $mainframe->redirect('index.php', JText::_('ALERTNOTAUTH'));
                return 0;
            }
        }

        $order_items = $order->getAllItems();
        
        $_languages = JSFactory::getModel("languages");
        $languages = $_languages->getAllLanguages(1);        
        
        $select_language = JHTML::_('select.genericlist', $languages, 'lang', 'class = "inputbox" style="float:none"','language', 'name', $order->lang);
        
        $country = JSFactory::getTable('country', 'jshop');
        $countries = $country->getAllCountries();
        $select_countries = JHTML::_('select.genericlist', $countries, 'country', 'class = "inputbox"','country_id', 'name', $order->country );
        $select_d_countries = JHTML::_('select.genericlist', $countries, 'd_country', 'class = "inputbox"','country_id', 'name', $order->d_country);
        
		$option_title = array();
        foreach($jshopConfig->user_field_title as $key=>$value){
            if ($key>0) $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name');
        }    
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name', $order->title);
        $select_d_titles = JHTML::_('select.genericlist', $option_title,'d_title','class = "inputbox endes"','title_id','title_name', $order->d_title);
        
		$order->birthday = getDisplayDate($order->birthday, $jshopConfig->field_birthday_format);
        $order->d_birthday = getDisplayDate($order->d_birthday, $jshopConfig->field_birthday_format);
		
        $client_types = array(); 
        foreach ($jshopConfig->user_field_client_type as $key => $value) {
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name', $order->client_type);

        $jshopConfig->user_field_client_type[0]="";
        if (isset($jshopConfig->user_field_client_type[$order->client_type])){
            $order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];
        }else{
            $order->client_type_name = '';
        }
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        
        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
        $order->payment_name = $pm_method->$name;
        
        $order->order_tax_list = $order->getTaxExt();
        
        $_currency = JSFactory::getModel("currencies");
        $currency_list = $_currency->getAllCurrencies();
        $order_currency = 0;
        foreach($currency_list as $k=>$v){
            if ($v->currency_code_iso==$order->currency_code_iso) $order_currency = $v->currency_id;
        }
        $select_currency = JHTML::_('select.genericlist', $currency_list, 'currency_id','class = "inputbox"','currency_id','currency_code', $order_currency);
        
        $display_price_list = array();
        $display_price_list[] = JHTML::_('select.option', 0, _JSHOP_PRODUCT_BRUTTO_PRICE, 'id', 'name');
        $display_price_list[] = JHTML::_('select.option', 1, _JSHOP_PRODUCT_NETTO_PRICE, 'id', 'name');
        $display_price_select = JHTML::_('select.genericlist', $display_price_list, 'display_price', 'onchange="updateOrderTotalValue();"', 'id', 'name', $order->display_price);
        
        $shippings = JSFactory::getModel("shippings");
        $shippings_list = $shippings->getAllShippings(0);
        $shippings_select = JHTML::_('select.genericlist', $shippings_list, 'shipping_method_id', '', 'shipping_id', 'name', $order->shipping_method_id);
        
        $payments = JSFactory::getModel("payments");
        $payments_list = $payments->getAllPaymentMethods(0);
        $payments_select = JHTML::_('select.genericlist', $payments_list, 'payment_method_id', '', 'payment_id', 'name', $order->payment_method_id);
        
        $deliverytimes = JSFactory::getAllDeliveryTime();
        $first=array(0=>"- - -");
		$delivery_time_select = JHTML::_('select.genericlist', array_merge($first,$deliverytimes), 'order_delivery_times_id', '', 'id', 'name', $order->delivery_times_id);
        
        $users = JSFactory::getModel('users');
        $users_list = $users->getUsers();
        $first = array(0=>'- - -');
        $users_list_select = JHTML::_('select.genericlist', array_merge($first,$users_list), 'user_id', 'onchange="updateBillingShippingForUser(this.value);"', 'user_id', 'name', $order->user_id);

        filterHTMLSafe($order);
        foreach($order_items as $k=>$v){
            JFilterOutput::objectHTMLSafe($order_items[$k]);
        }

		JHTML::_('behavior.calendar');
        $view=$this->getView("orders", 'html');
        $view->setLayout("edit");
        $view->assign('config', $jshopConfig); 
        $view->assign('order', $order);  
        $view->assign('order_items', $order_items); 
        $view->assign('config_fields', $config_fields);
        $view->assign('etemplatevar', '');
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('order_id',$order_id);
        $view->assign('select_countries', $select_countries);
        $view->assign('select_d_countries', $select_d_countries);
		$view->assign('select_titles', $select_titles);
        $view->assign('select_d_titles', $select_d_titles);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('select_currency', $select_currency);
        $view->assign('display_price_select', $display_price_select);
        $view->assign('shippings_select', $shippings_select);
        $view->assign('payments_select', $payments_select);
        $view->assign('select_language', $select_language);
        $view->assign('delivery_time_select', $delivery_time_select);
        $view->assign('users_list_select', $users_list_select);
        $view->assign('client_id', $client_id);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditOrders', array(&$view));
        $view->displayEdit();
    }

    function save(){
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $post = JRequest::get('post');
        $client_id = JRequest::getInt('client_id',0);        
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;
        
        $dispatcher = JDispatcher::getInstance();
        
        $order_id = intval($post['order_id']);
        $orders = JSFactory::getModel("orders");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        if (!$order_id){
            $order->user_id = -1;
            $order->order_date = getJsDate();
            $orderNumber = $jshopConfig->next_order_number;
            $jshopConfig->updateNextOrderNumber();
            $order->order_number = $order->formatOrderNumber($orderNumber);
            $order->order_hash = md5(time().$order->order_total.$order->user_id);
            $order->file_hash = md5(time().$order->order_total.$order->user_id."hashfile");
            $order->ip_address = $_SERVER['REMOTE_ADDR'];
            $order->order_status = $jshopConfig->default_status_order;
        }
		$order->order_m_date = getJsDate();
        $order_created_prev = $order->order_created;
        if ($post['birthday']) $post['birthday'] = getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        if ($post['d_birthday']) $post['d_birthday'] = getJsDateDB($post['d_birthday'], $jshopConfig->field_birthday_format);
		if ($post['invoice_date']) $post['invoice_date'] = getJsDateDB($post['invoice_date'], $jshopConfig->store_date_format);
        
        if (!$jshopConfig->hide_tax){
            $post['order_tax'] = 0;
            $order_tax_ext = array();
            if (isset($post['tax_percent'])){
                foreach($post['tax_percent'] as $k=>$v){
                    if ($post['tax_percent'][$k]!="" || $post['tax_value'][$k]!=""){
                        $order_tax_ext[number_format($post['tax_percent'][$k],2)] = $post['tax_value'][$k];
                    }
                }
            }
            $post['order_tax_ext'] = serialize($order_tax_ext);
            $post['order_tax'] = number_format(array_sum($order_tax_ext),2);
        }
        
        $currency = JSFactory::getTable('currency', 'jshop');
        $currency->load($post['currency_id']);
        $post['currency_code'] = $currency->currency_code;
        $post['currency_code_iso'] = $currency->currency_code_iso;
        $post['currency_exchange'] = $currency->currency_value;

        $dispatcher->trigger('onBeforeSaveOrder', array(&$post, &$file_generete_pdf_order));

        $order->bind($post);
		$order->delivery_times_id = $post['order_delivery_times_id'];
        $order->store();
        $order_id = $order->order_id;
        $order_items = $order->getAllItems();
        $orders->saveOrderItem($order_id, $post, $order_items);
        
        $order->items = null;
        $vendor_id = $order->getVendorIdForItems();        
        $order->vendor_id = $vendor_id;
        $order->store();
        
        JSFactory::loadLanguageFile($order->getLang());

        if ($jshopConfig->order_send_pdf_client || $jshopConfig->order_send_pdf_admin){
            $order->load($order_id);
            $order->items = null;
            $order->products = $order->getAllItems();
            JSFactory::loadLanguageFile($order->getLang());
            $lang = JSFactory::getLang($order->getLang());
            
            $order->order_date = strftime($jshopConfig->store_date_format, strtotime($order->order_date));
            $order->order_tax_list = $order->getTaxExt();
            $country = JSFactory::getTable('country', 'jshop');
            $country->load($order->country);
            $field_country_name = $lang->get("name");
            $order->country = $country->$field_country_name;
            
            $d_country = JSFactory::getTable('country', 'jshop');
            $d_country->load($order->d_country);
            $field_country_name = $lang->get("name");
            $order->d_country = $d_country->$field_country_name;

            $shippingMethod = JSFactory::getTable('shippingMethod', 'jshop');
            $shippingMethod->load($order->shipping_method_id);
            
            $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
            $pm_method->load($order->payment_method_id);
            
            $name = $lang->get("name");
            $description = $lang->get("description");
            $order->shipping_information = $shippingMethod->$name;
            $order->payment_name = $pm_method->$name;
            $order->payment_information = $order->payment_params;
			
            if ($jshopConfig->order_send_pdf_client || $jshopConfig->order_send_pdf_admin){
                include_once($file_generete_pdf_order);
                $order->pdf_file = generatePdf($order);
                $order->insertPDF();
            }
        }
        
        if ($order->order_created==1 && $order_created_prev==0){
            $order->items = null;
            JSFactory::loadLanguageFile($order->getLang());
            JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');
            $checkout = JSFactory::getModel('checkout', 'jshop');
            if ($jshopConfig->send_order_email){
                $checkout->sendOrderEmail($order_id, 1);
            }    
        }
        
        JSFactory::loadAdminLanguageFile();
        $dispatcher->trigger('onAfterSaveOrder', array(&$order, &$file_generete_pdf_order) );
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id);
    }
    
	function stat_file_download_clear(){        
        $order_id = JRequest::getInt("order_id");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $order->file_stat_downloads = '';
        $order->store();
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id);
    }
    
    function send(){
        $order_id = JRequest::getInt("order_id");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        JSFactory::loadLanguageFile($order->getLang());
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jshopping/models');
        $checkout = JSFactory::getModel('checkout', 'jshop');
        $checkout->sendOrderEmail($order_id, 1);
        JSFactory::loadAdminLanguageFile();
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id, _JSHOP_MAIL_HAS_BEEN_SENT);
    }
    
    function transactions(){
        $order_id = JRequest::getInt("order_id");
        $jshopConfig = JSFactory::getConfig();
        
        $orders = JSFactory::getModel("orders");
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $rows = $order->getListTransactions();
        
        $_list_order_status = $orders->getAllOrderStatus();
        $list_order_status = array();
        foreach($_list_order_status as $v){
            $list_order_status[$v->status_id] = $v->name;
        }
        
        $view = $this->getView("orders", 'html');
        $view->setLayout("transactions");
        $view->assign('config', $jshopConfig); 
        $view->assign('order', $order);
        $view->assign('rows', $rows);
        $view->assign('list_order_status', $list_order_status);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeShowOrderTransactions', array(&$view));
        $view->displayTrx();   
    }
    
    function cancel(){
        $client_id = JRequest::getInt('client_id',0);
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id);
    }
}
?>