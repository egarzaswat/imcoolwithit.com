<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
include_once(JPATH_SITE."/components/com_jshopping/payments/payment.php");
include_once(JPATH_SITE."/components/com_jshopping/shippingform/shippingform.php");

class jshopCheckout{
    
    function __construct(){
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshopCheckout', array(&$this));
    }
    
    function sendOrderEmail($order_id, $manuallysend = 0){
        $mainframe = JFactory::getApplication();
        $lang = JSFactory::getLang();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $order = JSFactory::getTable('order', 'jshop');
        $jshopConfig->user_field_title[0] = '';
        $jshopConfig->user_field_client_type[0] = '';
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;

        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');

        $order->load($order_id);

        $status = JSFactory::getTable('orderStatus', 'jshop');
        $status->load($order->order_status);
        $name = $lang->get("name");
        $order->status = $status->$name;
        $order->order_date = strftime($jshopConfig->store_date_format, strtotime($order->order_date));
        $order->products = $order->getAllItems();
        $order->weight = $order->getWeightItems();
        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = JSFactory::getAllDeliveryTime();
            if (isset($deliverytimes[$order->delivery_times_id])){
                $order->order_delivery_time = $deliverytimes[$order->delivery_times_id];
            }else{
                $order->order_delivery_time = '';
            }
            if ($order->order_delivery_time==""){
                $order->order_delivery_time = $order->delivery_time;
            }
        }
        $order->order_tax_list = $order->getTaxExt();
        $show_percent_tax = 0;        
        if (count($order->order_tax_list)>1 || $jshopConfig->show_tax_in_product) $show_percent_tax = 1;        
        if ($jshopConfig->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if (($jshopConfig->hide_tax || count($order->order_tax_list)==0) && $order->order_discount==0 && $jshopConfig->without_shipping && $order->order_payment==0) $hide_subtotal = 1;
        
        if ($order->weight==0 && $jshopConfig->hide_weight_in_cart_weight0){
            $jshopConfig->show_weight_order = 0;
        }
        
        $country = JSFactory::getTable('country', 'jshop');
        $country->load($order->country);
        $field_country_name = $lang->get("name");
        $order->country = $country->$field_country_name;        
        
        $d_country = JSFactory::getTable('country', 'jshop');
        $d_country->load($order->d_country);
        $field_country_name = $lang->get("name");
        $order->d_country = $d_country->$field_country_name;
        if ($jshopConfig->show_delivery_date && !datenull($order->delivery_date)){
            $order->delivery_date_f = formatdate($order->delivery_date);
        }else{
            $order->delivery_date_f = '';
        }
        
        $order->title = $jshopConfig->user_field_title[$order->title];
        $order->d_title = $jshopConfig->user_field_title[$order->d_title];
		$order->birthday = getDisplayDate($order->birthday, $jshopConfig->field_birthday_format);
        $order->d_birthday = getDisplayDate($order->d_birthday, $jshopConfig->field_birthday_format);
		$order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];
		
        $shippingMethod = JSFactory::getTable('shippingMethod', 'jshop');
        $shippingMethod->load($order->shipping_method_id);
        
        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
		$paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        
        $name = $lang->get("name");
        $description = $lang->get("description");
        $order->shipping_information = $shippingMethod->$name;
        $shippingForm = $shippingMethod->getShippingForm();
        if ($shippingForm){
            $shippingForm->prepareParamsDispayMail($order, $shippingMethod);
        }
        $order->payment_name = $pm_method->$name;
        $order->payment_information = $order->payment_params;
		if ($payment_system){
            $payment_system->prepareParamsDispayMail($order, $pm_method);
        }
        if ($pm_method->show_descr_in_email) $order->payment_description = $pm_method->$description;  else $order->payment_description = "";

        $statictext = JSFactory::getTable("statictext","jshop");
        $rowstatictext = $statictext->loadData("order_email_descr");        
        $order_email_descr = $rowstatictext->text;
        $order_email_descr = str_replace("{name}",$order->f_name, $order_email_descr);
        $order_email_descr = str_replace("{family}",$order->l_name, $order_email_descr);
        $order_email_descr = str_replace("{email}",$order->email, $order_email_descr);
        $order_email_descr = str_replace("{title}",$order->title, $order_email_descr);
		
