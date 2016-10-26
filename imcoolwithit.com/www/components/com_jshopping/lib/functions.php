<?php
/**
* @version      4.9.0 09.01.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

include_once(dirname(__FILE__)."/parse_string.php");
include_once(dirname(__FILE__)."/shop_item_menu.php");

function getJsFrontRequestController(){
    $controller = JRequest::getCmd('controller');
    if (!$controller){
        $controller = JRequest::getCmd('view');
        if ($controller){
            JRequest::setVar('controller', $controller);
        }
    }
    if (!$controller) $controller = "category";
    JDispatcher::getInstance()->trigger('onAfterGetJsFrontRequestController', array(&$controller));
return $controller;
}

function js_add_trigger($vars = array(), $name = ''){
    list(,$caller) = debug_backtrace();    
    JDispatcher::getInstance()->trigger('on'.ucfirst($caller['class']).ucfirst($caller['function']).ucfirst($name), array(&$caller['object'], &$vars));
    return $vars;
}

function setMetaData($title, $keyword, $description, $params=null){
    $config = JFactory::getConfig();
    $document =JFactory::getDocument();
    if ($title=='' && $params && $params->get('page_title')!=''){
        $title = $params->get('page_title');
    }
    if ($keyword=='' && $params && $params->get('menu-meta_keywords')!=''){
        $keyword = $params->get('menu-meta_keywords');
    }
    if ($description=='' && $params && $params->get('menu-meta_description')!=''){
        $description = $params->get('menu-meta_description');
    }
    if ($config->get('sitename_pagetitles')==1){
        $title = $config->get('sitename')." - ".$title;
    }
    if ($config->get('sitename_pagetitles')==2){
        $title = $title." - ".$config->get('sitename');
    }
    $document->setTitle($title);
    $document->setMetadata('keywords',$keyword);  
    $document->setMetadata('description',$description);
}

function parseArrayToParams($array) {
    $str = '';
    foreach ($array as $key => $value) {
        $str .= $key."=".$value."\n";
    }
    return $str;
}

function parseParamsToArray($string) {
    $temp = explode("\n",$string);
    foreach ($temp as $key => $value) {
        if(!$value) continue;
        $temp2 = explode("=",$value);
        $array[$temp2[0]] = $temp2[1];
    }
    return $array;
}

function getParseParamsSerialize($data){
    if ($data!=""){
        return unserialize($data);
    }else{
        return array();
    }
}

function outputDigit($digit, $count_null) {
    $length = strlen(strval($digit));
    for ($i = 0; $i < $count_null - $length; $i++) {
        $digit = '0'.$digit;
    }
    return $digit;
}

function splitValuesArrayObject($array_object,$property_name) {
    $return = '';
	if (is_array($array_object)){
		foreach($array_object as $key=>$value){
	        $return .= $array_object[$key]->$property_name.', ';
	    }
	    $return = "( ".substr($return,0,strlen($return) - 2)." )";
    }
    return $return;
}

function getTextNameArrayValue($names, $values){
    $return = '';
    foreach ($names as $key=>$value){
        $return .= $names[$key].": ".$values[$key]."\n";
    }
    return $return;
}

function strToHex($string){
    $hex='';
    for ($i=0;$i<strlen($string);$i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function hexToStr($hex){
    $string='';
    for ($i=0;$i<strlen($hex)-1;$i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function insertValueInArray($value, &$array) {
    if ($key = array_search($value, $array)) return $key;
    $array[$value] = $value;
    asort($array);
    return $key-1;
}

function appendExtendPathWay($array, $page) {
    $app =JFactory::getApplication();
    $pathway = $app->getPathway();
    JDispatcher::getInstance()->trigger('onBeforeAppendExtendPathWay', array(&$array, &$page, &$pathway));
    foreach($array as $cat){
        $pathway->addItem($cat->name, SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$cat->category_id, 1));
    }
    JDispatcher::getInstance()->trigger('onAfterAppendExtendPathWay', array(&$array, &$page, &$pathway));
}

function appendPathWay($page, $url = ""){
    $app =JFactory::getApplication();
    $pathway = $app->getPathway();
    JDispatcher::getInstance()->trigger('onBeforeAppendPathWay', array(&$page, &$url, &$pathway));
    if ($url!=""){
        $pathway->addItem($page, $url);
    }else{
        $pathway->addItem($page);
    }
    JDispatcher::getInstance()->trigger('onAfterAppendPathWay', array(&$page, &$url, &$pathway));
}

function getMainCurrencyCode(){
    $jshopConfig = JSFactory::getConfig();
    $currency = JSFactory::getTable('currency', 'jshop');
    $currency->load($jshopConfig->mainCurrency);
return $currency->currency_code;
}

function formatprice($price, $currency_code = null, $currency_exchange = 0, $style_currency = 0) {
    $jshopConfig = JSFactory::getConfig();

    if ($currency_exchange){
        $price = $price * $jshopConfig->currency_value;
    }
    if ($jshopConfig->formatprice_style_currency_span && $style_currency!=-1){
        $style_currency = 1;
    }
    if (!$currency_code) $currency_code = $jshopConfig->currency_code;
    $price = number_format($price, $jshopConfig->decimal_count, $jshopConfig->decimal_symbol, $jshopConfig->thousand_separator);
	if ($style_currency==1) $currency_code = '<span class="currencycode">'.$currency_code.'</span>';
	$return = str_replace("Symb", $currency_code, str_replace("00", $price, $jshopConfig->format_currency[$jshopConfig->currency_format]));
    extract(js_add_trigger(get_defined_vars(), "after"));
    return $return;
}

function formatEPrice($price){
    $jshopConfig = JSFactory::getConfig();
    return number_format($price, $jshopConfig->product_price_precision, '.', '');
}

function formatdate($date, $showtime = 0){
    $jshopConfig = JSFactory::getConfig();
    $format = $jshopConfig->store_date_format;
    if ($showtime) $format = $format." %H:%M:%S";
    return strftime($format, strtotime($date));
}

function formattax($val){
    $jshopConfig = JSFactory::getConfig();
    $val = floatval($val);
    return str_replace(".", $jshopConfig->decimal_symbol, $val);
}

function formatweight($val, $unitid = 0, $show_unit = 1){
    $jshopConfig = JSFactory::getConfig();
    if (!$unitid){
        $unitid = $jshopConfig->main_unit_weight;
    }
    $units = JSFactory::getAllUnits();
    $unit = $units[$unitid];
    if ($show_unit){
        $sufix = " ".$unit->name;
    }else{
        $sufix = "";
    }
    $val = floatval($val);
    return str_replace(".", $jshopConfig->decimal_symbol, $val).$sufix;
}

function formatqty($val){
    return floatval($val);
}

function sprintCurrency($id, $field = 'currency_code'){
    $all_currency = JSFactory::getAllCurrency();
return $all_currency[$id]->$field;
}

function sprintUnitWeight(){
    $jshopConfig = JSFactory::getConfig();
    $units = JSFactory::getAllUnits();
    $unit = $units[$jshopConfig->main_unit_weight];
return $unit->name;
}

/**
* get system language
* 
* @param int $client (0 - site, 1 - admin)
*/
function getAllLanguages($client=0){
    $pattern = '#(.*?)\(#is';
    $client	=JApplicationHelper::getClientInfo($client);
    $rows = array();
    jimport('joomla.filesystem.folder');
    $path = JLanguage::getLanguagePath($client->path);
    $dirs = JFolder::folders( $path );
    foreach($dirs as $dir){
        $files = JFolder::files( $path.'/'.$dir, '^([-_A-Za-z]*)\.xml$' );
        foreach($files as $file){
            $data = JApplicationHelper::parseXMLLangMetaFile($path.'/'.$dir.'/'.$file);
            $row = new StdClass();
            $row->descr = $data['name'];
            $row->language = substr($file,0,-4);
            $row->lang = substr($row->language,0,2);
            $row->name = $data['name'];
            preg_match($pattern, $row->name, $matches);
            if (isset($matches[1])) $row->name = trim($matches[1]);
            if (!is_array($data)) continue;
            $rows[] = $row;
        }
    }
    return $rows;
}

