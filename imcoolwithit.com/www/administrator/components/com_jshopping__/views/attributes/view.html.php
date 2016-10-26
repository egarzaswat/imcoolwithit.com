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

class JshoppingViewAttributes extends JViewLegacy{

    function displayList($tpl=null){        
        JToolBarHelper::title( _JSHOP_LIST_ATTRIBUTES, 'generic.png' ); 
        JToolBarHelper::addNew();        
        JToolBarHelper::deleteList();        
        JToolBarHelper::spacer();        
        JToolBarHelper::custom("addgroup", "folder", "folder", _JSHOP_GROUP, false);
        parent::display($tpl);	
    }

    function displayEdit($tpl=null){
        JToolBarHelper::title( $temp = ($this->attribut->attr_id) ? (_JSHOP_EDIT_ATTRIBUT.' / '.$this->attribut->{JSFactory::getLang()->get('name')}) : (_JSHOP_NEW_ATTRIBUT), 'generic.png' );
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        parent::display($tpl);
    }
}