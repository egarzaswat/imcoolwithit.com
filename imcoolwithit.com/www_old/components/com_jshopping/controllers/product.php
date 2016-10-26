<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die('Restricted access');
jimport('joomla.application.component.controller');

class JshoppingControllerProduct extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerProduct', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $mainframe =JFactory::getApplication();
        $db =JFactory::getDBO();
        $ajax = JRequest::getInt('ajax');
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        JSFactory::loadJsFilesLightBox();
        $session =JFactory::getSession();
        $tmpl = JRequest::getVar("tmpl");
        if ($tmpl!="component"){
            $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        }
        $product_id = JRequest::getInt('product_id');
        $category_id = JRequest::getInt('category_id');
        $attr = JRequest::getVar("attr");
        $back_value = $session->get('product_back_value');        
        if (!isset($back_value['pid'])) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if ($back_value['pid']!=$product_id) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if (!is_array($back_value['attr'])) $back_value['attr'] = array();
        if (count($back_value['attr'])==0 && is_array($attr)) $back_value['attr'] = $attr;
        $dispatcher =JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadProduct', array(&$product_id, &$category_id, &$back_value));
        $dispatcher->trigger('onBeforeLoadProductList', array());

        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);
        $listcategory = $product->getCategories(1);

        if (!getDisplayPriceForProduct($product->product_price)){
            $jshopConfig->attr_display_addprice = 0;
        }
        
        $attributesDatas = $product->getAttributesDatas($back_value['attr']);
        $product->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];
        
        $attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);
        if (count($attributes)){
            $_attributevalue = JSFactory::getTable('AttributValue', 'jshop');
            $all_attr_values = $_attributevalue->getAllAttributeValues();
        }else{
            $all_attr_values = array();
        }

        $session->set('product_back_value',array());
        $product->getExtendsData();

        $category = JSFactory::getTable('category', 'jshop');
        $category->load($category_id);
        $category->name = $category->getName();
        
		$dispatcher->trigger('onBeforeCheckProductPublish', array(&$product, &$category, &$category_id, &$listcategory));
        if ($category->category_publish==0 || $product->product_publish==0 || !in_array($product->access, $user->getAuthorisedViewLevels()) || !in_array($category_id, $listcategory)){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }
        
        if (getShopMainPageItemid()==JRequest::getInt('Itemid')){
            appendExtendPathway($category->getTreeChild(), 'product');
        }
        appendPathWay($product->name);
        if ($product->meta_title=="") $product->meta_title = $product->name;
        setMetaData($product->meta_title, $product->meta_keyword, $product->meta_description);
        
        $product->hit();
        
        $product->product_basic_price_unit_qty = 1;
        if ($jshopConfig->admin_show_product_basic_price){
            $product->getBasicPriceInfo();
        }else{
            $product->product_basic_price_show = 0;
        }
        
        $view_name = "product";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        
        if ($product->product_template=="") $product->product_template = "default";
        $view->setLayout("product_".$product->product_template);
        
        $_review = JSFactory::getTable('review', 'jshop');
        if(($allow_review = $_review->getAllowReview()) > 0) {
            $arr_marks = array();
            $arr_marks[] = JHTML::_('select.option',  '0', _JSHOP_NOT, 'mark_id', 'mark_value' );
            for ($i = 1; $i <= $jshopConfig->max_mark; $i++) {
                $arr_marks[] = JHTML::_('select.option', $i, $i, 'mark_id', 'mark_value' );
            }
            $text_review = '';
            $select_review = JHTML::_('select.genericlist', $arr_marks, 'mark', 'class="inputbox" size="1"','mark_id', 'mark_value' );
        } else {
            $select_review = '';
            $text_review = $_review->getText();
        }
        if ($allow_review){
            JSFactory::loadJsFilesRating();
        }

        if ($jshopConfig->product_show_manufacturer_logo || $jshopConfig->product_show_manufacturer){
            $product->manufacturer_info = $product->getManufacturerInfo();
            if (!isset($product->manufacturer_info)){
                $product->manufacturer_info = new stdClass();
                $product->manufacturer_info->manufacturer_logo = '';
                $product->manufacturer_info->name = '';
            }
        }else{
            $product->manufacturer_info = new stdClass();
            $product->manufacturer_info->manufacturer_logo = '';
            $product->manufacturer_info->name = '';
        }
        
        if ($jshopConfig->product_show_vendor){
            $vendorinfo = $product->getVendorInfo();
            $vendorinfo->urllistproducts = SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$vendorinfo->id,1);
            $vendorinfo->urlinfo = SEFLink("index.php?option=com_jshopping&controller=vendor&task=info&vendor_id=".$vendorinfo->id,1);
            $product->vendor_info = $vendorinfo;
        }else{
            $product->vendor_info = null;
        }        
        
        if ($jshopConfig->admin_show_product_extra_field){
            $product->extra_field = $product->getExtraFields();
        }else{
            $product->extra_field = null;
        }
        
        if ($jshopConfig->admin_show_freeattributes){
            $product->getListFreeAttributes();
            foreach($product->freeattributes as $k=>$v){
                if (!isset($back_value['freeattr'][$v->id])) $back_value['freeattr'][$v->id] = '';
                $product->freeattributes[$k]->input_field = '<input type="text" class="inputbox" size="40" name="freeattribut['.$v->id.']" value="'.$back_value['freeattr'][$v->id].'" />';
            }
            $attrrequire = $product->getRequireFreeAttribute();
            $product->freeattribrequire = count($attrrequire);
        }else{
            $product->freeattributes = null;
            $product->freeattribrequire = 0;
        }
        if ($jshopConfig->product_show_qty_stock){
            $product->qty_in_stock = getDataProductQtyInStock($product);
        }
        
        if (!$jshopConfig->admin_show_product_labels) $product->label_id = null;
        if ($product->label_id){
            $image = getNameImageLabel($product->label_id);
            if ($image){
                $product->_label_image = $jshopConfig->image_labels_live_path."/".$image;
            }
            $product->_label_name = getNameImageLabel($product->label_id, 2);
        }
        
        $hide_buy = 0;
        if ($jshopConfig->user_as_catalog) $hide_buy = 1;
        if ($jshopConfig->hide_buy_not_avaible_stock && $product->product_quantity <= 0) $hide_buy = 1;
        
        $available = "";
        if ( ($product->getQty() <= 0) && $product->product_quantity >0 ){
            $available = _JSHOP_PRODUCT_NOT_AVAILABLE_THIS_OPTION;
        }elseif ($product->product_quantity <= 0){
            $available = _JSHOP_PRODUCT_NOT_AVAILABLE;
        }

        $product->_display_price = getDisplayPriceForProduct($product->getPriceCalculate());
        if (!$product->_display_price){
            $product->product_old_price = 0;
            $product->product_price_default = 0;
            $product->product_basic_price_show = 0;
            $product->product_is_add_price = 0;
            $product->product_tax = 0;
            $jshopConfig->show_plus_shipping_in_product = 0;
        }
        
        if (!$product->_display_price) $hide_buy = 1;

        $default_count_product = 1;
        if ($jshopConfig->min_count_order_one_product>1){
            $default_count_product = $jshopConfig->min_count_order_one_product;
        }
        if ($back_value['qty']){
            $default_count_product = $back_value['qty'];
        }

        if (trim($product->description)=="") $product->description = $product->short_description;
        
        if ($jshopConfig->use_plugin_content){
            changeDataUsePluginContent($product, "product");
        }
        
        $product->hide_delivery_time = 0;
        if (!$product->getDeliveryTimeId()){
            $product->hide_delivery_time = 1;
        }
        
        $product->button_back_js_click = "history.go(-1);";
        if ($session->get('jshop_end_page_list_product') && $jshopConfig->product_button_back_use_end_list){
            $product->button_back_js_click = "location.href='".$session->get('jshop_end_page_list_product')."';";
        }
		
        $displaybuttons = '';
        if ($jshopConfig->hide_buy_not_avaible_stock && $product->getQty() <= 0) $displaybuttons = 'display:none;';
        
        $product_images = $product->getImages();
        $product_videos = $product->getVideos();
        $product_demofiles = $product->getDemoFiles();

        $dispatcher->trigger('onBeforeDisplayProductList', array(&$product->product_related));
        $dispatcher->trigger('onBeforeDisplayProduct', array(&$product, &$view, &$product_images, &$product_videos, &$product_demofiles) );

        $view->assign('config', $jshopConfig);
        $view->assign('image_path', $jshopConfig->live_path.'/images');
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('image_product_path', $jshopConfig->image_product_live_path);
        $view->assign('video_product_path', $jshopConfig->video_product_live_path);
        $view->assign('video_image_preview_path', $jshopConfig->video_product_live_path);
        $view->assign('product', $product);
        $view->assign('category_id', $category_id);
        $view->assign('images', $product_images);
        $view->assign('videos', $product_videos);
        $view->assign('demofiles', $product_demofiles);
        $view->assign('attributes', $attributes);
        $view->assign('all_attr_values', $all_attr_values);
        $view->assign('related_prod', $product->product_related);
        $view->assign('path_to_image', $jshopConfig->live_path . 'images/');
        $view->assign('live_path', JURI::root());
        $view->assign('enable_wishlist', $jshopConfig->enable_wishlist);
        $view->assign('action', SEFLink('index.php?option=com_jshopping&controller=cart&task=add',1));
        $view->assign('urlupdateprice', SEFLink('index.php?option=com_jshopping&controller=product&task=ajax_attrib_select_and_price&product_id='.$product_id.'&ajax=1',1,1));
        if ($allow_review){
            $context = "jshoping.list.front.product.review";
            $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', 20, 'int');
            $limitstart = JRequest::getInt('limitstart');
            $total =  $product->getReviewsCount();
            $view->assign('reviews', $product->getReviews($limitstart, $limit));
            jimport('joomla.html.pagination');
            $pagination = new JPagination($total, $limitstart, $limit);
            $pagenav = $pagination->getPagesLinks();
            $view->assign('pagination', $pagenav);
			$view->assign('pagination_obj', $pagination);
            $view->assign('display_pagination', $pagenav!="");
        }
        $view->assign('allow_review', $allow_review);
        $view->assign('select_review', $select_review);
        $view->assign('text_review', $text_review);
        $view->assign('stars_count', floor($jshopConfig->max_mark / $jshopConfig->rating_starparts));
        $view->assign('parts_count', $jshopConfig->rating_starparts);
        $view->assign('user', $user);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $view->assign('hide_buy', $hide_buy);
        $view->assign('available', $available);
        $view->assign('default_count_product', $default_count_product);
        $view->assign('folder_list_products', "list_products");
        $view->assign('back_value', $back_value);
		$view->assign('displaybuttons', $displaybuttons);
        $dispatcher->trigger('onBeforeDisplayProductView', array(&$view));
        $view->display();
        $dispatcher->trigger('onAfterDisplayProduct', array(&$product));
        if ($ajax) die();
    }
    
    function getfile(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
		$user = JFactory::getUser();

        $id = JRequest::getInt('id'); 
        $oid = JRequest::getInt('oid');
        $hash = JRequest::getVar('hash');
        $rl = JRequest::getInt('rl');
        
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($oid);
        if ($order->file_hash!=$hash){
            JError::raiseError(500, "Error download file");
            return 0;
        }
        
        if (!in_array($order->order_status, $jshopConfig->payment_status_enable_download_sale_file)){
            JError::raiseWarning(500, _JSHOP_FOR_DOWNLOAD_ORDER_MUST_BE_PAID);
            return 0;
        }

        if ($rl==1){
            //fix for IE
            $newurl = JURI::root()."index.php?option=com_jshopping&controller=product&task=getfile&oid=".$oid."&id=".$id."&hash=".$hash; 
            print "<script type='text/javascript'>location.href='".$newurl."';</script>";
            die();
        }
		
		if ($jshopConfig->user_registered_download_sale_file && $order->user_id>0 && $order->user_id!=$user->id){
            checkUserLogin();
        }

        if ($jshopConfig->max_day_download_sale_file && (time() > ($order->getStatusTime()+(86400*$jshopConfig->max_day_download_sale_file))) ){
            JError::raiseWarning(500, _JSHOP_TIME_DOWNLOADS_FILE_RESTRICTED);
            return 0; 
        }
        
        $items = $order->getAllItems();
		$filesid = array();
        if ($jshopConfig->order_display_new_digital_products){
            $product = JSFactory::getTable('product', 'jshop');
            foreach($items as $item){
                $product->product_id = $item->product_id;
				$product->setAttributeActive(unserialize($item->attributes));
                $files = $product->getSaleFiles();
                foreach($files as $_file){
                    $filesid[] = $_file->id;
                }
            }
        }else{
            foreach($items as $item){
                $arrayfiles = unserialize($item->files);
                foreach($arrayfiles as $_file){
                    $filesid[] = $_file->id;
                }
            }
        }
        
        if (!in_array($id, $filesid)){
            JError::raiseError(500, "Error download file");
            return 0;
        }
        
        $stat_download = $order->getFilesStatDownloads();
        
        if ($jshopConfig->max_number_download_sale_file>0 && $stat_download[$id]['download'] >= $jshopConfig->max_number_download_sale_file){
            JError::raiseWarning(500, _JSHOP_NUMBER_DOWNLOADS_FILE_RESTRICTED);
            return 0;
        }
        
        $file = JSFactory::getTable('productFiles', 'jshop');
        $file->load($id);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterLoadProductFile', array(&$file, &$order));
        $downloadFile = $file->file;
        if ($downloadFile==""){
            JError::raiseWarning('', "Error download file");
            return 0;
        }
        $file_name = $jshopConfig->files_product_path."/".$downloadFile;
        if (!file_exists($file_name)){
            JError::raiseWarning('', "Error. File not exist");
            return 0;
        }
        
        $stat_download[$id]['download'] = intval($stat_download[$id]['download']) + 1;
        $stat_download[$id]['time'] = getJsDate();
        
        $order->setFilesStatDownloads($stat_download);
        $order->store();
        
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
    
    function reviewsave(){
        $mainframe =JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $user = JFactory::getUser(); 
        $post = JRequest::get('post');
        $backlink = JRequest::getVar('back_link');
        $product_id = JRequest::getInt('product_id');
        JRequest::checkToken() or jexit('Invalid Token');

        $dispatcher =JDispatcher::getInstance();
        
        $review = JSFactory::getTable('review', 'jshop');
        
        if ($review->getAllowReview() <= 0) {
            JError::raiseWarning('', jshopReview::getText());
            $this->setRedirect($backlink);
            return 0;
        }
                
        $review->bind($post);
        $review->time = getJsDate();
        $review->user_id = $user->id;
        $review->ip = $_SERVER['REMOTE_ADDR'];
        if ($jshopConfig->display_reviews_without_confirm){
            $review->publish = 1;    
        }

        $dispatcher->trigger( 'onBeforeSaveReview', array(&$review) );

        if (!$review->check()) {
            JError::raiseWarning('', _JSHOP_ENTER_CORRECT_INFO_REVIEW);
            $this->setRedirect($backlink);
            return 0;
        }
        $review->store();

        $dispatcher->trigger( 'onAfterSaveReview', array(&$review) );

        $product = JSFactory::getTable('product', 'jshop');
        $product->load($product_id);
        $product->loadAverageRating();
        $product->loadReviewsCount();
        $product->store();

        $lang = JSFactory::getLang();
        $name = $lang->get("name");

        $view_name = "product";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, 'html', '', $view_config);
        $view->setLayout("commentemail");
        $view->assign('product_name', $product->$name);
        $view->assign('user_name', $review->user_name);
        $view->assign('user_email', $review->user_email);
        $view->assign('mark', $review->mark);
        $view->assign('review', $review->review);
        $message = $view->loadTemplate();

        $mailfrom = $mainframe->getCfg('mailfrom');
        $fromname = $mainframe->getCfg('fromname');
        $mailer =JFactory::getMailer();
        $mailer->setSender(array($mailfrom, $fromname));
        $mailer->addRecipient(explode(',',$jshopConfig->contact_email));
        $mailer->setSubject(_JSHOP_NEW_COMMENT);
        $mailer->setBody($message);
        $mailer->isHTML(true);
        $send = $mailer->Send();

        if ($jshopConfig->display_reviews_without_confirm){
            $this->setRedirect($backlink, _JSHOP_YOUR_REVIEW_SAVE_DISPLAY);
        }else{
            $this->setRedirect($backlink, _JSHOP_YOUR_REVIEW_SAVE);
        }
    }

    /**
    * get attributes html selects, price for select attribute 
    */
    function ajax_attrib_select_and_price(){
        $db = JFactory::getDBO();        
        $jshopConfig = JSFactory::getConfig();
                
        $product_id = JRequest::getInt('product_id');
        $change_attr = JRequest::getInt('change_attr');
        if ($jshopConfig->use_decimal_qty){
            $qty = floatval(str_replace(",",".",JRequest::getVar('qty',1)));
        }else{
            $qty = JRequest::getInt('qty',1);
        }
        if ($qty < 0) $qty = 0;
        $attribs = JRequest::getVar('attr');
        if (!is_array($attribs)) $attribs = array();
        $freeattr = JRequest::getVar('freeattr');
        if (!is_array($freeattr)) $freeattr = array();

        $dispatcher =JDispatcher::getInstance();        
        $dispatcher->trigger('onBeforeLoadDisplayAjaxAttrib', array(&$product_id, &$change_attr, &$qty, &$attribs, &$freeattr));
        
        $product = JSFactory::getTable('product', 'jshop'); 
        $product->load($product_id);
		$dispatcher->trigger('onBeforeLoadDisplayAjaxAttrib2', array(&$product));
        
        $attributesDatas = $product->getAttributesDatas($attribs);
        $product->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];
        $product->setFreeAttributeActive($freeattr);
        
        $attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);

        $rows = array();
        foreach($attributes as $k=>$v){            
            $rows[] = '"id_'.$k.'":"'.json_value_encode($v->selects, 1).'"';
        }

        $pricefloat = $product->getPrice($qty, 1, 1, 1);
        $price = formatprice($pricefloat);
        $available = intval($product->getQty() > 0);
		$displaybuttons = intval(intval($product->getQty() > 0) || $jshopConfig->hide_buy_not_avaible_stock==0);
        $ean = $product->getEan();
        $weight = formatweight($product->getWeight());
        $basicprice = formatprice($product->getBasicPrice());
        
        $rows[] = '"price":"'.json_value_encode($price).'"';
        $rows[] = '"pricefloat":"'.$pricefloat.'"';
        $rows[] = '"available":"'.$available.'"';
        $rows[] = '"ean":"'.json_value_encode($ean).'"';
        if ($jshopConfig->admin_show_product_basic_price){
            $rows[] = '"basicprice":"'.json_value_encode($basicprice).'"';
        }
        if ($jshopConfig->product_show_weight){
            $rows[] = '"weight":"'.json_value_encode($weight).'"';
        }
        if ($jshopConfig->product_list_show_price_default && $product->product_price_default>0){
            $rows[] = '"pricedefault":"'.json_value_encode(formatprice($product->product_price_default)).'"';
        }
        if ($jshopConfig->product_show_qty_stock){
            $qty_in_stock = getDataProductQtyInStock($product);
            $rows[] = '"qty":"'.json_value_encode(sprintQtyInStock($qty_in_stock)).'"';
        }
		
        $product->updateOtherPricesIncludeAllFactors();

        if (is_array($product->product_add_prices)){
            foreach($product->product_add_prices as $k=>$v){
                $rows[] = '"pq_'.$v->product_quantity_start.'":"'.json_value_encode(formatprice($v->price)).'"';
            }
        }
        if ($product->product_old_price){
            $old_price = formatprice($product->product_old_price);
            $rows[] = '"oldprice":"'.json_value_encode($old_price).'"';
        }
		$rows[] = '"displaybuttons":"'.$displaybuttons.'"';
        if ($jshopConfig->hide_delivery_time_out_of_stock){
            $rows[] = '"showdeliverytime":"'.$product->getDeliveryTimeId().'"';            
        }
        
        if ($jshopConfig->use_extend_attribute_data){
            $template_path = $jshopConfig->template_path.$jshopConfig->template."/product";
            $images = $product->getImages();
            $videos = $product->getVideos();
			$demofiles = $product->getDemoFiles();
			$tmp = array();
            foreach($images as $img){
                $tmp[] = '"'.$img->image_name.'"';
            }
			if (!file_exists($template_path."/block_image_thumb.php")){
				$displayimgthumb = intval((count($images)>1) || (count($videos) && count($images)));
				$rows[] = '"images":['.implode(",", $tmp).'],"displayimgthumb":"'.$displayimgthumb.'"';
            }

			$view_name = "product";
			$view_config = array("template_path"=>$template_path);
			$view = $this->getView($view_name, getDocumentType(), '', $view_config);
			$view->setLayout("demofiles");
			$view->assign('config', $jshopConfig);
			$view->assign('demofiles', $demofiles);
            $demofiles = $view->loadTemplate();            
            $rows[] = '"demofiles":"'.json_value_encode($demofiles, 1).'"';

			if (file_exists($template_path."/block_image_thumb.php")){
                $product->getDescription();
                
                $view_name = "product";
                $view_config = array("template_path"=>$template_path);
                $view = $this->getView($view_name, getDocumentType(), '', $view_config);
                $view->setLayout("block_image_thumb");
                $view->assign('config', $jshopConfig);            
                $view->assign('images', $images);            
                $view->assign('videos', $videos);            
                $view->assign('image_product_path', $jshopConfig->image_product_live_path);            
                $dispatcher->trigger('onBeforeDisplayProductViewBlockImageThumb', array(&$view));
                $block_image_thumb = $view->loadTemplate();
                
                $view_name = "product";
                $view_config = array("template_path"=>$template_path);
                $view = $this->getView($view_name, getDocumentType(), '', $view_config);
                $view->setLayout("block_image_middle");
                $view->assign('config', $jshopConfig);            
                $view->assign('images', $images);            
                $view->assign('videos', $videos);            
                $view->assign('product', $product);            
                $view->assign('noimage', $jshopConfig->noimage);            
                $view->assign('image_product_path', $jshopConfig->image_product_live_path);
                $view->assign('path_to_image', $jshopConfig->live_path.'images/');
                $dispatcher->trigger('onBeforeDisplayProductViewBlockImageMiddle', array(&$view));
                $block_image_middle = $view->loadTemplate();

                $rows[] = '"block_image_thumb":"'.json_value_encode($block_image_thumb,1).'"';

                $rows[] = '"block_image_middle":"'.json_value_encode($block_image_middle,1).'"';
            }
        }
        
        $dispatcher->trigger('onBeforeDisplayAjaxAttrib', array(&$rows, &$product) );
        print '{'.implode(",",$rows).'}';
        die();
    }
    
    function showmedia(){
        $jshopConfig = JSFactory::getConfig();
        $media_id = JRequest::getInt('media_id');
        $file = JSFactory::getTable('productfiles', 'jshop');
        $file->load($media_id);

        $scripts_load = '<script type="text/javascript" src="'.JURI::root().'media/jui/js/jquery.min.js"></script>';
        //$scripts_load .= '<script type="text/javascript" src="'.JURI::root().'components/com_jshopping/js/jquery/jquery-noconflict.js"></script>';
        $scripts_load .= '<script type="text/javascript" src="'.JURI::root().'components/com_jshopping/js/jquery/jquery.media.js"></script>';

        $view_name = "product";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("playmedia");
        $view->assign('config', $jshopConfig);
        $view->assign('filename', $file->demo);
        $view->assign('description', $file->demo_descr);
        $view->assign('scripts_load', $scripts_load);
        $dispatcher =JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductShowMediaView', array(&$view) );
        $view->display(); 
        die();
    }
}
?>