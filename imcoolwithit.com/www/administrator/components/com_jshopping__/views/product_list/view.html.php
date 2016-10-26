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

class JshoppingViewProduct_list extends JViewLegacy
{
    function display($tpl=null){
        JToolBarHelper::title( _JSHOP_LIST_PRODUCT, 'generic.png' ); 
        JToolBarHelper::addNew();
        JToolBarHelper::custom('copy', 'copy', 'copy_f2.png', JText::_('JLIB_HTML_BATCH_COPY'));
        JToolBarHelper::editList('editlist');
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::deleteList();
        parent::display($tpl);
	}
    function displaySelectable($tpl=null){
        parent::display($tpl);
    }
}
?>