<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCart extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerCart', array(&$this));
    }
    
    function display($cachable = false, $urlparams = false){
        $this->view();
    }

    function add(){
        header("Cache-Control: no-cache, must-revalidate");
        $jshopConfig = JSFactory::getConfig(); 
        if ($jshopConfig->user_as_catalog || !getDisplayPriceShop()) return 0;

        $ajax = JRequest::getInt('ajax');
        $product_id = JRequest::getInt('product_id');
        $category_id = JRequest::getInt('category_id');
        if ($jshopConfig->use_decimal_qty){
            $quantity = floatval(str_replace(",",".",JRequest::getVar('quantity',1)));
            $quantity = round($quantity, $jshopConfig->cart_decimal_qty_precision);
        }else{
            $quantity = JRequest::getInt('quantity',1);
        }
        $to = JRequest::getVar('to',"cart");
        if ($to!="cart" && $to!="wishlist") $to = "cart"; 

        $jshop_attr_id = JRequest::getVar('jshop_attr_id');
        if (!is_array($jshop_attr_id)) $jshop_attr_id = array();
        foreach($jshop_attr_id as $k=>$v) $jshop_attr_id[intval($k)] = intval($v);

        $freeattribut = JRequest::getVar("freeattribut");
        if (!is_array($freeattribut)) $freeattribut = array();
        
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load($to);        
        if (!$cart->add($product_id, $quantity, $jshop_attr_id, $freeattribut)){
            if ($ajax){
                print getMessageJson();
                die();
            }
            $session =JFactory::getSession();
            $back_value = array('pid'=>$product_id, 'attr'=>$jshop_attr_id, 'freeattr'=>$freeattribut,'qty'=>$quantity);
            $session->set('product_back_value', $back_value);
            $this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category_id.'&product_id='.$product_id,1,1));
            return 0;
        }

        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }

        if ($jshopConfig->not_redirect_in_cart_after_buy){
            if ($to=="wishlist"){
                $message = _JSHOP_ADDED_TO_WISHLIST;
            }else{
                $message = _JSHOP_ADDED_TO_CART;
            }
            $this->setRedirect($_SERVER['HTTP_REFERER'], $message);
            return 1;
        }

        if ($to=="wishlist")
            $this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=wishlist&task=view',1,1));
        else
            $this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=cart&task=view',0,1));
    }

    function view(){
	    $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->user_as_catalog) return 0;
		$db = JFactory::getDBO();
        $session = JFactory::getSession();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $ajax = JRequest::getInt('ajax');

		$cart = JSFactory::getModel('cart', 'jshop');
		$cart->load();
		$cart->addLinkToProducts(1);
        $cart->setDisplayFreeAttributes();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("cart");
        if (getThisURLMainPageShop()){
            $document = JFactory::getDocument();
            appendPathWay(_JSHOP_CART);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_CART;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{            
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }

        $shopurl = SEFLink('index.php?option=com_jshopping&controller=category',1);
        if ($jshopConfig->cart_back_to_shop=="product"){
            $endpagebuyproduct = xhtmlUrl($session->get('jshop_end_page_buy_product'));
        }elseif ($jshopConfig->cart_back_to_shop=="list"){
            $endpagebuyproduct =  xhtmlUrl($session->get('jshop_end_page_list_product'));
        }
        if (isset($endpagebuyproduct) && $endpagebuyproduct){
            $shopurl = $endpagebuyproduct;
        }

		$statictext = JSFactory::getTable("statictext","jshop");
        $tmp = $statictext->loadData("cart");
        $cartdescr = $tmp->text;

        
        $weight_product = $cart->getWeightProducts();
        if ($weight_product==0 && $jshopConfig->hide_weight_in_cart_weight0){
            $jshopConfig->show_weight_order = 0;
        }
        
        if ($jshopConfig->shop_user_guest==1){
            $href_checkout = SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2&check_login=1',1, 0, $jshopConfig->use_ssl);
        }else{
            $href_checkout = SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1, 0, $jshopConfig->use_ssl);
        }
        
        $tax_list = $cart->getTaxExt(0, 1);
        $show_percent_tax = 0;
        if (count($tax_list)>1 || $jshopConfig->show_tax_in_product) $show_percent_tax = 1;
        if ($jshopConfig->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if (($jshopConfig->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ) $hide_subtotal = 1;
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayCart', array(&$cart));

        $view_name = "cart";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("cart");

        $view->assign('config', $jshopConfig);
		$view->assign('products', $cart->products);
		$view->assign('summ', $cart->getPriceProducts());
		$view->assign('image_product_path', $jshopConfig->image_product_live_path);
		$view->assign('image_path', $jshopConfig->live_path);
        $view->assign('no_image', $jshopConfig->noimage);
		$view->assign('href_shop', $shopurl);
        $view->assign('href_checkout', $href_checkout);
        $view->assign('discount', $cart->getDiscountShow());
		$view->assign('free_discount', $cart->getFreeDiscount());
		$view->assign('use_rabatt', $jshopConfig->use_rabatt_code);
		$view->assign('tax_list', $cart->getTaxExt(0, 1));
        $view->assign('fullsumm', $cart->getSum(0, 1));
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('weight', $weight_product);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
		$view->assign('cartdescr', $cartdescr);
        $dispatcher->trigger('onBeforeDisplayCartView', array(&$view));
		$view->display();
        if ($ajax) die();
    }

    function delete(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = JRequest::getInt('ajax');
        $number_id = JRequest::getInt('number_id');
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load();    
        $cart->delete($number_id);
        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }
        $this->setRedirect( SEFLink('index.php?option=com_jshopping&controller=cart&task=view',0,1) );
    }

    function refresh(){
        $ajax = JRequest::getInt('ajax');
        $quantitys = JRequest::getVar('quantity');
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load();
        $cart->refresh($quantitys);
        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }
        $this->setRedirect( SEFLink('index.php?option=com_jshopping&controller=cart&task=view',0,1) );
    }

    function discountsave(){
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onLoadDiscountSave', array() );
        
        $ajax = JRequest::getInt('ajax');
        $coupon = JSFactory::getTable('coupon', 'jshop');
        $code = JRequest::getVar('rabatt');

        if ($coupon->getEnableCode($code)){
            $cart = JSFactory::getModel('cart', 'jshop');
            $cart->load();
            $dispatcher->trigger('onBeforeDiscountSave', array(&$coupon, &$cart) );
            $cart->setRabatt($coupon->coupon_id, $coupon->coupon_type, $coupon->coupon_value);
            $dispatcher->trigger('onAfterDiscountSave', array(&$coupon, &$cart) );
            if ($ajax){
                print getOkMessageJson($cart);
                die();
            }
        }else{
            JError::raiseWarning('', $coupon->error);
            if ($ajax){
                print getMessageJson();
                die();
            }
        }
        $this->setRedirect( SEFLink('index.php?option=com_jshopping&controller=cart&task=view',0,1) );
    }
}
?>