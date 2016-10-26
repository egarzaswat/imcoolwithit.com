<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewLanguages_list extends JViewLegacy
{
    function display($tpl=null){
        
        JToolBarHelper::title( _JSHOP_LIST_LANGUAGE, 'generic.png' ); 
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        parent::display($tpl);
	}
}
?>