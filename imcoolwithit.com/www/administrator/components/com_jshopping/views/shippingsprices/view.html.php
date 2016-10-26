<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewShippingsprices extends JViewLegacy
{
    function displayList($tpl=null){        
        JToolBarHelper::title( _JSHOP_SHIPPING_PRICES_LIST, 'generic.png' ); 
        JToolBarHelper::custom("back", 'arrow-left', 'arrow-left', _JSHOP_LIST_SHIPPINGS, false);
        JToolBarHelper::addNew();
        JToolBarHelper::deleteList();        
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        JToolBarHelper::title($this->sh_method_price->sh_pr_method_id ? (_JSHOP_EDIT_SHIPPING_PRICES) : (_JSHOP_NEW_SHIPPING_PRICES), 'generic.png' ); 
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}
?>