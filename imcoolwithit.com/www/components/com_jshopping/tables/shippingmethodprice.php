<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopShippingMethodPrice extends JTable {

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_shipping_method_price', 'sh_pr_method_id', $_db );
    }
    
	function getPricesWeight($sh_pr_method_id, $id_country, &$cart){
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();

        $query = "SELECT (sh_pr_weight.shipping_price + sh_pr_weight.shipping_package_price) AS shipping_price, sh_pr_weight.shipping_weight_from, sh_pr_weight.shipping_weight_to, sh_price.shipping_tax_id
                  FROM `#__jshopping_shipping_method_price` AS sh_price
                  INNER JOIN `#__jshopping_shipping_method_price_weight` AS sh_pr_weight ON sh_pr_weight.sh_pr_method_id = sh_price.sh_pr_method_id
                  INNER JOIN `#__jshopping_shipping_method_price_countries` AS sh_pr_countr ON sh_pr_weight.sh_pr_method_id = sh_pr_countr.sh_pr_method_id
                  WHERE sh_price.sh_pr_method_id = '" . $db->escape($sh_pr_method_id) . "'AND sh_pr_countr.country_id = '" . $db->escape($id_country) . "' 
                  ORDER BY sh_pr_weight.shipping_weight_from";
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach($list as $k=>$v){
            $list[$k]->shipping_price = $list[$k]->shipping_price * $jshopConfig->currency_value;            
            $list[$k]->shipping_price = getPriceCalcParamsTax($list[$k]->shipping_price, $list[$k]->shipping_tax_id, $cart->products);
        }
        return $list; 
    }

    function getPrices($orderdir = "asc") {
        $query = "SELECT * FROM `#__jshopping_shipping_method_price_weight` AS sh_price
                  WHERE sh_price.sh_pr_method_id = '" . $this->_db->escape($this->sh_pr_method_id) . "'
                  ORDER BY sh_price.shipping_weight_from ".$orderdir;
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    function getCountries() {
        $lang = JSFactory::getLang();
        $query = "SELECT sh_country.country_id, countries.`".$lang->get('name')."` as name
                  FROM `#__jshopping_shipping_method_price_countries` AS sh_country
                  INNER JOIN `#__jshopping_countries` AS countries ON countries.country_id = sh_country.country_id
                  WHERE sh_country.sh_pr_method_id = '" . $this->_db->escape($this->sh_pr_method_id) . "'";
        $this->_db->setQuery($query);        
        return $this->_db->loadObjectList();
    }

    function getTax(){        
        $taxes = JSFactory::getAllTaxes();        
        return $taxes[$this->shipping_tax_id];
    }
    
    function getTaxPackage(){
        $taxes = JSFactory::getAllTaxes();
        return $taxes[$this->package_tax_id];
    }
    
    function getGlobalConfigPriceNull($cart){
        $jshopConfig = JSFactory::getConfig();
        return ($cart->getSum() >= ($jshopConfig->summ_null_shipping * $jshopConfig->currency_value) && $jshopConfig->summ_null_shipping > 0);
    }

    function calculateSum(&$cart){
        $jshopConfig = JSFactory::getConfig();
        if ($this->getGlobalConfigPriceNull($cart)){
            return 0;
        }

        $price = $this->shipping_stand_price;
        $package = $this->package_stand_price;
        $prices = array('shipping'=>$price,'package'=>$package);

        $extensions = JSFactory::getShippingExtList($this->shipping_method_id);
        foreach($extensions as $extension){
            if (isset($extension->exec->version) && $extension->exec->version==2){
                $prices = $extension->exec->getPrices($cart, $this->getParams(), $prices, $extension, $this);
            }else{
                $price = $extension->exec->getPrice($cart, $this->getParams(), $price, $extension, $this);
                $prices = array('shipping'=>$price,'package'=>$package);
            }
        }

        $prices['shipping'] = $prices['shipping'] * $jshopConfig->currency_value;
        $prices['shipping'] = getPriceCalcParamsTax($prices['shipping'], $this->shipping_tax_id, $cart->products);
        $prices['package'] = $prices['package'] * $jshopConfig->currency_value;
        $prices['package'] = getPriceCalcParamsTax($prices['package'], $this->package_tax_id, $cart->products);
    return $prices;
    }

    function calculateTax($sum){
        $jshopConfig = JSFactory::getConfig();
        $pricetax = getPriceTaxValue($sum, $this->getTax(), $jshopConfig->display_price_front_current);
        return $pricetax;
    }
    function calculateTaxPackage($sum){
        $jshopConfig = JSFactory::getConfig();
        $pricetax = getPriceTaxValue($sum, $this->getTaxPackage(), $jshopConfig->display_price_front_current);
        return $pricetax;
    }
    
    function getShipingPriceForTaxes($price, $cart){
        if ($this->shipping_tax_id==-1){
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
    
    function getPackegePriceForTaxes($price, $cart){
        if ($this->package_tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTaxPackage()] = $price;
        }
    return $prices;
    }

    function calculateShippingTaxList($price, $cart){
        $jshopConfig = JSFactory::getConfig();
        if ($this->shipping_tax_id==-1){
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
            $taxes[$this->getTax()] = $this->calculateTax($price);
        }
    return $taxes;
    }
    
    function calculatePackageTaxList($price, $cart){
        $jshopConfig = JSFactory::getConfig();
        if ($this->package_tax_id==-1){
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
            $taxes[$this->getTaxPackage()] = $this->calculateTaxPackage($price);
        }
    return $taxes;
    }
    
    function isCorrectMethodForCountry($id_country) {
        $query = "SELECT `sh_method_country_id` FROM `#__jshopping_shipping_method_price_countries` WHERE `country_id` = '".$this->_db->escape($id_country)."' AND `sh_pr_method_id` = '".$this->_db->escape($this->sh_pr_method_id)."'";
        $this->_db->setQuery($query);
        $this->_db->query();
        return ($this->_db->getNumRows())?(1):(0);
    }
    
    function setParams($params){
        $this->params = serialize($params);
    }
    
    function getParams(){
        if ($this->params==""){
            return array();
        }else{
            return unserialize($this->params);
        }
    }

}
?>