function installNewLanguages($defaultLanguage = "", $show_message = 1){
    $db =JFactory::getDBO();
    $jshopConfig = JSFactory::getConfig();
    $session =JFactory::getSession();
    $joomlaLangs = getAllLanguages();
    $checkedlanguage = $session->get('jshop_checked_language');
    if (is_array($checkedlanguage)){
        $newlanguages = 0;
        foreach($joomlaLangs as $lang){
            if (!in_array($lang->language, $checkedlanguage)) $newlanguages++;  
        }
        if ($newlanguages==0) return 0;
    }
    
    $query = "select * from #__jshopping_languages";
    $db->setQuery($query);
    $shopLangs = $db->loadObjectList();
    $shopLangsTag = array();
    foreach($shopLangs as $lang){
        $shopLangsTag[] = $lang->language;
    }

    if (!$defaultLanguage) $defaultLanguage = $jshopConfig->defaultLanguage;
    
    $checkedlanguage = array();
    $installed_new_lang = 0;
    
    foreach($joomlaLangs as $lang){
        $checkedlanguage[] = $lang->language;
        if (!in_array($lang->language, $shopLangsTag)){
            $ml = JSFactory::getLang();
            if ($ml->addNewFieldLandInTables($lang->language, $defaultLanguage)){
                $installed_new_lang = 1;
                $query = "insert into #__jshopping_languages set `language`='".$db->escape($lang->language)."', `name`='".$db->escape($lang->name)."', `publish`='1'";
                $db->setQuery($query);
                $db->query();
                if ($show_message){
                    JError::raiseNotice("", _JSHOP_INSTALLED_NEW_LANGUAGES.": ".$lang->name);
                }
            }
        }
    }       
    $session->set("jshop_checked_language", $checkedlanguage);
    return 1;
}

function recurseTree($cat, $level, $all_cats, &$categories, $is_select) {
    $probil = '';
    if($is_select) {
        for ($i = 0; $i < $level; $i++) {
            $probil .= '-- ';
        }
        $cat->name = ($probil . $cat->name);
        $categories[] = JHTML::_('select.option', $cat->category_id, $cat->name,'category_id','name' );
    } else {
        $cat->level = $level;
        $categories[] = $cat;
    }
    foreach ($all_cats as $categ) {
        if($categ->category_parent_id == $cat->category_id) {
            recurseTree($categ, ++$level, $all_cats, $categories, $is_select);
            $level--;
        }
    }
    return $categories;
}

function buildTreeCategory($publish = 1, $is_select = 1, $access = 1) {
	$jshopConfig = JSFactory::getConfig();
    $db =JFactory::getDBO();
    $lang = JSFactory::getLang();
    $user = JFactory::getUser();
    $where = array();
    if ($publish){
        $where[] = "category_publish = '1'";
    }
    if ($access){
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $where[] =' access IN ('.$groups.')';
    }
    $add_where = "";
    if (count($where)){
        $add_where = " where ".implode(" and ", $where);
    }
    $query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
           	  ".$add_where." ORDER BY category_parent_id, ordering";
    $db->setQuery($query);
    $all_cats = $db->loadObjectList();
    
    $categories = array();
	if(count($all_cats)) {
        foreach ($all_cats as $key => $value) {
            if(!$value->category_parent_id){
                recurseTree($value, 0, $all_cats, $categories, $is_select);
            }
        }
    }
    return $categories;
}

function _getCategoryParent($cat, $parent){
    $res = array();
    foreach($cat as $obj){
        if ($obj->category_parent_id == $parent) $res[] = $obj;
    } 
return $res;
}

function _getResortCategoryTree(&$cats, $allcats){
    foreach($cats as $k=>$v){
        $cats_sub = _getCategoryParent($allcats, $v->category_id);
        if (count($cats_sub)){
            _getResortCategoryTree($cats_sub, $allcats);
        }
        $cats[$k]->subcat = $cats_sub;
    }
}

/**
* get Tree category
* @param int $publish
* @return array
*/
function getTreeCategory($publish = 1, $access = 1){
    $jshopConfig = JSFactory::getConfig();
    $db =JFactory::getDBO();
    $user = JFactory::getUser();
    $lang = JSFactory::getLang();
    $where = array();
    if ($publish){
        $where[] = "category_publish = '1'";
    }
    if ($access){
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $where[] =' access IN ('.$groups.')';
    }
    $add_where = "";
    if (count($where)){
        $add_where = " where ".implode(" and ", $where);
    }
    $query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id FROM `#__jshopping_categories` ".$add_where." ORDER BY category_parent_id, ordering";
    $db->setQuery($query);
    $allcats = $db->loadObjectList();
        
    $cats = _getCategoryParent($allcats, 0);
    _getResortCategoryTree($cats, $allcats);
    
return $cats;
}

/**
* check date Format date yyyy-mm-dd
*/
function checkMyDate($date) {
    if (trim($date)=="") return false;
    $arr = explode("-",$date);
return checkdate($arr[1],$arr[2],$arr[0]);
}

function getThisURLMainPageShop(){
    $shopMainPageItemid = getShopMainPageItemid();
    $Itemid = JRequest::getInt("Itemid");
return ($shopMainPageItemid==$Itemid && $Itemid!=0);
}

