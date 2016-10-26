<?php
/**
* @version      4.8.1 09.01.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
include_once(dirname(__FILE__)."/lib/factory.php");
include_once(dirname(__FILE__)."/lib/shop_item_menu.php");

function jshoppingBuildRoute(&$query){
    $segments = array();
    JSFactory::loadLanguageFile();
    $shim = shopItemMenu::getInstance();
	JPluginHelper::importPlugin('jshoppingrouter');
    $dispatcher = JDispatcher::getInstance();
    $dispatcher->trigger('onBeforeBuildRoute', array(&$query, &$segments));
    $categoryitemidlist = $shim->getListCategory();
    $manufactureritemidlist = $shim->getListManufacturer();
    $contentitemidlist = $shim->getListContent();
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    
    if (isset($query['view']) && !isset($query['controller'])){
        $query['controller'] = $query['view'];
        unset($query['view']);
    }
    
    if (isset($query['controller'])){
        $controller = $query['controller'];
    }else{
        $controller = "";
    }
    if (!isset($query['task'])){
        $query['task'] = '';
    }
    
    if (isset($query['Itemid']) && $query['Itemid'] && isset($query['controller']) && $query['task']==""){
        $menuItem = $menu->getItem($query['Itemid']);
        $micontroller = $menuItem->query['controller'];
        if ($micontroller && $query['controller'] && $micontroller==$query['controller']){
            unset($query['controller']);       
        }
    }
    
    if ($controller=="category" && $query['task']=="view" && $query['category_id']){
        if (isset($categoryitemidlist[$query['category_id']])){
            $query['Itemid'] = $categoryitemidlist[$query['category_id']];
            unset($query['controller']);
            unset($query['category_id']);
            unset($query['task']);
        }else{
            $catalias = JSFactory::getAliasCategory();
            if (isset($catalias[$query['category_id']])){
                $segments[] = $catalias[$query['category_id']];
                unset($query['controller']);
                unset($query['task']); 
                unset($query['category_id']);
            }
        }
    }
    
    if ($controller=="product" && $query['task']=="view" && $query['category_id'] && $query['product_id']){
        $prodalias = JSFactory::getAliasProduct();
        $catalias = JSFactory::getAliasCategory();
        if (isset($categoryitemidlist[$query['category_id']]) && isset($prodalias[$query['product_id']])){
            $query['Itemid'] = $categoryitemidlist[$query['category_id']];
            unset($query['controller']);
            unset($query['category_id']);
            unset($query['task']);
            $segments[] = $prodalias[$query['product_id']];
            unset($query['product_id']);
        }elseif (isset($catalias[$query['category_id']]) && isset($prodalias[$query['product_id']])){
            $segments[] = $catalias[$query['category_id']];
            $segments[] = $prodalias[$query['product_id']];
            unset($query['controller']);
            unset($query['task']);
            unset($query['category_id']);
            unset($query['product_id']);
        }
    }
    
    if ($controller=="manufacturer" && $query['task']=="view" && $query['manufacturer_id']){
        if (isset($manufactureritemidlist[$query['manufacturer_id']])){
            $query['Itemid'] = $manufactureritemidlist[$query['manufacturer_id']];
            unset($query['controller']);
            unset($query['task']);
            unset($query['manufacturer_id']);
        }else{
            $manalias = JSFactory::getAliasManufacturer();
            if (isset($manalias[$query['manufacturer_id']])){
                $segments[] = $manalias[$query['manufacturer_id']];
                unset( $query['controller'] );
                unset( $query['task'] ); 
                unset( $query['manufacturer_id'] );
            }
        }
    }
    
    if ($controller=="content" && $query['task']=="view" && $query['page']){
        if (isset($contentitemidlist[$query['page']])){
            $query['Itemid'] = $contentitemidlist[$query['page']];
            unset($query['controller']);
            unset($query['task']);
            unset($query['page']);
        }
    }
    
    if ($controller=="cart" && $shim->getCart()){
        $query['Itemid'] = $shim->getCart();
        unset($query['controller']);
    }
    if ($controller=="wishlist" && $shim->getWishlist()){
        $query['Itemid'] = $shim->getWishlist();
        unset($query['controller']);
    }
    if ($controller=="search" && $shim->getSearch()){
        $query['Itemid'] = $shim->getSearch();
        unset($query['controller']);
    }
    if ($controller=="member" && $query['task']=="login" && $shim->getLogin()){
        $query['Itemid'] = $shim->getLogin();
        unset($query['controller']);
        unset($query['task']);
        $controller = "none";
    }
    if ($controller=="member" && $query['task']=="logout" && $shim->getLogout()){
        $query['Itemid'] = $shim->getLogout();
        unset($query['controller']);
        unset($query['task']);
        $controller = "none";
    }
    if ($controller=="member" && $query['task']=="editaccount" && $shim->getEditaccount()){
        $query['Itemid'] = $shim->getEditaccount();
        unset($query['controller']);
        unset($query['task']);
        $controller = "none";
    }
    if ($controller=="member" && $query['task']=="orders" && $shim->getOrders()){
        $query['Itemid'] = $shim->getOrders();
        unset($query['controller']);
        unset($query['task']);
        $controller = "none";
    }
    if ($controller=="member" && $query['task']=="register" && $shim->getRegister()){
        $query['Itemid'] = $shim->getRegister();
        unset($query['controller']);
        unset($query['task']);
        $controller = "none";
    }
    if ($controller=="member" && $shim->getUser()){
        $query['Itemid'] = $shim->getUser();
        unset($query['controller']);
    }
    if ($controller=="vendor" && $shim->getVendor()){
        $query['Itemid'] = $shim->getVendor();
        unset($query['controller']);
    }
    if ($controller=="checkout" && $shim->getCheckout()){
        $query['Itemid'] = $shim->getCheckout();
        unset($query['controller']);
    }

    if(isset($query['controller'])) {
        $segments[] = $query['controller'];
        unset($query['controller']);
    }

    if(isset($query['task'])) {
        $segments[] = $query['task'];
        unset($query['task']); 
    }
    
    if ($controller=="category" || $controller=="product"){
        if(isset($query['category_id'])) {
            $segments[] = $query['category_id'];
            unset($query['category_id']); 
        } 
        
        if(isset($query['product_id'])) {
            $segments[] = $query['product_id'];
            unset($query['product_id']); 
        }   
    }
        
    if ($controller=="manufacturer"){
        if(isset($query['manufacturer_id'])) {
            $segments[] = $query['manufacturer_id'];
            unset($query['manufacturer_id']); 
        } 
    }
    
    if ($controller=="content"){
        if(isset($query['page'])) {
            $segments[] = $query['page'];
            unset($query['page']);
        }
    }
	$dispatcher->trigger('onAfterBuildRoute', array(&$query, &$segments));
    return $segments;
}

function jshoppingParseRoute($segments){
    $vars = array();
    JSFactory::loadLanguageFile();
    $reservedFirstAlias = JSFactory::getReservedFirstAlias();    
    $menu = JFactory::getApplication()->getMenu();    
    $menuItem = $menu->getActive();
	JPluginHelper::importPlugin('jshoppingrouter');
    $dispatcher = JDispatcher::getInstance();
    $dispatcher->trigger('onBeforeParseRoute', array(&$vars, &$segments));
    $segments[0] = getSeoSegment($segments[0]);
    if (isset($segments[1])){
        $segments[1] = getSeoSegment($segments[1]);
    }else{
        $segments[1] = "";
    }
    if (!$menuItem->query['controller'] && $menuItem->query['view']){
        $menuItem->query['controller'] = $menuItem->query['view'];
    }

    if (isset($menuItem->query['controller'])){
        if ($menuItem->query['controller']=="cart"){
            $vars['controller'] = "cart";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="wishlist"){
            $vars['controller'] = "wishlist";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="search"){
            $vars['controller'] = "search";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="member" && $menuItem->query['task']==""){
            $vars['controller'] = "member";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="checkout"){
            $vars['controller'] = "checkout";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="vendor" && $menuItem->query['task']==""){
            $vars['controller'] = "vendor";
            $vars['task'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="content" && $menuItem->query['task']=="view"){
            $vars['controller'] = "content";
            $vars['page'] = $segments[0];
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        if ($menuItem->query['controller']=="category" && $menuItem->query['category_id'] && $segments[1]==""){
            $prodalias = JSFactory::getAliasProduct();
            $product_id = array_search($segments[0], $prodalias, true);
            if (!$product_id){
                JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
            }
            
            $vars['controller'] = "product";
            $vars['task'] = "view";
            $vars['category_id'] = $menuItem->query['category_id'];
            $vars['product_id'] = $product_id;
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
    }

    if ($segments[0] && !in_array($segments[0], $reservedFirstAlias)){
        $catalias = JSFactory::getAliasCategory();
        $category_id = array_search($segments[0], $catalias, true);
        if ($category_id && $segments[1]==""){
            $vars['controller'] = "category";
            $vars['task'] = "view";
            $vars['category_id'] = $category_id;
			$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
            return $vars;
        }
        
        if ($category_id && $segments[1]!=""){
            $prodalias = JSFactory::getAliasProduct();
            $product_id = array_search($segments[1], $prodalias, true);
            if (!$product_id){
                JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
            }
            if ($category_id && $product_id){
                $vars['controller'] = "product";
                $vars['task'] = "view";
                $vars['category_id'] = $category_id;
                $vars['product_id'] = $product_id;
				$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
                return $vars;
            }
        }
        
        if (!$category_id && $segments[1]==""){
            $manalias = JSFactory::getAliasManufacturer();
            $manufacturer_id = array_search($segments[0], $manalias, true);
            if ($manufacturer_id){
                $vars['controller'] = "manufacturer";
                $vars['task'] = "view";
                $vars['manufacturer_id'] = $manufacturer_id;
				$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
                return $vars;
            }
        }

        JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);

    }else{
        $vars['controller'] = $segments[0];
        $vars['task'] = $segments[1];
        
        if ($vars['controller']=="category" && $vars['task']=="view"){
            $vars['category_id'] = $segments[2];
        }
        
        if ($vars['controller']=="product" && $vars['task']=="view"){
            $vars['category_id'] = $segments[2];
            $vars['product_id'] = $segments[3];
        }
        
        if ($vars['controller']=="product" && $vars['task']=="ajax_attrib_select_and_price"){
            $vars['product_id'] = $segments[2];
        }
            
        if ($vars['controller']=="manufacturer" && isset($segments[2])){
            $vars['manufacturer_id'] = $segments[2];
        }
        
        if ($vars['controller']=="content"){
            $vars['page'] = $segments[2];
        }
    }
$dispatcher->trigger('onAfterParseRoute', array(&$vars, &$segments));
return $vars;
}
?>