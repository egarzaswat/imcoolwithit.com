<?php
/**
* @version      4.9.0 31.05.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerManufacturer extends JControllerLegacy{
    
    function __construct($config = array()){
        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        exit;
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingproducts');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerManufacturer', array(&$this));
    }
	
	function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $document = JFactory::getDocument();
		$jshopConfig = JSFactory::getConfig();
		$manufacturer = JSFactory::getTable('manufacturer', 'jshop');
        $ordering = $jshopConfig->manufacturer_sorting==1 ? "ordering" : "name";
		$rows = $manufacturer->getAllManufacturers(1, $ordering, 'asc');

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayListManufacturers', array(&$rows));

        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("manufacturers");
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        
        $statictext = JSFactory::getTable("statictext","jshop");
        $rowstatictext = $statictext->loadData("manufacturer");
        $manufacturer->description = $rowstatictext->text;
        
        $view_name = "manufacturer";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
		$view->setLayout("manufacturers");
		$view->assign("rows", $rows);
		$view->assign("image_manufs_live_path", $jshopConfig->image_manufs_live_path);
        $view->assign('noimage', $jshopConfig->noimage);
        $view->assign('count_manufacturer_to_row', $jshopConfig->count_manufacturer_to_row);
        $view->assign('params', $params);        
		$view->assign('manufacturer', $manufacturer);
        $dispatcher->trigger('onBeforeDisplayManufacturerView', array(&$view) );
		$view->display();
	}	
	
	function view(){
	    $mainframe = JFactory::getApplication();
		$jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $session->set("jshop_end_page_buy_product", $_SERVER['REQUEST_URI']);
        $session->set("jshop_end_page_list_product", $_SERVER['REQUEST_URI']);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoadProductList', array());
		
        $manufacturer_id = JRequest::getInt('manufacturer_id');
        $category_id = JRequest::getInt('category_id');
        $label_id = JRequest::getInt('label_id');
        $vendor_id = JRequest::getInt('vendor_id');
		$manufacturer = JSFactory::getTable('manufacturer', 'jshop');		
		$manufacturer->load($manufacturer_id);
		$manufacturer->getDescription();

        $dispatcher->trigger('onBeforeDisplayManufacturer', array(&$manufacturer));
        
        if ($manufacturer->manufacturer_publish==0){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }

        if (getShopManufacturerPageItemid()==JRequest::getInt('Itemid')){
            appendPathWay($manufacturer->name);
        }
        
        if ($manufacturer->meta_title=="") $manufacturer->meta_title = $manufacturer->name;
        setMetaData($manufacturer->meta_title, $manufacturer->meta_keyword, $manufacturer->meta_description);
        
		$action = xhtmlUrl($_SERVER['REQUEST_URI']);
        
        if (!$manufacturer->products_page){
		    $manufacturer->products_page = $jshopConfig->count_products_to_page; 
        }
        $count_product_to_row = $manufacturer->products_row;
        if (!$count_product_to_row){
		    $count_product_to_row = $jshopConfig->count_products_to_row;
        }
				
		$context = "jshoping.manufacturlist.front.product";
        $contextfilter = "jshoping.list.front.product.manf.".$manufacturer_id;
        $orderby = $mainframe->getUserStateFromRequest($context.'orderby', 'orderby', $jshopConfig->product_sorting_direction, 'int');
        $order = $mainframe->getUserStateFromRequest($context.'order', 'order', $jshopConfig->product_sorting, 'int');
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $manufacturer->products_page, 'int');
        if (!$limit) $limit = $manufacturer->products_page;
        $limitstart = JRequest::getInt('limitstart');
        if ($order==4){
            $order = 1;
        }

        $orderbyq = getQuerySortDirection($order, $orderby);
        $image_sort_dir = getImgSortDirection($order, $orderby);
        $field_order = $jshopConfig->sorting_products_field_s_select[$order];
        $filters = getBuildFilterListProduct($contextfilter, array("manufacturers"));

        $total = $manufacturer->getCountProducts($filters);
       
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $pagenav = $pagination->getPagesLinks();
        
		$dispatcher->trigger('onBeforeFixLimitstartDisplayProductList', array(&$limitstart, &$total, 'manufacturer'));
        if ($limitstart>=$total) $limitstart = 0;

		$rows = $manufacturer->getProducts($filters, $field_order, $orderbyq, $limitstart, $limit);
		addLinkToProducts($rows, 0, 1);		
	
		foreach($jshopConfig->sorting_products_name_s_select as $key => $value){
            $sorts[] = JHTML::_('select.option', $key, $value, 'sort_id', 'sort_value' );
        }

        insertValueInArray($manufacturer->products_page, $jshopConfig->count_product_select); //insert products_page count
        foreach($jshopConfig->count_product_select as $key => $value){            
            $product_count[] = JHTML::_('select.option',$key, $value, 'count_id', 'count_value' );
        }        
        $sorting_sel = JHTML::_('select.genericlist', $sorts, 'order', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','sort_id', 'sort_value', $order );
        $product_count_sel = JHTML::_('select.genericlist', $product_count, 'limit', 'class = "inputbox" size = "1" onchange = "submitListProductFilters()"','count_id', 'count_value', $limit );
        
        $_review = JSFactory::getTable('review', 'jshop');
        $allow_review = $_review->getAllowReview();
        
        if ($jshopConfig->show_product_list_filters){
            $filter_categorys = $manufacturer->getCategorys();
            $first_category = array();
            $first_category[] = JHTML::_('select.option', 0, _JSHOP_ALL, 'id', 'name');
            if (isset($filters['categorys'][0])){
                $active_category = $filters['categorys'][0];
            }else{
                $active_category = 0;
            }
            $categorys_sel = JHTML::_('select.genericlist', array_merge($first_category, $filter_categorys), 'categorys[]', 'class = "inputbox" onchange = "submitListProductFilters()"','id', 'name', $active_category);
        }else{
            $categorys_sel = '';
        }
        
        if ($jshopConfig->use_plugin_content){
            changeDataUsePluginContent($manufacturer, "manufacturer");
        }
        
        $willBeUseFilter = willBeUseFilter($filters);
        $display_list_products = (count($rows)>0 || $willBeUseFilter);

        $dispatcher->trigger('onBeforeDisplayProductList', array(&$rows));

        $view_name = "manufacturer";
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
        $view->assign('filter_show_manufacturer', 0);
        $view->assign('pagination', $pagenav);
		$view->assign('pagination_obj', $pagination);
        $view->assign('display_pagination', $pagenav!="");
		$view->assign("rows", $rows);
		$view->assign("count_product_to_row", $count_product_to_row);
		$view->assign("manufacturer", $manufacturer);
        $view->assign('action', $action);
        $view->assign('allow_review', $allow_review);
		$view->assign('orderby', $orderby);		
		$view->assign('product_count', $product_count_sel);
        $view->assign('sorting', $sorting_sel);
        $view->assign('categorys_sel', $categorys_sel);
        $view->assign('filters', $filters);
        $view->assign('willBeUseFilter', $willBeUseFilter);
        $view->assign('display_list_products', $display_list_products);
        $view->assign('shippinginfo', SEFLink($jshopConfig->shippinginfourl,1));
        $dispatcher->trigger('onBeforeDisplayProductListView', array(&$view) );	
		$view->display();
	}	
}
?>