function getShopMainPageItemid(){
static $Itemid;
    if (!isset($Itemid)){
        $shim = shopItemMenu::getInstance();
        $Itemid = $shim->getShop();
        if (!$Itemid){
            $Itemid = $shim->getProducts();
        }
    }
return $Itemid;
}

function getShopManufacturerPageItemid(){
static $Itemid;
    if (!isset($Itemid)){
        $shim = shopItemMenu::getInstance();
        $Itemid = $shim->getManufacturer();
    }
return $Itemid;
}

function getDefaultItemid(){
return getShopMainPageItemid();
}

function checkUserLogin(){
    $jshopConfig = JSFactory::getConfig();
    $user = JFactory::getUser();
    header("Cache-Control: no-cache, must-revalidate");
    if(!$user->id) {
        $mainframe =JFactory::getApplication();
        $return = base64_encode($_SERVER['REQUEST_URI']);
        $session =JFactory::getSession();
        $session->set("return", $return);
        $mainframe->redirect(SEFLink('index.php?option=com_jshopping&controller=member&task=login', 1, 1, $jshopConfig->use_ssl));
        exit();
    }
return 1;
}

function addLinkToProducts(&$products, $default_category_id = 0, $useDefaultItemId = 0){
    $jshopConfig = JSFactory::getConfig();
    foreach($products as $key=>$value){
        $category_id = (!$default_category_id)?($products[$key]->category_id):($default_category_id);
        if (!$category_id) $category_id = 0;
        $products[$key]->product_link = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category_id.'&product_id='.$products[$key]->product_id, $useDefaultItemId);
        $products[$key]->buy_link = '';
        if ($jshopConfig->show_buy_in_category && $products[$key]->_display_price){
            if (!($jshopConfig->hide_buy_not_avaible_stock && ($products[$key]->product_quantity <= 0))){
                $products[$key]->buy_link = SEFLink('index.php?option=com_jshopping&controller=cart&task=add&category_id='.$category_id.'&product_id='.$products[$key]->product_id, 1);
            }
        }
    }
}

function getJHost(){
    return $_SERVER["HTTP_HOST"];
}

function searchChildCategories($category_id,$all_categories,&$cat_search) {
    foreach ($all_categories as $all_cat) {
        if($all_cat->category_parent_id == $category_id) {
            searchChildCategories($all_cat->category_id, $all_categories, $cat_search);
            $cat_search[] = $all_cat->category_id;
        }
    }
}

/**
* set Sef Link
* 
* @param string $link
* @param int $useDefaultItemId - (0 - current itemid, 1 - shop page itemid, 2 -manufacturer itemid)
* @param int $redirect
*/
function SEFLink($link, $useDefaultItemId = 0, $redirect = 0, $ssl=null){
	$app = JFactory::getApplication();
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $dispatcher->trigger('onLoadJshopSefLink', array(&$link, &$useDefaultItemId, &$redirect, &$ssl));
    $defaultItemid = getDefaultItemid();
    if ($useDefaultItemId==2){
        $Itemid = getShopManufacturerPageItemid();
        if (!$Itemid) $Itemid = $defaultItemid;
    }elseif ($useDefaultItemId==1){
        $Itemid = $defaultItemid;
    }else{
        $Itemid = JRequest::getInt('Itemid');
        if (!$Itemid) $Itemid = $defaultItemid;
    }
    $dispatcher->trigger('onAfterLoadJshopSefLinkItemid', array(&$Itemid, &$link, &$useDefaultItemId, &$redirect, &$ssl));
	if (!preg_match('/Itemid=/', $link)){        
        if (!preg_match('/\?/', $link)) $sp = "?"; else $sp = "&";
        $link .= $sp.'Itemid='.$Itemid;
    }
   	$link = JRoute::_($link, (($redirect) ? (false) : (true)), $ssl);
	if ($app->isAdmin()){
        $link = str_replace('/administrator', '', $link);
    }
return $link;
}

function compareX64($a,$b){
return base64_encode($a)==$b;
}

function replaceNbsp($string) {
return (str_replace(" ","_",$string));
}

function replaceToNbsp($string) {
return (str_replace("_"," ",$string));
}

function replaceWWW($str){
return str_replace("www.","",$str);
}

function sprintRadioList($list, $name, $params, $key, $val, $actived = null, $separator = ' '){
    $html = "";
    $id = str_replace("[","",$name);
    $id = str_replace("]","",$id);
    foreach($list as $obj){
        $id_text = $id.$obj->$key;
        if ($obj->$key == $actived) $sel = ' checked="checked"'; else $sel = '';
        $html.='<span class="input_type_radio"><input type="radio" name="'.$name.'" id="'.$id_text.'" value="'.$obj->$key.'"'.$sel.' '.$params.'> <label for="'.$id_text.'">'.$obj->$val."</label></span>".$separator;
    }
return $html;
}

function saveToLog($file, $text){
    $jshopConfig = JSFactory::getConfig();
    if (!$jshopConfig->savelog) return 0;
    if ($file=='paymentdata.log' && !$jshopConfig->savelogpaymentdata) return 0;
    $f = fopen($jshopConfig->log_path.$file, "a+");
    fwrite($f, date('Y-m-d H:i:s')." ".$text."\r\n");
    fclose($f);
return 1;
}

function displayTextJSC(){
    $conf = JSFactory::getConfig();
    if (getJsFrontRequestController()!='content' && !compareX64(replaceWWW(getJHost()),$conf->licensekod)){
        print $conf->copyrightText;
    }
}

function filterHTMLSafe(&$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ){
    if (is_object( $mixed )){
        foreach (get_object_vars( $mixed ) as $k => $v){
            if (is_array( $v ) || is_object( $v ) || $v == NULL) {
                continue;
            }
            if (is_string( $exclude_keys ) && $k == $exclude_keys) {
                continue;
            } else if (is_array( $exclude_keys ) && in_array( $k, $exclude_keys )) {
                continue;
            }
            $mixed->$k = htmlspecialchars( $v, $quote_style, 'UTF-8' );
        }
    }
}

function saveAsPrice($val){
    $val = str_replace(",",".",$val);
    preg_match('/-?[0-9]+(\.[0-9]+)?/', $val, $matches);
return floatval($matches[0]);
}

function getPriceDiscount($price, $discount){
    return $price - ($price*$discount/100);
}

function getSeoSegment($str){
    return str_replace(":", "-", $str);
}

function setPrevSelLang($lang){
    $session =JFactory::getSession();
    $session->set("js_history_sel_lang", $lang);
}
function getPrevSelLang(){
    $session =JFactory::getSession();
    return $session->get("js_history_sel_lang");
}

