<?php
/**
* @version      4.9.0 09.01.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerPartners extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct($config);
        $this->registerTask('add', 'edit' );
        $this->registerTask('apply', 'save');
        checkAccessController("products");

        $post = JRequest::get('post');
        $cat_id = $post["category_id"][0];

        $category_id = ($cat_id) ? $cat_id : $_GET['category_id'];


        if($category_id == 1) addSubmenu("offers");
        if($category_id == 3) addSubmenu("coffee");
        if($category_id == 4) addSubmenu("restaurants");
        if($category_id == 5) addSubmenu("moviesevents");

    }
    
    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();    
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $products = JSFactory::getModel("products");
        $id_vendor_cuser = getIdVendorForCUser();
		
		
        $context = "jshoping.list.admin.product";
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', $jshopConfig->adm_prod_list_default_sorting, 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', $jshopConfig->adm_prod_list_default_sorting_dir, 'cmd');
        
        if (isset($_GET['category_id']) && $_GET['category_id']==="0"){            
            $mainframe->setUserState($context.'category_id', 0);
            $mainframe->setUserState($context.'manufacturer_id', 0);
			$mainframe->setUserState($context.'vendor_id', -1);
            $mainframe->setUserState($context.'label_id', 0);
            $mainframe->setUserState($context.'publish', 0);
            $mainframe->setUserState($context.'text_search', '');
        }

        $category_id = $mainframe->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $manufacturer_id = $mainframe->getUserStateFromRequest($context.'manufacturer_id', 'manufacturer_id', 0, 'int');
		$vendor_id = $mainframe->getUserStateFromRequest($context.'vendor_id', 'vendor_id', -1, 'int');
        $label_id = $mainframe->getUserStateFromRequest($context.'label_id', 'label_id', 0, 'int');
        $publish = $mainframe->getUserStateFromRequest($context.'publish', 'publish', 0, 'int');
        $text_search = $mainframe->getUserStateFromRequest($context.'text_search', 'text_search', '');
        if ($category_id && $filter_order=='category') $filter_order = 'product_id';
		
        $filter = array("category_id"=>$category_id, "manufacturer_id"=>$manufacturer_id, "vendor_id"=>$vendor_id, "label_id"=>$label_id, "publish"=>$publish, "text_search"=>$text_search);
        if ($id_vendor_cuser){
            $filter["vendor_id"] = $id_vendor_cuser;
        }
        
        $show_vendor = $jshopConfig->admin_show_vendors;
        if ($id_vendor_cuser) $show_vendor = 0;
                
        $total = $products->getCountAllProducts($filter);
        
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        
        $rows = $products->getAllProducts($filter, $pagination->limitstart, $pagination->limit, $filter_order, $filter_order_Dir);

        if ($show_vendor){
            $main_vendor = JSFactory::getTable('vendor', 'jshop');
            $main_vendor->loadMain();
			
			$vendorsModel = JSFactory::getModel('vendors');
            $vendors = $vendorsModel->getAllVendorsNames(1);
            
            $firstVendor = array();
            $firstVendor[0] = new stdClass();
            $firstVendor[0]->id = -1;
            $firstVendor[0]->name = " - "._JSHOP_VENDOR." - ";
            $lists['vendors'] = JHTML::_('select.genericlist', array_merge($firstVendor, $vendors), 'vendor_id','onchange="document.adminForm.submit();"', 'id', 'name', $vendor_id);
            
            foreach($rows as $k=>$v){
                if ($v->vendor_id){
                    $rows[$k]->vendor_name = $v->v_f_name." ".$v->v_l_name;
                }else{
                    $rows[$k]->vendor_name = $main_vendor->f_name." ".$main_vendor->l_name;
                }
            }
        }
        
        $parentTop = new stdClass();
        $parentTop->category_id = 0;
        $parentTop->name = "- "._JSHOP_CATEGORY." -";
        $categories_select = buildTreeCategory(0,1,0);
        array_unshift($categories_select, $parentTop);    
        $lists['treecategories'] = JHTML::_('select.genericlist', $categories_select, 'category_id', 'class="chosen-select" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id );
        
        $manuf1 = array();
        $manuf1[0] = new stdClass();
        $manuf1[0]->manufacturer_id = '0';
        $manuf1[0]->name = " - "._JSHOP_NAME_MANUFACTURER." - ";

        $_manufacturer = JSFactory::getModel('manufacturers');
        $manufs = $_manufacturer->getList();
        $manufs = array_merge($manuf1, $manufs);
        $lists['manufacturers'] = JHTML::_('select.genericlist', $manufs, 'manufacturer_id',' onchange="document.adminForm.submit();"', 'manufacturer_id', 'name', $manufacturer_id);
        
        // product labels
        if ($jshopConfig->admin_show_product_labels) {
            $_labels = JSFactory::getModel("productLabels");
            $alllabels = $_labels->getList();
            $first = array();
            $first[] = JHTML::_('select.option', '0', " - "._JSHOP_LABEL." - ", 'id','name');
            $lists['labels'] = JHTML::_('select.genericlist', array_merge($first, $alllabels), 'label_id','style="width: 100px;" onchange="document.adminForm.submit();"','id','name', $label_id);
        }
        //
        
        $f_option = array();
        $f_option[] = JHTML::_('select.option', 0, " - "._JSHOP_SHOW." - ", 'id', 'name');
        $f_option[] = JHTML::_('select.option', 1, _JSHOP_PUBLISH, 'id', 'name');
        $f_option[] = JHTML::_('select.option', 2, _JSHOP_UNPUBLISH, 'id', 'name');
        $lists['publish'] = JHTML::_('select.genericlist', $f_option, 'publish', 'style="width: 100px;" onchange="document.adminForm.submit();"', 'id', 'name', $publish);
        
        foreach($rows as $key=>$v){
            if ($rows[$key]->label_id){
                $image = getNameImageLabel($rows[$key]->label_id);
                if ($image){
                    $rows[$key]->_label_image = $jshopConfig->image_labels_live_path."/".$image;
                }
                $rows[$key]->_label_name = getNameImageLabel($rows[$key]->label_id, 2);
            }
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayListProducts', array(&$rows));

        $view=$this->getView("partners_list", 'html');
        $view->assign('rows', $rows);
        $view->assign('lists', $lists);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('category_id', $category_id);
        $view->assign('manufacturer_id', $manufacturer_id);
        $view->assign('pagination', $pagination);
        $view->assign('text_search', $text_search);
        $view->assign('config', $jshopConfig);
        $view->assign('show_vendor', $show_vendor);        
        $view->assign('is_partner', true);
        $dispatcher->trigger('onBeforeDisplayListProductsView', array(&$view));
        $view->display();        
    }
    
    function edit(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onLoadEditProduct', array());
        $id_vendor_cuser = getIdVendorForCUser();
        $category_id = JRequest::getInt('category_id');
        
        $tmpl_extra_fields = null;
        
        $product_id = JRequest::getInt('product_id');
        $product_attr_id = JRequest::getInt('product_attr_id');        
        
        //parent product
        if ($product_attr_id){
            //JRequest::setVar("hidemainmenu", 1);
            $product_attr = JSFactory::getTable('productAttribut', 'jshop');
            $product_attr->load($product_attr_id);
			if ($product_attr->ext_attribute_product_id){
                $product_id = $product_attr->ext_attribute_product_id;
            }else{
                $product = JSFactory::getTable('product', 'jshop');
                $product->parent_id = $product_attr->product_id;
                $product->store();
                $product_id = $product->product_id;
                $product_attr->ext_attribute_product_id = $product_id;
                $product_attr->store();
            }            
        }        
        
        if ($id_vendor_cuser && $product_id){
            checkAccessVendorToProduct($id_vendor_cuser, $product_id);
        }
        
        $products = JSFactory::getModel("products");
        
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);
        $_productprice = JSFactory::getTable('productPrice', 'jshop');
        $product->product_add_prices = $_productprice->getAddPrices($product_id);        
        $product->product_add_prices = array_reverse($product->product_add_prices);
        $name = $lang->get("name");
        $product->name = $product->$name;

        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
 
        $nofilter = array();
        JFilterOutput::objectHTMLSafe( $product, ENT_QUOTES, $nofilter);

        $edit = intval($product_id);

        if (!$product_id) {
            $rows = array();
            $product->product_quantity = 1;
            $product->product_publish = 1;
        }
 
		$product->product_quantity = floatval($product->product_quantity);
        $_tax = JSFactory::getModel("taxes");
        $all_taxes = $_tax->getAllTaxes();
        
        if ($edit){
            $images = $product->getImages();
            $videos = $product->getVideos();
            $files  = $product->getFiles();
            $categories_select = $product->getCategories();
            $categories_select_list = array();
            foreach($categories_select as $v){
                $categories_select_list[] = $v->category_id;
            }
            $related_products = $products->getRelatedProducts($product_id);
        } else {
            $images = array();
            $videos = array();
            $files = array();
            $categories_select = null;
            if ($category_id) {
                $categories_select = $category_id;
            }
            $related_products = array();
            $categories_select_list = array();
        }
        if ($jshopConfig->tax){
            $list_tax = array();
            foreach ($all_taxes as $tax){
                $list_tax[] = JHTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
            }
            $withouttax = 0;
        }else{
            $withouttax = 1;
        }

        $categories = buildTreeCategory(0,1,0);
        if (count($categories)==0) JError::raiseNotice(0, _JSHOP_PLEASE_ADD_CATEGORY);
        $lists['images'] = $images;
        $lists['videos'] = $videos;
        $lists['files'] = $files;

        $manuf1 = array();
        $manuf1[0] = new stdClass();
        $manuf1[0]->manufacturer_id = '0';
        $manuf1[0]->name = _JSHOP_NONE;

        $_manufacturer =JSFactory::getModel('manufacturers');
        $manufs = $_manufacturer->getList();
        $manufs = array_merge($manuf1, $manufs);

        //Attributes
        $_attribut = JSFactory::getModel('attribut');
        $list_all_attributes = $_attribut->getAllAttributes(2, $categories_select_list);
        $_attribut_value =JSFactory::getModel('attributValue');
        $lists['attribs'] = $product->getAttributes();
        $lists['ind_attribs'] = $product->getAttributes2();
        $lists['attribs_values'] = $_attribut_value->getAllAttributeValues(2);
        $all_attributes = $list_all_attributes['dependent'];

        $lists['ind_attribs_gr'] = array();
        foreach($lists['ind_attribs'] as $v){
            $lists['ind_attribs_gr'][$v->attr_id][] = $v;
        }
        
		foreach ($lists['attribs'] as $key => $attribs){
            $lists['attribs'][$key]->count = floatval($attribs->count);
        }
		
        $first = array();
        $first[] = JHTML::_('select.option', '0',_JSHOP_SELECT, 'value_id','name');

        foreach ($all_attributes as $key => $value){
            $values_for_attribut = $_attribut_value->getAllValues($value->attr_id);
            $all_attributes[$key]->values_select = JHTML::_('select.genericlist', array_merge($first, $values_for_attribut),'value_id['.$value->attr_id.']','class = "inputbox" size = "5" multiple="multiple" id = "value_id_'.$value->attr_id.'"','value_id','name');
            $all_attributes[$key]->values = $values_for_attribut;
        }        
        $lists['all_attributes'] = $all_attributes;
        $product_with_attribute = (count($lists['attribs']) > 0);
        
        //independent attribute
        $all_independent_attributes = $list_all_attributes['independent'];
        
        $price_modification = array();
        $price_modification[] = JHTML::_('select.option', '+','+', 'id','name');
        $price_modification[] = JHTML::_('select.option', '-','-', 'id','name');
        $price_modification[] = JHTML::_('select.option', '*','*', 'id','name');
        $price_modification[] = JHTML::_('select.option', '/','/', 'id','name');
        $price_modification[] = JHTML::_('select.option', '=','=', 'id','name');
        $price_modification[] = JHTML::_('select.option', '%','%', 'id','name');
        
        foreach ($all_independent_attributes as $key => $value){
            $values_for_attribut = $_attribut_value->getAllValues($value->attr_id);            
            $all_independent_attributes[$key]->values_select = JHTML::_('select.genericlist', array_merge($first, $values_for_attribut),'attr_ind_id_tmp_'.$value->attr_id.'','class = "inputbox middle2" ','value_id','name');
            $all_independent_attributes[$key]->values = $values_for_attribut;
            $all_independent_attributes[$key]->price_modification_select = JHTML::_('select.genericlist', $price_modification,'attr_price_mod_tmp_'.$value->attr_id.'','class = "inputbox small3" ','id','name');
            $all_independent_attributes[$key]->submit_button = '<input type = "button" class="btn" onclick = "addAttributValue2('.$value->attr_id.');" value = "'._JSHOP_ADD_ATTRIBUT.'" />';
        }        
        $lists['all_independent_attributes'] = $all_independent_attributes;
		$lists['dep_attr_button_add'] = '<input type="button" class="btn" onclick="addAttributValue()" value="'._JSHOP_ADD.'" />';
        // End work with attributes and values

        // delivery Times
        if ($jshopConfig->admin_show_delivery_time) {
            $_deliveryTimes = JSFactory::getModel("deliveryTimes");
            $all_delivery_times = $_deliveryTimes->getDeliveryTimes();                
            $all_delivery_times0 = array();
            $all_delivery_times0[0] = new stdClass();
            $all_delivery_times0[0]->id = '0';
            $all_delivery_times0[0]->name = _JSHOP_NONE;        
            $lists['deliverytimes'] = JHTML::_('select.genericlist', array_merge($all_delivery_times0, $all_delivery_times),'delivery_times_id','class = "inputbox" size = "1"','id','name',$product->delivery_times_id);        
        }
        //

        // units
        $_units = JSFactory::getModel("units");
        $allunits = $_units->getUnits();
        if ($jshopConfig->admin_show_product_basic_price){
            $lists['basic_price_units'] = JHTML::_('select.genericlist', $allunits, 'basic_price_unit_id','class = "inputbox"','id','name',$product->basic_price_unit_id);
        }
        if (!$product->add_price_unit_id) $product->add_price_unit_id = $jshopConfig->product_add_price_default_unit;
        $lists['add_price_units'] = JHTML::_('select.genericlist', $allunits, 'add_price_unit_id','class = "inputbox middle"','id','name', $product->add_price_unit_id);
        //
        
        // product labels
        if ($jshopConfig->admin_show_product_labels){
            $_labels = JSFactory::getModel("productLabels");
            $alllabels = $_labels->getList();
            $first = array();
            $first[] = JHTML::_('select.option', '0',_JSHOP_SELECT, 'id','name');        
            $lists['labels'] = JHTML::_('select.genericlist', array_merge($first, $alllabels), 'label_id','class = "inputbox" size = "1"','id','name',$product->label_id);
        }
        //
        
        // access rights
        $accessgroups = getAccessGroups();        
        $lists['access'] = JHTML::_('select.genericlist', $accessgroups, 'access','class = "inputbox" size = "1"','id','title', $product->access);
        
        //currency
        $current_currency = $product->currency_id;
        if (!$current_currency) $current_currency = $jshopConfig->mainCurrency;
        $_currency = JSFactory::getModel("currencies");
        $currency_list = $_currency->getAllCurrencies();
        $lists['currency'] = JHTML::_('select.genericlist', $currency_list, 'currency_id','class = "inputbox small2"','currency_id','currency_code', $current_currency);
        
        // vendors
        $display_vendor_select = 0;
        if ($jshopConfig->admin_show_vendors){
            $_vendors = JSFactory::getModel("vendors");
            $listvebdorsnames = $_vendors->getAllVendorsNames(1);
            $first = array();
            $lists['vendors'] = JHTML::_('select.genericlist', array_merge($first, $listvebdorsnames), 'vendor_id','class = "inputbox" size = "1"', 'id', 'name', $product->vendor_id);
            
            $display_vendor_select = 1;
            if ($id_vendor_cuser > 0) $display_vendor_select = 0;            
        }
        //
        
        //product extra field
        if ($jshopConfig->admin_show_product_extra_field) {
            $categorys_id = array();
            if (is_array($categories_select)){
                foreach($categories_select as $tmp){
                    $categorys_id[] = $tmp->category_id;
                }        
            }
            $tmpl_extra_fields = $this->_getHtmlProductExtraFields($categorys_id, $product);
        }
        //
        
        //free attribute
        if ($jshopConfig->admin_show_freeattributes){
            $_freeattributes = JSFactory::getModel("freeattribut");        
            $listfreeattributes = $_freeattributes->getAll();
            $activeFreeAttribute = $product->getListFreeAttributes();
            $listIdActiveFreeAttribute = array();
            foreach($activeFreeAttribute as $_obj){
                $listIdActiveFreeAttribute[] = $_obj->id;
            }            
            foreach($listfreeattributes as $k=>$v){
                if (in_array($v->id, $listIdActiveFreeAttribute)){
                    $listfreeattributes[$k]->pactive = 1;
                }
            }
        }

        $lists['manufacturers'] = JHTML::_('select.genericlist', $manufs,'product_manufacturer_id','class = "inputbox" size = "1"','manufacturer_id','name',$product->product_manufacturer_id);
        
        $tax_value = 0;
        if ($jshopConfig->tax){
            foreach($all_taxes as $tax){
                if ($tax->tax_id == $product->product_tax_id){
                    $tax_value = $tax->tax_value;
                    break; 
                }
            }
        }
        
        if ($product_id){
            $product->product_price = formatEPrice($product->product_price);
            
            if ($jshopConfig->display_price_admin==0){
                $product->product_price2 = formatEPrice($product->product_price / (1 + $tax_value / 100));
            }else{
                $product->product_price2 = formatEPrice($product->product_price * (1 + $tax_value / 100));
            }
        }else{
            $product->product_price2 = '';
        }
        

        $category_select_onclick = "";
        if ($jshopConfig->admin_show_product_extra_field) $category_select_onclick = 'onclick="reloadProductExtraField(\''.$product_id.'\')"';
        
        if ($jshopConfig->tax){
            $lists['tax'] = JHTML::_('select.genericlist', $list_tax,'product_tax_id','class = "inputbox" size = "1" onchange = "updatePrice2('.$jshopConfig->display_price_admin.');"','tax_id','tax_name',$product->product_tax_id);
        }

        //$lists['categories'] = JHTML::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox" size="10" multiple = "multiple" '.$category_select_onclick, 'category_id', 'name', $categories_select);
        $lists['categories'] = JHTML::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox" size="1" '.$category_select_onclick, 'category_id', 'name', $categories_select);
        $lists['templates'] = getTemplates('product', $product->product_template);
        
        $_product_option = JSFactory::getTable('productOption', 'jshop');
        $product_options = $_product_option->getProductOptions($product_id);
        $product->product_options = $product_options;

        $productExtraOptions = JSFactory::getTable('productExtraOptions', 'jshop');
        $productExtraOptions->load($product_id);
        
        if ($jshopConfig->return_policy_for_product){ 
            $_statictext = JSFactory::getModel("statictext");
            $first = array();
            $first[] = JHTML::_('select.option', '0', _JSHP_STPAGE_return_policy, 'id', 'alias');
            $statictext_list = $_statictext->getList(1);
            $lists['return_policy'] = JHTML::_('select.genericlist', array_merge($first, $statictext_list), 'options[return_policy]','class = "inputbox"','id','alias', $product_options['return_policy']);
        }
        
        $dispatcher->trigger('onBeforeDisplayEditProduct', array(&$product, &$related_products, &$lists, &$listfreeattributes, &$tax_value));

        $view=$this->getView("partner_edit", 'html');
        $view->setLayout("default");
        $view->assign('product', $product);
        $view->assign('category_id', $category_id);
        $view->assign('product_extra_options', $productExtraOptions);
        $view->assign('lists', $lists);
        $view->assign('related_products', $related_products);
        $view->assign('edit', $edit);
        $view->assign('product_with_attribute', $product_with_attribute);
        $view->assign('tax_value', $tax_value);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('tmpl_extra_fields', $tmpl_extra_fields);
        $view->assign('withouttax', $withouttax);
        $view->assign('display_vendor_select', $display_vendor_select);
        $view->assign('listfreeattributes', $listfreeattributes);
        $view->assign('product_attr_id', $product_attr_id);
        foreach($languages as $lang){
            $view->assign('plugin_template_description_'.$lang->language, '');
        }
        $view->assign('plugin_template_info', '');
        $view->assign('plugin_template_attribute', '');
        $view->assign('plugin_template_freeattribute', '');
        $view->assign('plugin_template_images', '');
        $view->assign('plugin_template_related', '');
        $view->assign('plugin_template_files', '');
        $view->assign('plugin_template_extrafields', '');
        $dispatcher->trigger('onBeforeDisplayEditProductView', array(&$view) );
		$view->display();
    }
    
    function save(){
        $jshopConfig = JSFactory::getConfig();
        require_once($jshopConfig->path.'lib/image.lib.php');
        require_once($jshopConfig->path.'lib/uploadfile.class.php');

        $dispatcher = JDispatcher::getInstance();

        $db = JFactory::getDBO();
        $post = JRequest::get('post');
        $categories = $post['category_id'];

        $_products = JSFactory::getModel("products");
        $product = JSFactory::getTable('product', 'jshop');
        $product_extra_options = JSFactory::getTable('productextraoptions', 'jshop');
        $_alias = JSFactory::getModel("alias");
        $_lang = JSFactory::getModel("languages");
        $id_vendor_cuser = getIdVendorForCUser();

        if ($id_vendor_cuser && $post['product_id']){
            checkAccessVendorToProduct($id_vendor_cuser, $post['product_id']);
        }
		$post['different_prices'] = 0;
        if (isset($post['product_is_add_price']) && $post['product_is_add_price']) $post['different_prices'] = 1;

        if (!isset($post['product_publish'])) $post['product_publish'] = 0;
        if (!isset($post['product_is_add_price'])) $post['product_is_add_price'] = 0;
        if (!isset($post['unlimited'])) $post['unlimited'] = 0;        
        $post['product_price'] = saveAsPrice($post['product_price']);
        $post['product_old_price'] = saveAsPrice($post['product_old_price']);
        if (isset($post['product_buy_price']))
            $post['product_buy_price'] = saveAsPrice($post['product_buy_price']);
        else 
            $post['product_buy_price'] = null;
        $post['product_weight'] = saveAsPrice($post['product_weight']);
        if(!isset($post['related_products'])) $post['related_products'] = array();
        if (!$post['product_id']) $post['product_date_added'] = getJsDate();
        if (!isset($post['attrib_price'])) $post['attrib_price'] = null;
        if (!isset($post['attrib_ind_id'])) $post['attrib_ind_id'] = null;
        if (!isset($post['attrib_ind_price'])) $post['attrib_ind_price'] = null;
        if (!isset($post['attrib_ind_price_mod'])) $post['attrib_ind_price_mod'] = null;
        if (!isset($post['freeattribut'])) $post['freeattribut'] = null;
        $post['date_modify'] = getJsDate();
        $post['edit'] = intval($post['product_id']);
        if (!isset($post['product_add_discount'])) $post['product_add_discount'] = 0;
        $post['min_price'] = $_products->getMinimalPrice($post['product_price'], $post['attrib_price'], array($post['attrib_ind_id'], $post['attrib_ind_price_mod'], $post['attrib_ind_price']), $post['product_is_add_price'], $post['product_add_discount']);
        if ($id_vendor_cuser){
            $post['vendor_id'] = $id_vendor_cuser;
        }
        
        if (isset($post['attr_count']) && is_array($post['attr_count'])){
            $qty = 0;
            foreach($post['attr_count'] as $key => $_qty) {
				$post['attr_count'][$key] = saveAsPrice($_qty);
                if ($_qty > 0) $qty += $post['attr_count'][$key];
            }

            $post['product_quantity'] = $qty;
        }
		
        if ($post['unlimited']){
            $post['product_quantity'] = 1;
        }
		
		$post['product_quantity'] = saveAsPrice($post['product_quantity']);
		
        if (isset($post['productfields']) && is_array($post['productfields'])){
            foreach($post['productfields'] as $productfield=>$val){
                if (is_array($val)){
                    $post[$productfield] = implode(',', $val);
                }
            }
        }
        if ($jshopConfig->admin_show_product_extra_field){
            $_productfields = JSFactory::getModel("productFields");
            $list_productfields = $_productfields->getList(1);
            foreach($list_productfields as $v){
                if ($v->type==0 && !isset($post['extra_field_'.$v->id])){
                    $post['extra_field_'.$v->id] = '';
                }
            }
        }

        if (is_array($post['attrib_price'])){
            if (count(array_unique($post['attrib_price']))>1) $post['different_prices'] = 1;
        }
        if (is_array($post['attrib_ind_price'])){
            $tmp_attr_ind_price = array();
            foreach($post['attrib_ind_price'] as $k=>$v){
                $tmp_attr_ind_price[] = $post['attrib_ind_price_mod'][$k].$post['attrib_ind_price'][$k];
            }
            if (count(array_unique($tmp_attr_ind_price))>1) $post['different_prices'] = 1;
        }

        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            $post['title_'.$lang->language] = trim($post['title_'.$lang->language]);
            $post['zip_'.$lang->language] = trim($post['zip_'.$lang->language]);
            $post['city_'.$lang->language] = trim($post['city_'.$lang->language]);
            $post['state_'.$lang->language] = trim($post['state_'.$lang->language]);
            $post['longitude_'.$lang->language] = trim($post['longitude_'.$lang->language]);
            $post['latitude_'.$lang->language] = trim($post['latitude_'.$lang->language]);

            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]=="") $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            $post['alias_'.$lang->language] = JApplication::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias2Group($post['alias_'.$lang->language], $lang->language, $post['product_id'])){
                $post['alias_'.$lang->language] = "";
                JError::raiseWarning("", _JSHOP_ERROR_ALIAS_ALREADY_EXIST);
            }            
            $post['description_'.$lang->language] = JRequest::getVar('description'.$lang->id,'','post',"string", 2);
            $post['short_description_'.$lang->language] = JRequest::getVar('short_description_'.$lang->language,'','post',"string", 2);
        }
        
        $dispatcher->trigger('onBeforeDisplaySaveProduct', array(&$post, &$product) );

        if (!$product->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=partners");
            return 0;
        }
        
        if (($product->min_price==0 || $product->product_price==0) && !$jshopConfig->user_as_catalog && $product->parent_id==0){
            JError::raiseNotice("", _JSHOP_YOU_NOT_SET_PRICE);    
        }
        
        if (isset($post['set_main_image'])) {
            $image= JSFactory::getTable('image', 'jshop');
            $image->load($post['set_main_image']);
            if ($image->image_id){
                $product->image = $image->image_name;
            }
        }
        
        if (!$product->store()){
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE."<br>".$product->_error);
            $this->setRedirect("index.php?option=com_jshopping&controller=partners&task=edit&product_id=".$product->product_id);
            return 0;
        }

        $product_id = $product->product_id;

        foreach($categories as $category){
            if ($category == 1){

                $post['permanent'] = $post['permanent'] == 1 ? true : false;
                $post['male'] = $post['male'] == 1 ? true : false;
                $post['female'] = $post['female'] == 1 ? true : false;

                if (!$product_extra_options->bind($post)) {
                    JError::raiseWarning("",_JSHOP_ERROR_BIND);
                    $this->setRedirect("index.php?option=com_jshopping&controller=partners");
                    return 0;
                }

                if(empty($product_extra_options->product_id)){
                    $product_extra_options->product_id = $product_id;
                    $query = "INSERT INTO `#__products_offer_extra_options` (product_id) VALUES (" . $product_id . ")";
                    $db->setQuery($query);
                    $db->query();
                }

                if (!$product_extra_options->store()){
                    JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE."<br>".$product->_error);
                    $this->setRedirect("index.php?option=com_jshopping&controller=partners&task=edit&product_id=".$product->product_id);
                    return 0;
                }
            }
        }
        
        $dispatcher->trigger('onAfterSaveProduct', array(&$product));

        if ($jshopConfig->admin_show_product_video && $product->parent_id==0) {
            $_products->uploadVideo($product, $product_id, $post);
        }

        $_products->uploadImages($product, $product_id, $post);

        if ($jshopConfig->admin_show_product_files){
            $_products->uploadFiles($product, $product_id, $post);
        }

        $_products->saveAttributes($product, $product_id, $post);
        
        if ($jshopConfig->admin_show_freeattributes){
            $_products->saveFreeAttributes($product_id, $post['freeattribut']);
        }
        
        if ($post['product_is_add_price']){
            $_products->saveAditionalPrice($product_id, $post['product_add_discount'], $post['quantity_start'], $post['quantity_finish']);
        }
        
        if ($product->parent_id==0){
            $_products->setCategoryToProduct($product_id, $post['category_id']);
        }
        
        $_products->saveRelationProducts($product, $product_id, $post);
        $_products->saveProductOptions($product_id, (array)$post['options']);
        
        $dispatcher->trigger('onAfterSaveProductEnd', array($product->product_id) );

        if ($product->parent_id!=0){
            print "<script type='text/javascript'>window.close();</script>";            
            die();
        }

        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=partners&task=edit&product_id=".$product->product_id."&category_id=".$post['category_id'][0], 'Saved');
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=partners&category_id=".$post['category_id'][0], 'Saved');
        }
    }
    
    function publish(){
        $post = JRequest::get('post');
        $cat_id = $post["category_id"][0];

        $this->_publishProduct(1);
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$cat_id);
    }
    
    function unpublish(){
        $post = JRequest::get('post');
        $cat_id = $post["category_id"][0];

        $this->_publishProduct(0);
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$cat_id);
    }    
    
    function _publishProduct($flag) {
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $cid = JRequest::getVar('cid');        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishProduct', array(&$cid, &$flag) );
        foreach ($cid as $key => $value){
            $query = "UPDATE `#__jshopping_products` SET `product_publish` = '" . $db->escape($flag) . "' WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        
        $dispatcher->trigger( 'onAfterPublishProduct', array(&$cid, &$flag) );
    }
    
    function editlist(){
        $cid = JRequest::getVar('cid');
        if (count($cid)==1){
            $this->setRedirect("index.php?option=com_jshopping&controller=products&task=edit&product_id=".$cid[0]);
            return 0;
        }
        $id_vendor_cuser = getIdVendorForCUser();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onLoadEditListProduct', array());
        
        $products = JSFactory::getModel("products");
        $product = JSFactory::getTable('product', 'jshop');
        $_lang = JSFactory::getModel("languages");
        
        $_tax = JSFactory::getModel("taxes");
        $all_taxes = $_tax->getAllTaxes();
        
        $list_tax = array();
        $list_tax[] = JHTML::_('select.option', -1, "- - -",'tax_id','tax_name');
        foreach($all_taxes as $tax){
            $list_tax[] = JHTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
        }
        if (count($all_taxes)==0) $withouttax = 1; else $withouttax = 0;

        $categories = buildTreeCategory(0,1,0);
        
        $manuf1 = array();
        $manuf1[-1] = new stdClass();
        $manuf1[-1]->manufacturer_id = '-1';
        $manuf1[-1]->name = "- - -";
        $manuf1[0] = new stdClass();
        $manuf1[0]->manufacturer_id = '0';
        $manuf1[0]->name = _JSHOP_NONE;

        $_manufacturer =JSFactory::getModel('manufacturers');
        $manufs = $_manufacturer->getList();
        $manufs = array_merge($manuf1, $manufs);
        
        $price_modification = array();
        $price_modification[] = JHTML::_('select.option', '+','+', 'id','name');
        $price_modification[] = JHTML::_('select.option', '-','-', 'id','name');
        $price_modification[] = JHTML::_('select.option', '*','*', 'id','name');
        $price_modification[] = JHTML::_('select.option', '/','/', 'id','name');
        $price_modification[] = JHTML::_('select.option', '=','=', 'id','name');
        $price_modification[] = JHTML::_('select.option', '%','%', 'id','name');        
        $lists['price_mod_price'] = JHTML::_('select.genericlist', $price_modification,'mod_price','','id','name');
        $lists['price_mod_old_price'] = JHTML::_('select.genericlist', $price_modification,'mod_old_price','','id','name');
        
        if ($jshopConfig->admin_show_delivery_time) {
            $_deliveryTimes = JSFactory::getModel("deliveryTimes");
            $all_delivery_times = $_deliveryTimes->getDeliveryTimes();                
            $all_delivery_times0 = array();
            $all_delivery_times0[-1] = new stdClass();
            $all_delivery_times0[-1]->id = '-1';
            $all_delivery_times0[-1]->name = "- - -";
            $all_delivery_times0[0] = new stdClass();
            $all_delivery_times0[0]->id = '0';
            $all_delivery_times0[0]->name = _JSHOP_NONE;
            $lists['deliverytimes'] = JHTML::_('select.genericlist', array_merge($all_delivery_times0, $all_delivery_times),'delivery_times_id','class = "inputbox" size = "1"','id','name');
        }
        //

        // units
        $_units = JSFactory::getModel("units");
        $allunits = $_units->getUnits();
        if ($jshopConfig->admin_show_product_basic_price){
            $lists['basic_price_units'] = JHTML::_('select.genericlist', $allunits, 'basic_price_unit_id','class = "inputbox"','id','name');
        }        
        //
        
        // product labels
        if ($jshopConfig->admin_show_product_labels) {
            $_labels = JSFactory::getModel("productLabels");
            $alllabels = $_labels->getList();
            $first = array();
            $first[] = JHTML::_('select.option', '-1',"- - -", 'id','name');
            $first[] = JHTML::_('select.option', '0',_JSHOP_SELECT, 'id','name');
            $lists['labels'] = JHTML::_('select.genericlist', array_merge($first, $alllabels), 'label_id','class = "inputbox"','id','name');
        }
        //
        
        // access rights
        $accessgroups = getAccessGroups();        
        $first = array();
        $first[] = JHTML::_('select.option', '-1',"- - -", 'id','title');
        $lists['access'] = JHTML::_('select.genericlist', array_merge($first, $accessgroups), 'access','class = "inputbox"','id','title');
        
        //currency
        $current_currency = $product->currency_id;
        if (!$current_currency) $current_currency = $jshopConfig->mainCurrency;
        $_currency = JSFactory::getModel("currencies");
        $currency_list = $_currency->getAllCurrencies();
        $lists['currency'] = JHTML::_('select.genericlist', $currency_list, 'currency_id','class = "inputbox"','currency_id','currency_code', $current_currency);
        
        // vendors
        $display_vendor_select = 0;
        if ($jshopConfig->admin_show_vendors){
            $_vendors = JSFactory::getModel("vendors");
            $listvebdorsnames = $_vendors->getAllVendorsNames(1);
            $first = array();
            $first[] = JHTML::_('select.option', '-1',"- - -", 'id','name');
            $lists['vendors'] = JHTML::_('select.genericlist', array_merge($first, $listvebdorsnames), 'vendor_id','class = "inputbox" size = "1"', 'id', 'name');
            
            $display_vendor_select = 1;
            if ($id_vendor_cuser > 0) $display_vendor_select = 0;
        }		
        
		//extra field
		$category_select_onclick = "";
        if ($jshopConfig->admin_show_product_extra_field) {            
			$category_select_onclick = 'onclick="reloadProductExtraField(\'0\')" ';
            $tmpl_extra_fields = $this->_getHtmlProductExtraFields();
        }
        //
        
        $published = array();
        $published[] = JHTML::_('select.option', '-1', "- - -", 'value', 'name');
        $published[] = JHTML::_('select.option', 0, _JSHOP_UNPUBLISH, 'value', 'name');
        $published[] = JHTML::_('select.option', 1, _JSHOP_PUBLISH, 'value', 'name');       
        $lists['product_publish'] = JHTML::_('select.genericlist', $published, 'product_publish', '', 'value', 'name');
        
        $lists['manufacturers'] = JHTML::_('select.genericlist', $manufs,'product_manufacturer_id','class = "inputbox" size = "1"','manufacturer_id','name');
        
        $lists['tax'] = JHTML::_('select.genericlist', $list_tax,'product_tax_id','class = "inputbox" size = "1"','tax_id','tax_name');
        $lists['categories'] = JHTML::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox" size="10" multiple = "multiple" '.$category_select_onclick, 'category_id', 'name');
        $lists['templates'] = getTemplates('product', "", 1);
        
        $view=$this->getView("product_edit", 'html');
        $view->setLayout("editlist");
        $view->assign('lists', $lists);
        $view->assign('cid', $cid);
        $view->assign('config', $jshopConfig);        
        $view->assign('withouttax', $withouttax);
        $view->assign('display_vendor_select', $display_vendor_select);
		$view->assign('tmpl_extra_fields', $tmpl_extra_fields);
        $view->assign('etemplatevar', '');
        $dispatcher->trigger('onBeforeDisplayEditListProductView', array(&$view) );
        $view->editGroup();
        
    }
    
    function savegroup(){
        $jshopConfig = JSFactory::getConfig();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforSaveListProduct', array() );
        
        $cid = JRequest::getVar('cid');
        $post = JRequest::get('post');
        $_products = JSFactory::getModel("products");
        foreach($cid as $id){
            $product = JSFactory::getTable('product', 'jshop');
            $product->load($id);
            if ($post['access']!=-1){
                $product->set('access', $post['access']);
            }
            if ($post['product_publish']!=-1){
                $product->set('product_publish', $post['product_publish']);
            }
            if ($post['product_weight']!=""){
                $product->set('product_weight', $post['product_weight']);
            }
            if ($post['product_quantity']!=""){
                $product->set('product_quantity', $post['product_quantity']);
                $product->set('unlimited', 0);
            }            
            if (isset($post['unlimited']) && $post['unlimited']){
                $product->set('product_quantity', 1);
                $product->set('unlimited', 1);
            }
            if (isset($post['product_template']) && $post['product_template'] != -1){
                $product->set('product_template', $post['product_template']);
            }
            if (isset($post['product_tax_id']) && $post['product_tax_id']!=-1){
                $product->set('product_tax_id', $post['product_tax_id']);
            }
            if (isset($post['product_manufacturer_id']) && $post['product_manufacturer_id']!=-1){
                $product->set('product_manufacturer_id', $post['product_manufacturer_id']);
            }
            if (isset($post['vendor_id']) && $post['vendor_id']!=-1){
                $product->set('vendor_id', $post['vendor_id']);
            }
            if (isset($post['delivery_times_id']) && $post['delivery_times_id']!=-1){
                $product->set('delivery_times_id', $post['delivery_times_id']);
            }
            if (isset($post['label_id']) && $post['label_id']!=-1){
                $product->set('label_id', $post['label_id']);
            }
            if (isset($post['weight_volume_units']) && $post['weight_volume_units']!=""){
                $product->set('weight_volume_units', $post['weight_volume_units']);
                $product->set('basic_price_unit_id', $post['basic_price_unit_id']);
            }
            if ($post['product_price']!=""){
                $oldprice = $product->product_price;
                $price = $_products->getModPrice($product->product_price, saveAsPrice($post['product_price']), $post['mod_price']);
                $product->set('product_price', $price);
                if ($post['use_old_val_price']==1){
                    $product->set('product_old_price', $oldprice);
                }
            }
            if (isset($post['product_old_price']) && $post['product_old_price']!=""){
                $price = $_products->getModPrice($product->product_old_price, saveAsPrice($post['product_old_price']), $post['mod_old_price']);
                $product->set('product_old_price', $price);
            }
            if (isset($post['product_price']) && $post['product_price']!="" || $post['product_old_price']!=""){                
                $product->set('currency_id', $post['currency_id']);
            }
            if (isset($post['category_id']) && $post['category_id']){
                $_products->setCategoryToProduct($id, $post['category_id']);
            }
			if ($jshopConfig->admin_show_product_extra_field){				
				$_productfields = JSFactory::getModel("productFields");
				$list_productfields = $_productfields->getList(1);
				foreach($list_productfields as $v){
					$_nef = 'extra_field_'.$v->id;
					switch($v->type){
						case 0:							
							if (isset($post['productfields'][$_nef]) and is_array($post['productfields'][$_nef]) and count($post['productfields'][$_nef]) > 0){
								if ($v->multilist == 1 || ($v->multilist == 0 and !in_array(0,$post['productfields'][$_nef]))){
									$product->set($_nef, implode(',', $post['productfields'][$_nef]));
                                }
							}
						break;
						case 1:
							if (isset($post[$_nef]) and $post[$_nef] != ''){
								$product->set($_nef, $post[$_nef]);
                            }
						break;
					}
				}
			}
            $_products->updatePriceAndQtyDependAttr($id, $post);
            $product->store();
            
            if ($post['product_price']!=""){
                $mprice = $product->getMinimumPrice();
                $product->set('min_price', $mprice);
            }
            if (!$product->unlimited){
                $qty = $product->getFullQty();
                $product->set('product_quantity', $qty);
            }
			$product->date_modify = getJsDate();
            $product->store();
            unset($product);
        }

        $dispatcher->trigger('onAfterSaveListProductEnd', array($cid, $post) );
        $this->setRedirect("index.php?option=com_jshopping&controller=products", _JSHOP_PRODUCT_SAVED);
    }

    function copy(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $text = array();
        $cid = JRequest::getVar('cid');
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeCopyProduct', array(&$cid) );
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $_products = JSFactory::getModel("products");
        // Get all data about products
        $tables = array('attr', 'attr2', 'images', 'prices', 'relations', 'to_categories', 'videos', 'files');
        foreach ($cid as $key=>$value){
            $product = JSFactory::getTable('product', 'jshop');
            $product->load($value);
            $product->product_id = null;                        
            foreach($languages as $lang){
                $name_alias = 'alias_'.$lang->language;
                if ($product->$name_alias){
                    $product->$name_alias = $product->$name_alias.date('ymdHis');
                }
            }
            $product->product_date_added = getJsDate();
            $product->date_modify = getJsDate();
            $product->average_rating = 0;
            $product->reviews_count = 0;
            $product->hits = 0;
            $product->store();

            $array = array();
            foreach($tables as $table){
                $query = "SELECT * FROM `#__jshopping_products_".$table."` AS prod_table WHERE prod_table.product_id = '" . $db->escape($value) . "'";
                $db->setQuery($query);
                $array[] = $db->loadAssocList();
            }

            $i = 0;
            foreach($array as $key2=>$value2){
                if (count($value2)){
                    foreach($value2 as $key3=>$value3){
                        $db->setQuery($_products->copyProductBuildQuery($tables[$i], $value3, $product->product_id));
                        $db->query();
                    }
                }
                $i++;                
            }
            
            //change order in category
            $query = "select * from #__jshopping_products_to_categories where product_id='".$product->product_id."'";
            $db->setQuery($query);
            $list = $db->loadObjectList();
        
            foreach($list as $val){
                $query = "select max(product_ordering) as k from #__jshopping_products_to_categories where category_id='".$val->category_id."' ";
                $db->setQuery($query);
                $ordering = $db->loadResult() + 1;
                
                $query = "update #__jshopping_products_to_categories set product_ordering='".$ordering."' where category_id='".$val->category_id."' and product_id='".$product->product_id."' ";
                $db->setQuery($query);
                $db->query();
            }
			
			$query = "update #__jshopping_products_attr set ext_attribute_product_id=0 where product_id='".$product->product_id."'";
            $db->setQuery($query);
            $list = $db->loadObjectList();
            
			$dispatcher->trigger('onCopyProductEach', array(&$cid, &$key, &$value, &$product));
            $text[] = sprintf(_JSHOP_PRODUCT_COPY_TO, $value, $product->product_id)."<br>";
        }
        
        $dispatcher->trigger('onAfterCopyProduct', array(&$cid));
        
        $this->setRedirect("index.php?option=com_jshopping&controller=products", implode("</li><li>",$text));
    }
    
    function order(){
        $order = JRequest::getVar("order");
        $product_id = JRequest::getInt("product_id");
        $number = JRequest::getInt("number");
        $category_id = JRequest::getInt("category_id");

        $db = JFactory::getDBO();
        switch ($order) {
            case 'up':
                $query = "SELECT a.*
                       FROM `#__jshopping_products_to_categories` AS a
                       WHERE a.product_ordering < '" . $number . "' AND a.category_id = '" . $category_id . "'
                       ORDER BY a.product_ordering DESC
                       LIMIT 1";
                break;
            case 'down':
                $query = "SELECT a.*
                       FROM `#__jshopping_products_to_categories` AS a
                       WHERE a.product_ordering > '" . $number . "' AND a.category_id = '" . $category_id . "'
                       ORDER BY a.product_ordering ASC
                       LIMIT 1";
        }

        $db->setQuery($query);
        $row = $db->loadObject();

        $query1 = "UPDATE `#__jshopping_products_to_categories` AS a
                     SET a.product_ordering = '" . $row->product_ordering . "'
                     WHERE a.product_id = '" . $product_id . "' AND a.category_id = '" . $category_id . "'";
        $query2 = "UPDATE `#__jshopping_products_to_categories` AS a
                     SET a.product_ordering = '" . $number . "'
                     WHERE a.product_id = '" . $row->product_id . "' AND a.category_id = '" . $category_id . "'";
        $db->setQuery($query1);
        $db->query();
        $db->setQuery($query2);
        $db->query();
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$category_id); 
    }
    
    function saveorder(){
        $db = JFactory::getDBO();
        $category_id = JRequest::getInt("category_id");
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        
        foreach($cid as $k=>$product_id){
            $query = "UPDATE `#__jshopping_products_to_categories`
                     SET product_ordering = '".intval($order[$k])."'
                     WHERE product_id = '".intval($product_id)."' AND category_id = '".intval($category_id)."'";
            $db->setQuery($query);
            $db->query();        
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$category_id); 
    }
    
    function remove(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $text = array();
        $cid = JRequest::getVar('cid');

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeRemoveProduct', array(&$cid) );

        foreach($cid as $key=>$value){

            $product = JSFactory::getTable('product', 'jshop');
            $product->load($value);
            $query = "DELETE FROM `#__jshopping_products` WHERE `product_id` = '".$db->escape($value)."' or `parent_id` = '".$db->escape($value)."' ";
            $db->setQuery($query);
            $db->query();

            $query = "DELETE FROM `#__products_offer_extra_options` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();

            $query = "DELETE FROM `#__jshopping_products_attr` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
            
            $query = "DELETE FROM `#__jshopping_products_attr2` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
            
            $query = "DELETE FROM `#__jshopping_products_prices` WHERE `product_id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();
            
            $query = "DELETE FROM `#__jshopping_products_relations` WHERE `product_id` = '" . $db->escape($value) . "' OR `product_related_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();

            $query = "DELETE FROM `#__jshopping_products_to_categories` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();

            $images = $product->getImages();
            $videos = $product->getVideos();
            $files = $product->getFiles();

            if (count($images)){
                foreach($images as $image){
                    $query = "select count(*) as k from #__jshopping_products_images where image_name='".$db->escape($image->image_name)."' and product_id!='".$db->escape($value)."'";                    
                    $db->setQuery($query);
                    if (!$db->loadResult()){
                        @unlink(getPatchProductImage($image->image_name,'',1));
                        @unlink(getPatchProductImage($image->image_name,'',2));
                        @unlink(getPatchProductImage($image->image_name,'',3));
                    }
                }
            }
            
            $query = "DELETE FROM `#__jshopping_products_images` WHERE `product_id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();

            if (count($videos)) {
                foreach ($videos as $video) {
                    $query = "select count(*) as k from #__jshopping_products_videos where video_name='".$db->escape($video->video_name)."' and product_id!='".$db->escape($value)."'";                    
                    $db->setQuery($query);
                    if (!$db->loadResult()){
                        @unlink($jshopConfig->video_product_path . "/" . $video->video_name);
                        if ($video->video_preview){
                            @unlink($jshopConfig->video_product_path . "/" . $video->video_preview);
                        }
                    }
                }
            }
            
            $query = "DELETE FROM `#__jshopping_products_videos` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
            
            if (count($files)){
                foreach($files as $file){
                    $query = "select count(*) as k from #__jshopping_products_files where demo='".$db->escape($file->demo)."' and product_id!='".$db->escape($value)."'";
                    $db->setQuery($query);
                    if (!$db->loadResult()){
                        @unlink($jshopConfig->demo_product_path."/".$file->demo);
                    }
                    
                    $query = "select count(*) as k from #__jshopping_products_files where file='".$db->escape($file->file)."' and product_id!='".$db->escape($value)."'";
                    $db->setQuery($query);
                    if (!$db->loadResult()){
                        @unlink($jshopConfig->files_product_path."/".$file->file);
                    }            
                }
            }
            
            $query = "DELETE FROM `#__jshopping_products_files` WHERE `product_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
            
            $text[]= sprintf(_JSHOP_PRODUCT_DELETED, $value)."<br>";
        }
        
        $dispatcher->trigger( 'onAfterRemoveProduct', array(&$cid) );

        $cat_id = JRequest::getVar('category_id');
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$cat_id, implode("</li><li>",$text));
    }
    
    function cancel(){
        $post = JRequest::get('post');
        $cat_id = $post["category_id"][0];

        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$cat_id);
    }
    
    function delete_foto(){
        $image_id = JRequest::getInt("id");
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        
        $query = "SELECT * FROM `#__jshopping_products_images` WHERE image_id = '".$db->escape($image_id)."'";
        $db->setQuery($query);
        $row = $db->loadObject();
        
        $query = "DELETE FROM `#__jshopping_products_images` WHERE `image_id` = '".$db->escape($image_id)."'";
        $db->setQuery($query);
        $db->query();
        
        $query = "select count(*) as k from #__jshopping_products_images where image_name='".$db->escape($row->image_name)."' and product_id!='".$db->escape($row->product_id)."'";
        $db->setQuery($query);
        if (!$db->loadResult()){
            @unlink(getPatchProductImage($row->image_name,'',1));
            @unlink(getPatchProductImage($row->image_name,'',2));
            @unlink(getPatchProductImage($row->image_name,'',3));
        }
        
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($row->product_id);
        if ($product->image==$row->image_name){
            $product->image = '';
            $list_images = $product->getImages();
            if (count($list_images)){
                $product->image = $list_images[0]->image_name;
            } 
            $product->store();
        }
        
        die();
    }
    
    function delete_video(){
        $video_id = JRequest::getInt("id");
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        
        $query = "SELECT * FROM `#__jshopping_products_videos` WHERE video_id = '" . $db->escape($video_id) . "'";
        $db->setQuery($query);
        $row = $db->loadObject();
        
        $query = "select count(*) from #__jshopping_products_videos where video_name='".$db->escape($row->video_name)."' and product_id!='".$db->escape($row->product_id)."'";                    
        $db->setQuery($query);
        if (!$db->loadResult()){
            @unlink($jshopConfig->video_product_path . "/" . $row->video_name);
            if ($row->video_preview){
                @unlink($jshopConfig->video_product_path . "/" . $row->video_preview);
            }
        }

        $query = "DELETE FROM `#__jshopping_products_videos` WHERE `video_id` = '" . $db->escape($video_id) . "'";
        $db->setQuery($query);
        $db->query();
        die();
    }
    
    function delete_file(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $id = JRequest::getInt("id");
        $type = JRequest::getVar("type");
        
        $query = "SELECT * FROM `#__jshopping_products_files` WHERE `id` = '" . $db->escape($id) . "'";
        $db->setQuery($query);
        $row = $db->loadObject();
        
        $delete_row = 0;
                
        if ($type=="demo"){
            if ($row->file==""){
                $query = "DELETE FROM `#__jshopping_products_files` WHERE `id` = '" . $db->escape($id) . "'";
                $db->setQuery($query);
                $db->query();
                $delete_row = 1;
            }else{
                $query = "update `#__jshopping_products_files` set `demo`='', demo_descr='' WHERE `id` = '" . $db->escape($id) . "'";
                $db->setQuery($query);
                $db->query();
            }
            
            $query = "select count(*) as k from #__jshopping_products_files where demo='".$db->escape($row->demo)."'";
            $db->setQuery($query);
            if (!$db->loadResult()){
                @unlink($jshopConfig->demo_product_path."/".$row->demo);
            }
        }
        
        if ($type=="file"){
            if ($row->demo==""){
                $query = "DELETE FROM `#__jshopping_products_files` WHERE `id` = '" . $db->escape($id) . "'";
                $db->setQuery($query);
                $db->query();
                $delete_row = 1;
            }else{
                $query = "update `#__jshopping_products_files` set `file`='', file_descr='' WHERE `id` = '" . $db->escape($id) . "'";
                $db->setQuery($query);
                $db->query();
            }
            
            $query = "select count(*) as k from #__jshopping_products_files where file='".$db->escape($row->file)."'";
            $db->setQuery($query);
            if (!$db->loadResult()){
                @unlink($jshopConfig->files_product_path."/".$row->file);
            }
        }
        print $delete_row;
        die();    
    }
    
    function search_related(){
        $mainframe = JFactory::getApplication();
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();        
        $products = JSFactory::getModel("products");        
        
        $text_search = JRequest::getVar("text");
        $limitstart = JRequest::getInt("start");
        $no_id = JRequest::getInt("no_id");
        $limit = 20;
        
        $filter = array("without_product_id"=>$no_id, "text_search"=>$text_search);
        $total = $products->getCountAllProducts($filter);
        $rows = $products->getAllProducts($filter, $limitstart, $limit);
        $page = ceil($total/$limit);

        $view=$this->getView("product_list", 'html');
        $view->setLayout("search_related");
        $view->assign('rows', $rows);
        $view->assign('config', $jshopConfig);
        $view->assign('limit', $limit);
        $view->assign('pages', $page);
        $view->assign('no_id', $no_id);
        $view->display();
        die();
    } 
    
    function product_extra_fields(){
        $product_id = JRequest::getInt("product_id");
        $cat_id = JRequest::getVar("cat_id");
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);
        
        $categorys = array();
        if (is_array($cat_id)){
            foreach($cat_id as $cid){
                $categorys[] = intval($cid);        
            }
        }        
        
        print $this->_getHtmlProductExtraFields($categorys, $product);
        die();    
    }
    
    function _getHtmlProductExtraFields($categorys=array(), $product = null){
		if($product === null) $product = new stdClass;
		$_productfields = JSFactory::getModel("productFields");
        $list = $_productfields->getList(1);

        $_productfieldvalues = JSFactory::getModel("productFieldValues");
        $listvalue = $_productfieldvalues->getAllList();
        
        $f_option = array();
        $f_option[] = JHTML::_('select.option', 0, " - - - ", 'id', 'name');
        
        $fields = array();
        foreach($list as $v){
            $insert = 0;
            if ($v->allcats==1){
                $insert = 1;
            }else{
                $cats = unserialize($v->cats);
                foreach($categorys as $catid){
                    if (in_array($catid, $cats)) $insert = 1;
                }
            }
            if ($insert){
                $obj = new stdClass();
                $obj->id = $v->id;
                $obj->name = $v->name;
                $obj->groupname = $v->groupname;
                $tmp = array();
                foreach($listvalue as $lv){
                    if ($lv->field_id==$v->id) $tmp[] = $lv;
                }                
                $name = 'extra_field_'.$v->id;
                if ($v->type==0){
                    if ($v->multilist==1){
                        $attr = 'multiple="multiple" size="10"';
                    }else{
                        $attr = "";
                    }
                    $obj->values = JHTML::_('select.genericlist', array_merge($f_option, $tmp), 'productfields['.$name.'][]', $attr, 'id', 'name', explode(',',$product->$name));
                }else{
                    $obj->values = "<input type='text' name='".$name."' value='".$product->$name."' />";
                }
                $fields[] = $obj;
            }
        }
        $view=$this->getView("product_edit", 'html');
        $view->setLayout("extrafields_inner");
        $view->assign('fields', $fields);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadTemplateHtmlProductExtraFields', array(&$view));
        return $view->loadTemplate();
    }

    function getfilesale(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $id = JRequest::getVar('id');

        $file = JSFactory::getTable('productFiles', 'jshop');
        $file->load($id);
        $downloadFile = $file->file;

        $file_name = $jshopConfig->files_product_path."/".$downloadFile;
        if (!file_exists($file_name)){
            JError::raiseWarning('', "Error. File not exist");
            return 0;
        }

        ob_end_clean();
        @set_time_limit(0);
        $fp = fopen($file_name, "rb");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . (string)(filesize($file_name)));
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header("Content-Transfer-Encoding: binary");

        while( (!feof($fp)) && (connection_status()==0) ){
            print(fread($fp, 1024*8));
            flush();
        }
        fclose($fp);
        die();
    }
    
    function loadproductinfo(){        
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onLoadInfoProduct', array());
        $id_vendor_cuser = getIdVendorForCUser();        
        $product_id = JRequest::getInt('product_id');
		$currency_id = JRequest::getInt('currency_id');
        $layout = JRequest::getVar('layout','productinfo_json');
        
        if ($id_vendor_cuser && $product_id){
            checkAccessVendorToProduct($id_vendor_cuser, $product_id);
        }
        
        $products = JSFactory::getModel("products");
        
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);
        $product->getDescription();
        
		$currency = JSFactory::getTable('currency', 'jshop');
        $currency->load($currency_id);
        if ($currency_id){
            $currency_value = $currency->currency_value;
        }else{
            $currency_value = 1;
        }
        $product_price = getPriceFromCurrency($product->product_price, $product->currency_id, $currency_value);
		
        $res = array();
        $res['product_id'] = $product->product_id;
        $res['product_ean'] = $product->product_ean;
        $res['tokens'] = $product->tokens;
        $res['product_price'] = $product_price;
        $res['delivery_times_id'] = $product->delivery_times_id;
        $res['vendor_id'] = fixRealVendorId($product->vendor_id);
        $res['product_weight'] = $product->product_weight;
        $res['product_tax'] = $product->getTax();
        $res['product_name'] = $product->name;
		$res['thumb_image'] = getPatchProductImage($product->image,'thumb');

        $view=$this->getView("product_edit", 'html');
        $view->setLayout($layout);
        $view->assign('res', $res);
        $view->assign('edit', null);
        $view->assign('product', $product);
        $dispatcher->trigger('onBeforeDisplayLoadInfoProduct', array(&$view) );
        $view->display();
    die();
    }

	function getvideocode() {
		$video_id = JRequest::getInt('video_id');
		$productvideo = JSFactory::getTable('productvideo', 'jshop');
		$productvideo->load($video_id);
		
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterLoadVideoCodeForProduct', array(&$productvideo));
		
		$view=$this->getView('product_edit', 'html');
        $view->setLayout('product_video_code');
        $view->assign('code', $productvideo->video_code);
        
		$dispatcher->trigger('onBeforeDisplayVideoCodeForProduct', array(&$view) );
        $view->display();
		die();
	}
}
?>