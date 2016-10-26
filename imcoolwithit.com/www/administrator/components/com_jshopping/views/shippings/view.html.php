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

class JshoppingViewShippings extends JViewLegacy{
    
    function displayList($tpl=null){        
        JToolBarHelper::title( _JSHOP_LIST_SHIPPINGS, 'generic.png' ); 
        JToolBarHelper::addNew();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::deleteList();
        JToolBarHelper::custom("ext_price_calc", "cog", "cog" ,_JSHOP_SHIPPING_EXT_PRICE_CALC, false);        
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){
        JToolBarHelper::title( $temp = ($this->edit) ? (_JSHOP_EDIT_SHIPPING.' / '.$this->shipping->{JSFactory::getLang()->get('name')}) : (_JSHOP_NEW_SHIPPING), 'generic.png' ); 
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}
?>