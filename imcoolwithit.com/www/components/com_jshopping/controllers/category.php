<?php
/**
* @version      4.7.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCategory extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerCategory', array(&$this));
    }
    
    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();
        $category_id = 0;
        
        $ordering = $jshopConfig->category_sorting==1 ? "ordering" : "name";
        $category = JSFactory::getTable('category', 'jshop');
        $category->load($category_id);
        $categories = $category->getChildCategories($ordering, 'asc', 1);
        $category->getDescription();

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayMainCategory', array(&$category, &$categories));

        setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description, $params);

        if ($jshopConfig->use_plugin_content){
            changeDataUsePluginContent($category, "category");
        }

        $view_name = "category";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("maincategory");
        $view->assign('category', $category);
        $view->assign('image_category_path', $jshopConfig->image_category_live_path);
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('categories', $categories);
        $view->assign('count_category_to_row', $jshopConfig->count_category_to_row);
        $view->assign('params', $params);
        $dispatcher->trigger('onBeforeDisplayCategoryView', array(&$view) );
        $view->display();
    }

    function view(){
        $mainframe = JFactory::getApplication();
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadProductList', array());

        $category_id = JRequest::getInt('category_id');
        $category = JSFactory::getTable('category', 'jshop');
        $category->load($category_id);
        $category->getDescription();
        $dispatcher->trigger('onAfterLoadCategory', array(&$category, &$user));

		if (!$category->category_id || $category->category_publish==0 || !in_array($category->access, $user->getAuthorisedViewLevels())){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }
        
        $manufacturer_id = JRequest::getInt('manufacturer_id');
        $label_id = JRequest::getInt('label_id');
        $vendor_id = JRequest::getInt('vendor_id');
        
        $view_name = "category";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
		if ($category->category_template=="") $category->category_template="default";
        $view->setLayout("category_".$category->category_template);        

        $jshopConfig->count_products_to_page = $category->products_page;

        $context = "jshoping.list.front.product";
        $contextfilter = "jshoping.list.front.product.cat.".$category_id;
        $orderby = $mainframe->getUserStateFromRequest( $context.'orderby', 'orderby', $jshopConfig->product_sorting_direction, 'int');
        $order = $mainframe->getUserStateFromRequest( $context.'order', 'order', $jshopConfig->product_sorting, 'int');
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $category->products_page, 'int');
        if (!$limit) $limit = $category->products_page;
        $limitstart = JRequest::getInt('limitstart');

        $orderbyq = getQuerySortDirection($order, $orderby);
        $image_sort_dir = getImgSortDirection($order, $orderby);
        $field_order = $jshopConfig->sorting_products_field_select[$order];
        $filters = getBuildFilterListProduct($contextfilter, array("categorys"));
        
        if (getShopMainPageItemid()==JRequest::getInt('Itemid')){
            appendExtendPathWay($category->getTreeChild(), 'category');
        }
        
        $orderfield = $jshopConfig->category_sorting==1 ? "ordering" : "name";
        $sub_categories = $category->getChildCategories($orderfield, 'asc', $publish = 1);
        $dispatcher->trigger( 'onBeforeDisplayCategory', array(&$category, &$sub_categories) );

        if ($category->meta_title=="") $category->meta_title = $category->name;
        setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description);
        
        $total = $category->getCountProducts($filters);
        $action = xhtmlUrl($_SERVER['REQUEST_URI']);
		
		$dispatcher->trigger('onBeforeFixLimitstartDisplayProductList', array(&$limitstart, &$total, 'category'));
        if ($limitstart>=$total) $limitstart = 0;

        $products = $category->getProducts($filters, $field_order, $orderbyq, $limitstart, $limit);
		addLinkToProducts($products, $category_id);

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $pagenav = $pagination->getPagesLinks();
        
        foreach($jshopConfig->sorting_products_name_select as $key=>$value){
            $sorts[] = JHTML::_('select.option', $key, $value, 'sort_id', 'sort_value' );
        }

        insertValueInArray($category->products_page, $jshopConfig->count_product_select); //insert category count
        foreach ($jshopConfig->count_product_select as $key => $value){
            $product_count[] = JHTML::_('select.option',$key, $value, 'count_id', 'count_value' );
        }
        $sorting_sel = JHTML::_('select.genericlist', $sorts, 'order', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','sort_id', 'sort_value', $order );
        $product_count_sel = JHTML::_('select.genericlist', $product_count, 'limit', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','count_id', 'count_value', $limit );
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        
        if (!$category->category_ordertype) $category->category_ordertype = 1;
        
        $manufacuturers_sel = '';
        if ($jshopConfig->show_product_list_filters){
            $filter_manufactures = $category->getManufacturers();
            $first_manufacturer = array();
            $first_manufacturer[] = JHTML::_('select.option', 0, _JSHOP_ALL, 'id', 'name');
            if (isset($filters['manufacturers'][0])){
                $active_manufacturer = $filters['manufacturers'][0];            
            }else{
                $active_manufacturer = 0;
            }
            $manufacuturers_sel = JHTML::_('select.genericlist', array_merge($first_manufacturer, $filter_manufactures), 'manufacturers[]', 'class = "inputbox" onchange = "submitListProductFilters()"','id', 'name', $active_manufacturer);
        }
        
        if ($jshopConfig->use_plugin_content){
            changeDataUsePluginContent($category, "category");
        }

        $willBeUseFilter = willBeUseFilter($filters);
        $display_list_products = (count($products)>0 || $willBeUseFilter);

        $dispatcher->trigger('onBeforeDisplayProductList', array(&$products));
        $view->assign('config', $jshopConfig);
        $view->assign('template_block_list_product', "list_products/list_products.php");
        $view->assign('template_no_list_product', "list_products/no_products.php");
        $view->assign('template_block_form_filter', "list_products/form_filters.php");
        $view->assign('template_block_pagination', "list_products/block_pagination.php");
        $view->assign('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->assign('filter_show', 1);
        $view->assign('filter_show_category', 0);
        $view->assign('filter_show_manufacturer', 1);
        $view->assign('pagination', $pagenav);
		$view->assign('pagination_obj', $pagination);
        $view->assign('display_pagination', $pagenav!="");
        $view->assign('rows', $products);
        $view->assign('count_product_to_row', $category->products_row);
        $view->assign('image_category_path', $jshopConfig->image_category_live_path);
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('category', $category);
        $view->assign('categories', $sub_categories);
        $view->assign('count_category_to_row', $jshopConfig->count_category_to_row);
        $view->assign('allow_review', $allow_review);
        $view->assign('product_count', $product_count_sel);
        $view->assign('sorting', $sorting_sel);
        $view->assign('action', $action);
        $view->assign('orderby', $orderby);
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