function setFilterAlias($alias){
    $alias = str_replace(" ","-",$alias);
    $alias = (string) preg_replace('/[\x00-\x1F\x7F<>"\'$#%&\?\/\.\)\(\{\}\+\=\[\]\\\,:;]/', '', $alias);
    $alias = JString::strtolower($alias);
return $alias;
}

function showMarkStar($rating){
    $jshopConfig = JSFactory::getConfig();
    $count = floor($jshopConfig->max_mark / $jshopConfig->rating_starparts);
    $width = $count * 16;
    $rating = round($rating);
    $width_active = intval($rating * 16 / $jshopConfig->rating_starparts);
    $html = "<div class='stars_no_active' style='width:".$width."px'>";
    $html .= "<div class='stars_active' style='width:".$width_active."px'>";
    $html .= "</div>";
    $html .= "</div>";
return $html;
}

function getNameImageLabel($id, $type = 1){
static $listLabels;
    $jshopConfig = JSFactory::getConfig();
    if (!$jshopConfig->admin_show_product_labels) return "";
    if (!is_array($listLabels)){
        $productLabel = JSFactory::getTable('productLabel', 'jshop');
        $listLabels = $productLabel->getListLabels();
    }
    $obj = $listLabels[$id];
    if ($type==1)
        return $obj->image;
    else
        return $obj->name;
}

function getPriceFromCurrency($price, $currency_id = 0, $current_currency_value = 0){
    $jshopConfig = JSFactory::getConfig();
    if ($currency_id){
        $all_currency = JSFactory::getAllCurrency();
        $value = $all_currency[$currency_id]->currency_value;
        if (!$value) $value = 1;
        $pricemaincurrency = $price / $value;
    }else{
        $pricemaincurrency = $price;
    }
    if (!$current_currency_value){
        $current_currency_value = $jshopConfig->currency_value;
    }
return $pricemaincurrency * $current_currency_value;
}

function listProductUpdateData($products, $setUrl = 0){
    $jshopConfig = JSFactory::getConfig();
    $userShop = JSFactory::getUserShop();
    $taxes = JSFactory::getAllTaxes();
    if ($jshopConfig->product_list_show_manufacturer){
        $manufacturers = JSFactory::getAllManufacturer();
    }
    if ($jshopConfig->product_list_show_vendor){
        $vendors = JSFactory::getAllVendor();
    }
    if ($jshopConfig->show_delivery_time){
        $deliverytimes = JSFactory::getAllDeliveryTime();
    }

    $image_path = $jshopConfig->image_product_live_path;
    $noimage = $jshopConfig->noimage;
    
	JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher = JDispatcher::getInstance();    
	
    foreach($products as $key=>$value){
		$dispatcher->trigger('onListProductUpdateDataProduct', array(&$products, &$key, &$value));
		$use_userdiscount = 1;
		if ($jshopConfig->user_discount_not_apply_prod_old_price && $products[$key]->product_old_price>0) $use_userdiscount = 0;
        $products[$key]->_original_product_price = $products[$key]->product_price;
		$products[$key]->product_price_wp = $products[$key]->product_price;
        $products[$key]->product_price_default = 0;
        if ($jshopConfig->product_list_show_min_price){
            if ($products[$key]->min_price > 0) $products[$key]->product_price = $products[$key]->min_price;
        }
        $products[$key]->show_price_from = 0;
        if ($jshopConfig->product_list_show_min_price && $value->different_prices){
            $products[$key]->show_price_from = 1;
        }
		
        $products[$key]->product_price = getPriceFromCurrency($products[$key]->product_price, $products[$key]->currency_id);
        $products[$key]->product_old_price = getPriceFromCurrency($products[$key]->product_old_price, $products[$key]->currency_id);
        $products[$key]->product_price_wp = getPriceFromCurrency($products[$key]->product_price_wp, $products[$key]->currency_id);
		
        $products[$key]->product_price = getPriceCalcParamsTax($products[$key]->product_price, $products[$key]->tax_id);
        $products[$key]->product_old_price = getPriceCalcParamsTax($products[$key]->product_old_price, $products[$key]->tax_id);
		$products[$key]->product_price_wp = getPriceCalcParamsTax($products[$key]->product_price_wp, $products[$key]->tax_id);
        
        if ($userShop->percent_discount && $use_userdiscount){
            $products[$key]->product_price_default = $products[$key]->_original_product_price;
            $products[$key]->product_price_default = getPriceFromCurrency($products[$key]->product_price_default, $products[$key]->currency_id);
            $products[$key]->product_price_default = getPriceCalcParamsTax($products[$key]->product_price_default, $products[$key]->tax_id);

            $products[$key]->product_price = getPriceDiscount($products[$key]->product_price, $userShop->percent_discount);
            $products[$key]->product_old_price = getPriceDiscount($products[$key]->product_old_price, $userShop->percent_discount);
			$products[$key]->product_price_wp = getPriceDiscount($products[$key]->product_price_wp, $userShop->percent_discount);
        }
        
		if ($jshopConfig->list_products_calc_basic_price_from_product_price){
            $products[$key]->basic_price_info = getProductBasicPriceInfo($value, $products[$key]->product_price_wp);
        }else{
			$products[$key]->basic_price_info = getProductBasicPriceInfo($value, $products[$key]->product_price);
        }
		
        if ($value->tax_id){
            $products[$key]->tax = $taxes[$value->tax_id];
        }
        
        if ($jshopConfig->product_list_show_manufacturer && $value->product_manufacturer_id && isset($manufacturers[$value->product_manufacturer_id])){
            $products[$key]->manufacturer = $manufacturers[$value->product_manufacturer_id];
        }else{
            $products[$key]->manufacturer = new stdClass();
            $products[$key]->manufacturer->name = '';
        }        
        if ($jshopConfig->admin_show_product_extra_field){
            $products[$key]->extra_field = getProductExtraFieldForProduct($value);
        } else {
            $products[$key]->extra_field = '';
        }
        if ($jshopConfig->product_list_show_vendor){
            $vendordata = $vendors[$value->vendor_id];
            $vendordata->products = SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$vendordata->id,1);
            $products[$key]->vendor = $vendordata;
        }else{
            $products[$key]->vendor = '';
        }
        if ($jshopConfig->hide_delivery_time_out_of_stock && $products[$key]->product_quantity<=0){
            $products[$key]->delivery_times_id = 0;
            $value->delivery_times_id = 0;
        }
        if ($jshopConfig->show_delivery_time && $value->delivery_times_id){
            $products[$key]->delivery_time = $deliverytimes[$value->delivery_times_id];
        }else{
            $products[$key]->delivery_time = '';
        }
        $products[$key]->_display_price = getDisplayPriceForProduct($products[$key]->product_price);
        if (!$products[$key]->_display_price){
            $products[$key]->product_old_price = 0;
            $products[$key]->product_price_default = 0;
            $products[$key]->basic_price_info['price_show'] = 0;
            $products[$key]->tax = 0;
            $jshopConfig->show_plus_shipping_in_product = 0;
        }
        if ($jshopConfig->product_list_show_qty_stock){
            $products[$key]->qty_in_stock = getDataProductQtyInStock($products[$key]);
        }
        $image = getPatchProductImage($products[$key]->image, 'thumb');
        $products[$key]->product_name_image = $products[$key]->image;
        $products[$key]->product_thumb_image = $image;
        if (!$image) $image = $noimage;
        $products[$key]->image = $image_path."/".$image;
        $products[$key]->template_block_product = "product.php";
        if (!$jshopConfig->admin_show_product_labels) $products[$key]->label_id = null;
        if ($products[$key]->label_id){
            $image = getNameImageLabel($products[$key]->label_id);
            if ($image){
                $products[$key]->_label_image = $jshopConfig->image_labels_live_path."/".$image;
            }
            $products[$key]->_label_name = getNameImageLabel($products[$key]->label_id, 2);
        }
        if ($jshopConfig->display_short_descr_multiline){
            $products[$key]->short_description = nl2br($products[$key]->short_description);
        }
    }
    
    if ($setUrl){
        addLinkToProducts($products, 0, 1);
    }
      
    $dispatcher->trigger('onListProductUpdateData', array(&$products));