        $rowstatictext = $statictext->loadData("order_email_descr_end");
        $order_email_descr_end = $rowstatictext->text;
        $order_email_descr_end = str_replace("{name}",$order->f_name, $order_email_descr_end);
        $order_email_descr_end = str_replace("{family}",$order->l_name, $order_email_descr_end);
        $order_email_descr_end = str_replace("{email}",$order->email, $order_email_descr_end);
		$order_email_descr_end = str_replace("{title}",$order->title, $order_email_descr_end);
        if ($jshopConfig->show_return_policy_text_in_email_order){
            $list = $order->getReturnPolicy();
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $rptext = implode('<div class="return_policy_space"></div>', $listtext);
            $order_email_descr_end = $rptext.$order_email_descr_end;
        }

        $text_total = _JSHOP_ENDTOTAL;
        if (($jshopConfig->show_tax_in_product || $jshopConfig->show_tax_product_in_cart) && (count($order->order_tax_list)>0)){
            $text_total = _JSHOP_ENDTOTAL_INKL_TAX;
        }
        
        $uri = JURI::getInstance();
        $liveurlhost = $uri->toString(array("scheme",'host', 'port'));
        
        if ($jshopConfig->admin_show_vendors){
            $listVendors = $order->getVendors();
        }else{
            $listVendors = array();
        }

        $vendors_send_message = $jshopConfig->vendor_order_message_type==1;
        $vendor_send_order = $jshopConfig->vendor_order_message_type==2;
        $vendor_send_order_admin = (($jshopConfig->vendor_order_message_type==2 && $order->vendor_type == 0 && $order->vendor_id) || $jshopConfig->vendor_order_message_type==3);
        if ($vendor_send_order_admin) $vendor_send_order = 0;
        $admin_send_order = 1;
        if ($jshopConfig->admin_not_send_email_order_vendor_order && $vendor_send_order_admin && count($listVendors)) $admin_send_order = 0;

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSendEmailsOrder', array(&$order, &$listVendors, &$file_generete_pdf_order, &$admin_send_order));
        
        //client message
        include_once(JPATH_COMPONENT_SITE."/views/checkout/view.html.php");
        $view_name = "checkout";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = new JshoppingViewCheckout($view_config);
        $view->setLayout("orderemail");
        $view->assign('client', 1);
        $view->assign('show_customer_info', 1);
        $view->assign('show_weight_order', 1);
        $view->assign('show_total_info', 1);
        $view->assign('show_payment_shipping_info', 1);
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('order_email_descr', $order_email_descr);
        $view->assign('order_email_descr_end', $order_email_descr_end);
        $view->assign('config', $jshopConfig);
        $view->assign('order', $order);
        $view->assign('products', $order->products);
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('text_total',$text_total);
        $view->assign('liveurlhost',$liveurlhost);
        $dispatcher->trigger('onBeforeCreateTemplateOrderMail', array(&$view));
        $message_client = $view->loadTemplate();

        //admin message
        $view_name = "checkout";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = new JshoppingViewCheckout($view_config);
        $view->setLayout("orderemail");
        $view->assign('client', 0);
        $view->assign('show_customer_info', 1);
        $view->assign('show_weight_order', 1);
        $view->assign('show_total_info', 1);
        $view->assign('show_payment_shipping_info', 1);
        $view->assign('config_fields', $config_fields);
        $view->assign('order_email_descr', $order_email_descr);
        $view->assign('order_email_descr_end', $order_email_descr_end);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('config', $jshopConfig);
        $view->assign('order',$order);
        $view->assign('products', $order->products);
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('text_total',$text_total);
        $view->assign('liveurlhost',$liveurlhost);
        $dispatcher->trigger('onBeforeCreateTemplateOrderMail', array(&$view));
        $message_admin = $view->loadTemplate();
        
