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

class JshoppingControllerProducts extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerProducts', array(&$this));
    }
	
	function display($cachable = false, $urlparams = false){
	    $mainframe = JFactory::getApplication();
		$jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadProductList', array());
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();

        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("all-products");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        
        $category_id = JRequest::getInt('category_id');
        $manufacturer_id = JRequest::getInt('manufacturer_id');
        $label_id = JRequest::getInt('label_id');
        $vendor_id = JRequest::getInt('vendor_id');

		$action = xhtmlUrl($_SERVER['REQUEST_URI']);
		$products_page = $jshopConfig->count_products_to_page;
		$count_product_to_row = $jshopConfig->count_products_to_row;

		$context = "jshoping.alllist.front.product";
        $contextfilter = "jshoping.list.front.product.fulllist";
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
        $filters = getBuildFilterListProduct($contextfilter, array());

        $total = $product->getCountAllProducts($filters);
       
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $pagenav = $pagination->getPagesLinks();
        
		$dispatcher->trigger('onBeforeFixLimitstartDisplayProductList', array(&$limitstart, &$total, 'products'));
        if ($limitstart>=$total) $limitstart = 0;

		$rows = $product->getAllProducts($filters, $field_order, $orderbyq, $limitstart, $limit);
		addLinkToProducts($rows, 0, 1);		
	
		foreach ($jshopConfig->sorting_products_name_s_select as $key => $value) {
            $sorts[] = JHTML::_('select.option', $key, $value, 'sort_id', 'sort_value' );
        }

        insertValueInArray($products_page, $jshopConfig->count_product_select); //insert products_page count
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
            if ($jshopConfig->manufacturer_sorting==2){
                $morder = 'name';
            }else{
                $morder = 'ordering';
            }
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
        }else{
            $manufacuturers_sel = '';
            $categorys_sel = '';
        }

        $willBeUseFilter = willBeUseFilter($filters);
        $display_list_products = (count($rows)>0 || $willBeUseFilter);

        $dispatcher->trigger( 'onBeforeDisplayProductList', array(&$rows) );
        $view_name = "products";
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
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
		$view->assign("rows", $rows);
		$view->assign("count_product_to_row", $count_product_to_row);
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
    
    function tophits(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("tophitsproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $count_product_to_row = $jshopConfig->count_products_to_row_tophits;
        $contextfilter = "jshoping.list.front.product.tophits";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getTopHitsProducts($jshopConfig->count_products_to_page_tophits, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;
        
        $dispatcher->trigger( 'onBeforeDisplayProductList', array(&$rows) );

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }

    function toprating(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("topratingproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $count_product_to_row = $jshopConfig->count_products_to_row_toprating;
        $contextfilter = "jshoping.list.front.product.toprating";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getTopRatingProducts($jshopConfig->count_products_to_page_toprating, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;

        $dispatcher->trigger( 'onBeforeDisplayProductList', array(&$rows) );

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
    
    function label(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("labelproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $label_id = JRequest::getInt("label_id");
        $count_product_to_row = $jshopConfig->count_products_to_row_label;
        $contextfilter = "jshoping.list.front.product.label";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getProductLabel($label_id, $jshopConfig->count_products_to_page_label, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;
        
        $dispatcher->trigger('onBeforeDisplayProductList', array(&$rows));

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
    
    function bestseller(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("bestsellerproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $count_product_to_row = $jshopConfig->count_products_to_row_bestseller;
        $contextfilter = "jshoping.list.front.product.bestseller";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getBestSellers($jshopConfig->count_products_to_page_bestseller, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;

        $dispatcher->trigger( 'onBeforeDisplayProductList', array(&$rows) );

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
    
    function random(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );
        
        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("randomproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $count_product_to_row = $jshopConfig->count_products_to_row_random;
        $contextfilter = "jshoping.list.front.product.random";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getRandProducts($jshopConfig->count_products_to_page_random, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;

        $dispatcher->trigger( 'onBeforeDisplayProductList', array(&$rows) );

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);        
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
    
    function last(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLoadProductList', array() );

        $product = JSFactory::getTable('product', 'jshop');
        $params = $mainframe->getParams();
        $header = getPageHeaderOfParams($params);
        $prefix = $params->get('pageclass_sfx');
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("lastproducts");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);

        $count_product_to_row = $jshopConfig->count_products_to_row_last;
        $contextfilter = "jshoping.list.front.product.last";
        $filters = getBuildFilterListProduct($contextfilter, array());
        $rows = $product->getLastProducts($jshopConfig->count_products_to_page_last, null, $filters);
        addLinkToProducts($rows, 0, 1);
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        $display_list_products = count($rows)>0;
        $jshopConfig->show_sort_product = 0;
        $jshopConfig->show_count_select_products = 0;
        $jshopConfig->show_product_list_filters = 0;
        
        $dispatcher->trigger('onBeforeDisplayProductList', array(&$rows));

        $view_name = "products";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("products");
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign("header", $header);
        $view->assign("prefix", $prefix);
        $view->assign("rows", $rows);
        $view->assign("count_product_to_row", $count_product_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('display_pagination', 0);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );
        $view->display();
    }
}
?>