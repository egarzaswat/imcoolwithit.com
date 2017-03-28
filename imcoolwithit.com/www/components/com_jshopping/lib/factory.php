<?php
/**
* @version      4.8.1 09.01.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

error_reporting(error_reporting() & ~E_NOTICE);
JTable::addIncludePath(JPATH_ROOT.'/components/com_jshopping/tables');
include_once(JPATH_ROOT."/components/com_jshopping/lib/jtableauto.php");
include_once(JPATH_ROOT."/components/com_jshopping/lib/multilangfield.php");
include_once(JPATH_ROOT."/components/com_jshopping/tables/config.php");
require_once(JPATH_ROOT."/components/com_jshopping/lib/functions.php");

class JSFactory{

    public static function getConfig(){
    static $config;
        if (!is_object($config)){
            JPluginHelper::importPlugin('jshopping');
            $dispatcher = JDispatcher::getInstance();
            $db = JFactory::getDBO();
            $config = new jshopConfig($db);
            include(dirname(__FILE__)."/default_config.php");
            if (file_exists(dirname(__FILE__)."/user_config.php")) include(dirname(__FILE__)."/user_config.php");
            $dispatcher->trigger('onBeforeLoadJshopConfig', array($config));
            $config->load($config->load_id);
            $config->loadOtherConfig();
            $config->loadCurrencyValue();
            $config->loadFrontLand();
            $config->loadLang();

            list($config->pdf_header_width, $config->pdf_header_height, $config->pdf_footer_width, $config->pdf_footer_height) = explode(":", $config->pdf_parameters);
            if (!$config->allow_reviews_prod){
                unset($config->sorting_products_field_select[5]);
                unset($config->sorting_products_name_select[5]);
                unset($config->sorting_products_field_s_select[5]);
                unset($config->sorting_products_name_s_select[5]);
            }

            if ($config->product_count_related_in_row<1) $config->product_count_related_in_row = 1;

            if ($config->user_as_catalog){
                $config->show_buy_in_category = 0;
            }
            if (!$config->stock){
                $config->hide_product_not_avaible_stock = 0;
                $config->hide_buy_not_avaible_stock = 0;
                $config->hide_text_product_not_available = 1;
                $config->product_list_show_qty_stock = 0;
                $config->product_show_qty_stock = 0;
            }

            if ($config->hide_product_not_avaible_stock || $config->hide_buy_not_avaible_stock){
                $config->controler_buy_qty = 1;
            }else{
                $config->controler_buy_qty = 0;
            }

            $config->display_price_front_current = $config->getDisplayPriceFront();// 0 - Brutto, 1 - Netto

            if ($config->template==""){
                $config->template = "default";
            }

            if ($config->show_product_code || $config->show_product_code_in_cart){
                $config->show_product_code_in_order = 1;
            }else{
                $config->show_product_code_in_order = 0;
            }

            if ($config->admin_show_vendors==0){
                $config->vendor_order_message_type = 0; //0 - none, 1 - mesage, 2 - order if not multivendor
                $config->admin_not_send_email_order_vendor_order = 0;
                $config->product_show_vendor = 0;
                $config->product_show_vendor_detail = 0;
            }

            //$config->copyrightText = '<span id="mxcpr"><a target="_blank" href="http://www.webdesigner-profi.de/">Copyright MAXXmarketing Webdesigner GmbH</a></span>';

            if ($config->image_resize_type==0){
                $config->image_cut = 1;
                $config->image_fill = 2;
            }elseif ($config->image_resize_type==1){
                $config->image_cut = 0;
                $config->image_fill = 2;
            }else{
                $config->image_cut = 0;
                $config->image_fill = 0;
            }
            if (!$config->tax){
                $config->show_tax_in_product = 0;
                $config->show_tax_product_in_cart = 0;
                $config->hide_tax = 1;
            }
            if (!$config->admin_show_delivery_time){
                $config->show_delivery_time = 0;
                $config->show_delivery_time_checkout = 0;
                $config->show_delivery_time_step5 = 0;
                $config->display_delivery_time_for_product_in_order_mail = 0;
                $config->show_delivery_date = 0;
            }
			if (!$config->admin_show_product_basic_price){
                $config->cart_basic_price_show = 0;
            }
            $config->use_ssl = intval($config->use_ssl);

            $dispatcher->trigger('onLoadJshopConfig', array(&$config));
        }
    return $config;
    }

    public static function getUserShop(){
    static $usershop;
        if (!is_object($usershop)){
            $user = JFactory::getUser();
            $db = JFactory::getDBO();
            require_once(JPATH_ROOT."/components/com_jshopping/tables/usershop.php");
            $usershop = new jshopUserShop($db);
            if($user->id){
                if(!$usershop->isUserInShop($user->id)) {
                    $usershop->addUserToTableShop($user);
                    $usershop->giveTokens($user);
                }
                $usershop->load($user->id);
                $usershop->percent_discount = $usershop->getDiscount();
            }else{
                $usershop->percent_discount = 0;
            }
            JDispatcher::getInstance()->trigger('onAfterGetUserShopJSFactory', array(&$usershop));
        }
    return $usershop;
    }

    public static function getUserShopGuest(){
    static $userguest;
        if (!is_object($userguest)){
            require_once(JPATH_ROOT."/components/com_jshopping/models/userguest.php");
            $userguest = new jshopUserGust();
            $userguest->load();
            $userguest->percent_discount = 0;
            JDispatcher::getInstance()->trigger('onAfterGetUserShopGuestJSFactory', array(&$userguest));
        }
    return $userguest;
    }

    public static function getUser(){
        $user = JFactory::getUser();
        if ($user->id){
            $adv_user = JSFactory::getUserShop();
        }else{
            $adv_user = JSFactory::getUserShopGuest();
        }
    return $adv_user;
    }

    public static function loadCssFiles(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_css) return 0;
        if (!$load){
            $document = JFactory::getDocument();
            $jshopConfig = JSFactory::getConfig();
            $document->addStyleSheet(JURI::root().'components/com_jshopping/css/'.$jshopConfig->template.'.css');
            if (file_exists(JPATH_ROOT.'/components/com_jshopping/css/'.$jshopConfig->template.'.custom.css')){
                $document->addStyleSheet(JURI::root().'components/com_jshopping/css/'.$jshopConfig->template.'.custom.css');
            }
            $load = 1;
        }
    }

    public static function loadJsFiles(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            $document = JFactory::getDocument();
            JHtml::_('behavior.framework');
            JHtml::_('bootstrap.framework');
            if ($jshopConfig->load_javascript){
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.media.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/functions.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/validateForm.js');
            }
            $load = 1;
        }
    }

    public static function loadJsFilesRating(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            if ($jshopConfig->load_javascript){
                $document = JFactory::getDocument();
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.MetaData.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.rating.pack.js');
                $document->addStyleSheet(JURI::root().'components/com_jshopping/css/jquery.rating.css');
            }
            $load = 1;
        }
    }

    public static function loadJsFilesLightBox(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_jquery_lightbox) return 0;
        if (!$load){
            $document = JFactory::getDocument();
            $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.lightbox-0.5.pack.js');
            $document->addStyleSheet(JURI::root().'components/com_jshopping/css/jquery.lightbox-0.5.css');
            $document->addScriptDeclaration('function initJSlightBox(){
                jQuery("a.lightbox").lightBox({
                    imageLoading: "'.JURI::root().'components/com_jshopping/images/loading.gif",
                    imageBtnClose: "'.JURI::root().'components/com_jshopping/images/close.gif",
                    imageBtnPrev: "'.JURI::root().'components/com_jshopping/images/prev.gif",
                    imageBtnNext: "'.JURI::root().'components/com_jshopping/images/next.gif",
                    imageBlank: "'.JURI::root().'components/com_jshopping/images/blank.gif",
                    txtImage: "'._JSHOP_IMAGE.'",
                    txtOf: "'._JSHOP_OF.'"
                });
            }
            jQuery(function() { initJSlightBox(); });');
            $load = 1;
        }
    }

    public static function reloadConfigFieldTLF(){
        $jshopConfig = JSFactory::getConfig();
        $reload = array('user_field_client_type','user_field_title','sorting_products_name_select','sorting_products_name_s_select','count_product_select');
        foreach($reload as $field){
            $tmp = $jshopConfig->$field;
            foreach($tmp as $k=>$v){
                if (defined($v)) $tmp[$k] = constant($v);
            }
            $jshopConfig->$field = $tmp;
        }
    }

    public static function loadLanguageFile($langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/components/com_jshopping/lang/';
        if (file_exists($langpatch.'override/'.$langtag.'.php'))
            require_once($langpatch.'override/'.$langtag.'.php');
        if (file_exists($langpatch.$langtag.'.php'))
            require_once($langpatch.$langtag.'.php');
        else
            require_once($langpatch.'en-GB.php');
        JSFactory::reloadConfigFieldTLF();
    }

    public static function loadExtLanguageFile($extname, $langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        if(file_exists(JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php'))
            require_once (JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php');
        else
            require_once (JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/en-GB.php');
    }

    public static function loadAdminLanguageFile($langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/administrator/components/com_jshopping/lang/';
        if (file_exists($langpatch.'override/'.$langtag.'.php'))
            require_once($langpatch.'override/'.$langtag.'.php');
        if (file_exists($langpatch.$langtag.'.php'))
            require_once($langpatch.$langtag.'.php');
        else
            require_once($langpatch.'en-GB.php');
        JSFactory::reloadConfigFieldTLF();
    }

    public static function loadExtAdminLanguageFile($extname, $langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        if(file_exists(JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php'))
            require_once (JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php');
        else
            require_once (JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/en-GB.php');
    }

    public static function getLang($langtag = ""){
    static $ml;
        if (!is_object($ml) || $langtag!=""){
            $jshopConfig = JSFactory::getConfig();
            $ml = new multiLangField();
            if ($langtag==""){
                $langtag = $jshopConfig->getLang();
            }
            $ml->setLang($langtag);
            JDispatcher::getInstance()->trigger('onAfterGetLangJSFactory', array(&$ml, &$langtag));
        }
    return $ml;
    }

    public static function getReservedFirstAlias(){
    static $alias;
        if (!is_array($alias)){
            jimport('joomla.filesystem.folder');
            $files = JFolder::files(JPATH_ROOT."/components/com_jshopping/controllers");
            $alias = array();
            foreach($files as $file){
                $alias[] = str_replace(".php","", $file);
            }
        }
    return $alias;
    }

    public static function getAliasCategory(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select category_id as id, `".$lang->get('alias')."` as alias from #__jshopping_categories where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAliasManufacturer(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select manufacturer_id as id, `".$lang->get('alias')."` as alias from #__jshopping_manufacturers where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAliasProduct(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select product_id as id, `".$lang->get('alias')."` as alias from #__jshopping_products where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $k=>$row){
                $alias[$row->id] = $row->alias;
                unset($rows[$k]);
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAllAttributes($resformat = 0){
    static $attributes;
        if (!is_array($attributes)){
            $_attrib = JSFactory::getTable("attribut","jshop");
            $attributes = $_attrib->getAllAttributes();
        }
        if ($resformat==0){
            return $attributes;
        }
        if ($resformat==1){
            $attributes_format1 = array();
            foreach($attributes as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($resformat==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($attributes as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }

    public static function getAllUnits(){
    static $rows;
        if (!is_array($rows)){
            $_unit = JSFactory::getTable("unit","jshop");
            $rows = $_unit->getAllUnits();
        }
    return $rows;
    }

    public static function getAllTaxesOriginal(){
    static $rows;
        if (!is_array($rows)){
            $_tax = JSFactory::getTable('tax', 'jshop');
            $_rows = $_tax->getAllTaxes();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->tax_id] = $row->tax_value;
            }
        }
    return $rows;
    }

    public static function getAllTaxes(){
    static $rows;
        if (!is_array($rows)){
            $jshopConfig = JSFactory::getConfig();
            $dispatcher = JDispatcher::getInstance();
            $_tax = JSFactory::getTable('tax', 'jshop');
            $rows = JSFactory::getAllTaxesOriginal();
            if ($jshopConfig->use_extend_tax_rule){
                $country_id = 0;
                $adv_user = JSFactory::getUserShop();
                $country_id = $adv_user->country;
                if ($jshopConfig->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                    $country_id = $adv_user->d_country;
                }
                $client_type = $adv_user->client_type;
                $enter_tax_id = $adv_user->tax_number!="";
                if (!$country_id){
                    $adv_user = JSFactory::getUserShopGuest();
                    $country_id = $adv_user->country;
                    if ($jshopConfig->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                        $country_id = $adv_user->d_country;
                    }
                    $client_type = $adv_user->client_type;
                    $enter_tax_id = $adv_user->tax_number!="";
                }
                if ($country_id){
                    $_rowsext = $_tax->getExtTaxes();
                    $dispatcher->trigger('beforeGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    foreach($_rowsext as $v){
                        if (in_array($country_id, $v->countries)){
                            if ($jshopConfig->ext_tax_rule_for==1){
                                if ($enter_tax_id){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }
                            }else{
                                if ($client_type==2){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }
                            }
                        }
                    }
                    $dispatcher->trigger('afterGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    unset($_rowsext);
                }
            }
        $dispatcher->trigger('afterGetAllTaxes', array(&$rows) );
        }
    return $rows;
    }

    public static function getAllManufacturer(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dispatcher = JDispatcher::getInstance();
            $adv_result = "manufacturer_id as id, `".$lang->get('name')."` as name, manufacturer_logo, manufacturer_url";
            $dispatcher->trigger('onBeforeQueryGetAllManufacturer', array(&$adv_result));
            $query = "select ".$adv_result." from #__jshopping_manufacturers where manufacturer_publish='1'";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getMainVendor(){
    static $row;
        if (!isset($row)){
            $row = JSFactory::getTable('vendor', 'jshop');
            $row->loadMain();
        }
    return $row;
    }

    public static function getAllVendor(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $query = "select id, shop_name, l_name, f_name from #__jshopping_vendors";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            $mainvendor = JSFactory::getMainVendor();
            $rows[0] = $mainvendor;
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTime(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, `".$lang->get('name')."` as name from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->name;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTimeDays(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, days from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->days;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllProductExtraField(){
    static $list;
        if (!is_array($list)){
            $productfield = JSFactory::getTable('productfield', 'jshop');
            $list = $productfield->getList();
        }
    return $list;
    }

    public static function getAllProductExtraFieldValue(){
    static $list;
        if (!is_array($list)){
            $productfieldvalue = JSFactory::getTable('productfieldvalue', 'jshop');
            $list = $productfieldvalue->getAllList(1);
        }
    return $list;
    }

    public static function getAllProductExtraFieldValueDetail(){
    static $list;
        if (!is_array($list)){
            $productfieldvalue = JSFactory::getTable('productfieldvalue', 'jshop');
            $list = $productfieldvalue->getAllList(2);
        }
    return $list;
    }

    public static function getDisplayListProductExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = JSFactory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getProductListDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }

    public static function getDisplayFilterExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = JSFactory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getFilterDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }

    public static function getAllCurrency(){
    static $list;
        if (!is_array($list)){
            $currency =JSFactory::getTable('currency', 'jshop');
            $_list = $currency->getAllCurrencies();
            $list = array();
            foreach($_list as $row){
                $list[$row->currency_id] = $row;
            }
        }
    return $list;
    }

    public static function getShippingExtList($for_shipping = 0){
    static $list;
        if (!is_array($list)){
            $jshopConfig = JSFactory::getConfig();
            $path = $jshopConfig->path."shippings";
            $shippingext = JSFactory::getTable('shippingext', 'jshop');
            $_list = $shippingext->getList(1);
            $list = array();
            foreach($_list as $row){
                $extname = $row->alias;
                $filepatch = $path."/".$extname."/".$extname.".php";
                if (file_exists($filepatch)){
                    include_once($filepatch);
                    $row->exec = new $extname();
                    $list[$row->id] = $row;
                }else{
                    JError::raiseWarning("",'Load ShippingExt "'.$extname.'" error.');
                }
            }
        }
        if ($for_shipping==0){
            return $list;
        }
        $returnlist = array();
        foreach($list as $row){
            if ($row->shipping_method!=""){
                $sm = unserialize($row->shipping_method);
            }else{
                $sm = array();
            }
            if(!isset($sm[$for_shipping])){
                $sm[$for_shipping]=1;
            }
            if ($sm[$for_shipping]!=="0"){
                $returnlist[] = $row;
            }
        }
    return $returnlist;
    }

    public static function getTable($type, $prefix = 'jshop', $config = array()){
        JDispatcher::getInstance()->trigger('onJSFactoryGetTable', array(&$type, &$prefix, &$config));
        $table = JTable::getInstance($type, $prefix, $config);
        JDispatcher::getInstance()->trigger('onAfterJSFactoryGetTable', array(&$table, &$type, &$prefix, &$config));
        return $table;
    }

    public static function getModel($type, $prefix = 'JshoppingModel', $config = array()){
        JDispatcher::getInstance()->trigger('onJSFactoryGetModel', array(&$type, &$prefix, &$config));
        $model = JModelLegacy::getInstance($type, $prefix, $config);
        JDispatcher::getInstance()->trigger('onAfterJSFactoryGetModel', array(&$model, &$type, &$prefix, &$config));
        return $model;
    }

    public static function getSendLocationFunc(){
        return __DIR__;
    }

    public static function markVisitors($id){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName('#__visitors'))
            ->set('`read`=1')
            ->where('id=' . $id);
        $db->setQuery($query);

        $db->execute();
    }

    public static function isUserOffline($user){
        $db = JFactory::getDbo();
        $query	= $db->getQuery(true)
            ->select($db->quoteName('last_visit'))
            ->from($db->quoteName('#__jshopping_users'))
            ->where("`user_id` = {$user}");
        $db->setQuery($query);
        $last_visit = $db->loadResult();

        $interval = 15*60;
        return (time() - strtotime($last_visit)) > $interval;
    }

    public static function getAge($birthday){
        $date = new DateTime();
        $dateWasBorn = new DateTime($birthday);
        $currentAge = $date->diff($dateWasBorn);

        return $currentAge->format('%y');
    }

    public static function getYearBirthday($age){
        $current_year = date("Y");
        $year_birthday = $current_year - $age;

        return $year_birthday;
    }

    public static function getDateDiffFormat($datetime, $online=""){
        if($online == ""){
            $online = JText::_('DATE_FORMAT_ONLINE');
        }
        $date_explode = explode(" ", $datetime);
        $date = explode("-", $date_explode[0]);
        $time = explode(":", $date_explode[1]);

        $year_action = $date[0];
        $month_action = $date[1];
        $day_action = $date[2];

        $hours_action = $time[0];
        $minutes_action = $time[1];
        $seconds_action = $time[2];

//        $date_explode = explode(" ", JFactory::getDate()->toSql());
        $date_explode = explode(" ", date("Y-m-d H:i:s"));
        $date = explode("-", $date_explode[0]);
        $time = explode(":", $date_explode[1]);

        $year_cur = $date[0];
        $month_cur = $date[1];
        $day_cur = $date[2];

        $hours_cur = $time[0];
        $minutes_cur = $time[1];
        $seconds_cur = $time[2];

        $diff_year = $year_cur - $year_action;
        $diff_month = $month_cur - $month_action;
        $diff_day = $day_cur - $day_action;
        $diff_hours = $hours_cur - $hours_action;
        $diff_minutes = $minutes_cur - $minutes_action;
        $diff_seconds = $seconds_cur - $seconds_action;

        $timestamp = mktime($hours_action,$minutes_action,$seconds_action,$month_action,$day_action,$year_action);

        $isset = false;
        if( $diff_day >= 7 || $diff_month >0 || $diff_year >0 ){
            $result = date("M d, Y", $timestamp);
            $isset = true;
        } else if( ($diff_day < 7) && ($diff_day > 1) && !$isset ){
            $result = $diff_day . " days ago";
            $isset = true;
        } else if( $diff_day == 1 && !$isset ){
            $result = JText::_('DATE_FORMAT_YESTERDAY');
            $isset = true;
        } else if( $diff_hours > 0 && !$isset ){
            $result = $diff_hours == 1 ? $diff_hours . JText::_('DATE_FORMAT_HOUR_AGO') : $diff_hours . JText::_('DATE_FORMAT_HOURS_AGO');
            $isset = true;
        } else if( $diff_minutes > 0 && !$isset ){
            $result = $diff_minutes == 1 ? $diff_minutes . JText::_('DATE_FORMAT_MINUTE_AGO') : $diff_minutes . JText::_('DATE_FORMAT_MINUTES_AGO');
            $isset = true;
        } else if( $diff_seconds > 0 && $diff_seconds < 30 && !$isset ){
            $result = $online;
            $isset = true;
        } else if( $diff_seconds > 0 && $diff_seconds > 30 && !$isset ){
            $result = $diff_seconds . JText::_('DATE_FORMAT_SECONDS_AGO');
            $isset = true;
        }
        return $result;
    }

    public static function getDateTimeDiffFormat($datetime){
        $datetime = date_create($datetime);
        $result = date_format($datetime, 'F jS, g:ia');

        return $result;
    }

    public static function getDateFormatMonthYearNumber($datetime){
        $datetime = date_create($datetime);
        $result = date_format($datetime, 'F j, Y');

        return $result;
    }

    public static function getMetaData($page){
        $db	= JFactory::getDbo();
        $query	= $db->getQuery(true)
            ->select( $db->quoteName(array('alias', 'title', 'keywords', 'description', 'header', 'content')) )
            ->from($db->quoteName('#__meta_data'))
            ->where("`page`='" . $page . "'");

        $db->setQuery($query);
        $result = $db->loadAssocList();

        return $result[0];
    }

    public static function calculateDistance($lat1, $lon1, $lat2, $lon2){
        $dist = 3958.0*acos( sin($lat1/57.29577951)*sin($lat2/57.29577951) + cos($lat1/57.29577951)*cos($lat2/57.29577951)*cos($lon1/57.29577951 - $lon2/57.29577951) );

        return round($dist);
    }

    public static function getExpiresDays($date){
        $date = explode(" ", $date);
        $start = strtotime($date[0]);
        $end = time();

        if ($end < $start) return ceil(abs($end - $start) / 86400);
        else return ceil(abs($end - $start) / 86400)*(-1);
    }

    public static function getExpires($datetime, $days){
        $date_explode = explode(" ", $datetime);
        $date = explode("-", $date_explode[0]);
        $day_action = $date[2];

        $date_explode = explode(" ", date("Y-m-d H:i:s"));
        $date = explode("-", $date_explode[0]);
        $day_cur = $date[2];

        $diff_day = $day_cur - $day_action;

        return ($days-$diff_day);
    }

    public static function existImage($path, $file){
        if(!file_exists(JPATH_ROOT . $path . $file) || $file == ""){
            return $path . "no-image.jpg";
        } else {
            return $path . $file;
        }

    }

    public static function getPagination($count_pages, $link, $page_active){
        $link = 'https://' . $_SERVER['SERVER_NAME'] . '/' . $link;
        if (strpos($link, '?')){
            $page = "&page=";
        } else {
            $page = "?page=";
        }
        $pagination = array();
        for($i=0; $i<$count_pages; $i++){
            if($i == 0){
                array_push($pagination, $link);
            } else {
                if (strpos($link, '?')) {
                    array_push($pagination, $link . $page . ($i+1));
                } else {
                    array_push($pagination, $link . $page . ($i+1));
                }

            }
        }

        $pagination_html = "";

        if(count($pagination) > 0){
            $pagination_html = '<div class="pagination">';

            if($page_active >= 2){
                $pagination_html .= '<div class="pagination_part">';
                $pagination_html .= '<a href="' . $link . '">' . JText::_('PAGINATION_FIRST') . '</a>';
            } /*else {
                $pagination_html .= '<span>' . JText::_('PAGINATION_FIRST') . '</span>';
            }*/

            if($page_active > 2){
                $pagination_html .= '<a href="' . $link . $page . ($page_active-1) . '">' . JText::_('PAGINATION_PREVIOUS') . '</a>';
                $pagination_html .= '</div>';
            } else if($page_active == 2){
                $pagination_html .= '<a href="' . $link . '">' . JText::_('PAGINATION_PREVIOUS') . '</a>';
                $pagination_html .= '</div>';
            }/* else {
                $pagination_html .= '<span>' . JText::_('PAGINATION_PREVIOUS') . '</span>';
            }*/

            $pagination_html .= '<div class="pagination_part">';
            foreach($pagination as $key => $value){
                if( ($page_active == $key+1) || ( ($key == 0) && ($page_active == $key) ) ){
                    $pagination_html .= '<span>' . ($key+1) . '</span>';
                } else {
                    $pagination_html .= '<a href="' . $value . '">' . ($key+1) . '</a>';
                }
            }
            $pagination_html .= '</div>';

            if($page_active == 0){
                $pagination_html .= '<div class="pagination_part">';
                $pagination_html .= '<a href="' . $link . $page . '2">' . JText::_('PAGINATION_NEXT') . '</a>';
            } else if($page_active < count($pagination)){
                $pagination_html .= '<div class="pagination_part">';
                $pagination_html .= '<a href="' . $link . $page . ($page_active+1) . '">' . JText::_('PAGINATION_NEXT') . '</a>';
            }

            if($page_active < count($pagination)){
                $pagination_html .= '<a href="' . $link . $page . (count($pagination)) . '">' . JText::_('PAGINATION_LAST') . '</a>';
                $pagination_html .= '</div>';
            }

            $pagination_html .= '</div>';
        }

        return $pagination_html;
    }

    public static function getHeaderMenu(){
        $modelMyProfile = JSFactory::getModel('user', 'jshop');
        $count_tokens = $modelMyProfile->getCountUserTokens(JSFactory::getUser()->user_id);

        $menu = '<div class="row">';
        $menu .= '<div class="col-md-8 col-md-offset-2 col-xs-12">';
        $menu .= '<div class="top_menu_container">';
        $menu .= '<div class="top_menu">';
        $menu .= '<a class="home" href="' . JText::_('LINK_MY_ACCOUNT') . '" title="' . JText::_('MENU_HOME') . '"></a>';
        $menu .= '<a class="get_together" href="' . JText::_('LINK_SPONSORS') . '" title="' . JText::_('MENU_GET_TOGETHER') . '"></a>';
        $menu .= '<a class="earn_tokens" href="' . JText::_('LINK_EARN_TOKENS') . '" title="' . JText::_('MENU_EARN_TOKENS') . '">' . '<span class="coins">' . $count_tokens . '</span>' . '</a>';
        $menu .= '<a class="logout" href="' . JText::_('LINK_LOG_OUT') . '" title="' . JText::_('MENU_EARN_LOGOUT') . '"></a>';
        $menu .= '</div>';
        $menu .= '</div>';
        $menu .= '</div>';
        $menu .= '</div>';

        if(($_REQUEST['controller'] != 'earntokens' && $_REQUEST['task'] != 'verify_status')){
            print $menu;
        } else {
            $session = JFactory::getSession();
            $user_sess = $session->get('user');
            if( isset($user_sess->username) && ($user_sess->username != null) ){
//            if( !isset($_SESSION['__default']['user']->username) || ($_SESSION['__default']['user']->username == null) ){
                print '';
            } else {
                print $menu;
            }
        }

    }

    public static function getComponentHeader(){
        if(JSFactory::getUser()->register_activate == 1){
            $modelMyProfile = JSFactory::getModel('user', 'jshop');
            $count_tokens = $modelMyProfile->getCountUserTokens(JSFactory::getUser()->user_id);

            $modelVisitors = JSFactory::getModel('visitors', 'jshop');
            $count_new_visitors = $modelVisitors->getProfileCountNewVisitors();

            $modelFriends = JSFactory::getModel('friends', 'jshop');
            $count_friends = $modelFriends->getCountFriends();

            $modelMessages = JSFactory::getModel('messaging', 'jshop');
            $count_new_messages = $modelMessages->getCountNewMessages();

            $modelUsersList = JSFactory::getModel('usersList', 'jshop');
            $searchParams = $modelUsersList->searchParamsCurrentUser(JSFactory::getUser());
            $first_user_from_list = $modelUsersList->usersList($searchParams, 0, 1, array('user_id'), array(), true);

            if(count($first_user_from_list) == 0){
                $quick_search_link = JText::_('LINK_USERS_LIST');
            } else {
                $quick_search_link = JText::_('LINK_USER_PAGE') . '?user=' .$first_user_from_list[0]->user_id;
            }

            $html = '<div class="header-links col-lg-8 col-sm-7 col-xs-12 visible-xs" style="background: rgb(65, 168, 220)">';
            $html .= '<ul class="nav menu_home">';
            $html .= '<li><a href="' . JText::_('LINK_USERS_LIST') . '" class="search">Search</a></li>';
//            $html .= '<li><a href="' . $quick_search_link . '" class="quick-search">Quick Connect</a></li>';
            $html .= '<li><a href="/partners">Cool4You</a></li>';
            $html .= '<li><a href="/sponsors">Linc Ups</a></li>';
            $html .= '</ul>';
            $html .= '</div>';

            $html .= '<div class="clr"></div>';


            $html .= '<div class="menu-top">';
                $html .= '<div class="flex-menu container visible-xs">';
                    $html .= '<div class="btn-group">';
                        $html .= '<button type="button" data-toggle="dropdown" class="menu-button dropdown-toggle"><i class="icon-menu"></i></button>';
                        $html .= '<ul class="dropdown-menu">';
                          $html .= '<button type="button" data-toggle="dropdown" class="menu-button dropdown-toggle">X</button>';

                            $html .= '<li><a href="' . JText::_('LINK_EARN_TOKENS') . '" title="Earn more Credits">';
                                $html .= '<span class="link-left"><span class="cool-count">';
                                if($count_tokens > 0){
                                    $html .= $count_tokens;
                                } else {
                                    $html .= 0;
                                }
                                $html .= '</span></span>';
                                $html .= '<span class="cool-menu-icons"><img src="/templates/protostar/images/system/token.png"></span>';
                                $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_TOKENS') . '</span>';
                                $html .= '';
                            $html .= '</a></li>';
                            $html .= '<li><a href="' . JText::_('LINK_VISITORS') . '">';
                                if($count_new_visitors > 0){
                                    $html .= '<span class="link-left"><span class="cool-count">'. $count_new_visitors . '</span></span>';
                                } else {
                                    $html .= '<span class="link-left hide_"><span class="cool-count">'. $count_new_visitors . '</span></span>';
                                }
                                $html .= '<span class="cool-menu-icons"><img src="/templates/protostar/images/system/cool_visitors_menu.png"></span>';
                                $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_VISITORS') . '</span>';
                                $html .= '';
                            $html .= '</a></li>';
                            $html .= '<li><a href="' . JText::_('LINK_FRIENDS') . '">';
//                                if($count_friends > 0){
//                                    $html .= '<span class="link-left"><span class="cool-count">'. $count_friends . '</span></span>';
//                                } else {
                                    $html .= '<span class="link-left hide_"><span class="cool-count">'. $count_friends . '</span></span>';
//                                }

                                $html .= '<span class="cool-menu-icons"><img src="/templates/protostar/images/system/connections_icon.png"></span>';
                                $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_CONNECTIONS') . '</span>';
                                $html .= '';
                            $html .= '</a></li>';
                            $html .= '<li><a href="' . JText::_('LINK_MY_BOOKMARKS') . '">';
                                $html .= '<span class="link-left hide_"><span class="cool-count"></span></span>';
                                $html .= '<span class="cool-menu-icons"><img src="/templates/protostar/images/system/bookmarks_icon.png"></span>';
                                $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_SAVED_PROFILES') . '</span></span>';
                                $html .= '';
                            $html .= '</a></li>';
                            $html .= '<li><a href="/member/logout">';
                                $html .= '<span class="link-left hide_"><span class="cool-count"></span></span>';
                                $html .= '<span class="cool-menu-icons"><img src="/templates/protostar/images/system/sign_out.png"></span>';
                                $html .= '<span class="cool-menu-text">Sign out</span></span>';
                                $html .= '';
                            $html .= '</a></li>';
                        $html .= '</ul>';
                    $html .= '</div>';



//                    --------------------------------------------------------------------------------------------------------

                    $html .= '<div class="menu-top-left">';
                        $html .= '<a href="' . JText::_('LINK_EDIT_ACCOUNT') . '" title="Settings">';
                            $html .= '<div class="link-top">';
                                $html .= '<img src="/templates/protostar/images/system/settings_icon.png">';
                            $html .= '</div>';
                            $html .= '<span class="cool-menu-text">Settings</span>';
                        $html .= '</a>';
                    $html .= '</div>';

                    $html .= '<div class="menu-top-center">';
                        $html .= '<span class="big">';
                            $html .= '<a href="' . JText::_('LINK_MY_ACCOUNT') . '" title="My Profile"> ' . JSFactory::getUser()->u_name . '</a>';
                        $html .= '</span>';
                        $html .= '<a class="home-link" href="' . JText::_('LINK_MY_ACCOUNT') . '">My Profile</a>';
                    $html .= '</div>';

                    $html .= '<div class="menu-top-right">';
                        $html .= '<a ';
                        if($count_new_messages > 0){
                            $html .= 'class="link-count"';
                        }
                        $html .= ' href="' . JText::_('LINK_MESSAGING_RECEIVED') . '" title="View your messages">';
                            $html .= '<div class="link-top">';
                                if($count_new_messages > 0){
                                    $html .= '<span class="cool-count">'. $count_new_messages . '</span>';
                                }
                                $html .= '<img style="width: 35px;" src="/templates/protostar/images/system/cool_messages.png">';
                            $html .= '</div>';
                            $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_MESSAGES') . '</span>';
                        $html .= '</a>';

                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';





// ---------------------------------------------------------------------------------------------------------------------
            $html .= '<div class="flex-menu container hidden-xs">';
                $html .= '<div class="menu-top-left">';
                    $html .= '<a href="' . JText::_('LINK_EDIT_ACCOUNT') . '" title="Settings">';
                        $html .= '<div class="link-top">';
                            $html .= '<img src="/templates/protostar/images/system/settings_icon.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">Settings</span>';
                    $html .= '</a>';
                $html .= '</div>';

                $html .= '<div class="menu-top-center">';
                    $html .= '<span class="big">';
                        $html .= '<a href="' . JText::_('LINK_MY_ACCOUNT') . '" title="My Profile"> ' . JSFactory::getUser()->u_name . '</a>';
                    $html .= '</span>';
                    $html .= '<a class="home-link" href="' . JText::_('LINK_MY_ACCOUNT') . '">My Profile</a>';
                $html .= '</div>';

                $html .= '<div class="menu-top-right hidden-xs">';
                    $html .= '<a href="' . JText::_('LINK_USERS_LIST') . '" title="Quick Connect">';
                        $html .= '<div class="link-top">';
                            $html .= '<img src="/templates/protostar/images/system/search_icon.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_SEARCH') . '</span>';
                    $html .= '</a>';
/*
                    $html .= '<a href="' . $quick_search_link . '" title="Quick Connect">';
                        $html .= '<div class="link-top">';
                            $html .= '<img src="/templates/protostar/images/system/saved_profiles_icon.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_QUICK_CONNECT') . '</span>';
                    $html .= '</a>';*/

                    $html .= '<a href="' . JText::_('LINK_MY_BOOKMARKS') . '" title="View Saved Profiles">';
                        $html .= '<div class="link-top">';
                            $html .= '<img src="/templates/protostar/images/system/bookmarks_icon.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_SAVED_PROFILES') . '</span>';
                    $html .= '</a>';

                    $html .= '<a href="' . JText::_('LINK_FRIENDS') . '" title="View Friends">';
                        $html .= '<div class="link-top">';