        //vendors messages or order
        if ($vendors_send_message || $vendor_send_order){
            foreach($listVendors as $k=>$datavendor){
                if ($vendors_send_message){
                    $show_customer_info = 0;
                    $show_weight_order = 0;
                    $show_total_info = 0;
                    $show_payment_shipping_info = 0;
                }
                if ($vendor_send_order){
                    $show_customer_info = 1;
                    $show_weight_order = 0;
                    $show_total_info = 0;
                    $show_payment_shipping_info = 1;
                }
                $vendor_order_items = $order->getVendorItems($datavendor->id);
                $view_name = "checkout";
                $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
                $view = new JshoppingViewCheckout($view_config);
                $view->setLayout("orderemail");
                $view->assign('client', 0);
                $view->assign('show_customer_info', $show_customer_info);
                $view->assign('show_weight_order', $show_weight_order);
                $view->assign('show_total_info', $show_total_info);
                $view->assign('show_payment_shipping_info', $show_payment_shipping_info);
                $view->assign('config_fields', $config_fields);
                $view->assign('count_filed_delivery', $count_filed_delivery);
                $view->assign('order_email_descr', $order_email_descr);
                $view->assign('order_email_descr_end', $order_email_descr_end);
                $view->assign('config', $jshopConfig);
                $view->assign('order', $order);
                $view->assign('products', $vendor_order_items);
                $view->assign('show_percent_tax', $show_percent_tax);
                $view->assign('hide_subtotal', $hide_subtotal);
                $view->assign('noimage',$jshopConfig->noimage);
                $view->assign('text_total',$text_total);
                $view->assign('liveurlhost',$liveurlhost);
                $view->assign('show_customer_info',$vendor_send_order);
                $dispatcher->trigger('onBeforeCreateTemplateOrderPartMail', array(&$view));
                $message_vendor = $view->loadTemplate();
                $listVendors[$k]->message = $message_vendor;
            }
        }
		$pdfsend = 1;
        if ($jshopConfig->send_invoice_manually && !$manuallysend) $pdfsend = 0;
        
        if ($pdfsend && ($jshopConfig->order_send_pdf_client || $jshopConfig->order_send_pdf_admin)){
            include_once($file_generete_pdf_order);
			$order->setInvoiceDate();
            $order->pdf_file = generatePdf($order, $jshopConfig);
            $order->insertPDF();
        }
        
        $mailfrom = $mainframe->getCfg('mailfrom');
        $fromname = $mainframe->getCfg('fromname');
        
        //send mail client
		if ($order->email){
			$mailer = JFactory::getMailer();
			$mailer->setSender(array($mailfrom, $fromname));
			$mailer->addRecipient($order->email);
			$mailer->setSubject( sprintf(_JSHOP_NEW_ORDER, $order->order_number, $order->f_name." ".$order->l_name));
			$mailer->setBody($message_client);
			if ($pdfsend && $jshopConfig->order_send_pdf_client){
				$mailer->addAttachment($jshopConfig->pdf_orders_path."/".$order->pdf_file);
			}
			$mailer->isHTML(true);
            $dispatcher->trigger('onBeforeSendOrderEmailClient', array(&$mailer, &$order, &$manuallysend, &$pdfsend));
			$send = $mailer->Send();
        }
		
        //send mail admin
        if ($admin_send_order){
            $mailer = JFactory::getMailer();
            $mailer->setSender(array($mailfrom, $fromname));
            $mailer->addRecipient(explode(',',$jshopConfig->contact_email));
            $mailer->setSubject( sprintf(_JSHOP_NEW_ORDER, $order->order_number, $order->f_name." ".$order->l_name));
            $mailer->setBody($message_admin);
            if ($pdfsend && $jshopConfig->order_send_pdf_admin){
                $mailer->addAttachment($jshopConfig->pdf_orders_path."/".$order->pdf_file);
            }
            $mailer->isHTML(true);
            $dispatcher->trigger('onBeforeSendOrderEmailAdmin', array(&$mailer, &$order, &$manuallysend, &$pdfsend));
            $send = $mailer->Send();
        }

        //send mail vendors
        if ($vendors_send_message || $vendor_send_order){
            foreach($listVendors as $k=>$vendor){
                $mailer = JFactory::getMailer();
                $mailer->setSender(array($mailfrom, $fromname));
                $mailer->addRecipient($vendor->email);
                $mailer->setSubject( sprintf(_JSHOP_NEW_ORDER_V, $order->order_number, ""));
                $mailer->setBody($vendor->message);
                $mailer->isHTML(true);
                $dispatcher->trigger('onBeforeSendOrderEmailVendor', array(&$mailer, &$order, &$manuallysend, &$pdfsend, &$vendor, &$vendors_send_message, &$vendor_send_order));
                $send = $mailer->Send();
            }
        }

