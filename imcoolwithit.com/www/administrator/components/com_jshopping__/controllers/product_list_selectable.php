<?php
/**
* @version      4.10.0 13.06.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.html.pagination');
class JShoppingControllerProduct_List_Selectable extends JControllerLegacy{
    
	function display($cachable = false, $urlparams = false){
        checkAccessController("product_list_selectable");
		JHTML::_('behavior.framework');
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$jshopConfig = JSFactory::getConfig();
		$prodMdl = JSFactory::getModel('Products', 'JShoppingModel');

		$context = "jshoping.list.admin.product";
		$limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		if (isset($_GET['category_id']) && $_GET['category_id'] === "0"){
			$app->setUserState($context.'category_id', 0);
			$app->setUserState($context.'manufacturer_id', 0);
			$app->setUserState($context.'label_id', 0);
			$app->setUserState($context.'publish', 0);
			$app->setUserState($context.'text_search', '');
		}

		$category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
		$manufacturer_id = $app->getUserStateFromRequest($context.'manufacturer_id', 'manufacturer_id', 0, 'int');
		$label_id = $app->getUserStateFromRequest($context.'label_id', 'label_id', 0, 'int');
		$publish = $app->getUserStateFromRequest($context.'publish', 'publish', 0, 'int');
		$text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        $eName = JRequest::getVar('e_name');
		$jsfname = JRequest::getVar('jsfname');
        $eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);        
        if (!$jsfname) $jsfname = 'selectProductBehaviour';
		
		$filter = array("category_id" => $category_id,"manufacturer_id" => $manufacturer_id,"label_id" => $label_id,"publish" => $publish,"text_search" => $text_search);
		$total = $prodMdl->getCountAllProducts($filter);
		$pagination = new JPagination($total, $limitstart, $limit);
		$rows = $prodMdl->getAllProducts($filter, $pagination->limitstart, $pagination->limit);

        $parentTop = new stdClass();
		$parentTop->category_id = 0;
		$parentTop->name = " - "._JSHOP_CATEGORY." - ";
		$categories_select = buildTreeCategory(0,1,0);
		
		array_unshift($categories_select, $parentTop);  
		  
		$lists['treecategories'] = JHTML::_('select.genericlist', $categories_select, 'category_id', 'class="chosen-select" style="width: 150px;" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);
		
		$manuf1 = array();
        $manuf1[0] = new stdClass();
		$manuf1[0]->manufacturer_id = '0';
		$manuf1[0]->name = " - "._JSHOP_NAME_MANUFACTURER." - ";
        $manufs = JSFactory::getModel('Manufacturers', 'JShoppingModel')->getList();

		$manufs = array_merge($manuf1, $manufs);
		$lists['manufacturers'] = JHTML::_('select.genericlist', $manufs, 'manufacturer_id', 'class="chosen-select" style="width: 150px;" onchange="document.adminForm.submit();"', 'manufacturer_id', 'name', $manufacturer_id);

		if ($jshopConfig->admin_show_product_labels) {
			$alllabels = JSFactory::getModel('ProductLabels', 'JShoppingModel')->getList();
			$first = array();
			$first[] = JHTML::_('select.option', '0', " - "._JSHOP_LABEL." - ", 'id','name');        
			$lists['labels'] = JHTML::_('select.genericlist', array_merge($first, $alllabels), 'label_id', 'class="chosen-select" style="width: 100px;" onchange="document.adminForm.submit();"','id','name', $label_id);
		}

		$f_option = array();
		$f_option[] = JHTML::_('select.option', 0, " - "._JSHOP_SHOW." - ", 'id', 'name');
		$f_option[] = JHTML::_('select.option', 1, _JSHOP_PUBLISH, 'id', 'name');
		$f_option[] = JHTML::_('select.option', 2, _JSHOP_UNPUBLISH, 'id', 'name');
		$lists['publish'] = JHTML::_('select.genericlist', $f_option, 'publish', 'class="chosen-select" style="width: 100px;" onchange="document.adminForm.submit();"', 'id', 'name', $publish);
		
		$view = $this->getView('product_list', 'html');
        $view->setLayout("selectable");
		$view->assign('rows', $rows);
		$view->assign('lists', $lists);
		$view->assign('category_id', $category_id);
		$view->assign('manufacturer_id', $manufacturer_id);
		$view->assign('pagination', $pagination);
		$view->assign('text_search', $text_search);
        $view->assign('config', $jshopConfig);        
		$view->assign('eName', $eName);		
		$view->assign('jsfname', $jsfname);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductListSelectable', array(&$view));
		$view->displaySelectable();
	}
}
?>		