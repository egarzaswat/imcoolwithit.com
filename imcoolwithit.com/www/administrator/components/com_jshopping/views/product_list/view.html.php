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

        $cat_id = $_GET["category_id"];

        if($cat_id == 1) JToolBarHelper::title( 'Offers', 'generic.png' );
        if($cat_id == 3) JToolBarHelper::title( 'Coffee or Milk Shake', 'generic.png' );
        if($cat_id == 4) JToolBarHelper::title( 'Restaurants', 'generic.png' );
        if($cat_id == 5) JToolBarHelper::title( 'Movies & Events', 'generic.png' );


        JToolBarHelper::addNew();
        //JToolBarHelper::custom('copy', 'copy', 'copy_f2.png', JText::_('JLIB_HTML_BATCH_COPY'));
        //JToolBarHelper::editList('editlist');
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