return $products;
}

function getProductBasicPriceInfo($obj, $price){
    $jshopConfig = JSFactory::getConfig();
    $price_show = $obj->weight_volume_units!=0;

    if (!$jshopConfig->admin_show_product_basic_price || $price_show==0){
        return array("price_show"=>0);
    }

    $units = JSFactory::getAllUnits();
    $unit = $units[$obj->basic_price_unit_id];
    $basic_price = $price / $obj->weight_volume_units * $unit->qty;

    return array("price_show"=>$price_show, "basic_price"=>$basic_price, "name"=>$unit->name, "unit_qty"=>$unit->qty);
}

function getProductExtraFieldForProduct($product){
    $fields = JSFactory::getAllProductExtraField();
    $fieldvalues = JSFactory::getAllProductExtraFieldValue();
    $displayfields = JSFactory::getDisplayListProductExtraFieldForCategory($product->category_id);
    $rows = array();
    foreach($displayfields as $field_id){
        $field_name = "extra_field_".$field_id;
        if ($fields[$field_id]->type==0){
            if ($product->$field_name!=0){
                $listid = explode(',', $product->$field_name);
                $tmp = array();
                foreach($listid as $extrafiledvalueid){
                    $tmp[] = $fieldvalues[$extrafiledvalueid];
                }
                $extra_field_value = implode(", ", $tmp);
                $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$extra_field_value);
            }
        }else{
            if ($product->$field_name!=""){
                $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$product->$field_name);
            }
        }
    }
return $rows;
}

function getPriceTaxRatioForProducts($products, $group='tax'){
    $prodtaxes = array();
    foreach($products as $k=>$v){
        if (!isset($prodtaxes[$v[$group]])) $prodtaxes[$v[$group]] = 0;
        $prodtaxes[$v[$group]]+= $v['price']*$v['quantity'];
    }
    $sumproducts = array_sum($prodtaxes);        
    foreach($prodtaxes as $k=>$v){
		if ($sumproducts>0){
			$prodtaxes[$k] = $v/$sumproducts;
		} else {
			$prodtaxes[$k] = 0;
		}
    }
return $prodtaxes;
}

function getFixBrutopriceToTax($price, $tax_id){
    $jshopConfig = JSFactory::getConfig();
    if ($jshopConfig->no_fix_brutoprice_to_tax==1){
        return $price;
    }
    $taxoriginal = JSFactory::getAllTaxesOriginal();
    $taxes = JSFactory::getAllTaxes();
    $tax = $taxes[$tax_id];
    $tax2 = $taxoriginal[$tax_id];
    if ($tax!=$tax2){
        $price = $price / (1 + $tax2 / 100);
        $price = $price * (1+$tax/100);    
    }
return $price;
}

function getPriceCalcParamsTax($price, $tax_id, $products=array()){
    $jshopConfig = JSFactory::getConfig();
    $taxes = JSFactory::getAllTaxes();
    if ($tax_id==-1){
        $prodtaxes = getPriceTaxRatioForProducts($products);
    }
    if ($jshopConfig->display_price_admin==0 && $tax_id>0){
        $price = getFixBrutopriceToTax($price, $tax_id);
    }
    if ($jshopConfig->display_price_admin==0 && $tax_id==-1){
        $prices = array();
        $prodtaxesid = getPriceTaxRatioForProducts($products,'tax_id');
        foreach($prodtaxesid as $k=>$v){            
            $prices[$k] = getFixBrutopriceToTax($price*$v, $k);
        }
        $price = array_sum($prices);
    }
    if ($tax_id>0){
        $tax = $taxes[$tax_id];
    }elseif ($tax_id==-1){
        $prices = array();
        foreach($prodtaxes as $k=>$v){
            $prices[] = array('tax'=>$k, 'price'=>$price*$v);
        }
    }else{
        $taxlist = array_values($taxes);
        $tax = $taxlist[0];
    }
    if ($jshopConfig->display_price_admin == 1 && $jshopConfig->display_price_front_current == 0){
        if ($tax_id==-1){
            $price = 0;
            foreach($prices as $v){
                $price+= $v['price'] * (1 + $v['tax'] / 100);
            }
        }else{
            $price = $price * (1 + $tax / 100);
        }
    }
    if ($jshopConfig->display_price_admin == 0 && $jshopConfig->display_price_front_current == 1){
        if ($tax_id==-1){
            $price = 0;
            foreach($prices as $v){
                $price+= $v['price'] / (1 + $v['tax'] / 100);
            }
        }else{
            $price = $price / (1 + $tax / 100);
        }
    }
return $price;
}

function changeDataUsePluginContent(&$data, $type){
    $mainframe =JFactory::getApplication();
    $dispatcher =JDispatcher::getInstance();
    JPluginHelper::importPlugin('content');
    $obj = new stdClass();
    $params = $mainframe->getParams('com_content');
    
    if ($type=="product"){
        $obj->product_id = $data->product_id;
    }
    if ($type=="category"){
        $obj->category_id = $data->category_id;
    }
    if ($type=="manufacturer"){
        $obj->manufacturer_id = $data->manufacturer_id;
    }
    if (!isset($data->name)) $data->name = '';
    $obj->text = $data->description;
    $obj->title = $data->name;
    $results = $dispatcher->trigger('onContentPrepare', array('com_content.article', &$obj, &$params, 0));
    $data->description = $obj->text;
    return 1;
}

