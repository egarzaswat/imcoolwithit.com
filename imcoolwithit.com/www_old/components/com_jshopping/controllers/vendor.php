<?php
/**
* @version      4.9.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerVendor extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerVendor', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $document = JFactory::getDocument();
        $jshopConfig = JSFactory::getConfig();
        
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("vendors");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $context = "jshoping.list.front.vendor";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $jshopConfig->count_products_to_page, 'int');
        $limitstart = JRequest::getInt('limitstart');
        
        $vendor = JSFactory::getTable('vendor', 'jshop');
        $total = $vendor->getCountAllVendors();
        
        if ($limitstart>=$total) $limitstart = 0;
        
        $rows = $vendor->getAllVendors(1, $limitstart, $limit);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayListVendors', array(&$rows) );
        
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $pagenav = $pagination->getPagesLinks();
        
        foreach($rows as $k=>$v){
            $rows[$k]->link = SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$v->id);
            if (!$v->logo){
                $rows[$k]->logo = $jshopConfig->image_vendors_live_path."/".$jshopConfig->noimage;
            }
        }

        $view_name = "vendor";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("vendors");
        $view->assign("rows", $rows);        
        $view->assign('count_to_row', $jshopConfig->count_category_to_row);
        $view->assign('params', $params);
        $view->assign('pagination', $pagenav);
        $view->assign('display_pagination', $pagenav!="");
        $dispatcher->trigger('onBeforeDisplayVendorView', array(&$view) );
        $view->display();
    }  

    function info(){
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->product_show_vendor_detail){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;   
        }
        $vendor_id = JRequest::getInt("vendor_id");
        $vendor = JSFactory::getTable('vendor', 'jshop');
        $vendor->load($vendor_id);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayVendorInfo', array(&$vendor) );
        
        $title =  $vendor->shop_name;
        $header =  $vendor->shop_name;
        appendPathWay($title);
        
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("vendor-info-".$vendor_id);
        if (!isset($seodata)) {
            $seodata = new stdClass();
            $seodata->title = '';
            $seodata->keyword = '';
            $seodata->description = '';
        }
        if ($seodata->title==""){
            $seodata->title = $title;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $lang = JSFactory::getLang();
        $country = JSFactory::getTable('country', 'jshop');
        $country->load($vendor->country);
        $_name = $lang->get("name");
        $vendor->country = $country->$_name;

        $view_name = "vendor";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("info");
        $view->assign('vendor', $vendor);
        $view->assign('header', $header);
        $dispatcher->trigger('onBeforeDisplayVendorInfoView', array(&$view) );
        $view->display();
        
    }
    
    function products(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadProductList', array());
        
        $vendor_id = JRequest::getInt("vendor_id");
        $vendor = JSFactory::getTable('vendor', 'jshop');
        $vendor->load($vendor_id);

        $dispatcher->trigger( 'onBeforeDisplayVendor', array(&$vendor) );
        
        appendPathWay($vendor->shop_name);
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("vendor-product-".$vendor_id);
        if (!isset($seodata->title) || $seodata->title==""){
            $seodata = new stdClass();
            $seodata->title = $vendor->shop_name;
            $seodata->keyword = $vendor->shop_name;;
            $seodata->description = $vendor->shop_name;;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $action = xhtmlUrl($_SERVER['REQUEST_URI']);
        
        $products_page = $jshopConfig->count_products_to_page;
        $count_product_to_row = $jshopConfig->count_products_to_row;

        $context = "jshoping.vendor.front.product";
        $contextfilter = "jshoping.list.front.product.vendor.".$vendor_id;
        $orderby = $mainframe->getUserStateFromRequest( $context.'orderby', 'orderby', $jshopConfig->product_sorting_direction, 'int');
        $order = $mainframe->getUserStateFromRequest( $context.'order', 'order', $jshopConfig->product_sorting, 'int');
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $products_page, 'int');
        if (!$limit) $limit = $products_page;
        $limitstart = JRequest::getInt('limitstart');
        if ($order==4){
            $order = 1;
        }

        $orderbyq = getQuerySortDirection($order, $orderby);
        $image_sort_dir = getImgSortDirection($order, $orderby);
        $field_order = $jshopConfig->sorting_products_field_s_select[$order];
        $filters = getBuildFilterListProduct($contextfilter, array("vendors"));

        $total = $vendor->getCountProducts($filters);
       
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $pagenav = $pagination->getPagesLinks();
        
		$dispatcher->trigger('onBeforeFixLimitstartDisplayProductList', array(&$limitstart, &$total, 'vendor'));
        if ($limitstart>=$total) $limitstart = 0;

        $rows = $vendor->getProducts($filters, $field_order, $orderbyq, $limitstart, $limit);
        addLinkToProducts($rows, 0, 1);
    
        foreach ($jshopConfig->sorting_products_name_s_select as $key => $value) {
            $sorts[] = JHTML::_('select.option', $key, $value, 'sort_id', 'sort_value' );
        }

        insertValueInArray($products_page, $jshopConfig->count_product_select);
        foreach ($jshopConfig->count_product_select as $key => $value) {
            $product_count[] = JHTML::_('select.option',$key, $value, 'count_id', 'count_value' );
        }
        $sorting_sel = JHTML::_('select.genericlist', $sorts, 'order', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','sort_id', 'sort_value', $order );
        $product_count_sel = JHTML::_('select.genericlist', $product_count, 'limit', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','count_id', 'count_value', $limit );

        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();

        if ($jshopConfig->show_product_list_filters){
            $first_el = JHTML::_('select.option', 0, _JSHOP_ALL, 'manufacturer_id', 'name' );
            $_manufacturers = JSFactory::getTable('manufacturer', 'jshop');
            $listmanufacturers = $_manufacturers->getList();
            array_unshift($listmanufacturers, $first_el);
            if (isset($filters['manufacturers'][0])){
                $active_manufacturer = $filters['manufacturers'][0];
            }else{
                $active_manufacturer = '';
            }
            $manufacuturers_sel = JHTML::_('select.genericlist', $listmanufacturers, 'manufacturers[]', 'class = "inputbox" onchange = "submitListProductFilters()"','manufacturer_id','name', $active_manufacturer);

            $first_el = JHTML::_('select.option', 0, _JSHOP_ALL, 'category_id', 'name' );
            $categories = buildTreeCategory(1);
            array_unshift($categories, $first_el);
            if (isset($filters['categorys'][0])){
                $active_category = $filters['categorys'][0];
            }else{
                $active_category = 0;
            }
            $categorys_sel = JHTML::_('select.genericlist', $categories, 'categorys[]', 'class = "inputbox" onchange = "submitListProductFilters()"', 'category_id', 'name', $active_category);
        } else {
            $categorys_sel = null;
            $manufacuturers_sel = null;
        }

        $willBeUseFilter = willBeUseFilter($filters);
        $display_list_products = (count($rows)>0 || $willBeUseFilter);
        
        $dispatcher->trigger('onBeforeDisplayProductList', array(&$rows));

        $view_name = "vendor";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_no_list_product', "list_products/no_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->assign('filter_show', 1);
        $view->assign('filter_show_category', 1);
        $view->assign('filter_show_manufacturer', 1);
        $view->assign('pagination', $pagenav);
		$view->assign('pagination_obj', $pagination);
        $view->assign('display_pagination', $pagenav!="");
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign("vendor", $vendor);
        $view->assign('action', $action);
        $view->assign('allow_review', $allow_review);
        $view->assign('orderby', $orderby);
        $view->assign('product_count', $product_count_sel);
        $view->assign('sorting', $sorting_sel);
        $view->assign('categorys_sel', $categorys_sel);
        $view->assign('manufacuturers_sel', $manufacuturers_sel);
        $view->assign('filters', $filters);
        $view->assign('willBeUseFilter', $willBeUseFilter);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
    
}
?>