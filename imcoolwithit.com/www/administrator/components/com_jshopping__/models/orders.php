<?php
/**
* @version      4.11.2 13.06.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class JshoppingModelOrders extends JModelLegacy{    

    function getCountAllOrders($filters) {
        $db = JFactory::getDBO();
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".$db->escape($filters['status_id'])."'";
        }
        if($filters['user_id']) $where .= " and O.user_id = '".$db->escape($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = $db->escape($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }
        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['year']!=0) $year = $filters['year']; else $year="%";
        if ($filters['month']!=0) $month = $filters['month']; else $month="%";
        if ($filters['day']!=0) $day = $filters['day']; else $day="%";
        $where .= " and O.order_date like '".$year."-".$month."-".$day." %'";
        
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $where .= " and OI.vendor_id='".$db->escape($filters['vendor_id'])."'";
            $query = "SELECT COUNT(distinct O.order_id) FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY O.order_id DESC";
        }else{
            $query = "SELECT COUNT(O.order_id) FROM `#__jshopping_orders` as O where 1 ".$where;
        }
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetCountAllOrders', array(&$query, &$filters));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllOrders($limitstart, $limit, $filters, $filter_order, $filter_order_Dir){
        $db = JFactory::getDBO(); 
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".$db->escape($filters['status_id'])."'";
        }
        if($filters['user_id']) $where .= " and O.user_id = '".$db->escape($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = $db->escape($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }
        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['year']!=0) $year = $filters['year']; else $year="%";
        if ($filters['month']!=0) $month = $filters['month']; else $month="%";
        if ($filters['day']!=0) $day = $filters['day']; else $day="%";
        $where .= " and O.order_date like '".$year."-".$month."-".$day." %'";
        
        $order = $filter_order." ".$filter_order_Dir;
        
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $where .= " and OI.vendor_id='".$db->escape($filters['vendor_id'])."'";
            $query = "SELECT distinct O.* FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY ".$order;
        }else{
            $query = "SELECT O.*, V.l_name as v_name, V.f_name as v_fname, concat(O.f_name,' ',O.l_name) as name FROM `#__jshopping_orders` as O
                  left join `#__jshopping_vendors` as V on V.id=O.vendor_id
                  where 1 $where ORDER BY ".$order;
        }
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetAllOrders', array(&$query, &$filters, &$filter_order, &$filter_order_Dir));
		$db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getAllOrderStatus($order = null, $orderDir = null) {
        $db = JFactory::getDBO(); 
        $lang = JSFactory::getLang();
        $ordering = "status_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT status_id, status_code, `".$lang->get('name')."` as name FROM `#__jshopping_order_status` ORDER BY ".$ordering;
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getMinYear(){
        $db = JFactory::getDBO();
        $query = "SELECT min(order_date) FROM `#__jshopping_orders`";
        $db->setQuery($query);
        $res = substr($db->loadResult(),0, 4);
        if (intval($res)==0) $res = "2010";
        extract(js_add_trigger(get_defined_vars(), "before"));
        return $res;
    }
    
    function saveOrderItem($order_id, $post, $old_items){
        $db = JFactory::getDBO();
        if (!isset($post['product_name'])) $post['product_name'] = array();

        $edit_order_items = array();
        foreach($post['product_name'] as $k=>$v){
            $order_item_id = intval($post['order_item_id'][$k]);
            $edit_order_items[] = $order_item_id;
            $order_item = JSFactory::getTable('orderItem', 'jshop');
            $order_item->order_item_id = $order_item_id;
            $order_item->order_id = $order_id;
            $order_item->product_id = $post['product_id'][$k];
            $order_item->product_ean = $post['product_ean'][$k];
            $order_item->product_name = $post['product_name'][$k];
            $order_item->product_quantity = saveAsPrice($post['product_quantity'][$k]);
            $order_item->product_item_price = $post['product_item_price'][$k];
            $order_item->product_tax = $post['product_tax'][$k];
            $order_item->product_attributes = $post['product_attributes'][$k];
            if (json_decode($post['attributes'][$k])){
                $attribute = (array)json_decode($post['attributes'][$k]);
                $order_item->attributes = serialize($attribute);
            }else{
                $order_item->attributes = $post['attributes'][$k];
            }
            $order_item->product_freeattributes = $post['product_freeattributes'][$k];
            $order_item->weight = $post['weight'][$k];
            if (isset($post['delivery_times_id'][$k])){
                $order_item->delivery_times_id = $post['delivery_times_id'][$k];
            }else{
                $order_item->delivery_times_id = 0;
            }
            $order_item->vendor_id = $post['vendor_id'][$k];
            $order_item->thumb_image = $post['thumb_image'][$k];
            $order_item->files = serialize(array());
            $order_item->store();
            unset($order_item);
        }

        foreach($old_items as $k=>$v){
            if (!in_array($v->order_item_id, $edit_order_items)){
                $order_item = JSFactory::getTable('orderItem', 'jshop');
                $order_item->delete($v->order_item_id);                
            }
        }
        extract(js_add_trigger(get_defined_vars(), "before"));
        return 1;
    }
    
    function getCartProductsFromOrderProducts($items){
        $products = array();
        foreach($items as $k=>$v){
            $prod = array();
            $prod['product_id'] = $v['product_id'];
            $prod['quantity'] = $v['product_quantity'];
            $prod['tax'] = $v['product_tax'];
            $prod['product_name'] = $v['product_name'];
            $prod['thumb_image'] = $v['thumb_image'];
            $prod['ean'] = $v['product_ean'];
            $prod['weight'] = $v['weight'];
            $prod['delivery_times_id'] = $v['delivery_times_id'];
            $prod['vendor_id'] = $v['vendor_id'];
            $prod['price'] = $v['product_item_price'];
            $products[] = $prod;
        }
        extract(js_add_trigger(get_defined_vars(), "before"));
        return $products;
    }
    
    function loadtaxorder($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $display_price_front_current = $data_order['display_price'];
        $taxes = array();
        $AllTaxes = JSFactory::getAllTaxes();
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        
        // tax product
        foreach($products as $key=>$product){
            $tax = floatval($product['product_tax']);
            $price = $product['product_item_price'] * $product['product_quantity'];
            $SumTax = (isset($taxes[$tax]))?$taxes[$tax]:0;
            $taxes[$tax] =  $SumTax + getPriceTaxValue($price, $tax, $display_price_front_current);     
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        // payment
        if ($data_order['order_payment']!=0){
            $price = $data_order['order_payment'];
            $payment_method_id = $data_order['payment_method_id'];
            $paym_method = JSFactory::getTable('paymentmethod', 'jshop');
            $paym_method->load($payment_method_id);
            $paym_method->setCart($cart);
            $payment_taxes = $paym_method->calculateTaxList($price);            
            foreach($payment_taxes as $k=>$v){
                $k = floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }            
        }
        
        //shipping
        $shipping_method = JSFactory::getTable('shippingMethod', 'jshop');
        $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice', 'jshop');
        $shipping_method_price->load($sh_pr_method_id);
        
        // tax shipping
        if ($data_order['order_shipping']>0){
            $price = $data_order['order_shipping'];            
            $shipping_taxes = $shipping_method_price->calculateShippingTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }
        }
        // tax package
        if ($data_order['order_package']>0){
            $price = $data_order['order_package'];
            $shipping_taxes = $shipping_method_price->calculatePackageTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }
        }
        
        $taxes_array = array();
        foreach($taxes as $tax=>$value){
            if ($tax>0){
                $taxes_array[] = array('tax'=>$tax, 'value'=>$value);
            }
        }
        extract(js_add_trigger(get_defined_vars(), "before"));
        return $taxes_array;
    }
    
    function loadshippingprice($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $all_currency = JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'];
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        $shipping_method = JSFactory::getTable('shippingMethod', 'jshop');
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice', 'jshop');
        
        $shipping_price_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        if (!$shipping_price_method_id){
            return null;
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        $shipping_method_price->load($shipping_price_method_id);            
        $prices = $shipping_method_price->calculateSum($cart);
        extract(js_add_trigger(get_defined_vars(), "before"));
        return $prices; 
    }
    
    function loadpaymentprice($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $all_currency = JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'];
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        $AllTaxes = JSFactory::getAllTaxes();
        
        $paym_method = JSFactory::getTable('paymentmethod', 'jshop');
        $paym_method->load($data_order['payment_method_id']);
        if ($paym_method->price_type==2){
            
            $total = 0;
            foreach($products as $key=>$product){
                $tax = floatval($product['product_tax']);
                $product_price = $product['product_item_price'] * $product['product_quantity'];
                if ($data_order['display_price']){
                    $product_price = $product_price + $product_price * $tax / 100;    
                }
                $total += $product_price;
            }
            
            $cproducts = $this->getCartProductsFromOrderProducts($products);
            $cart = JSFactory::getModel('cart', 'jshop');
            $cart->products = array();
            $cart->loadProductsFromArray($cproducts);
            $cart->loadPriceAndCountProducts();
            
            $shipping_method = JSFactory::getTable('shippingMethod', 'jshop');
            $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);            
            $shipping_method_price = JSFactory::getTable('shippingMethodPrice', 'jshop');
            $shipping_method_price->load($sh_pr_method_id);
            
            $tax = floatval($AllTaxes[$shipping_method_price->shipping_tax_id]);
            $shipping_price = $data_order['order_shipping'];
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculateShippingTaxList($shipping_price, $cart);                
                foreach($shipping_taxes as $k=>$v){
                    $shipping_price = $shipping_price + $v;    
                }                
            }
            $total += $shipping_price;
            
            $tax = floatval($AllTaxes[$shipping_method_price->package_tax_id]);
            $package_price = $data_order['order_package'];
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculatePackageTaxList($package_price, $cart);
                foreach($shipping_taxes as $k=>$v){
                    $package_price = $package_price + $v;    
                }                
            }
            $total += $package_price;

            $price = $total * $paym_method->price / 100;
            if ($data_order['display_price']){
                $price = getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
            }
        }else{
            $price = $paym_method->price * $jshopConfig->currency_value; 
            $price = getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
        }
        extract(js_add_trigger(get_defined_vars(), "before"));
        return $price;
    }
    
}
?>