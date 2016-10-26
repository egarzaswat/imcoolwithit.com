<?php
/**
* @version      4.11.0 20.08.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JshoppingControllerShippingsPrices extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("shippingsprices");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
		$db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $jshopConfig = JSFactory::getConfig();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.shippingsprices";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "shipping_price.sh_pr_method_id", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $shipping_id_back = JRequest::getInt("shipping_id_back");
        $shippings = JSFactory::getModel("shippings");
        $rows = $shippings->getAllShippingPrices(0, $shipping_id_back, $filter_order, $filter_order_Dir);
        $currency = JSFactory::getTable('currency', 'jshop');
        $currency->load($jshopConfig->mainCurrency);
        
        $query = "select MPC.sh_pr_method_id, C.`".$lang->get("name")."` as name from #__jshopping_shipping_method_price_countries as MPC 
                  left join #__jshopping_countries as C on C.country_id=MPC.country_id order by MPC.sh_pr_method_id, C.ordering";
        $db->setQuery($query);
        $list = $db->loadObjectList();        
        $shipping_countries = array();        
        foreach($list as $smp){
            $shipping_countries[$smp->sh_pr_method_id][] = $smp->name;
        }
        unset($list);
        foreach($rows as $k=>$row){
            $rows[$k]->countries = "";
            if (is_array($shipping_countries[$row->sh_pr_method_id])){
                if (count($shipping_countries[$row->sh_pr_method_id])>10){
                    $tmp =  array_slice($shipping_countries[$row->sh_pr_method_id],0,10);
                    $rows[$k]->countries = implode(", ",$tmp)."...";
                }else{
                    $rows[$k]->countries = implode(", ",$shipping_countries[$row->sh_pr_method_id]);
                }                
            }
        }
                        
		$view = $this->getView("shippingsprices", 'html');
        $view->setLayout("list");
		$view->assign('rows', $rows);
        $view->assign('currency', $currency);
        $view->assign('shipping_id_back', $shipping_id_back);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayShippngsPrices', array(&$view));
		$view->displayList(); 
	}
    
    function edit(){
        $jshopConfig = JSFactory::getConfig();
        $sh_pr_method_id = JRequest::getInt('sh_pr_method_id');
        $shipping_id_back = JRequest::getInt("shipping_id_back");
        $db = JFactory::getDBO();
        $sh_method_price = JSFactory::getTable('shippingMethodPrice', 'jshop');
        $sh_method_price->load($sh_pr_method_id);
        $sh_method_price->prices = $sh_method_price->getPrices();
        if ($jshopConfig->tax){        
		    $taxes = JSFactory::getModel("taxes");
		    $all_taxes = $taxes->getAllTaxes();
		    $list_tax = array();		
		    foreach ($all_taxes as $tax) {
			    $list_tax[] = JHTML::_('select.option', $tax->tax_id,$tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
		    }
            $list_tax[] = JHTML::_('select.option', -1,_JSHOP_PRODUCT_TAX_RATE,'tax_id','tax_name');
            $lists['taxes'] = JHTML::_('select.genericlist', $list_tax,'shipping_tax_id','class="inputbox"','tax_id','tax_name',$sh_method_price->shipping_tax_id);
            $lists['package_taxes'] = JHTML::_('select.genericlist', $list_tax,'package_tax_id','class="inputbox"','tax_id','tax_name',$sh_method_price->package_tax_id);
        }
		$shippings = JSFactory::getModel("shippings");
		$countries = JSFactory::getModel("countries");		
        $actived = $sh_method_price->shipping_method_id;
        if (!$actived) $actived = $shipping_id_back;        
		$lists['shipping_methods'] = JHTML::_('select.genericlist', $shippings->getAllShippings(0),'shipping_method_id','class = "inputbox" size = "1"','shipping_id','name', $actived);
		$lists['countries'] = JHTML::_('select.genericlist', $countries->getAllCountries(0),'shipping_countries_id[]','class = "inputbox" size = "10", multiple = "multiple"','country_id','name', $sh_method_price->getCountries());
        
        if ($jshopConfig->admin_show_delivery_time) {
            $_deliveryTimes = JSFactory::getModel("deliveryTimes");
            $all_delivery_times = $_deliveryTimes->getDeliveryTimes();                
            $all_delivery_times0 = array();
            $all_delivery_times0[0] = new stdClass();
            $all_delivery_times0[0]->id = '0';
            $all_delivery_times0[0]->name = _JSHOP_NONE;        
            $lists['deliverytimes'] = JHTML::_('select.genericlist', array_merge($all_delivery_times0, $all_delivery_times),'delivery_times_id','class = "inputbox"','id','name', $sh_method_price->delivery_times_id);
        }
        
        $currency = JSFactory::getTable('currency', 'jshop');
        $currency->load($jshopConfig->mainCurrency);        
        $extensions = JSFactory::getShippingExtList($actived);

		$view=$this->getView("shippingsprices", 'html');
        $view->setLayout("edit");
		$view->assign('sh_method_price', $sh_method_price);
		$view->assign('lists', $lists);
        $view->assign('shipping_id_back', $shipping_id_back);
        $view->assign('currency', $currency);
        $view->assign('extensions', $extensions);
        $view->assign('config', $jshopConfig);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditShippingsPrices', array(&$view));
        $view->displayEdit();
    }
	
	function save(){        
    	$sh_method_id = JRequest::getInt("sh_method_id");
        $shipping_id_back = JRequest::getInt("shipping_id_back");
        
        $dispatcher = JDispatcher::getInstance();
		
        $shippings = JSFactory::getModel("shippings");
		$shipping_pr = JSFactory::getTable('shippingMethodPrice', 'jshop');
        $post = JRequest::get("post");        
        $post['shipping_stand_price'] = saveAsPrice($post['shipping_stand_price']);
        $dispatcher->trigger( 'onBeforeSaveShippingPrice', array(&$post) );
        
        $countries = JRequest::getVar('shipping_countries_id');
		if (!$shipping_pr->bind($post)){
			JError::raiseWarning("",_JSHOP_ERROR_BIND);
			$this->setRedirect("index.php?option=com_jshopping&controller=shippingsprices");
			return 0;
		}
        if (isset($post['sm_params'])){
            $shipping_pr->setParams($post['sm_params']);
        }else{
            $shipping_pr->setParams(array());
		}
	
		if (!$shipping_pr->store()) {
			JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
			$this->setRedirect("index.php?option=com_jshopping&controller=shippingsprices");
			return 0;
		}

		$shippings->savePrices($shipping_pr->sh_pr_method_id, $post);
		$shippings->saveCountries($shipping_pr->sh_pr_method_id, $countries);

        $dispatcher->trigger('onAfterSaveShippingPrice', array(&$shipping_pr));

		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=shippingsprices&task=edit&sh_pr_method_id=".$shipping_pr->sh_pr_method_id."&shipping_id_back=".$shipping_id_back); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=shippingsprices&shipping_id_back=".$shipping_id_back);
        }

	}

	function remove(){
		$cid = JRequest::getVar("cid");
		$db = JFactory::getDBO();
        $shipping_id_back = JRequest::getInt("shipping_id_back");
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveShippingPrice', array(&$cid) );
		$text = '';
		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_shipping_method_price`
					  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if ($db->query()) {
				$text .= _JSHOP_SHIPPING_DELETED;
				$query = "DELETE FROM `#__jshopping_shipping_method_price_weight`
						  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
				$db->setQuery($query);
				$db->query();
				
				$query = "DELETE FROM `#__jshopping_shipping_method_price_countries`
						  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
				$db->setQuery($query);
				$db->query();
			} else {
				$text .= _JSHOP_ERROR_SHIPPING_DELETED;
			}
		}
        
        $dispatcher->trigger( 'onAfterRemoveShippingPrice', array(&$cid) );
		
		$this->setRedirect("index.php?option=com_jshopping&controller=shippingsprices&shipping_id_back=".$shipping_id_back, $text);
	}
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
    }

}
?>