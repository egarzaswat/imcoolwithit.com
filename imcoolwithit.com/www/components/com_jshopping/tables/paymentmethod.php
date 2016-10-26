<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopPaymentMethod extends JTableAvto {

    function __construct(&$_db){
        parent::__construct('#__jshopping_payment_method', 'payment_id', $_db);
        JPluginHelper::importPlugin('jshoppingcheckout');
    }
    
    function loadFromClass($class){
        $db = JFactory::getDBO();
        $query = "SELECT payment_id FROM `#__jshopping_payment_method` WHERE payment_class='".$db->escape($class)."'";
        extract(js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = $db->loadResult();
        return $this->load($id);
    }

    function getAllPaymentMethods($publish = 1, $shipping_id = 0){
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $query_where = ($publish)?("WHERE payment_publish = '1'"):("");
        $lang = JSFactory::getLang();
        $query = "SELECT payment_id, `".$lang->get("name")."` as name, `".$lang->get("description")."` as description , payment_code, payment_class, scriptname, payment_publish, payment_ordering, payment_params, payment_type, price, price_type, tax_id, image FROM `#__jshopping_payment_method` $query_where ORDER BY payment_ordering";
        extract(js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($shipping_id && $jshopConfig->step_4_3){
            $sh = JSFactory::getTable('shippingMethod', 'jshop');            
            $sh->load($shipping_id);
            $payments = $sh->getPayments();
            if (count($payments)>0){
                foreach($rows as $k=>$v){
                    if (!in_array($v->payment_id, $payments)) unset($rows[$k]);
                }
                $rows = array_values($rows);
            }
        }
    return $rows;
    }

    /**
    * get id payment for payment_class
    */
    function getId(){
        $db = JFactory::getDBO();
        $query = "SELECT payment_id FROM `#__jshopping_payment_method` WHERE payment_class = '".$db->escape($this->class)."'";
        extract(js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCart(&$cart){
        $this->_cart = $cart;
    }
    
    function getCart(){
        return $this->_cart;
    }
    
    function getPrice(){
        $jshopConfig = JSFactory::getConfig();
        if ($this->price_type==2){
            $cart = $this->getCart();
            $price = $cart->getSummForCalculePlusPayment() * $this->price / 100;
            if ($jshopConfig->display_price_front_current){
                $price = getPriceCalcParamsTax($price, $this->tax_id, $cart->products);
            }
        }else{
            $cart = $this->getCart();
            $price = $this->price * $jshopConfig->currency_value; 
            $price = getPriceCalcParamsTax($price, $this->tax_id, $cart->products);
        }
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterGetPricePaymant', array(&$this, &$price));        
        return $price;
    }
    
    function getTax(){        
        $taxes = JSFactory::getAllTaxes();        
        return $taxes[$this->tax_id];
    }
    
    function calculateTax(){
        $jshopConfig = JSFactory::getConfig();
        $price = $this->getPrice();
        $pricetax = getPriceTaxValue($price, $this->getTax(), $jshopConfig->display_price_front_current);
        return $pricetax;
    }
    
    function getPriceForTaxes($price){
        if ($this->tax_id==-1){
            $cart = $this->getCart();
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTax()] = $price;
        }
    return $prices;
    }
    
    function calculateTaxList($price){
        $cart = $this->getCart();
        $jshopConfig = JSFactory::getConfig();
        if ($this->tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[] = array('tax'=>$k, 'price'=>$price*$v);
            }
            $taxes = array();
            if ($jshopConfig->display_price_front_current==0){
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/(100+$v['tax']);
                }
            }else{
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/100;
                }
            }    
        }else{
            $taxes = array();
            $taxes[$this->getTax()] = $this->calculateTax();
        }        
    return $taxes;
    }
    
    /**
    * static
    * get config payment for classname
    */
    function getConfigsForClassName($classname) {
        $db = JFactory::getDBO(); 
        $query = "SELECT payment_params FROM `#__jshopping_payment_method` WHERE payment_class = '".$db->escape($classname)."'";
        extract(js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $params_str = $db->loadResult();
        $parseString = new parseString($params_str);
        $params = $parseString->parseStringToParams();
        return $params;
    }
    
    /**
    * get config    
    */
    function getConfigs(){
        $parseString = new parseString($this->payment_params);
        $params = $parseString->parseStringToParams();
        return $params;
    }
    
    function check(){
        if ($this->payment_class==""){
            $this->setError("Alias Empty");
            return 0;
        }
        return 1;
    }
	
    function getPaymentSystemData($script=''){
        $jshopConfig = JSFactory::getConfig();
        if ($script==''){
            if ($this->scriptname!=''){
                $script = $this->scriptname;
            }else{
                $script = $this->payment_class;
            }
        }else{
            $script = str_replace(array('.','/'),'', $script);
        }
        $data = new stdClass();
        
        if (!file_exists($jshopConfig->path.'payments/'.$script."/".$script.'.php')){
            $data->paymentSystemVerySimple = 1;
            $data->paymentSystemError = 0;
            $data->paymentSystem = null;
        }else{
            include_once($jshopConfig->path.'payments/'.$script."/".$script.'.php');
            if (!class_exists($script)){
                $data->paymentSystemVerySimple = 0;
                $data->paymentSystemError = 1;
                $data->paymentSystem = null;
            }else{
                $data->paymentSystemVerySimple = 0;
                $data->paymentSystemError = 0;
                $data->paymentSystem = new $script();
                $data->paymentSystem->setPmMethod($this);
            }
        }
    return $data;
    }
    
    function loadPaymentForm($payment_system, $params, $pmconfig){
        ob_start();
        $payment_system->showPaymentForm($params, $pmconfig);
        $html = ob_get_contents();
        ob_get_clean();
        return $html;
    }

}
?>