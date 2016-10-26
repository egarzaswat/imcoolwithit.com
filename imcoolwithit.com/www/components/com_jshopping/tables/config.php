<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

class jshopConfig extends JTableAvto {
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_config', 'id', $_db );
    }

    function transformPdfParameters() {
        if (is_array($this->pdf_parameters)){
            $this->pdf_parameters = implode(":",$this->pdf_parameters);
        }
    }

    function loadCurrencyValue(){
        $session = JFactory::getSession();
        $id_currency_session = $session->get('js_id_currency');
        $id_currency = JRequest::getInt('id_currency');
        $main_currency = $this->mainCurrency;
        if ($this->default_frontend_currency) $main_currency = $this->default_frontend_currency;
        
        if ($session->get('js_id_currency_orig') && $session->get('js_id_currency_orig')!=$main_currency) {
            $id_currency_session = 0;
            $session->set('js_update_all_price', 1);
        }

        if (!$id_currency && $id_currency_session){
            $id_currency = $id_currency_session;
        }

        $session->set('js_id_currency_orig', $main_currency);

        if ($id_currency){
            $this->cur_currency = $id_currency;
        }else{
            $this->cur_currency = $main_currency;
        }
        $session->set('js_id_currency', $this->cur_currency);
        $all_currency = JSFactory::getAllCurrency();
        $current_currency = $all_currency[$this->cur_currency];
        if (!$current_currency->currency_value) $current_currency->currency_value = 1;
        $this->currency_value = $current_currency->currency_value;
        $this->currency_code = $current_currency->currency_code;
        $this->currency_code_iso = $current_currency->currency_code_iso;
    }
    
    function getDisplayPriceFront(){
        $display_price = $this->display_price_front;

        if ($this->use_extend_display_price_rule > 0){
            $adv_user = JSFactory::getUserShop();
            $country_id = $adv_user->country;
            $client_type = $adv_user->client_type;
            if (!$adv_user->user_id){
                $adv_user = JSFactory::getUserShopGuest();
                $country_id = $adv_user->country;
                $client_type = $adv_user->client_type;
            }
            if (!$country_id){
                $country_id = $this->default_country;
            }    
            if ($country_id){
                $configDisplayPrice = JSFactory::getTable('configDisplayPrice', 'jshop');
                $rows = $configDisplayPrice->getList();
                foreach($rows as $v){
                    if (in_array($country_id, $v->countries)){
                        if ($client_type==2){
                            $display_price = $v->display_price_firma;
                        }else{
                            $display_price = $v->display_price;
                        }
                    }
                }
            }
        }
        return $display_price;
    }
    
    function getListFieldsRegister(){
        $config = new stdClass();
        include(JPATH_COMPONENT_SITE."/lib/default_config.php");        
        if ($this->fields_register!=""){
            $data = unserialize($this->fields_register);

            $data['editaccount']['looking_for']['display'] = 1;
            $data['editaccount']['looking_for']['require'] = 1;

            $data['editaccount']['age_look_from']['display'] = 1;
            $data['editaccount']['age_look_from']['require'] = 1;

            $data['editaccount']['age_look_to']['display'] = 1;
            $data['editaccount']['age_look_to']['require'] = 1;

            $data['editaccount']['distance']['display'] = 1;
            $data['editaccount']['distance']['require'] = 1;

        }else{
            $data = array();
        }



        foreach($fields_client as $type=>$_v){
            foreach($fields_client[$type] as $k=>$v){
                if (!isset($data[$type][$v])){
                    $data[$type][$v] = array('display'=>0,'require'=>0);                    
                }

                if (!isset($data[$type][$v]['display'])) $data[$type][$v]['display'] = 0;
                if (!isset($data[$type][$v]['require'])) $data[$type][$v]['require'] = 0;

            }
        }

        return $data;
    }
    
    function getEnableDeliveryFiledRegistration($type='address'){
        $tmp_fields = $this->getListFieldsRegister();
        $config_fields = (array)$tmp_fields[$type];
        $count = 0;
        foreach($config_fields as $k=>$v){
            if (substr($k, 0, 2)=="d_" && $v['display']==1) $count++;
        }
    return ($count>0);
    }
    
    function getProductListDisplayExtraFields(){
        if ($this->product_list_display_extra_fields!=""){
            return unserialize($this->product_list_display_extra_fields);
        }else{
            return array();
        }
    }

    function setProductListDisplayExtraFields($data){
        if (is_array($data)){
            $this->product_list_display_extra_fields = serialize($data);
        }else{
            $this->product_list_display_extra_fields = serialize(array());
        }
    }

    function getFilterDisplayExtraFields(){
        if ($this->filter_display_extra_fields!=""){
            return unserialize($this->filter_display_extra_fields);
        }else{
            return array();
        }
    }
    
    function setFilterDisplayExtraFields($data){
        if (is_array($data)){
            $this->filter_display_extra_fields = serialize($data);
        }else{
            $this->filter_display_extra_fields = serialize(array());
        }
    }
    
    function getProductHideExtraFields(){
        if ($this->product_hide_extra_fields!=""){
            return unserialize($this->product_hide_extra_fields);
        }else{
            return array();
        }
    }
    
    function setProductHideExtraFields($data){
        if (is_array($data)){
            $this->product_hide_extra_fields = serialize($data);
        }else{
            $this->product_hide_extra_fields = serialize(array());
        }
    }
    
    function getCartDisplayExtraFields(){
        if ($this->cart_display_extra_fields!=""){
            return unserialize($this->cart_display_extra_fields);
        }else{
            return array();
        }
    }
    
    function setCartDisplayExtraFields($data){
        if (is_array($data)){
            $this->cart_display_extra_fields = serialize($data);
        }else{
            $this->cart_display_extra_fields = serialize(array());
        }
    }
    
    function updateNextOrderNumber(){
        $db = JFactory::getDBO();
        $query = "update `#__jshopping_config` set next_order_number=next_order_number+1";
        $db->setQuery($query);
        $db->query();
    }
	
    function getNextOrderNumber(){
        $db = JFactory::getDBO();
        $query = "select next_order_number from `#__jshopping_config` where id=".intval($this->id);
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function loadOtherConfig(){
        if ($this->other_config!=""){
            $tmp = unserialize($this->other_config);
            foreach($tmp as $k=>$v){
                $this->$k = $v;
            }
        }
    }

    function getVersion(){
        $data = JApplicationHelper::parseXMLInstallFile($this->admin_path."jshopping.xml");
        return $data['version'];
    }
    
    function loadLang(){
        $this->cur_lang = JFactory::getLanguage()->getTag();
    }
    
    function loadFrontLand(){
        $params = JComponentHelper::getParams('com_languages');
        $this->frontend_lang = $params->get('site', 'en-GB');
    }
    
    function setLang($lang){
        $this->cur_lang = $lang;
    }
    
    function getLang(){
        return $this->cur_lang;
    }
    
    function getFrontLang(){
        return $this->frontend_lang;
    }
}
?>