<?php
/**
* @version      4.7.0 05.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class JTableAvto extends JTable{
    
    function getBuildQueryListProductDefaultResult($adfields=array()){
        $lang = JSFactory::getLang();
		if (count($adfields)>0) $adquery = ",".implode(', ',$adfields); else $adquery = '';
        return "prod.product_id, pr_cat.category_id, prod.`".$lang->get('name')."` as name, prod.`".$lang->get('short_description')."` as short_description, prod.product_ean, prod.image, prod.product_price, prod.currency_id, prod.product_tax_id as tax_id, prod.product_old_price, prod.product_weight, prod.average_rating, prod.reviews_count, prod.hits, prod.weight_volume_units, prod.basic_price_unit_id, prod.label_id, prod.product_manufacturer_id, prod.min_price, prod.product_quantity, prod.different_prices".$adquery;
    }
    
    function getBuildQueryListProduct($type, $restype, &$filters, &$adv_query, &$adv_from, &$adv_result){
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $originaladvres = $adv_result;
        
        $groups = implode(',', $user->getAuthorisedViewLevels());
        if ($type=="category"){
            $adv_query .=' AND prod.access IN ('.$groups.')';
        }else{
            $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        }
        
        if ($jshopConfig->show_delivery_time){            
            $adv_result .= ", prod.delivery_times_id";
        }        
        if ($jshopConfig->admin_show_product_extra_field){
            $adv_result .= getQueryListProductsExtraFields();
        }        
        if ($jshopConfig->product_list_show_vendor){
            $adv_result .= ", prod.vendor_id";
        }        
        if ($jshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if (isset($filters['categorys']) && $type!="category" && is_array($filters['categorys']) && count($filters['categorys'])){
            $adv_query .= " AND cat.category_id in (".implode(",",$filters['categorys']).")";
        }
        if (isset($filters['manufacturers']) && $type!="manufacturer" && is_array($filters['manufacturers']) && count($filters['manufacturers'])){
            $adv_query .= " AND prod.product_manufacturer_id in (".implode(",",$filters['manufacturers']).")";
        }        
        if (isset($filters['labels']) && is_array($filters['labels']) && count($filters['labels'])){
            $adv_query .= " AND prod.label_id in (".implode(",",$filters['labels']).")";
        }
        if (isset($filters['vendors']) && $type!="vendor" && is_array($filters['vendors']) && count($filters['vendors'])){
            $adv_query .= " AND prod.vendor_id in (".implode(",",$filters['vendors']).")";
        }        
        if (isset($filters['extra_fields']) && is_array($filters['extra_fields'])){
            foreach($filters['extra_fields'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val_id){
                        $tmp[] = " find_in_set('".$val_id."', prod.`extra_field_".$f_id."`) ";
                    }
                    $mchfilterlogic = 'OR';
                    if ($jshopConfig->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
                    $adv_query .= " AND (".$_tmp_adv_query.")";
                }elseif(is_string($vals) && $vals!=""){
                    $adv_query .= " AND prod.`extra_field_".$f_id."`='".$db->escape($vals)."'";
                }
            }
        }
        
        $this->getBuildQueryListProductFilterPrice($filters, $adv_query, $adv_from);
        
        if ($jshopConfig->product_list_show_qty_stock){
            $adv_result .= ", prod.unlimited";
        }
        
        if ($restype=="count"){
            $adv_result = $originaladvres;
        }    
    }
    
    function getBuildQueryListProductFilterPrice($filters, &$adv_query, &$adv_from){
        if (isset($filters['price_from'])){
            $price_from = getCorrectedPriceForQueryFilter($filters['price_from']);
        }else{
            $price_from = 0;
        }
        if (isset($filters['price_to'])){
            $price_to = getCorrectedPriceForQueryFilter($filters['price_to']);
        }else{
            $price_to = 0;
        }        
        if (!$price_from && !$price_to) return 0;
        
        $jshopConfig = JSFactory::getConfig();
        $userShop = JSFactory::getUserShop();
        $multyCurrency = count(JSFactory::getAllCurrency());
        if ($userShop->percent_discount){
            $price_part = 1-$userShop->percent_discount/100;
        }else{
            $price_part = 1;
        }
        
        $adv_query2 = "";
        $adv_from2 = "";
        
        if ($multyCurrency > 1){
            $adv_from2 .= " LEFT JOIN `#__jshopping_currencies` AS cr USING (currency_id) ";
            if ($price_to){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value )<=".$price_to." OR ( prod.min_price*$price_part / cr.currency_value)<=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) <= ".$price_to;
                }
            } 
            
            if ($price_from){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from." OR ( prod.min_price*$price_part / cr.currency_value ) >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from;
                }
            }
        }else{
            if ($price_to){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part <=".$price_to." OR prod.min_price*$price_part <=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part <= ".$price_to;
                }
            }
            if ($price_from){
                if ($jshopConfig->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part >= ".$price_from." OR prod.min_price*$price_part >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part >= ".$price_from;
                }
            }
        }
        
        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBuildQueryListProductFilterPrice', array($filters, &$adv_query, &$adv_from, &$adv_query2, &$adv_from2) );
        
        $adv_query .= $adv_query2;
        $adv_from .= $adv_from2;
    }
    
    function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
        $order_query = "";
        if (!$order) return $order_query;
        $order_original = $order;
        $jshopConfig = JSFactory::getConfig();
        $multyCurrency = count(JSFactory::getAllCurrency());
        if ($multyCurrency>1 && $order=="prod.product_price"){
            if (strpos($adv_from,"jshopping_currencies")===false){
                $adv_from .= " LEFT JOIN `#__jshopping_currencies` AS cr USING (currency_id) ";
            }
            if ($jshopConfig->product_list_show_min_price){
                $order = "prod.min_price/cr.currency_value";
            }else{
                $order = "prod.product_price/cr.currency_value";
            }
        }
        if ($order=="prod.product_price" && $jshopConfig->product_list_show_min_price){
            $order = "prod.min_price";
        }
        $order_query = " ORDER BY ".$order;
        if ($orderby){
            $order_query .= " ".$orderby;
        }
        
        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBuildQueryOrderListProduct', array($order, $orderby, &$adv_from, &$order_query, $order_original) );
        
    return $order_query;
    }
    
    function getBuildQueryListProductSimpleList($type, $array_categories, &$filters, &$adv_query, &$adv_from, &$adv_result){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
                
        if (is_array($array_categories) && count($array_categories)){
            $adv_query .= " AND pr_cat.category_id IN (".implode(",", $array_categories).")";
        }        
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        
        if ($jshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if ($jshopConfig->show_delivery_time){
            $adv_result .= ", prod.delivery_times_id";
        }
        if ($jshopConfig->admin_show_product_extra_field){
            $adv_result .= getQueryListProductsExtraFields();
        }
        if ($jshopConfig->product_list_show_vendor){
            $adv_result .= ", prod.vendor_id";
        }
        if ($jshopConfig->product_list_show_qty_stock){
            $adv_result .= ", prod.unlimited";
        }

        if (isset($filters['categorys']) && is_array($filters['categorys']) && count($filters['categorys'])){
            $adv_query .= " AND cat.category_id in (".implode(",",$filters['categorys']).")";
        }
        if (isset($filters['manufacturers']) && is_array($filters['manufacturers']) && count($filters['manufacturers'])){
            $adv_query .= " AND prod.product_manufacturer_id in (".implode(",",$filters['manufacturers']).")";
        }        
        if (isset($filters['labels']) && is_array($filters['labels']) && count($filters['labels'])){
            $adv_query .= " AND prod.label_id in (".implode(",",$filters['labels']).")";
        }
        if (isset($filters['vendors']) && is_array($filters['vendors']) && count($filters['vendors'])){
            $adv_query .= " AND prod.vendor_id in (".implode(",",$filters['vendors']).")";
        }        
        if (isset($filters['extra_fields']) && is_array($filters['extra_fields'])){
            foreach($filters['extra_fields'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val_id){
                        $tmp[] = " find_in_set('".$val_id."', prod.`extra_field_".$f_id."`) ";
                    }
                    $mchfilterlogic = 'OR';
                    if ($jshopConfig->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
                    $adv_query .= " AND (".$_tmp_adv_query.")";
                }elseif(is_string($vals) && $vals!=""){
                    $adv_query .= " AND prod.`extra_field_".$f_id."`='".$db->escape($vals)."'";
                }
            }
        }        
        $this->getBuildQueryListProductFilterPrice($filters, $adv_query, $adv_from);
    }
}
?>