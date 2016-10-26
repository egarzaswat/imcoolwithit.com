<?php
/**
* @version      4.10.0 20.12.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerStatistic extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        addSubmenu("other");
        checkAccessController("statistic");
    }
    
    function display($cachable = false, $urlparams = false){
        
        $jshopConfig = JSFactory::getConfig(); 

        $_statisctic = JSFactory::getModel("statistic");   
        
        $rows = $_statisctic->getAllOrderStatus();

        $today = $_statisctic->getOrderStatistics('day');  
        $week = $_statisctic->getOrderStatistics('week');  
        $month = $_statisctic->getOrderStatistics('month');  
        $year = $_statisctic->getOrderStatistics('year');  

        $category = $_statisctic->getCategoryStatistics(); 
        $manufacture = $_statisctic->getManufactureStatistics();     
        $product = $_statisctic->getProductStatistics() ; 
        $pr_instok = $_statisctic->getProductStatistics('1') ; 
        $pr_outstok = $_statisctic->getProductStatistics('2') ; 
        $pr_download = $_statisctic->getProductDownloadStatistics() ;
        
        $customer= $_statisctic->getUsersStatistics();  
        $customer_enabled= $_statisctic->getUsersStatistics('1'); 
        $customer_loggedin= $_statisctic->getUsersStatistics('2');  
        
        $stuff1= $_statisctic->getUsersStaffStatistics('1'); 
        $stuff2= $_statisctic->getUsersStaffStatistics('2'); 
        $stuff3= $_statisctic->getUsersStaffStatistics('3'); 

        $usergroups= $_statisctic->getUserGroupsStatistics();

        $view=$this->getView("statistic", 'html');        
        $view->assign('rows', $rows);   
        $view->assign('today', $today);   
        $view->assign('week', $week); 
        $view->assign('month', $month); 
        $view->assign('year', $year); 
        $view->assign('paid_status', $jshopConfig->payment_status_enable_download_sale_file);    
        $view->assign('category', $category);  
        $view->assign('manufacture', $manufacture);  
        $view->assign('product', $product); 
        $view->assign('pr_instok', $pr_instok);  
        $view->assign('pr_outstok', $pr_outstok);  
        $view->assign('pr_download', $pr_download); 
        $view->assign('customer', $customer);  
        $view->assign('customer_enabled', $customer_enabled); 
        $view->assign('customer_loggedin', $customer_loggedin);  
        $view->assign('stuff1', $stuff1);   
        $view->assign('stuff2', $stuff2);  
        $view->assign('stuff3', $stuff3); 
        $view->assign('usergroups', $usergroups);
        $view->sidebar = JHtmlSidebar::render(); 
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayStatistic', array(&$view));
        $view->display();        
    }
    
    
}

?>		