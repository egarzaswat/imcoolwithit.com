<?php
/**
* @version      4.9.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class jshopCart{
    
    var $type_cart = "cart"; //cart,wishlist
    var $products = array();
    var $count_product = 0;
    var $price_product = 0;
    var $summ = 0;
    var $rabatt_id = 0;
    var $rabatt_value = 0;
    var $rabatt_type = 0;
    var $rabatt_summ = 0;
    
    function __construct(){
        JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshopCart', array(&$this));
    }

    function load($type_cart = "cart"){
        $jshopConfig = JSFactory::getConfig();
        $this->type_cart = $type_cart;

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeCartLoad', array(&$this));

        $session = JFactory::getSession();
        $objcart = $session->get($this->type_cart);

        if (isset($objcart) && $objcart!=''){
            $temp_cart = unserialize($objcart);
            $this->products = $temp_cart->products;
            $this->rabatt_id = $temp_cart->rabatt_id;
            $this->rabatt_value = $temp_cart->rabatt_value;
            $this->rabatt_type = $temp_cart->rabatt_type;
            $this->rabatt_summ = $temp_cart->rabatt_summ;
        }
        
        if (isset($_COOKIE['jshopping_temp_cart']) && $this->type_cart=='wishlist' && !count($this->products)){
            $_tempcart = JSFactory::getModel('tempcart', 'jshop');
            $products = $_tempcart->getTempCart($_COOKIE['jshopping_temp_cart'], $this->type_cart);
            if (count($products)){
                $this->products = $products;
                $this->saveToSession();
            }
        }
        
        $this->loadPriceAndCountProducts();
        if ($jshopConfig->use_extend_tax_rule){
            $this->updateTaxForProducts();
            $this->saveToSession();
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterCartLoad', array(&$this));
    }

    function loadPriceAndCountProducts(){
        $jshopConfig = JSFactory::getConfig();
        $this->price_product = 0;
        $this->price_product_brutto = 0;
        $this->count_product = 0;
        if (count($this->products)){
            foreach($this->products as $prod){
                $this->price_product += $prod['price'] * $prod['quantity'];
                if ($jshopConfig->display_price_front_current==1){
                    $this->price_product_brutto += ($prod['price']*(1+$prod['tax']/100)) * $prod['quantity'];
                }else{
                    $this->price_product_brutto += $prod['price'] * $prod['quantity'];
                }
                $this->count_product += $prod['quantity'];
            }
        }
        JDispatcher::getInstance()->trigger('onAfterLoadPriceAndCountProducts', array(&$this));
    }

    function getPriceProducts(){
        return $this->price_product;
    }

    function getPriceBruttoProducts(){
        return $this->price_product_brutto;
    }

    function getCountProduct(){
        return $this->count_product;
    }

    function updateTaxForProducts(){
        if (count($this->products)){
            $taxes = JSFactory::getAllTaxes();
            foreach ($this->products as $k=>$prod) {
                $this->products[$k]['tax'] = $taxes[$prod['tax_id']];
            }
        }
    }

    /**
    * get cart summ price
    * @param mixed $incShiping - include price shipping
    * @param mixed $incRabatt - include discount
    * @param mixed $incPayment - include price payment
    */
    function getSum( $incShiping = 0, $incRabatt = 0, $incPayment = 0 ) {
        $jshopConfig = JSFactory::getConfig();
        
        $this->summ = $this->price_product;
        
        if ($jshopConfig->display_price_front_current==1){
            $this->summ = $this->summ + $this->getTax($incShiping, $incRabatt, $incPayment);
        }

        if ($incShiping){
            $this->summ = $this->summ + $this->getShippingPrice();
            $this->summ = $this->summ + $this->getPackagePrice();
        }
        
        if ($incPayment){
            $price_payment = $this->getPaymentPrice();
            $this->summ = $this->summ + $price_payment;
        }
        
        if ($incRabatt){
            $this->summ = $this->summ - $this->getDiscountShow();
            if ($this->summ < 0) $this->summ = 0;
        }
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterCartGetSum', array(&$this, &$incShiping, &$incRabatt, &$incPayment));
        return $this->summ;
    }

    function getDiscountShow(){
        $summForCalculeDiscount = $this->getSummForCalculeDiscount();
        if ($this->rabatt_summ > $summForCalculeDiscount){
            return $summForCalculeDiscount;
        }else{
            return $this->rabatt_summ;
        }
    }

    function getFreeDiscount(){
        $summForCalculeDiscount = $this->getSummForCalculeDiscount();
        if ($this->rabatt_summ > $summForCalculeDiscount){
            return $this->rabatt_summ - $summForCalculeDiscount;
        }else{
            return 0;
        }
    }    

    function getTax($incShiping = 0, $incRabatt = 0, $incPayment = 0){
        $taxes = $this->getTaxExt($incShiping, $incRabatt, $incPayment);
        $tax_summ = array_sum($taxes);
    return $tax_summ;
    }

    function getTaxExt($incShiping = 0, $incRabatt = 0, $incPayment = 0){
        $jshopConfig = JSFactory::getConfig();
        $tax_summ = array();
        foreach($this->products as $key=>$value){
            if ($value['tax']!=0){
                if (!isset($tax_summ[$value['tax']])) $tax_summ[$value['tax']] = 0;
                $tax_summ[$value['tax']] += $value['quantity'] * getPriceTaxValue($value['price'], $value['tax'], $jshopConfig->display_price_front_current);                
            }
        }

        if ($incShiping){
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
            $lst = $this->getPackageTaxList();
            foreach($lst as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
        }

        if ($incPayment){
            $lpt = $this->getPaymentTaxList();
            foreach($lpt as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
        }
        
        if ($incRabatt && $jshopConfig->calcule_tax_after_discount && $this->rabatt_summ>0){
            $tax_summ = $this->getTaxExtCalcAfterDiscount($incShiping, $incPayment);
        }
        
		$dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterCartGetTaxExt', array(&$this, &$tax_summ, &$incShiping, &$incRabatt, $incPayment));
        return $tax_summ;
    }

    function getTaxExtCalcAfterDiscount($incShiping = 0, $incPayment = 0){
        $jshopConfig = JSFactory::getConfig();
        $summ = array();
        foreach($this->products as $key=>$value){
            $summ[$value['tax']] += $value['quantity'] * $value['price'];
        }

        if ($jshopConfig->discount_use_full_sum){
            if ($incShiping && $this->display_item_shipping){
                $lspt = $this->getShippingPriceForTaxes();
                foreach($lspt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
                $lspt = $this->getPackagePriceForTaxes();
                foreach($lspt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
            }
            
            if ($incPayment && $this->display_item_payment){
                $lppt = $this->getPaymentPriceForTaxes();
                foreach($lppt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
            }
        }

        $allsum = array_sum($summ);
        $discountsum = $this->getDiscountShow();

        $calc_taxes = array();
        foreach($summ as $tax=>$val){
            $percent = $val / $allsum;
            $pwd = $val - ($discountsum * $percent);
            if ($pwd<0) $pwd = 0;
            if ($jshopConfig->display_price_front_current==1){
                $calc_taxes[$tax] = $pwd*$tax/100;
            }else{
                $calc_taxes[$tax] = $pwd*$tax/(100+$tax);
            }
        }

        if (!$jshopConfig->discount_use_full_sum){
            if ($incShiping && $this->display_item_shipping){
                $lst = $this->getShippingTaxList();
                foreach($lst as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
                $lst = $this->getPackageTaxList();
                foreach($lst as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
            }

            if ($incPayment && $this->display_item_payment){
                $lpt = $this->getPaymentTaxList();
                foreach($lpt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
            }
        }

        return $calc_taxes;
    }

    function setDisplayFreeAttributes(){
        $jshopConfig = JSFactory::getConfig();
        if (count($this->products)){
            if ($jshopConfig->admin_show_freeattributes){
                $_freeattributes = JSFactory::getTable('freeattribut', 'jshop');
                $namesfreeattributes = $_freeattributes->getAllNames();
            }
            foreach ($this->products as $k=>$prod){
                if ($jshopConfig->admin_show_freeattributes){
                    $freeattributes = unserialize($prod['freeattributes']);
                    if (!is_array($freeattributes)) $freeattributes = array();
                    $free_attributes_value = array();
                    foreach($freeattributes as $id=>$text){
                        $obj = new stdClass();
                        $obj->attr = $namesfreeattributes[$id];
                        $obj->value = $text;
                        $free_attributes_value[] = $obj;
                    }
                    $this->products[$k]['free_attributes_value'] = $free_attributes_value;
                }else{
                    $this->products[$k]['free_attributes_value'] = array();
                }
            }
        }
    }

    function setDisplayItem($shipping = 0, $payment = 0){
        $this->display_item_shipping = $shipping;
        $this->display_item_payment = $payment;
    }
    
    function setShippingsDatas($prices, $shipping_method_price){
        $this->setShippingPrice($prices['shipping']);
        $this->setShippingTaxId($shipping_method_price->shipping_tax_id);
        $this->setShippingTaxList($shipping_method_price->calculateShippingTaxList($prices['shipping'], $this));
        $this->setShippingPriceForTaxes($shipping_method_price->getShipingPriceForTaxes($prices['shipping'], $this));
        $this->setPackagePrice($prices['package']);
        $this->setPackageTaxId($shipping_method_price->package_tax_id);
        $this->setPackageTaxList($shipping_method_price->calculatePackageTaxList($prices['package'], $this));
        $this->setPackagePriceForTaxes($shipping_method_price->getPackegePriceForTaxes($prices['package'], $this));
    }

    function setShippingId($val){
        $session = JFactory::getSession();
        $session->set("shipping_method_id", $val);
    }

    function getShippingId() {
        $session = JFactory::getSession();
        return $session->get("shipping_method_id");
    }
    
    function setShippingPrId($val){
        $session = JFactory::getSession();
        $session->set("sh_pr_method_id", $val);
    }

    function getShippingPrId() {
        $session = JFactory::getSession();
        return $session->get("sh_pr_method_id");
    }

    function setShippingPrice($price){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping", $price);
    }
    function getShippingPrice() {
        $session = JFactory::getSession();
        $price = $session->get("jshop_price_shipping");
        return floatval($price);
    }
    
    function setPackagePrice($price){
        $session = JFactory::getSession();
        $session->set("jshop_price_package", $price);
    }
    function getPackagePrice() {
        $session = JFactory::getSession();
        $price = $session->get("jshop_price_package");
        return floatval($price);
    }

    //deprecated
    function setShippingPriceTax($price){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping_tax", $price);
    }

    function getShippingPriceTax() {
        $session = JFactory::getSession();
        $price = $session->get("jshop_price_shipping_tax");
        return floatval($price);
    }

    //deprecated
    function setShippingPriceTaxPercent($price){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping_tax_percent", $price);
    }

    function getShippingPriceTaxPercent(){
        $stl = $this->getShippingTaxList();
        if (is_array($stl) && count($stl)==1){
            $tmp = array_keys($stl);
            return $tmp[0];
        }else{
            return 0;
        }
    }
    
    function setShippingTaxId($id){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping_tax_id", $id);
    }
    function getShippingTaxId(){
        $session = JFactory::getSession();
        return $session->get("jshop_price_shipping_tax_id");
    }
    
    function setPackageTaxId($id){
        $session = JFactory::getSession();
        $session->set("jshop_price_package_tax_id", $id);
    }
    function getPackageTaxId(){
        $session = JFactory::getSession();
        return $session->get("jshop_price_package_tax_id");
    }
    
    function setShippingTaxList($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping_tax_list", $list);
    }
    function getShippingTaxList(){
        $session = JFactory::getSession();
        return (array)$session->get("jshop_price_shipping_tax_list");
    }
    
    function setPackageTaxList($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_package_tax_list", $list);
    }
    function getPackageTaxList(){
        $session = JFactory::getSession();
        return (array)$session->get("jshop_price_package_tax_list");
    }
    
    function setShippingPriceForTaxes($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_shipping_for_tax_list", $list);
    }
    function getShippingPriceForTaxes(){
        $session = JFactory::getSession();
        return $session->get("jshop_price_shipping_for_tax_list");
    }
    
    function setPackagePriceForTaxes($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_package_for_tax_list", $list);
    }
    function getPackagePriceForTaxes(){
        $session = JFactory::getSession();
        return $session->get("jshop_price_package_for_tax_list");
    }

    function getShippingNettoPrice(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->display_price_front_current==1){
            return $this->getShippingPrice();
        }else{
            $price = $this->getShippingPrice();
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                $price -= $value;
            }
            return $price;
        }
    }

    function getShippingBruttoPrice(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->display_price_front_current==1){
            $price = $this->getShippingPrice();
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getShippingPrice();
        }
    }
    
    function getPackageBruttoPrice(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->display_price_front_current==1){
            $price = $this->getPackagePrice();
            $lst = $this->getPackageTaxList();
            foreach($lst as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getPackagePrice();
        }
    }
    
    function setShippingParams($val){
        $session = JFactory::getSession();
        $session->set("shipping_params", $val);
    }

    function getShippingParams(){
        $session = JFactory::getSession();
        $val = $session->get("shipping_params");
        return $val;
    }

    function setPaymentId($val){
        $session = JFactory::getSession();
        $session->set("payment_method_id", $val);
    }

    function getPaymentId(){
        $session = JFactory::getSession();
        return intval($session->get("payment_method_id"));
    }

    function setPaymentPrice($val){
        $session = JFactory::getSession();
        $session->set("jshop_payment_price", $val);
    }

    function getPaymentPrice(){
        $session = JFactory::getSession();
        $price = $session->get("jshop_payment_price");
        return floatval($price);
    }
    
    function setPaymentDatas($price, $payment){
        $this->setPaymentPrice($price);
        $this->setPaymentTaxList($payment->calculateTaxList($price));
        $this->setPaymentPriceForTaxes($payment->getPriceForTaxes($price));
    }

    function getPaymentBruttoPrice(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->display_price_front_current==1){
            $price = $this->getPaymentPrice();
            $lpt = $this->getPaymentTaxList();
            foreach($lpt as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getPaymentPrice();
        }
        
    }
    
    function setPaymentTaxList($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_payment_tax_list", $list);
    }
    function getPaymentTaxList(){
        $session = JFactory::getSession();
        return (array)$session->get("jshop_price_payment_tax_list");
    }
    
    function setPaymentPriceForTaxes($list){
        $session = JFactory::getSession();
        $session->set("jshop_price_payment_for_tax_list", $list);
    }
    function getPaymentPriceForTaxes(){
        $session = JFactory::getSession();
        return $session->get("jshop_price_payment_for_tax_list");
    }
    
    //deprecated
    function setPaymentTax($val){
        $session = JFactory::getSession();
        $session->set("jshop_payment_tax", $val);
    }
    
    function getPaymentTax(){
        $session = JFactory::getSession();
        $price = $session->get("jshop_payment_tax");
        return $price;
    }
    
    //deprecated
    function setPaymentTaxPercent($val){
        $session = JFactory::getSession();
        $session->set("jshop_payment_tax_percent", $val);
    }

    function getPaymentTaxPercent(){
        $ptl = $this->getPaymentTaxList();
        if (is_array($ptl) && count($ptl)==1){
            $tmp = array_keys($ptl);
            return $tmp[0];
        }else{
            return 0;
        }
    }

    function setPaymentParams($val){
        $session = JFactory::getSession();
        $session->set("pm_params", $val);
    }

    function getPaymentParams(){
        $session = JFactory::getSession();
        $val = $session->get("pm_params");
        return $val;
    }    

    function getCouponId(){
        return $this->rabatt_id;
    }
    
    function setDeliveryDate($date){
        $session = JFactory::getSession();
        $session->set("jshop_delivery_date", $date);
    }
    function getDeliveryDate(){
        $session = JFactory::getSession();
    return $session->get("jshop_delivery_date");
    }

    function updateCartProductPrice() {
		$jshopConfig = JSFactory::getConfig();
        foreach($this->products as $key=>$value) {
            $product = JSFactory::getTable('product', 'jshop');
            $product->load($this->products[$key]['product_id']);
            $attr_id = unserialize($value['attributes']);
            $freeattributes = unserialize($value['freeattributes']);
            $product->setAttributeActive($attr_id);
            $product->setFreeAttributeActive($freeattributes);            
            $this->products[$key]['price'] = $product->getPrice($this->products[$key]['quantity'], 1, 1, 1, $this->products[$key]);
			if ($jshopConfig->cart_basic_price_show){
                $this->products[$key]['basicprice'] = $product->getBasicPrice();
            }
        }
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterUpdateCartProductPrice', array(&$this));
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
    }

    function add($product_id, $quantity, $attr_id, $freeattributes, $additional_fields = array(), $usetriggers = 1, &$errors = array(), $displayErrorMessage = 1){
        $jshopConfig = JSFactory::getConfig();
        if ($quantity <= 0){
            $errors['100'] = 'Error quantity';
			if ($displayErrorMessage){
                JError::raiseNotice(100, $errors['100']);
            }
            return 0;
        }
        $updateqty = 1;

        if ($usetriggers){
            $dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onBeforeAddProductToCart', array(&$this, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$updateqty, &$errors, &$displayErrorMessage, &$additional_fields, &$usetriggers));
        }

        $attr_serialize = serialize($attr_id);
        $free_attr_serialize = serialize($freeattributes);

        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);

        //check attributes
        if ( (count($product->getRequireAttribute()) > count($attr_id)) || in_array(0, $attr_id)){
            $errors['101'] = _JSHOP_SELECT_PRODUCT_OPTIONS;
            if ($displayErrorMessage){
                JError::raiseNotice(101, $errors['101']);
            }
            return 0;
        }

        //check free attributes
        if ($jshopConfig->admin_show_freeattributes){
            $allfreeattributes = $product->getListFreeAttributes();
            $error = 0;
            foreach($allfreeattributes as $k=>$v){
                if ($v->required && trim($freeattributes[$v->id])==""){
                    $error = 1;
                    $errors['102_'.$v->id] = sprintf(_JSHOP_PLEASE_ENTER_X, $v->name);
                    if ($displayErrorMessage){
                        JError::raiseNotice(102, $errors['102_'.$v->id]);
                    }
                }
            }
            if ($error){
                return 0;
            }
        }

		$product->setAttributeActive($attr_id);
        $product->setFreeAttributeActive($freeattributes);
        $qtyInStock = $product->getQtyInStock();
        $pidCheckQtyValue = $product->getPIDCheckQtyValue();

        $new_product = 1;
        if ($updateqty){
        foreach ($this->products as $key => $value){
            if ($value['product_id'] == $product_id && $value['attributes'] == $attr_serialize && $value['freeattributes']==$free_attr_serialize){
                $product_in_cart = $this->products[$key]['quantity'];
                $save_quantity = $product_in_cart + $quantity;

                $sum_quantity = $save_quantity;
                foreach ($this->products as $key2 => $value2){
                    if ($key==$key2) continue;
                    if ($value2['pid_check_qty_value'] == $pidCheckQtyValue){
                        $sum_quantity += $value2["quantity"];
                        $product_in_cart += $value2["quantity"];
                    }
                }

                if ($jshopConfig->max_count_order_one_product && $sum_quantity > $jshopConfig->max_count_order_one_product){
                    $errors['103'] = sprintf(_JSHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->max_count_order_one_product);
                    if ($displayErrorMessage){
                        JError::raiseNotice(103, $errors['103']);
                    }
                    return 0;
                }
                if ($jshopConfig->min_count_order_one_product && $sum_quantity < $jshopConfig->min_count_order_one_product){
                    $errors['104'] = sprintf(_JSHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->min_count_order_one_product);
                    if ($displayErrorMessage){
                        JError::raiseNotice(104, $errors['104']);
                    }
                    return 0;
                }

                if (!$product->unlimited && $jshopConfig->controler_buy_qty && ($sum_quantity > $qtyInStock)){
                    $balans = $qtyInStock - $product_in_cart;
                    if ($balans < 0) $balans = 0;
                    $errors['105'] = sprintf(_JSHOP_ERROR_EXIST_QTY_PRODUCT_IN_CART, $this->products[$key]['quantity'], $balans);
                    if ($displayErrorMessage){
                        JError::raiseWarning(105, $errors['105']);
                    }
                    return 0;
                }

                $this->products[$key]['quantity'] = $save_quantity;                
                $this->products[$key]['price'] = $product->getPrice($this->products[$key]['quantity'], 1, 1, 1, $this->products[$key]);
				if ($jshopConfig->cart_basic_price_show){
                    $this->products[$key]['basicprice'] = $product->getBasicPrice();
                }
				
                if ($usetriggers){
					$dispatcher->trigger('onBeforeSaveUpdateProductToCart', array(&$this, &$product, $key, &$errors, &$displayErrorMessage, &$product_in_cart, &$quantity));
                }

                $new_product = 0;
                break;
            }
        }
        }

        if ($new_product){
            $product_in_cart = 0;
            foreach ($this->products as $key2 => $value2){
                if ($value2['pid_check_qty_value'] == $pidCheckQtyValue){
                    $product_in_cart += $value2["quantity"];
                }
            }
            $sum_quantity = $product_in_cart + $quantity;

            if ($jshopConfig->max_count_order_one_product && $sum_quantity > $jshopConfig->max_count_order_one_product){
                $errors['106'] = sprintf(_JSHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->max_count_order_one_product);
                if ($displayErrorMessage){
                    JError::raiseNotice(106, $errors['106']);
                }
                return 0;
            }
            if ($jshopConfig->min_count_order_one_product && $sum_quantity < $jshopConfig->min_count_order_one_product){
                $errors['107'] = sprintf(_JSHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->min_count_order_one_product);
                if ($displayErrorMessage){
                    JError::raiseNotice(107, $errors['107']);
                }
                return 0;
            }

            if (!$product->unlimited && $jshopConfig->controler_buy_qty && ($sum_quantity > $qtyInStock)){
                $balans = $qtyInStock - $product_in_cart;
                if ($balans < 0) $balans = 0;
                $errors['108'] = sprintf(_JSHOP_ERROR_EXIST_QTY_PRODUCT, $balans);
                if ($displayErrorMessage){
                    JError::raiseWarning(108, $errors['108']);
                }
                return 0;
            }

            $product->getDescription();
            $temp_product['quantity'] = $quantity;
            $temp_product['product_id'] = $product_id;
            $temp_product['category_id'] = $product->getCategory();
            $temp_product['tax'] = $product->getTax();
            $temp_product['tax_id'] = $product->product_tax_id;
            $temp_product['product_name'] = $product->name;
            $temp_product['thumb_image'] = getPatchProductImage($product->getData('image'), '', 3);
            $temp_product['delivery_times_id'] = $product->getDeliveryTimeId();
            $temp_product['ean'] = $product->getEan();
            $temp_product['attributes'] = $attr_serialize;
            $temp_product['attributes_value'] = array();
            $temp_product['extra_fields'] = array();
            $temp_product['weight'] = $product->getWeight();
            $temp_product['vendor_id'] = fixRealVendorId($product->vendor_id);
            $temp_product['files'] = serialize($product->getSaleFiles());
            $temp_product['freeattributes'] = $free_attr_serialize;
            if ($jshopConfig->show_manufacturer_in_cart){
                $manufacturer_info = $product->getManufacturerInfo();
                $temp_product['manufacturer'] = $manufacturer_info->name;
            }else{
                $temp_product['manufacturer'] = '';
            }
            $temp_product['pid_check_qty_value'] = $pidCheckQtyValue;
            $i = 0;
            if (is_array($attr_id) && count($attr_id)){
                foreach($attr_id as $key=>$value){
                    $attr = JSFactory::getTable('attribut', 'jshop');
                    $attr_v = JSFactory::getTable('attributvalue', 'jshop');
                    $temp_product['attributes_value'][$i] = new stdClass();
					$temp_product['attributes_value'][$i]->attr_id = $key;
					$temp_product['attributes_value'][$i]->value_id = $value;
                    $temp_product['attributes_value'][$i]->attr = $attr->getName($key);
                    $temp_product['attributes_value'][$i]->value = $attr_v->getName($value);
                    $i++;
                }
            }
            
            if ($jshopConfig->admin_show_product_extra_field && count($jshopConfig->getCartDisplayExtraFields())>0){
                $extra_field = $product->getExtraFields(2);                
                $temp_product['extra_fields'] = $extra_field;
            }

			foreach($additional_fields as $k=>$v){
                if ($k!='after_price_calc'){
                    $temp_product[$k] = $v;
                }
            }
            
            if ($usetriggers){
                $dispatcher->trigger('onBeforeSaveNewProductToCartBPC', array(&$this, &$temp_product, &$product, &$errors, &$displayErrorMessage));
            }

            $temp_product['price'] = $product->getPrice($quantity, 1, 1, 1, $temp_product);
			if ($jshopConfig->cart_basic_price_show){
                $temp_product['basicprice'] = $product->getBasicPrice();
                $temp_product['basicpriceunit'] = $product->getBasicPriceUnit();
            }
			
			if (is_array($additional_fields['after_price_calc'])){
                foreach($additional_fields['after_price_calc'] as $k=>$v){
                    $temp_product[$k] = $v;
                }
            }
			
            if ($usetriggers){
                $dispatcher->trigger('onBeforeSaveNewProductToCart', array(&$this, &$temp_product, &$product, &$errors, &$displayErrorMessage));
            }
            $this->products[] = $temp_product;
        }

        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
        if ($usetriggers){
            $dispatcher->trigger('onAfterAddProductToCart', array(&$this, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$errors, &$displayErrorMessage) );
        }
        return 1;
    }

    function refresh($quantity){
        $jshopConfig = JSFactory::getConfig();

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeRefreshProductInCart', array(&$quantity, &$this));
                
        if (is_array($quantity) && count($quantity)){
            $lang = JSFactory::getLang();
            $name = $lang->get('name');
            foreach($quantity as $key=>$value){
                if ($jshopConfig->use_decimal_qty){
                    $value = floatval(str_replace(",",".",$value));
                    $value = round($value, $jshopConfig->cart_decimal_qty_precision);
                }else{
                    $value = intval($value);
                }
                if ($value < 0) $value = 0;
                $product = JSFactory::getTable('product', 'jshop');
                $product->load($this->products[$key]['product_id']);
                $attr = unserialize($this->products[$key]['attributes']);
                $free_attr = unserialize($this->products[$key]['freeattributes']);
                $product->setAttributeActive($attr);
                $product->setFreeAttributeActive($free_attr);
                $qtyInStock = $product->getQtyInStock();
                $checkqty = $value;
				$dispatcher->trigger('onRefreshProductInCartForeach', array(&$this, &$quantity, &$key, &$product, &$attr, &$free_attr, &$qtyInStock, &$checkqty, &$value));

                foreach($this->products as $key2 => $value2){
                    if ($key2!=$key && $value2['pid_check_qty_value']==$this->products[$key]['pid_check_qty_value']){
                        $checkqty += $value2["quantity"];
                    }
                }
                
                if ($jshopConfig->max_count_order_one_product && ($checkqty > $jshopConfig->max_count_order_one_product)){
                    JError::raiseNotice(111, sprintf(_JSHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->max_count_order_one_product));
                    return 0;
                }
                if ($jshopConfig->min_count_order_one_product && ($checkqty < $jshopConfig->min_count_order_one_product)){
                    JError::raiseNotice(112, sprintf(_JSHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $jshopConfig->min_count_order_one_product));
                    return 0;
                }
                if (!$product->unlimited && $jshopConfig->controler_buy_qty && ($checkqty > $qtyInStock)){
                    JError::raiseWarning(113, sprintf(_JSHOP_ERROR_EXIST_QTY_PRODUCT_BASKET, $product->$name, $qtyInStock));
                    continue;
                }
   
                $this->products[$key]['price'] = $product->getPrice($value, 1, 1, 1, $this->products[$key]);
				if ($jshopConfig->cart_basic_price_show){
                    $this->products[$key]['basicprice'] = $product->getBasicPrice();
                }
                $this->products[$key]['quantity'] = $value;
                if ($this->products[$key]['quantity'] == 0){
                    unset($this->products[$key]);
                }
                unset($product);
            }
        }
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
        $dispatcher->trigger('onAfterRefreshProductInCart', array(&$quantity, &$this));
        return 1;
    }
    
    function checkListProductsQtyInStore(){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeCheckListProductsQtyInStore', array(&$this));
        $lang = JSFactory::getLang();
        $name = $lang->get('name');
        $check = 1;
        
        foreach($this->products as $key=>$value){
			if ($value['pid_check_qty_value']=='nocheck') continue;
            $product = JSFactory::getTable('product', 'jshop');
            $product->load($this->products[$key]['product_id']);
            $attr = unserialize($this->products[$key]['attributes']);
            $product->setAttributeActive($attr);
            $qtyInStock = $product->getQtyInStock();
            $checkqty = $value["quantity"];
			$dispatcher->trigger('onCheckListProductsQtyInStoreForeach', array(&$this, &$key, &$product, &$attr, &$qtyInStock, &$checkqty));

            foreach($this->products as $key2=>$value2){
                if ($key2!=$key && $value2['pid_check_qty_value']==$this->products[$key]['pid_check_qty_value']){
                    $checkqty += $value2["quantity"];
                }
            }
            
            if (!$product->unlimited && $jshopConfig->controler_buy_qty && ($checkqty > $qtyInStock)){
                $check = 0;
                JError::raiseWarning('', sprintf(_JSHOP_ERROR_EXIST_QTY_PRODUCT_BASKET, $product->$name, $qtyInStock));
                continue;
            }
        }
        $dispatcher->trigger('onAfterCheckListProductsQtyInStore', array(&$this));
    return $check;
    }
    
    function checkCoupon(){
        if (!$this->getCouponId()){
            return 1;
        }
        $coupon = JSFactory::getTable('coupon', 'jshop');
        $coupon->load($this->getCouponId());
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeCheckCouponStep5save', array(&$this, &$coupon));
		
        if (!$coupon->coupon_publish || $coupon->used || ($coupon->type == 1 && $coupon->coupon_value < $this->rabatt_value)){
            return 0;
        }else{
            return 1;
        }
    }

    function getWeightProducts(){
        $weight_sum = 0;
        foreach ($this->products as $prod) {
            $weight_sum += $prod['weight'] * $prod['quantity'];
        }
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onGetWeightCartProducts', array(&$this, &$weight_sum));
        return $weight_sum;
    }

    function setRabatt($id, $type, $value) {
        $this->rabatt_id = $id;
        $this->rabatt_type = $type;
        $this->rabatt_value = $value;
        $this->reloadRabatValue();
        $this->saveToSession();
    }
    
    function getSummForCalculePlusPayment(){
        $jshopConfig = JSFactory::getConfig();
        $sum = $this->getPriceBruttoProducts();
        if ($this->display_item_shipping){
            $sum += $this->getShippingBruttoPrice();
            $sum += $this->getPackageBruttoPrice();
        }
        return $sum;
    }
    
    function getSummForCalculeDiscount(){
        $jshopConfig = JSFactory::getConfig();
        $sum = $this->getPriceProducts();
        if ($jshopConfig->discount_use_full_sum && $jshopConfig->display_price_front_current==1){
            $sum = $this->getPriceBruttoProducts();
        }
        if ($jshopConfig->discount_use_full_sum){
            if ($this->display_item_shipping) {
                $sum += $this->getShippingBruttoPrice();
                $sum += $this->getPackageBruttoPrice();
            }
            if ($this->display_item_payment) $sum += $this->getPaymentBruttoPrice();
        }
        return $sum;
    }
    
    function reloadRabatValue(){
        $jshopConfig = JSFactory::getConfig();
        if ($this->rabatt_type == 1){
            $this->rabatt_summ = $this->rabatt_value * $jshopConfig->currency_value; //value
        } else {
            $this->rabatt_summ = $this->rabatt_value / 100 * $this->getSummForCalculeDiscount(); //percent
        }
        $this->rabatt_summ = round($this->rabatt_summ, 2);
    }

    function updateDiscountData(){
        $this->reloadRabatValue();
        $this->saveToSession();
    }

    function addLinkToProducts($show_delete = 0, $type="cart") {
        $dispatcher = JDispatcher::getInstance();
        foreach($this->products as $key=>$value){
            $this->products[$key]['href'] = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$this->products[$key]['category_id'].'&product_id='.$value['product_id'], 1);
            if ($show_delete){
                $this->products[$key]['href_delete'] = SEFLink('index.php?option=com_jshopping&controller='.$type.'&task=delete&number_id='.$key);
            }
            if ($type=="wishlist"){
                $this->products[$key]['remove_to_cart'] = SEFLink('index.php?option=com_jshopping&controller='.$type.'&task=remove_to_cart&number_id='.$key);
            }
        }
        $dispatcher->trigger('onAfterAddLinkToProductsCart', array(&$this, &$show_delete, &$type));
    }
    
    /**
    * get vendor type
    * return (1 - multi vendors, 0 - single vendor)
    */
    function getVendorType(){
        $vendors = array();
        foreach ($this->products as $key => $value){
            $vendors[] = $value['vendor_id'];
        }
        $vendors = array_unique($vendors);
        if (count($vendors)>1){
            return 1;
        }else{
            return 0;
        }
    }
    
    /**
    * get id vendor
    * reutnr (-1) - if type == multivendors
    */
    function getVendorId(){
        $vendors = array();
        foreach ($this->products as $key => $value){
            $vendors[] = $value['vendor_id'];
        }
        $vendors = array_unique($vendors);
        if (count($vendors)==0){
            return 0;
        }elseif (count($vendors)>1){
            return -1;
        }else{
            return $vendors[0];
        }
    }
    
    function getDelivery(){
        $deliverytimes = JSFactory::getAllDeliveryTime();
        $deliverytimesdays = JSFactory::getAllDeliveryTimeDays();
        $min_id = 0;
        $max_id = 0;
        $min_days = 0;
        $max_days = 0;
        foreach($this->products as $prod){
            if ($prod['delivery_times_id']){
                if ($min_days==0){
                    $min_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $min_id = $prod['delivery_times_id'];
                }
                if ($deliverytimesdays[$prod['delivery_times_id']]<$min_days){
                    $min_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $min_id = $prod['delivery_times_id'];
                }
                if ($deliverytimesdays[$prod['delivery_times_id']]>$max_days){
                    $max_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $max_id = $prod['delivery_times_id'];
                }
            }
        }
        if ($min_id==$max_id){
            $delivery = $deliverytimes[$min_id];
        }else{
            $delivery = $deliverytimes[$min_id]." - ".$deliverytimes[$max_id];
        }
    return $delivery;
    }
    
    function getDeliveryDaysProducts(){
        $deliverytimes = JSFactory::getAllDeliveryTime();
        $deliverytimesdays = JSFactory::getAllDeliveryTimeDays();
        $day = 0;
        foreach($this->products as $prod){
            if ($prod['delivery_times_id']){
                if ($deliverytimesdays[$prod['delivery_times_id']]>$day){
                    $day = $deliverytimesdays[$prod['delivery_times_id']];
                }
            }
        }
    return $day;
    }
    
    function getReturnPolicy(){
        $products = array();
        foreach($this->products as $v){
            $products[] = $v['product_id'];
        }
        $products = array_unique($products);
        $statictext = JSFactory::getTable("statictext","jshop");
        $rows = $statictext->getReturnPolicyForProducts($products);
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterCartGetReturnPolicy', array(&$this, &$rows));
    return $rows;
    }
    
    function clear(){
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeClearCart', array(&$this));
        $session = JFactory::getSession();
        $this->products = array();
        $this->rabatt = 0;
        $this->rabatt_value = 0;
        $this->rabatt_type = 0;
        $this->rabatt_summ = 0;
        $this->summ = 0;
        $this->count_product = 0;        
        $this->price_product = 0;        
        $session->set($this->type_cart, "");
        $session->set("pm_method", "");
        $session->set("pm_params", "");
        $session->set("payment_method_id", "");
        $session->set("shipping_method_id", "");
        $session->set("jshop_price_shipping", "");
		$session->set('checkcoupon', 0);
    }

    function delete($number_id){
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDeleteProductInCart', array(&$number_id, &$this) );

        unset($this->products[$number_id]);
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();

        $dispatcher->trigger('onAfterDeleteProductInCart', array(&$number_id, &$this) );
    }

    function saveToSession(){
        $session = JFactory::getSession();
        $session->set($this->type_cart, serialize($this));        
        $_tempcart = JSFactory::getModel('tempcart', 'jshop');
        $_tempcart->insertTempCart($this);
        JDispatcher::getInstance()->trigger('onAfterSaveToSessionCart', array(&$this));        
    }

}
?>