function productTaxInfo($tax, $display_price = null){
    if (!isset($display_price)) {
        $jshopConfig = JSFactory::getConfig();
        $display_price = $jshopConfig->display_price_front_current;
    }
    if ($display_price==0){
        return sprintf(_JSHOP_INC_PERCENT_TAX, formattax($tax));
    }else{
        return sprintf(_JSHOP_PLUS_PERCENT_TAX, formattax($tax));
    }
}

function displayTotalCartTaxName($display_price = null){
    if (!isset($display_price)) {
        $jshopConfig = JSFactory::getConfig();
        $display_price = $jshopConfig->display_price_front_current;
    }
    if ($display_price==0){
        return _JSHOP_INC_TAX;
    }else{
        return _JSHOP_PLUS_TAX;
    }
}

function getPriceTaxValue($price, $tax, $price_netto = 0){
    if ($price_netto==0){
        $tax_value = $price * $tax / (100 + $tax);
    }else{
        $tax_value = $price * $tax / 100;
    }
return $tax_value;
}

function getCorrectedPriceForQueryFilter($price){
$jshopConfig = JSFactory::getConfig();

    $taxes = JSFactory::getAllTaxes();
    $taxlist = array_values($taxes);
    $tax = $taxlist[0];

    if ($jshopConfig->display_price_admin == 1 && $jshopConfig->display_price_front_current == 0){
        $price = $price / (1 + $tax / 100);
    }
    if ($jshopConfig->display_price_admin == 0 && $jshopConfig->display_price_front_current == 1){
        $price = $price * (1 + $tax / 100);
    }
    
    $price = $price / $jshopConfig->currency_value;
    return $price;
}

function updateAllprices( $ignore = array() ){
    $cart = JSFactory::getModel('cart', 'jshop');
    $cart->load();
    $cart->updateCartProductPrice();
    
    $sh_pr_method_id = $cart->getShippingPrId();
    if ($sh_pr_method_id){
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice', 'jshop');
        $shipping_method_price->load($sh_pr_method_id);
        $prices = $shipping_method_price->calculateSum($cart);
        $cart->setShippingsDatas($prices, $shipping_method_price);
    }
    $payment_method_id = $cart->getPaymentId();
    if ($payment_method_id){
        $paym_method = JSFactory::getTable('paymentmethod', 'jshop');
        $paym_method->load($payment_method_id);
        $paym_method->setCart($cart);
        $price = $paym_method->getPrice();
        $cart->setPaymentDatas($price, $paym_method);
    }
    
    $cart = JSFactory::getModel('cart', 'jshop');
    $cart->load('wishlist');
    $cart->updateCartProductPrice();   
}

function setNextUpdatePrices(){
    $session =JFactory::getSession();
    $session->set('js_update_all_price', 1);
}

function getMysqlVersion(){
    $session =JFactory::getSession();
    $mysqlversion = $session->get("js_get_mysqlversion");
    if ($mysqlversion ==""){
        $db = JFactory::getDBO(); 
        $query = "select version() as v";
        $db->setQuery($query);
        $mysqlversion = $db->loadResult();
        preg_match('/\d+\.\d+\.\d+/',$mysqlversion,$matches);
        $mysqlversion = $matches[0];
        $session->set("js_get_mysqlversion", $mysqlversion);
    }    
    return $mysqlversion;    
}

function filterAllowValue($data, $type){
    
    if ($type=="int+"){
        if (is_array($data)){
            foreach($data as $k=>$v){
                $v = intval($v);
                if ($v>0){
                    $data[$k] = $v;
                }else{
                    unset($data[$k]);
                }
            }
        }
    }
    
    if ($type=="array_int_k_v+"){
        if (is_array($data)){
            foreach($data as $k=>$v){
                $k = intval($k);
                if (is_array($v)){
                    foreach($v as $k2=>$v2){
                        $k2 = intval($k2);
                        $v2 = intval($v2);
                        if ($v2>0){
                            $data[$k][$k2] = $v2;
                        }else{
                            unset($data[$k][$k2]);
                        }
                    }
                }
            }
        }
    }
    
    return $data;
}

function getListFromStr($stelist){
    if (preg_match('/\,/', $stelist)){
        return filterAllowValue(explode(',',$stelist), 'int+');
    }else{
        return null;
    }
}

function willBeUseFilter($filters){
    $res = 0;    
    if (isset($filters['price_from']) && $filters['price_from']>0) $res = 1;
    if (isset($filters['price_to']) && $filters['price_to']>0) $res = 1;
    if (isset($filters['categorys']) && count($filters['categorys'])>0) $res = 1;
    if (isset($filters['manufacturers']) && count($filters['manufacturers'])>0) $res = 1;
    if (isset($filters['vendors']) && count($filters['vendors'])>0) $res = 1;    
    if (isset($filters['labels']) && count($filters['labels'])>0) $res = 1;
    if (isset($filters['extra_fields']) && count($filters['extra_fields'])>0) $res = 1;
    JDispatcher::getInstance()->trigger('onAfterWillBeUseFilterFunc', array(&$filters, &$res));
return $res;
}

/**
* spec function additional query for product list 
*/
function getQueryListProductsExtraFields(){
    $query = "";
    $list = JSFactory::getAllProductExtraField();
    $jshopConfig = JSFactory::getConfig();
    $config_list = $jshopConfig->getProductListDisplayExtraFields();
    foreach($list as $v){
        if (in_array($v->id, $config_list)){
            $query .= ", prod.`extra_field_".$v->id."` ";
        }
    }
return $query;
}

function getLicenseKeyAddon($alias){
static $keys;
    if (!isset($keys)) $keys = array();
    if (!isset($keys[$alias])){
        $addon = JSFactory::getTable('addon', 'jshop');
        $keys[$alias] = $addon->getKeyForAlias($alias);
    }
return $keys[$alias];
}

function getQuerySortDirection($fieldnum, $ordernum){
    $dir = "ASC";
    if ($ordernum) {
        $dir = "DESC";
        if ($fieldnum==5 || $fieldnum==6) $dir = "ASC";
    } else {
        $dir = "ASC";
        if ($fieldnum==5 || $fieldnum==6) $dir = "DESC";
    }
return $dir;
}

function getImgSortDirection($fieldnum, $ordernum){
    if ($ordernum) {
        $image = 'arrow_down.gif';
    } else {
        $image = 'arrow_up.gif';
    }
return $image;
}