        //vendor send order
        if ($vendor_send_order_admin){
            foreach($listVendors as $k=>$vendor){
                $mailer = JFactory::getMailer();
                $mailer->setSender(array($mailfrom, $fromname));
                $mailer->addRecipient($vendor->email);
                $mailer->setSubject( sprintf(_JSHOP_NEW_ORDER, $order->order_number, $order->f_name." ".$order->l_name));
                $mailer->setBody($message_admin);
                if ($pdfsend && $jshopConfig->order_send_pdf_admin){
                    $mailer->addAttachment($jshopConfig->pdf_orders_path."/".$order->pdf_file);
                }
                $mailer->isHTML(true);
                $dispatcher->trigger('onBeforeSendOrderEmailVendorOrder', array(&$mailer, &$order, &$manuallysend, &$pdfsend, &$vendor, &$vendors_send_message, &$vendor_send_order));
                $send = $mailer->Send();
            }
        }

        $dispatcher->trigger('onAfterSendEmailsOrder', array(&$order));
    }
    
    function changeStatusOrder($order_id, $status, $sendmessage = 1){
        $mainframe = JFactory::getApplication();
        
        $lang = JSFactory::getLang();
        $jshopConfig = JSFactory::getConfig();
        $restext = '';

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeChangeOrderStatus', array(&$order_id, &$status, &$sendmessage, &$restext));
            
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $order->order_status = $status;
        $order->order_m_date = getJsDate();
        $order->store();
        
        $vendorinfo = $order->getVendorInfo();

        $order_status = JSFactory::getTable('orderStatus', 'jshop');
        $order_status->load($status);
        
        if ($jshopConfig->order_stock_removed_only_paid_status){
            $product_stock_removed = (in_array($status, $jshopConfig->payment_status_enable_download_sale_file));
        }else{
            $product_stock_removed = (!in_array($status, $jshopConfig->payment_status_return_product_in_stock));
        }
        
        if ($order->order_created && !$product_stock_removed && $order->product_stock_removed==1){
            $order->changeProductQTYinStock("+");            
        }
        
        if ($order->order_created && $product_stock_removed && $order->product_stock_removed==0){
            $order->changeProductQTYinStock("-");            
        }
        
        $order_history = JSFactory::getTable('orderHistory', 'jshop');
        $order_history->order_id = $order->order_id;
        $order_history->order_status_id = $status;
        $order_history->status_date_added = getJsDate();
        $order_history->customer_notify = 1;
        $order_history->comments = $restext;
        $order_history->store();
        
        $name = $lang->get("name");
        
        $uri = JURI::getInstance();
        $liveurlhost = $uri->toString( array("scheme",'host', 'port'));
        $order_details_url = $liveurlhost.SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$order_id,1);
        if ($order->user_id==-1){
            $order_details_url = '';
        }
        
        $message = $this->getMessageChangeStatusOrder($order, $order_status->$name, $vendorinfo, $order_details_url);

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
         
        $mailfrom = $mainframe->getCfg('mailfrom');
        $fromname = $mainframe->getCfg('fromname');
        
        if ($sendmessage){
            //message client
            $subject = sprintf(_JSHOP_ORDER_STATUS_CHANGE_SUBJECT, $order->order_number);
            $mailer = JFactory::getMailer();
            $mailer->setSender(array($mailfrom, $fromname));
            $mailer->addRecipient($order->email);
            $mailer->setSubject($subject);
            $mailer->setBody($message);
            $mailer->isHTML(false);
            $dispatcher->trigger('onBeforeSendMailChangeOrderStatusClient', array(&$mailer, &$order_id, &$status, &$sendmessage, &$order));
            $send = $mailer->Send();
            
            //message admin
            if ($admin_send_order){
                $mailer = JFactory::getMailer();
                $mailer->setSender(array($mailfrom, $fromname));
                $mailer->addRecipient(explode(',',$jshopConfig->contact_email));
                $mailer->setSubject(_JSHOP_ORDER_STATUS_CHANGE_TITLE);
                $mailer->setBody($message);
                $mailer->isHTML(false);
                $dispatcher->trigger('onBeforeSendMailChangeOrderStatusAdmin', array(&$mailer, &$order_id, &$status, &$sendmessage, &$order));
                $send = $mailer->Send();
            }
            
            //message vendors
            if ($vendors_send_message || $vendor_send_order){
                foreach($listVendors as $k=>$datavendor){
                    $mailer = JFactory::getMailer();
                    $mailer->setSender(array($mailfrom, $fromname));
                    $mailer->addRecipient($datavendor->email);
                    $mailer->setSubject(_JSHOP_ORDER_STATUS_CHANGE_TITLE);
                    $mailer->setBody($message);
                    $mailer->isHTML(false);
                    $dispatcher->trigger('onBeforeSendMailChangeOrderStatusVendor', array(&$mailer, &$order_id, &$status, &$sendmessage, &$order));
                    $send = $mailer->Send();
                }
            }
        }
        $dispatcher->trigger('onAfterChangeOrderStatus', array(&$order_id, &$status, &$sendmessage));
    return 1;
    }
    
    function getMessageChangeStatusOrder($order, $newstatus, $vendorinfo, $order_details_url, $comments=''){
        $jshopConfig = JSFactory::getConfig();
        include_once(JPATH_COMPONENT_SITE."/views/checkout/view.html.php");
        $view_name = "order";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = new JshoppingViewCheckout($view_config);
        $view->setLayout("statusorder");
        $view->assign('order', $order);
        $view->assign('order_status', $newstatus);
        $view->assign('vendorinfo', $vendorinfo);
        $view->assign('order_detail', $order_details_url);
        $view->assign('comment', $comments);
        JDispatcher::getInstance()->trigger('onBeforeCreateMailOrderStatusView', array(&$view));
    return $view->loadTemplate();
    }
    
    function cancelPayOrder($order_id){
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
        $pmconfigs = $pm_method->getConfigs();
        $status = $pmconfigs['transaction_cancel_status'];
        if (!$status) $status = $pmconfigs['transaction_failed_status'];
        if ($order->order_created) $sendmessage = 1; else $sendmessage = 0;
        $this->changeStatusOrder($order_id, $status, $sendmessage);
        JDispatcher::getInstance()->trigger('onAfterCancelPayOrderJshopCheckout', array(&$order_id, $status, $sendmessage));
    }
    
    function setMaxStep($step){
        $session = JFactory::getSession();
        $jhop_max_step = $session->get('jhop_max_step');
        if (!isset($jhop_max_step)) $session->set('jhop_max_step', 2);
        $jhop_max_step = $session->get('jhop_max_step');
        $session->set('jhop_max_step', $step);
        JDispatcher::getInstance()->trigger('onAfterSetMaxStepJshopCheckout', array(&$step));
    }
    
    function checkStep($step){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        
        if ($step<10){
            if (!$jshopConfig->shop_user_guest){
                checkUserLogin();
            }
            
            $cart = JSFactory::getModel('cart', 'jshop');
            $cart->load();

            if ($cart->getCountProduct() == 0){
                $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }

            if ($jshopConfig->min_price_order && ($cart->getPriceProducts() < ($jshopConfig->min_price_order * $jshopConfig->currency_value) )){
                JError::raiseNotice("", sprintf(_JSHOP_ERROR_MIN_SUM_ORDER, formatprice($jshopConfig->min_price_order * $jshopConfig->currency_value)));
                $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }
            
            if ($jshopConfig->max_price_order && ($cart->getPriceProducts() > ($jshopConfig->max_price_order * $jshopConfig->currency_value) )){
                JError::raiseNotice("", sprintf(_JSHOP_ERROR_MAX_SUM_ORDER, formatprice($jshopConfig->max_price_order * $jshopConfig->currency_value)));
                $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                exit();
            }
        }

        if ($step>2){
            $jhop_max_step = $session->get("jhop_max_step");
            if (!$jhop_max_step){
                $session->set('jhop_max_step', 2);
                $jhop_max_step = 2;
            }
            if ($step > $jhop_max_step){
                if ($step==10){
                    $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=cart&task=view',1,1));
                }else{
                    JError::raiseWarning("", _JHOP_ERROR_STEP);
                    $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,1, $jshopConfig->use_ssl));
                }
                exit();
            }
        }
    }
    
    function deleteSession(){
        $session = JFactory::getSession();        
        $session->set('check_params', null);
        $session->set('cart', null);
        $session->set('jhop_max_step', null);        
        $session->set('jshop_price_shipping_tax_percent', null);
        $session->set('jshop_price_shipping', null);
        $session->set('jshop_price_shipping_tax', null);
        $session->set('pm_params', null);
        $session->set('payment_method_id', null);
        $session->set('jshop_payment_price', null);
        $session->set('shipping_method_id', null);
        $session->set('sh_pr_method_id', null);
        $session->set('jshop_price_shipping_tax_percent', null);                
        $session->set('jshop_end_order_id', null);
        $session->set('jshop_send_end_form', null);
        $session->set('show_pay_without_reg', 0);
        $session->set('checkcoupon', 0);
        JDispatcher::getInstance()->trigger('onAfterDeleteDataOrder', array(&$this));
    }
}
?>