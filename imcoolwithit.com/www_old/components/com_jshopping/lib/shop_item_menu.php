<?php
/**
* @version      4.7.0 05.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class shopItemMenu{
    static $instance = null;
    public $list = null;
    public $list_category = null;
    public $list_manufacturer = null;
    public $list_content = null;
    public $cart = null;
    public $wishlist = null;
    public $search = null;
    public $user = null;
    public $vendor = null;
    public $shop = null;
    public $manufacturer = null;
    public $products = null;
    public $checkout = null;
    public $login = null;
    public $logout = null;
    public $editaccount = null;
    public $orders = null;
    public $register = null;

    static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new shopItemMenu();
            self::$instance->init();
        }
        return self::$instance;
    }
    
    function init(){
        $list = $this->getList();
        $this->list_category = array();
        $this->list_manufacturer = array();
        $this->list_content = array();
        $this->cart = 0;
        $this->wishlist = 0;
        $this->search = 0;
        $this->user = 0;
        $this->vendor = 0;
        $this->shop = 0;
        $this->manufacturer = 0;
        $this->products = 0;
        $this->checkout = 0;
        $this->login = 0;
        $this->logout = 0;
        $this->editaccount = 0;
        $this->orders = 0;
        $this->register = 0;

        foreach($list as $k=>$v){
            $data = $v->data;
            if (!$data['controller'] && $data['view']){
                $data['controller'] = $data['view'];
                unset($data['view']);
                unset($data['layout']);
            }
            if (count($data)==3 && $data['controller']=="category" && $data['task']=="view" && $data['category_id']){
                $this->list_category[$data['category_id']] = $v->id;
            }
            if (count($data)==3 && $data['controller']=="manufacturer" && $data['task']=="view" && $data['manufacturer_id']){
                $this->list_manufacturer[$data['manufacturer_id']] = $v->id;
            }
            if (count($data)==3 && $data['controller']=="content" && $data['task']=="view" && $data['page']){
                $this->list_content[$data['page']] = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="login"){
                $this->login = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="logout"){
                $this->logout = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="editaccount"){
                $this->editaccount = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="orders"){
                $this->orders = $v->id;
            }
            if (count($data)==2 && $data['controller']=="user" && $data['task']=="register"){
                $this->register = $v->id;
            }
            if ($data['controller']=="cart"){
                $this->cart = $v->id;
            }
            if ($data['controller']=="wishlist"){
                $this->wishlist = $v->id;
            }
            if ($data['controller']=="search"){
                $this->search = $v->id;
            }
            if ($data['controller']=="category" && count($data)==1){
                $this->shop = $v->id;
            }
            if ($data['controller']=="manufacturer" && count($data)==1){
                $this->manufacturer = $v->id;
            }
            if ($data['controller']=="products" && count($data)==1){
                $this->products = $v->id;
            }
            if ($data['controller']=="user" && count($data)==1){
                $this->user = $v->id;
            }
            if ($data['controller']=="vendor" && count($data)==1){
                $this->vendor = $v->id;
            }
            if ($data['controller']=="checkout"){
                $this->checkout = $v->id;
            }            
        }
    }
    
    function getList(){
        if (!is_array($this->list)){
            $jshopConfig = JSFactory::getConfig();
            $current_lang = $jshopConfig->getLang();
            $user = JFactory::getUser();
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $db = JFactory::getDBO();
            $query = "select id,link from #__menu where `type`='component' and published=1 and link like '%option=com_jshopping%' and client_id=0 and (language='*' or language='".$current_lang."') and access IN (".$groups.")";
            $db->setQuery($query);
            $this->list = $db->loadObjectList();
            foreach($this->list as $k=>$v){
                $data = array();
                $v->link = str_replace("index.php?option=com_jshopping&","",$v->link);
                $tmp = explode('&', $v->link);
                foreach($tmp as $k2=>$v2){
                    $tmp2 = explode("=", $v2);
                    if ($tmp2[1]!=""){
                        $data[$tmp2[0]] = $tmp2[1];
                    }
                }
                $this->list[$k]->data = $data;
            }
        }
    return $this->list;
    }
    
    function getListCategory(){
    return $this->list_category;
    }
    
    function getListManufacturer(){
    return $this->list_manufacturer;
    }
    
    function getListContent(){
    return $this->list_content;
    }
    
    function getCart(){
    return $this->cart;
    }
    
    function getWishlist(){
    return $this->wishlist;
    }
    
    function getSearch(){
    return $this->search;
    }
    
    function getUser(){
    return $this->user;
    }
    
    function getLogin(){
    return $this->login;
    }
    
    function getLogout(){
    return $this->logout;
    }
    
    function getEditaccount(){
    return $this->editaccount;
    }
    
    function getOrders(){
    return $this->orders;
    }
    
    function getRegister(){
    return $this->register;
    }

    function getVendor(){
    return $this->vendor;
    }
    
    function getShop(){
    return $this->shop;
    }
    
    function getManufacturer(){
    return $this->manufacturer;
    }
    
    function getProducts(){
    return $this->products;
    }
    
    function getCheckout(){
    return $this->checkout;
    }
}
?>