//                            $html .= '<span class="cool-count">'. $count_friends . '</span>';
                            $html .= '<img src="/templates/protostar/images/system/connections_icon.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_CONNECTIONS') . '</span>';
                    $html .= '</a>';

                    if($count_new_visitors > 0){
                        $html .= '<a class="link-count" href="' . JText::_('LINK_VISITORS') . '" title="View your visitors">';
                    } else {
                        $html .= '<a href="' . JText::_('LINK_VISITORS') . '" title="View your visitors">';
                    }
                        $html .= '<div class="link-top">';
                            if($count_new_visitors > 0){
                                $html .= '<span class="cool-count">'. $count_new_visitors . '</span>';
                            }
                            $html .= '<img src="/templates/protostar/images/system/cool_visitors_menu.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_VISITORS') . '</span>';
                    $html .= '</a>';

                    if($count_new_messages > 0){
                        $html .= '<a class="link-count" href="' . JText::_('LINK_MESSAGING_RECEIVED') . '" title="View your messages">';
                    } else {
                        $html .= '<a href="' . JText::_('LINK_MESSAGING_RECEIVED') . '" title="View your messages">';
                    }
                        $html .= '<div class="link-top">';
                            if($count_new_messages >0){
                                $html .= '<span class="cool-count">'. $count_new_messages . '</span>';
                            }
                            $html .= '<img style="width: 35px;" src="/templates/protostar/images/system/cool_messages.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_MESSAGES') . '</span>';
                    $html .= '</a>';

                    if($count_tokens>=0){
                        $html .= '<a class="link-count" href="' . JText::_('LINK_EARN_TOKENS') . '" title="Earn more Credits">';
                    } else {
                        $html .= '<a href="' . JText::_('LINK_EARN_TOKENS') . '" title="Earn more Credits">';
                    }
                        $html .= '<div class="link-top">';
                            if($count_tokens>0){
                                $html .= '<span class="cool-count">'. $count_tokens . '</span>';
                            } else {
                                $html .= '<span class="cool-count">0</span>';
                            }
                            $html .= '<img style="max-height: 33px; margin-top: -1px;" src="/templates/protostar/images/system/token.png">';
                        $html .= '</div>';
                        $html .= '<span class="cool-menu-text">' . JText::_('COOL_TOP_TOKENS') . '</span>';
                    $html .= '</a>';
                $html .= '</div>';

                $html .= '</div>';
            $html .= '</div>';
            print $html;
        }
    }

    public static function getContentMenu(){
        $menu = '<div class="clr"></div> <div class="content_menu">';
        $menu .= '<a href="' . JText::_('LINK_SUPPORT') . '">' . JText::_('COMPONENT_CONTENT_MENU_SUPPORT') . '</a>';
        $menu .= '<a href="' . JText::_('LINK_PRIVACY') . '">' . JText::_('COMPONENT_CONTENT_MENU_PRIVACY') . '</a>';
        $menu .= '<a href="' . JText::_('LINK_TERMS') . '">' . JText::_('COMPONENT_CONTENT_MENU_TERMS') . '</a>';
        $menu .= '<a href="' . JText::_('LINK_SPONSORS') . '">' . JText::_('COMPONENT_CONTENT_MENU_TOGETHER') . '</a>';
        $menu .= '<a href="' . JText::_('LINK_FRIENDS') . '">' . JText::_('COMPONENT_CONTENT_MENU_COOL_WITH') . '</a>';
        $menu .= '</div>';
        return $menu;
    }

    public static function getLinkPermission(){
        if( $_REQUEST['controller'] == 'earntokens' && $_REQUEST['task'] == 'verify_status' ) {

        } else {
            $session = JFactory::getSession();
            $user_sess = $session->get('user');
            if( !isset($user_sess->username) || ($user_sess->username == null) ){
                header('Location: /');
                exit;
            }

            if(JSFactory::getUser()->register_activate == 0 && ($_REQUEST['controller'] != 'member' || $_REQUEST['task'] != 'settings') && !($_REQUEST['controller'] == 'member' && $_REQUEST['task'] == 'logout') ){
                header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT'));
                exit;
            }

            if(JSFactory::getUser()->block == 1 && ($_REQUEST['controller'] != 'member' || $_REQUEST['task'] != null)  && !($_REQUEST['controller'] == 'member' && $_REQUEST['task'] == 'logout') && !($_REQUEST['controller'] == 'member' && $_REQUEST['task'] == 'settings') ){
                header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
                exit;
            }
        }
    }

}
?>