function printContent(){
    $print = JRequest::getInt("print"); 
    $link =  str_replace("&", '&amp;', $_SERVER["REQUEST_URI"]);
    if (strpos($link,'?')===FALSE)
        $tmpl = "?tmpl=component&amp;print=1";
    else 
        $tmpl = "&amp;tmpl=component&amp;print=1";

    $html = '<div class="jshop_button_print">';
    if ($print==1)
        $html .= '<a onclick="window.print();return false;" href="#" title="'._JSHOP_PRINT.'"><img src="'.JURI::root().'components/com_jshopping/images/print.png" alt=""  /></a>';
    else
        $html .= '<a href="'.$link.$tmpl.'" title="'._JSHOP_PRINT.'" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;" rel="nofollow"><img src="'.JURI::root().'components/com_jshopping/images/print.png" alt=""  /></a>';
    $html .= '</div>';
    print $html;
}

function getPageHeaderOfParams(&$params){
    $header = "";
    if ($params->get('show_page_heading') && $params->get('page_heading')){
        $header = $params->get('page_heading');
    }
return $header;
}

function getMessageJson(){
   $errors = JError::getErrors();
   $rows = array();
   foreach($errors as $e){
      $message = str_replace("<br/>", "\n", $e->get('message'));
      $rows[] = array("level"=>$e->get('level'),"code"=>$e->get('code'), "message"=>$message);
   }
return json_encode($rows);
}

function getOkMessageJson($cart){
    $errors = JError::getErrors();
    if (count($errors)){
        return getMessageJson(); 
    }else{
        return json_encode($cart);
    }
}

function getAccessGroups(){
    $db = JFactory::getDBO(); 
    $query = "select id,title,rules from #__viewlevels order by ordering";
    $db->setQuery($query);
    $accessgroups = $db->loadObjectList();
return $accessgroups;
}

function getDisplayPriceShop(){
    $jshopConfig = JSFactory::getConfig();
    $user = JFactory::getUser();
    $display_price = 1;
    if ($jshopConfig->displayprice==1){
        $display_price = 0;
    }elseif($jshopConfig->displayprice==2 && !$user->id){
        $display_price = 0;
    }
return $display_price;
}

function getDisplayPriceForProduct($price){
    $jshopConfig = JSFactory::getConfig();
    $user = JFactory::getUser();
    $display_price = 1;
    if ($jshopConfig->displayprice==1){
        $display_price = 0;
    }elseif($jshopConfig->displayprice==2 && !$user->id){
        $display_price = 0;
    }
    if ($display_price && $price==0 && $jshopConfig->user_as_catalog){
        $display_price = 0;
    }
return $display_price;
}

function getDocumentType(){
    $document = JFactory::getDocument();
return $document->getType();
}

function sprintAtributeInCart($atribute){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $html = "";
    if (count($atribute)) $html .= '<div class="list_attribute">';
    foreach($atribute as $attr){
        $dispatcher->trigger('beforeSprintAtributeInCart', array(&$attr) );
        $html .= '<p class="jshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
    }
    if (count($atribute)) $html .= '</div>';
return $html;
}

function sprintFreeAtributeInCart($freeatribute){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $html = "";
    if (count($freeatribute)) $html .= '<div class="list_free_attribute">';
    foreach($freeatribute as $attr){
        $dispatcher->trigger('beforeSprintFreeAtributeInCart', array(&$attr) );
        $html .= '<p class="jshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
    }
    if (count($freeatribute)) $html .= '</div>';
return $html;
}

function sprintFreeExtraFiledsInCart($extra_fields){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $html = "";
    if (count($extra_fields)) $html .= '<div class="list_extra_field">';
    foreach($extra_fields as $f){
        $dispatcher->trigger('beforeSprintExtraFieldsInCart', array(&$f) );
        $html .= '<p class="jshop_cart_extra_field"><span class="name">'.$f['name'].'</span>: <span class="value">'.$f['value'].'</span></p>';
    }
    if (count($extra_fields)) $html .= '</div>';
return $html;
}

function sprintAtributeInOrder($atribute, $type="html"){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();    
    $dispatcher->trigger('beforeSprintAtributeInOrder', array(&$atribute, $type));
    if ($type=="html"){
        $html = nl2br($atribute);
    }else{
        $html = $atribute;
    }
return $html;
}

function sprintFreeAtributeInOrder($freeatribute, $type="html"){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $dispatcher->trigger('beforeSprintFreeAtributeInOrder', array(&$freeatribute, $type));
    if ($type=="html"){
        $html = nl2br($freeatribute);
    }else{
        $html = $freeatribute;
    }
return $html;
}

function sprintExtraFiledsInOrder($extra_fields, $type="html"){
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $dispatcher->trigger('beforeSprintExtraFieldsInOrder', array(&$extra_fields, $type));
    if ($type=="html"){
        $html = nl2br($extra_fields);
    }else{
        $html = $extra_fields;
    }
return $html;
}

function sprintBasicPrice($prod){
    if (is_object($prod)) $prod = (array)$prod;
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $dispatcher->trigger('beforeSprintBasicPrice', array(&$prod));
    $html = '';
    if ($prod['basicprice']>0){
        $html = formatprice($prod['basicprice'])." / ".$prod['basicpriceunit'];
    }
return $html;
}

function getDataProductQtyInStock($product){
    $qty = $product->product_quantity;
    if ($product instanceof jshopProduct){
        $qty = $product->getQty();
    }
    $qty = floatval($qty);
    $qty_in_stock = array("qty"=>$qty, "unlimited"=>$product->unlimited);
    if ($qty_in_stock['qty']<0) $qty_in_stock['qty'] = 0;
return $qty_in_stock;
}

function sprintQtyInStock($qty_in_stock){
    if (!is_array($qty_in_stock)){
        return $qty_in_stock;
    }else{
        if ($qty_in_stock['unlimited']){
            return _JSHOP_UNLIMITED;
        }else{
            return $qty_in_stock['qty'];
        }
    }
}

