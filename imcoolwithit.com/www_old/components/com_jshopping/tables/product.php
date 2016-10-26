<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProduct extends JTableAvto{

    function __construct(&$_db){
        parent::__construct('#__jshopping_products', 'product_id', $_db);
        JPluginHelper::importPlugin('jshoppingproducts');
    }
    
    function setAttributeActive($attribs){
        $jshopConfig = JSFactory::getConfig();
        $this->attribute_active = $attribs;
        if (is_array($this->attribute_active) && count($this->attribute_active)){
            $this->attribute_active_data = new stdClass();
            $allattribs = JSFactory::getAllAttributes(1);
            $dependent_attr = array();
            $independent_attr = array();
            foreach($attribs as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }else{
                    $independent_attr[$k] = $v;
                }
            }
            
            if (count($dependent_attr)){
                $where = "";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and PA.attr_".$k." = '".$this->_db->escape($v)."' ";
                }
                $query = "select PA.* from `#__jshopping_products_attr` as PA where PA.product_id = '".$this->_db->escape($this->product_id)."' ".$where; 
                $this->_db->setQuery($query);
                $this->attribute_active_data = $this->_db->loadObject();
                if ($jshopConfig->use_extend_attribute_data && $this->attribute_active_data->ext_attribute_product_id){
                    $this->attribute_active_data->ext_data = $this->getExtAttributeData($this->attribute_active_data->ext_attribute_product_id);
                }
            }
            
            if (count($independent_attr)){
				if (!isset($this->attribute_active_data)) $this->attribute_active_data = new stdClass();
                if (!isset($this->attribute_active_data->price)) $this->attribute_active_data->price = $this->product_price;
                foreach($independent_attr as $k=>$v){
                    $query = "select addprice, price_mod from #__jshopping_products_attr2 where product_id='".$this->_db->escape($this->product_id)."' and attr_id='".$this->_db->escape($k)."' and attr_value_id='".$this->_db->escape($v)."'";
                    $this->_db->setQuery($query);
                    $attr_data2 = $this->_db->loadObject();
                    if (count($attr_data2) > 0){
                        if ($attr_data2->price_mod=="+"){
                            $this->attribute_active_data->price += $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="-"){
                            $this->attribute_active_data->price -= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="*"){
                            $this->attribute_active_data->price *= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="/"){
                            $this->attribute_active_data->price /= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="%"){
                            $this->attribute_active_data->price *= $attr_data2->addprice/100;
                        }elseif ($attr_data2->price_mod=="="){
                            $this->attribute_active_data->price =  $attr_data2->addprice;
                        }
                    }
                }
            }
        }else{
            $this->attribute_active_data = NULL;
        }
		$dispatcher = JDispatcher::getInstance();			
		$dispatcher->trigger('onAfterSetAttributeActive', array(&$attribs, &$this));
    }
    
    function setFreeAttributeActive($freattribs){
        $this->free_attribute_active = $freattribs;
    }
    
    function getData($field){
        if (isset($this->attribute_active_data->ext_data) && isset($this->attribute_active_data->ext_data->$field)){
            return $this->attribute_active_data->ext_data->$field;
        }else{
            return $this->$field;
        }
    }
    
    //get require attribute
    function getRequireAttribute(){
        $require = array();
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_attributes) return $require;

        $allattribs = JSFactory::getAllAttributes(2);
        $dependent_attr = $allattribs['dependent'];
        $independent_attr = $allattribs['independent'];
        
        if (count($dependent_attr)){
            $prodAttribVal = $this->getAttributes();
            if (count($prodAttribVal)){
                $prodAtrtib = $prodAttribVal[0];
                foreach($dependent_attr as $attrib){
                    $field = "attr_".$attrib->attr_id;
                    if ($prodAtrtib->$field) $require[] = $attrib->attr_id;
                }
            }
        }
        
        if (count($independent_attr)){
            $prodAttribVal2 = $this->getAttributes2();
            foreach($prodAttribVal2 as $attrib){
                if (!in_array($attrib->attr_id, $require)){
                    $require[] = $attrib->attr_id;
                }
            }
        }

        return $require;
    }
    
    //get dependent attributs
    function getAttributes(){
        $query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id = '".$this->product_id."' ORDER BY product_attr_id";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    
    //get independent attributs
    function getAttributes2(){
        $query = "SELECT * FROM `#__jshopping_products_attr2` WHERE product_id = '".$this->product_id."' ORDER BY id";
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterQueryGetAttributes2', array(&$query));
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }   
    
    //get attrib values
    function getAttribValue($attr_id, $other_attr = array(), $onlyExistProduct = 0){
        $jshopConfig = JSFactory::getConfig();
        $allattribs = JSFactory::getAllAttributes(1);
        $lang = JSFactory::getLang();
        if ($allattribs[$attr_id]->independent==0){
            $where = "";
            foreach($other_attr as $k=>$v){
                $where.=" and PA.attr_$k='$v'";
            }
            if ($onlyExistProduct) $where.=" and PA.count>0 ";
            $sorting = $jshopConfig->attribut_dep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
            $field = "attr_".$attr_id;
            $query = "SELECT distinct PA.$field as val_id, V.`".$lang->get("name")."` as value_name, V.image
                      FROM `#__jshopping_products_attr` as PA INNER JOIN #__jshopping_attr_values as V ON PA.$field=V.value_id
                      WHERE PA.product_id = '".$this->product_id."' ".$where."
                      ORDER BY ".$sorting;
        }else{
            $sorting = $jshopConfig->attribut_nodep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
            $query = "select PA.attr_value_id as val_id, V.`".$lang->get("name")."` as value_name, V.image, price_mod, addprice 
                      from #__jshopping_products_attr2 as PA INNER JOIN #__jshopping_attr_values as V ON PA.attr_value_id=V.value_id
                      where PA.product_id = '".$this->product_id."' and PA.attr_id='".$attr_id."'
                      ORDER BY ".$sorting;
        }
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    
    function getAttributesDatas($selected = array()){
        $jshopConfig = JSFactory::getConfig();
        $data = array('attributeValues'=>array());
        $requireAttribute = $this->getRequireAttribute();
        $actived = array();
        foreach($requireAttribute as $attr_id){
            $options = $this->getAttribValue($attr_id, $actived, $jshopConfig->hide_product_not_avaible_stock);
            $data['attributeValues'][$attr_id] = $options;
            if (!$jshopConfig->product_attribut_first_value_empty){
                $actived[$attr_id] = $options[0]->val_id;
            }
            if (isset($selected[$attr_id])){
                $testActived = 0;
                foreach($options as $tmp) if ($tmp->val_id==$selected[$attr_id]) $testActived = 1;
                if ($testActived){
                    $actived[$attr_id] = $selected[$attr_id];
                }
            }
        }
        if (count($requireAttribute) == count($actived)){
            $data['attributeActive'] = $actived;
        }else{
            $data['attributeActive'] = array();
        }
        $data['attributeSelected'] = $actived;
    return $data;
    }
    
    function getPIDCheckQtyValue(){
        if (isset($this->attribute_active_data->product_attr_id)){
            return "A:".$this->attribute_active_data->product_attr_id;
        }else{
            return "P:".$this->product_id;
        }
    }
    
    function getListFreeAttributes(){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $query = "SELECT FA.id, FA.required, FA.`".$lang->get("name")."` as name, FA.`".$lang->get("description")."` as description, FA.type FROM `#__jshopping_products_free_attr` as PFA left join `#__jshopping_free_attr` as FA on FA.id=PFA.attr_id
                  WHERE PFA.product_id = '".$db->escape($this->product_id)."' order by FA.ordering";
        $this->_db->setQuery($query);
        $this->freeattributes = $this->_db->loadObjectList();
        return $this->freeattributes;
    }
    
    /**
    * use after getListFreeAttributes()
    */
    function getRequireFreeAttribute(){
        $rows = array();
        if ($this->freeattributes){
            foreach($this->freeattributes as $k=>$v){
                if ($v->required){
                    $rows[] = $v->id;
                }
            }
        }
    return $rows;
    }

    function getCategories($type_result = 0){
        if (!isset($this->product_categories)){
            $db = JFactory::getDBO(); 
            $query = "SELECT * FROM `#__jshopping_products_to_categories` WHERE product_id='".$db->escape($this->product_id)."'";
            $db->setQuery($query);
            $this->product_categories = $db->loadObjectList();
        }
        if ($type_result==1){
            $cats = array();
            foreach($this->product_categories as $v){
                $cats[] = $v->category_id;
            }
            return $cats;
        }else{
            return $this->product_categories;
        }
    }

    function getName() {
        $lang = JSFactory::getLang();
        $name = $lang->get('name');
        return $this->$name;
    }

    function getPriceWithParams(){
        if (isset($this->attribute_active_data->price)){
            return $this->attribute_active_data->price;
        }else{
            return $this->product_price;
        }
    }
    
    function getEan(){   
        if (isset($this->attribute_active_data->ean)){
            return $this->attribute_active_data->ean;
        }else{
            return $this->product_ean;
        }
    }
    
    function getQty(){
        if ($this->unlimited) return 1;
        if (isset($this->attribute_active_data->count)){
            return $this->attribute_active_data->count;
        }else{
            return $this->product_quantity;
        }
    }
    
    function getWeight(){
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->weight)){
            return $this->attribute_active_data->weight;
        }else{
            return $this->product_weight;
        }
    }
    
    function getWeight_volume_units(){
        if (isset($this->attribute_active_data->weight_volume_units) && $this->attribute_active_data->weight_volume_units > 0){
            return $this->attribute_active_data->weight_volume_units;
        }else{
            return $this->weight_volume_units;
        }
    }
    
    function getQtyInStock(){
        if ($this->unlimited) return 1;
        $qtyInStock = floatval($this->getQty());
        if ($qtyInStock < 0) $qtyInStock = 0;
    return $qtyInStock;
    }
    
    function getOldPrice(){
        if (isset($this->attribute_active_data->old_price)){
            return $this->attribute_active_data->old_price;
        }else{
            return $this->product_old_price;
        }    
    }

    function getImages(){
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
            $list = $this->attribute_active_data->ext_data->getImages();
            if (count($list)){
                return $list;
            }
        }
		
        $query = "SELECT I.*, IF(P.image=I.image_name,0,1) as sort FROM `#__jshopping_products_images` as I left join `#__jshopping_products` as P on P.product_id=I.product_id
                 WHERE I.product_id = '".$this->_db->escape($this->product_id)."' ORDER BY sort, I.ordering";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObjectList();
        foreach($list as $k=>$v){
            $title = $v->name;
            if (!$title){
                $title = $this->getName();
            }
            $list[$k]->_title = $title;
            $list[$k]->image_thumb = getPatchProductImage($v->image_name, '', 3);
            $list[$k]->image_full = getPatchProductImage($v->image_name, '', 1);
        }
    return $list;
    }

    function getVideos(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_video) return array();
        
        $query = "SELECT  video_name, video_id, video_preview, video_code FROM `#__jshopping_products_videos` WHERE product_id = '".$this->_db->escape($this->product_id)."'";
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    
    function getFiles(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_files) return array();
		if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getFiles();
			if (count($list)){
                return $list;
            }
		}
		$query = "SELECT * FROM `#__jshopping_products_files` WHERE product_id = '".$this->_db->escape($this->product_id)."' order by `ordering` ";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
    }
    
    function getDemoFiles(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getDemoFiles();
		}
        $query = "SELECT * FROM `#__jshopping_products_files` WHERE product_id = '".$this->_db->escape($this->product_id)."' and demo!='' order by `ordering` ";
        $this->_db->setQuery($query);
		$list0 = $this->_db->loadObjectList();
        return array_merge($list0, $list);
    }
    
    function getSaleFiles(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getSaleFiles();
		}
        $query = "SELECT id, file, file_descr FROM `#__jshopping_products_files` WHERE product_id = '".$this->_db->escape($this->product_id)."' and file!='' order by `ordering` ";
        $this->_db->setQuery($query);
		$list0 = $this->_db->loadObjectList();
        return array_merge($list0, $list);
    }
    
    function getManufacturerInfo(){
        $manufacturers = JSFactory::getAllManufacturer();
        if ($this->product_manufacturer_id && isset($manufacturers[$this->product_manufacturer_id])){
            return $manufacturers[$this->product_manufacturer_id];
        }else{
            return null;
        }
    }
    
    function getVendorInfo(){
        $vendors = JSFactory::getAllVendor();
        if (isset($vendors[$this->vendor_id])){
            return $vendors[$this->vendor_id];
        }else{
            return null;
        }
    }

    /**
    * get first catagory for product
    */    
    function getCategory() {
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query =' AND cat.access IN ('.$groups.')';
        $query = "SELECT pr_cat.category_id FROM `#__jshopping_products_to_categories` AS pr_cat
                LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                WHERE pr_cat.product_id = '".$this->_db->escape($this->product_id)."' AND cat.category_publish='1' ".$adv_query." LIMIT 0,1";
        $this->_db->setQuery($query);
        $this->category_id = $this->_db->loadResult();
        return $this->category_id;
    }
    
    function getFullQty(){
        if ($this->unlimited) return 1;
        $db = JFactory::getDBO();
        $query = "select count(*) as countattr, SUM(count) AS qty from `#__jshopping_products_attr` where product_id='".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $tmp = $db->loadObject();
        if ($tmp->countattr>0){
            return $tmp->qty;
        }else{
            return $this->product_quantity;
        }
    }
    
    function getMinimumPrice(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $min_price = $this->product_price;

        $query = "select count(*) as countattr, MIN(price) AS min_price from `#__jshopping_products_attr` where product_id='".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $tmp = $db->loadObject();
        if ($tmp->countattr>0){
            $min_price = $tmp->min_price;
        }
        
        $query = "select * from `#__jshopping_products_attr2` where product_id='".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $product_attr_ind = $db->loadObjectList();
        if ($product_attr_ind){
            $tmpprice = array();
            foreach($product_attr_ind as $key=>$val){
                if ($val->price_mod=="+"){
                    $tmpprice[] = $min_price + $val->addprice;
                }elseif ($val->price_mod=="-"){
                    $tmpprice[] = $min_price - $val->addprice;
                }elseif ($val->price_mod=="*"){
                    $tmpprice[] = $min_price * $val->addprice;
                }elseif ($val->price_mod=="/"){
                    $tmpprice[] = $min_price / $val->addprice;
                }elseif ($val->price_mod=="%"){
                    $tmpprice[] = $min_price * $val->addprice / 100;
                }elseif ($val->price_mod=="="){
                    $tmpprice[] = $val->addprice;
                }
            }
            $min_price = min($tmpprice);
        }

        $query = "select MAX(discount) as max_discount from `#__jshopping_products_prices` where product_id='".$db->escape($this->product_id)."'";
        $db->setQuery($query);
        $max_discount = $db->loadResult();
        if ($max_discount){
            if ($jshopConfig->product_price_qty_discount == 1){
                $min_price = $min_price - $max_discount;
            }else{
                $min_price = $min_price - ($min_price * $max_discount / 100);
            }
        }
        return $min_price;
    }
    
    function getExtendsData() {
        $this->getRelatedProducts();
        $this->getDescription();
        $this->getTax();
        $this->getPricePreview();
        $this->getDeliveryTime();
    }
    
    function getDeliveryTimeId($globqty = 0){
        $jshopConfig = JSFactory::getConfig();
        if ($globqty){
            $qty = $this->product_quantity;
        }else{
            $qty = $this->getQty();
        }
        if ($jshopConfig->hide_delivery_time_out_of_stock && $qty<=0){
            $this->delivery_times_id = 0;
        }
        return $this->delivery_times_id;
    }
    
    function getDeliveryTime($globqty = 0){
        $jshopConfig = JSFactory::getConfig();
        $dti = $this->getDeliveryTimeId($globqty);
        if ($jshopConfig->show_delivery_time && $dti){
            $deliveryTimes = JSFactory::getTable('deliveryTimes', 'jshop');
            $deliveryTimes->load($dti);
            $this->delivery_time = $deliveryTimes->getName();
        }else{
            $this->delivery_time = "";
        }
        return $this->delivery_time;
    }

    function getDescription() {
        $lang = JSFactory::getLang();
        $name = $lang->get('name');
        $short_description = $lang->get('short_description');
        $description = $lang->get('description');
        $meta_title = $lang->get('meta_title');
        $meta_keyword = $lang->get('meta_keyword');
        $meta_description = $lang->get('meta_description');
        
        $this->name = $this->$name;
        $this->short_description = $this->$short_description;
        $this->description = $this->$description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
    }
    
    function getPricePreview(){
        $this->getPrice(1, 1, 1, 1);
        if ($this->product_is_add_price){
            $this->product_add_prices = array_reverse($this->product_add_prices);
        }
        $this->updateOtherPricesIncludeAllFactors();
    }
    
	function getUseUserDiscount(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->user_discount_not_apply_prod_old_price && $this->product_old_price>0){
            return 0;
        }else{
            return 1;
        }
    }
	
    function getPrice($quantity=1, $enableCurrency=1, $enableUserDiscount=1, $enableParamsTax=1, $cartProduct=array()){
        $dispatcher = JDispatcher::getInstance();
        $jshopConfig = JSFactory::getConfig();
		$this->product_price_wp = $this->product_price;
        $this->product_price_calculate = $this->getPriceWithParams();
        $dispatcher->trigger('onBeforeCalculatePriceProduct', array(&$quantity, &$enableCurrency, &$enableUserDiscount, &$enableParamsTax, &$this, &$cartProduct));

        if ($this->product_is_add_price){
            $this->getAddPrices();
        }else{
            $this->product_add_prices = array();
        }
        
        if ($quantity && $this->product_is_add_price){
            foreach($this->product_add_prices as $key=>$value){
                if (($quantity >= $value->product_quantity_start && $quantity <= $value->product_quantity_finish) || ($quantity >= $value->product_quantity_start && $value->product_quantity_finish==0)){
                    $this->product_price_calculate = $value->price;
					$this->product_price_wp = $value->price_wp;
                    break;
                } 
            }
        }
        
        if ($enableCurrency){
            $this->product_price_calculate = getPriceFromCurrency($this->product_price_calculate, $this->currency_id);
			$this->product_price_wp = getPriceFromCurrency($this->product_price_wp, $this->currency_id);
			
        }
        
        if ($enableParamsTax){
            $this->product_price_calculate = getPriceCalcParamsTax($this->product_price_calculate, $this->product_tax_id);
			$this->product_price_wp = getPriceCalcParamsTax($this->product_price_wp, $this->product_tax_id);
        }
        
        if ($enableUserDiscount){
            $userShop = JSFactory::getUserShop();
            if ($userShop->percent_discount && $this->getUseUserDiscount()){
                $this->product_price_default = $this->product_price_calculate;
				$this->product_price_calculate = getPriceDiscount($this->product_price_calculate, $userShop->percent_discount);
				$this->product_price_wp = getPriceDiscount($this->product_price_wp, $userShop->percent_discount);
            }
        }
        $this->product_price_calculate1 = $this->product_price_calculate;
        $dispatcher->trigger('onCalculatePriceProduct', array($quantity, $enableCurrency, $enableUserDiscount, $enableParamsTax, &$this, &$cartProduct) );
        $this->product_price_calculate0 = $this->product_price_calculate;
        if ($jshopConfig->price_product_round){
            $this->product_price_calculate = round($this->product_price_calculate, $jshopConfig->decimal_count);
        }
        return $this->product_price_calculate;
    }
    
    function getPriceCalculate(){
        return $this->product_price_calculate;
    }
    
    function getBasicPriceInfo(){
        $this->product_basic_price_show = $this->weight_volume_units!=0;
        if (!$this->product_basic_price_show) return 0;
        $jshopConfig = JSFactory::getConfig();
        $units = JSFactory::getAllUnits();
        $unit = $units[$this->basic_price_unit_id];        
        if ($jshopConfig->calc_basic_price_from_product_price){
            $this->product_basic_price_wvu = $this->weight_volume_units;
        }else{
            $this->product_basic_price_wvu = $this->getWeight_volume_units();
        }
        $this->product_basic_price_weight = $this->product_basic_price_wvu * $unit->qty;
        if ($jshopConfig->calc_basic_price_from_product_price){
            $this->product_basic_price_calculate = $this->product_price_wp / $this->product_basic_price_weight;
        }else{
            $this->product_basic_price_calculate = $this->product_price_calculate1 / $this->product_basic_price_weight;
        }
        $this->product_basic_price_unit_name = $unit->name;
        $this->product_basic_price_unit_qty = $unit->qty;
        JDispatcher::getInstance()->trigger('onAfterGetBasicPriceInfoProduct', array(&$this));
        return 1;
    }
    
	function getBasicPrice(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_calculate;
    }
	
	function getBasicWeight(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_weight;
    }
	
	function getBasicPriceUnit(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_unit_name;
    }
	
    function getAddPrices(){
        $jshopConfig = JSFactory::getConfig();
        $productprice = JSFactory::getTable('productprice', 'jshop');
        $this->product_add_prices = $productprice->getAddPrices($this->product_id);
        
        $price = $this->getPriceWithParams();
		$price_wp = $this->product_price;
        foreach($this->product_add_prices as $k=>$v){
            if ($jshopConfig->product_price_qty_discount == 1){
                $this->product_add_prices[$k]->price = $price - $v->discount; //discount value
				$this->product_add_prices[$k]->price_wp = $price_wp - $v->discount;
            }else{
                $this->product_add_prices[$k]->price = $price - ($price * $v->discount / 100); //discount percent
				$this->product_add_prices[$k]->price_wp = $price_wp - ($price_wp * $v->discount / 100);
            }
        }
        
        if (!$this->add_price_unit_id) $this->add_price_unit_id = $jshopConfig->product_add_price_default_unit;
        $units = JSFactory::getAllUnits();
        $unit = $units[$this->add_price_unit_id];
        $this->product_add_price_unit = $unit->name;
        if ($this->product_add_price_unit=="") $this->product_add_price_unit=JSHP_ST_;
        JDispatcher::getInstance()->trigger('onAfterGetAddPricesProduct', array(&$this));
    }
    
    function getTax(){
        $taxes = JSFactory::getAllTaxes();
        $this->product_tax = $taxes[$this->product_tax_id];
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeGetTaxProduct', array(&$this));
        return $this->product_tax;
    }
    
    function updateOtherPricesIncludeAllFactors(){
        $jshopConfig = JSFactory::getConfig();
        $userShop = JSFactory::getUserShop();
        
        $this->product_old_price = $this->getOldPrice();
        $this->product_old_price = getPriceFromCurrency($this->product_old_price, $this->currency_id);
        $this->product_old_price = getPriceCalcParamsTax($this->product_old_price, $this->product_tax_id);
        if ($this->getUseUserDiscount()){
			$this->product_old_price = getPriceDiscount($this->product_old_price, $userShop->percent_discount);
		}
		
        if (is_array($this->product_add_prices)){
            foreach ($this->product_add_prices as $key=>$value){
                $this->product_add_prices[$key]->price = getPriceFromCurrency($this->product_add_prices[$key]->price, $this->currency_id);
                $this->product_add_prices[$key]->price = getPriceCalcParamsTax($this->product_add_prices[$key]->price, $this->product_tax_id);
				if ($this->getUseUserDiscount()){
					$this->product_add_prices[$key]->price = getPriceDiscount($this->product_add_prices[$key]->price, $userShop->percent_discount);
				}
            }
        }
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('updateOtherPricesIncludeAllFactors', array(&$this) );
    }
    
    function getExtraFields($type = 1){
        $_cats = $this->getCategories();
        $cats = array();
        foreach($_cats as $v){
            $cats[] = $v->category_id;
        }
        
        $fields = array();
        $jshopConfig = JSFactory::getConfig();
        $hide_fields = $jshopConfig->getProductHideExtraFields();
        $cart_fields = $jshopConfig->getCartDisplayExtraFields();
        $fieldvalues = JSFactory::getAllProductExtraFieldValue();
        $listfield = JSFactory::getAllProductExtraField();
        foreach($listfield as $val){
            if ($type==1 && in_array($val->id, $hide_fields)) continue;
            if ($type==2 && !in_array($val->id, $cart_fields)) continue;
            
            if ($val->allcats){
                $fields[] = $val;
            }else{
                $insert = 0;
                foreach($cats as $cat_id){
                    if (in_array($cat_id, $val->cats)) $insert = 1;
                }
                if ($insert){
                    $fields[] = $val;
                }
            }
        }
       
        $rows = array();
        foreach($fields as $field){
            $field_id = $field->id;
            $field_name = "extra_field_".$field_id;
            if ($field->type==0){
                if ($this->$field_name!=0){
                    $listid = explode(',', $this->$field_name);
                    $tmp = array();
                    foreach($listid as $extrafiledvalueid){
                        $tmp[] = $fieldvalues[$extrafiledvalueid];
                    }
                    $extra_field_value = implode($jshopConfig->multi_charactiristic_separator, $tmp);
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$extra_field_value, "groupname"=>$listfield[$field_id]->groupname);
                }
            }else{
                if ($this->$field_name!=""){
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$this->$field_name, "groupname"=>$listfield[$field_id]->groupname);
                }
            }
        }
        $grname = '';
        foreach($rows as $k=>$v){
            if ($v['groupname']!=$grname){
                $grname = $v['groupname'];
                $rows[$k]['grshow'] = 1;
            }else{
                $rows[$k]['grshow'] = 0;
            }
        }        
        return $rows;
    }
    
    function getRelatedProducts(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_product_related){
            $this->product_related = array();
            return $this->product_related;
        }

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $filters = array();
        $this->getBuildQueryListProductSimpleList("related", null, $filters, $adv_query, $adv_from, $adv_result);
        $order_query = "order by relation.id";
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetProductList', array("related_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT $adv_result FROM `#__jshopping_products_relations` AS relation
                INNER JOIN `#__jshopping_products` AS prod ON relation.product_related_id = prod.product_id
                LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = relation.product_related_id
                LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                $adv_from
                WHERE relation.product_id = '" . $this->_db->escape($this->product_id) . "' AND cat.category_publish='1' AND prod.product_publish = '1' ".$adv_query." group by prod.product_id ".$order_query;
        $this->_db->setQuery($query);
        $this->product_related = $this->_db->loadObjectList();
        foreach($this->product_related as $key=>$value) {
            $this->product_related[$key]->product_link = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$value->category_id.'&product_id='.$value->product_id, 1);
        }
        $this->product_related = listProductUpdateData($this->product_related, 1);
        return $this->product_related;
    }
    
    function getLastProducts($count, $array_categories = null, $filters = array()){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("last", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
        $order_query = "ORDER BY prod.product_id";

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetProductList', array("last_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id $order_query DESC LIMIT ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getRandProducts($count, $array_categories = null, $filters = array()){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("rand", $array_categories, $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("rand_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT count(distinct prod.product_id) FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from                  
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
        $db->setQuery($query);
        $totalrow = $db->loadResult();                  
        $totalrow = $totalrow - $count;
        if ($totalrow < 0) $totalrow = 0;
        $limitstart = rand(0, $totalrow);
        
        $order = array();
        $order[] = "name asc";
        $order[] = "name desc";
        $order[] = "prod.product_price asc";
        $order[] = "prod.product_price desc";
        $orderby = $order[rand(0,3)];
                 
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id order by ".$orderby." LIMIT ".$limitstart.", ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);        
        return $products;
    }    
    
    function getBestSellers($count, $array_categories = null, $filters = array()){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("best", $array_categories, $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("bestseller_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
 
        $query = "SELECT SUM(OI.product_quantity) as max_num, $adv_result FROM #__jshopping_order_item AS OI
                  INNER JOIN `#__jshopping_products` AS prod   ON prod.product_id=OI.product_id
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id
                  ORDER BY max_num desc LIMIT ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getProductLabel($label_id, $count, $array_categories = null, $filters = array(), $order_query = "ORDER BY name"){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("label", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
        if ($label_id){
            $adv_query .= " AND prod.label_id='".$db->escape($label_id)."'";
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetProductList', array("label_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' and prod.label_id!=0 AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id $order_query LIMIT ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getTopRatingProducts($count, $array_categories = null, $filters = array()){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("toprating", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
 
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeQueryGetProductList', array("top_rating_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ORDER BY prod.average_rating desc LIMIT ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getTopHitsProducts($count, $array_categories = null, $filters = array()){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("tophits", $array_categories, $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("top_hits_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ORDER BY prod.hits desc LIMIT ".$count;
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;   
    }
    
    function getAllProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0){
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("products", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);
 
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("all_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        if ($limit){
            $this->_db->setQuery($query, $limitstart, $limit);
        }else{
            $this->_db->setQuery($query);
        }
        $products = $this->_db->loadObjectList();
        $products = listProductUpdateData($products);
        return $products;
    }    
    
    function getCountAllProducts($filters) {
        $jshopConfig = JSFactory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "";
        $this->getBuildQueryListProduct("products", "count", $filters, $adv_query, $adv_from, $adv_result);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryCountProductList', array("all_products", &$adv_result, &$adv_from, &$adv_query, &$filters) );
        
        $db = JFactory::getDBO(); 
        $query = "SELECT COUNT(distinct prod.product_id) FROM `#__jshopping_products` as prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getReviews($limitstart = 0, $limit = 20) {
        $query = "SELECT * FROM `#__jshopping_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and publish='1' order by review_id desc";
        $this->_db->setQuery($query, $limitstart, $limit);
        $rows = $this->_db->loadObjectList();
        JDispatcher::getInstance()->trigger('onAfterGetReviewsProduct', array(&$this, &$rows, &$limitstart, &$limit));
        return $rows;
    }
    
    function getReviewsCount(){
        $query = "SELECT count(review_id) FROM `#__jshopping_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and publish='1'";
        $this->_db->setQuery($query);
        $row = $this->_db->loadResult();
        JDispatcher::getInstance()->trigger('onAfterGetReviewsCountProduct', array(&$this, &$row));
        return $row;
    }

    function getAverageRating() {
        $query = "SELECT ROUND(AVG(mark),2) FROM `#__jshopping_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and mark > 0 and publish='1'";
        $this->_db->setQuery($query);
        $row = $this->_db->loadResult();
        JDispatcher::getInstance()->trigger('onAfterGetAverageRatingProduct', array(&$this, &$row));
        return $row;
    }
    
    function loadAverageRating(){
        $this->average_rating = $this->getAverageRating();
        if (!$this->average_rating) $this->average_rating = 0;
    }
    
    function loadReviewsCount(){
        $this->reviews_count = $this->getReviewsCount();
    }
    
    function getExtAttributeData($pid){
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($pid);
    return $product;
    }
    
    function getBuildSelectAttributes($attributeValues, $attributeActive){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_attributes) return array();
        $dispatcher = JDispatcher::getInstance();
        $attrib = JSFactory::getAllAttributes();
		$userShop = JSFactory::getUserShop();
        $selects = array();

        foreach($attrib as $k=>$v){
            $attr_id = $v->attr_id;
            if (isset($attributeValues[$attr_id]) && $attributeValues[$attr_id]){
                if (isset($attributeActive[$attr_id])){
                    $_firstval = $attributeActive[$attr_id];
                }else{
                    $_firstval = 0;
                }
                $selects[$attr_id] = new stdClass();
                $selects[$attr_id]->attr_id = $attr_id;
                $selects[$attr_id]->attr_name = $v->name;
                $selects[$attr_id]->attr_description = $v->description;
                $selects[$attr_id]->groupname = $v->groupname;
                $selects[$attr_id]->firstval = $_firstval;
                $options = $attributeValues[$attr_id];
                $attrimage = array();
                foreach($options as $k2=>$v2){
                    $attrimage[$v2->val_id] = $v2->image;
					$addPrice = $v2->addprice;
                    $addPrice = getPriceFromCurrency($addPrice, $this->currency_id);
                    $addPrice = getPriceCalcParamsTax($addPrice, $this->product_tax_id);
                    if ($userShop->percent_discount){
                        $addPrice = getPriceDiscount($addPrice, $userShop->percent_discount);
                    }

                    $options[$k2]->addprice = $addPrice;
                }

                if ($v->attr_type==1){
                // attribut type select
                
                    if ($jshopConfig->attr_display_addprice){
                        foreach($options as $k2=>$v2){
                            if (($v2->price_mod=="+" || $v2->price_mod=="-" || $jshopConfig->attr_display_addprice_all_sign) && $v2->addprice>0){
                                $ext_price_info = " (".$v2->price_mod.formatprice($v2->addprice).")";
                                $options[$k2]->value_name .=$ext_price_info;
                            }
                        }
                    }

                    if ($jshopConfig->product_attribut_first_value_empty){
                        $first = array();
                        $first[] = JHTML::_('select.option', '0', _JSHOP_SELECT, 'val_id','value_name');
                        $options = array_merge($first, $options);
                    }
                    
                    if (isset($attributeActive[$attr_id]) && isset($attrimage[$attributeActive[$attr_id]])){
                        $_active_image = $attrimage[$attributeActive[$attr_id]];
                    }else{
                        $_active_image = '';
                    }
                    if (isset($attributeActive[$attr_id])){
                        $_select_active = $attributeActive[$attr_id];
                    }else{
                        $_select_active = '';
                    }
                    $selects[$attr_id]->selects = JHTML::_('select.genericlist', $options, 'jshop_attr_id['.$attr_id.']','class = "inputbox" size = "1" onchange="setAttrValue(\''.$attr_id.'\', this.value);"','val_id','value_name', $_select_active)."<span class='prod_attr_img'>".$this->getHtmlDisplayProdAttrImg($attr_id, $_active_image)."</span>";
                    $selects[$attr_id]->selects = str_replace(array("\n","\r","\t"), "", $selects[$attr_id]->selects);
                }else{
                // attribut type radio
                
                    foreach($options as $k2=>$v2){
                        if ($v2->image) $options[$k2]->value_name = "<img src='".$jshopConfig->image_attributes_live_path."/".$v2->image."' alt='' /> ".$v2->value_name;
                    }

                    if ($jshopConfig->attr_display_addprice){
                        foreach($options as $k2=>$v2){
                            if (($v2->price_mod=="+" || $v2->price_mod=="-" || $jshopConfig->attr_display_addprice_all_sign) && $v2->addprice>0){
                                $ext_price_info = " (".$v2->price_mod.formatprice($v2->addprice).")";
                                $options[$k2]->value_name .=$ext_price_info;
                            }
                        }
                    }

                    $radioseparator = '';
                    if ($jshopConfig->radio_attr_value_vertical) $radioseparator = "<br/>"; 
                    foreach($options as $k2=>$v2){
                        $options[$k2]->value_name = "<span class='radio_attr_label'>".$v2->value_name."</span>";
                    }

                    $selects[$attr_id]->selects = sprintRadioList($options, 'jshop_attr_id['.$attr_id.']','onclick="setAttrValue(\''.$attr_id.'\', this.value);"','val_id','value_name', $attributeActive[$attr_id], $radioseparator);
                    $selects[$attr_id]->selects = str_replace(array("\n","\r","\t"), "", $selects[$attr_id]->selects);
                }
                $dispatcher->trigger('onBuildSelectAttribute', array(&$attributeValues, &$attributeActive, &$selects, &$options, &$attr_id, &$v));
            }
        }
        $grname = '';
        foreach($selects as $k=>$v){
            if ($v->groupname!=$grname){
                $grname = $v->groupname;
                $selects[$k]->grshow = 1;
            }else{
                $selects[$k]->grshow = 0;
            }
        }
    return $selects;
    }

    function getHtmlDisplayProdAttrImg($attr_id, $img){
        $jshopConfig = JSFactory::getConfig();
        if ($img){
            $path = $jshopConfig->image_attributes_live_path;
        }else{
            $path = $jshopConfig->live_path."images";
            $img = "blank.gif";
        }
        $urlimg = $path."/".$img;
        
        $html = '<img id="prod_attr_img_'.$attr_id.'" src="'.$urlimg.'" alt="" />';
        return $html;
    }

}
?>