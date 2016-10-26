<?php
/**
* @version      4.6.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewUsers extends JViewLegacy
{
    function displayList($tpl=null){        
        JToolBarHelper::title( _JSHOP_USER_LIST, 'generic.png' );
        JToolBarHelper::addNew(); 
        JToolBarHelper::deleteList();        
        parent::display($tpl);
	}
    function displayEdit($tpl=null){
        $title = _JSHOP_USERS." / ";
        if ($this->user->user_id){
            $title.=$this->user->u_name;
        }else{
            $title.=_JSHOP_NEW;
        }
        JToolBarHelper::title($title, 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        parent::display($tpl);
    }
}
?>