function getBuildFilterListProduct($contextfilter, $no_filter = array()){
    $mainframe =JFactory::getApplication();
    $jshopConfig = JSFactory::getConfig();
    
    $category_id = JRequest::getInt('category_id');
    $manufacturer_id = JRequest::getInt('manufacturer_id');
    $label_id = JRequest::getInt('label_id');
    $vendor_id = JRequest::getInt('vendor_id');
    $price_from = saveAsPrice(JRequest::getVar('price_from'));
    $price_to = saveAsPrice(JRequest::getVar('price_to'));
    
    $categorys = $mainframe->getUserStateFromRequest( $contextfilter.'categorys', 'categorys', array());
    $categorys = filterAllowValue($categorys, "int+");
    $tmpcd = getListFromStr(JRequest::getVar('category_id'));    
    if (is_array($tmpcd) && !$categorys) $categorys = $tmpcd;
    
    $manufacturers = $mainframe->getUserStateFromRequest( $contextfilter.'manufacturers', 'manufacturers', array());
    $manufacturers = filterAllowValue($manufacturers, "int+");
    $tmp = getListFromStr(JRequest::getVar('manufacturer_id'));    
    if (is_array($tmp) && !$manufacturers) $manufacturers = $tmp;
    
    $labels = $mainframe->getUserStateFromRequest( $contextfilter.'labels', 'labels', array());
    $labels = filterAllowValue($labels, "int+");
    $tmplb = getListFromStr(JRequest::getVar('label_id'));    
    if (is_array($tmplb) && !$labels) $labels = $tmplb;
    
    $vendors = $mainframe->getUserStateFromRequest( $contextfilter.'vendors', 'vendors', array());
    $vendors = filterAllowValue($vendors, "int+");
    $tmp = getListFromStr(JRequest::getVar('vendor_id'));    
    if (is_array($tmp) && !$vendors) $vendors = $tmp;
    
    if ($jshopConfig->admin_show_product_extra_field){
        $extra_fields = $mainframe->getUserStateFromRequest( $contextfilter.'extra_fields', 'extra_fields', array());
        $extra_fields = filterAllowValue($extra_fields, "array_int_k_v+");
    }
    $fprice_from = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_from', 'fprice_from');
    $fprice_from = saveAsPrice($fprice_from);
    if (!$fprice_from) $fprice_from = $price_from;
    $fprice_to = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_to', 'fprice_to');
    $fprice_to = saveAsPrice($fprice_to);
    if (!$fprice_to) $fprice_to = $price_to;
    
    $filters = array();
    $filters['categorys'] = $categorys;
    $filters['manufacturers'] = $manufacturers;
    $filters['price_from'] = $fprice_from;
    $filters['price_to'] = $fprice_to;
    $filters['labels'] = $labels;
    $filters['vendors'] = $vendors;
    if ($jshopConfig->admin_show_product_extra_field){
        $filters['extra_fields'] = $extra_fields;
    }
    if ($category_id && !$filters['categorys']){
        $filters['categorys'][] = $category_id;
    }
    if ($manufacturer_id && !$filters['manufacturers']){
        $filters['manufacturers'][] = $manufacturer_id;
    }
    if ($label_id && !$filters['labels']){
        $filters['labels'][] = $label_id;
    }
    if ($vendor_id && !$filters['vendors']){
        $filters['vendors'][] = $vendor_id;
    }
    if (is_array($filters['vendors'])){
        $main_vendor = JSFactory::getMainVendor();
        foreach($filters['vendors'] as $vid){
            if ($vid == $main_vendor->id){
                $filters['vendors'][] = 0;
            }
        }
    }
    foreach($no_filter as $filterkey){
        unset($filters[$filterkey]);
    }
    JPluginHelper::importPlugin('jshoppingproducts');
    $dispatcher =JDispatcher::getInstance();
    $dispatcher->trigger('afterGetBuildFilterListProduct', array(&$filters));
return $filters;
}

function fixRealVendorId($id){
    if ($id==0){
        $mainvendor = JSFactory::getMainVendor();
        $id = $mainvendor->id;
    }
return $id;
}

function xhtmlUrl($url, $filter=1){
    if ($filter){
        $url = jsFilterUrl($url);
    }
    $url = str_replace("&","&amp;",$url);    
return $url;
}

function jsFilterUrl($url){
    $url = strip_tags($url);
return $url;
}

function getJsDate($date = 'now', $format='Y-m-d H:i:s', $local = true){
    $config = JFactory::getConfig();
    $date = JFactory::getDate($date, $config->get('offset'));
return $date->format($format, $local);
}

function getCalculateDeliveryDay($day, $date=null){
    if (!$date){
        $date = getJsDate();
    }
    $time = intval(strtotime($date) + $day*86400);
return date('Y-m-d H:i:s', $time);
}

function datenull($date){
return (substr($date,0,1)=="0");
}

function file_get_content_curl($url, $timeout = 5){
    if (function_exists('curl_init')){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }else{
        return null;
    }
}

function getJsDateDB($str, $format='%d.%m.%Y'){
    $f = str_replace(array("%d","%m","%Y"), array('dd','mm','yyyy'), $format);
    $pos = array(strpos($f, 'y'),strpos($f, 'm'),strpos($f, 'd'));
    $date = substr($str, $pos[0], 4).'-'.substr($str, $pos[1], 2).'-'.substr($str, $pos[2], 2);
return $date;
}
function getDisplayDate($date, $format='%d.%m.%Y'){
    if (datenull($date)){
        return '';
    }
    $adate = array(substr($date, 0, 4), substr($date, 5, 2), substr($date, 8, 2));
    $str = str_replace(array("%Y","%m","%d"), $adate, $format);
return $str;
}
function getPatchProductImage($name, $prefix = '', $pathtype = 0){
    $jshopConfig = JSFactory::getConfig();
    if ($name==''){
        return '';
    }
    if ($prefix!=''){
        $name = $prefix."_".$name;
    }
    if ($pathtype==1){
//        $name = $jshopConfig->image_product_live_path."/".$name;
        $name = $jshopConfig->image_product_path."/".$name;
    }
    if ($pathtype==2){
//        $name = $jshopConfig->image_product_path."/".$name;
        $name = $jshopConfig->image_product_path_medium."/".$name;
    }
    if ($pathtype==3){
        $name = $jshopConfig->image_product_path_small."/".$name;
    }
return $name;
}

function getPatchProductImages($name, $prefix = ''){
    $jshopConfig = JSFactory::getConfig();
    if ($name==''){
        return '';
    }
    if ($prefix!=''){
        $name = $prefix."/".$name;
    }

    $name = $jshopConfig->image_product_path_site."/".$name;

    return $name;
}

function getDBFieldNameFromConfig($name){
    $lang = JSFactory::getLang();
    $tmp = explode('.', $name);
    if (count($tmp)>1){
        $res = $tmp[0].'.';
        $field = $tmp[1];
    }else{
        $res = '';
        $field = $tmp[0];
    }
    $tmp2 = explode(':', $field);
    if (count($tmp2)>1 && $tmp2[0]=='ml'){
        $res .= '`'.$lang->get($tmp2[1]).'`';
    }else{
        $res .= '`'.$field.'`';
    }
return $res;
}

function json_value_encode($val, $textfix = 0){
    if ($textfix){
        $val = str_replace(array("\n","\r","\t"), "", $val);
    }

    $val = str_replace('"', '\"', $val);
    return $val;
}

function dataTimeLastVisitUser(){
    $userTable = JSFactory::getTable('userShop', 'jshop');
    $userTable->insertDataTimeToUser();
}

?>