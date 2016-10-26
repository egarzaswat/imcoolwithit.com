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

class JshoppingViewMeta_data extends JViewLegacy
{

    function displayList($tpl = null)
    {
        JToolBarHelper::title(_JSHOP_LIST_META_DATA, 'generic.png');
        //JToolBarHelper::addNew('edit', 'New meta data');
        //JToolBarHelper::deleteList();
        //JToolBarHelper::spacer();
        parent::display($tpl);
    }

    function displayEdit($tpl = null)
    {
        JToolBarHelper::title($temp = ($this->meta_data->id) ? (sprintf(_JSHOP_EDIT_META_DATA, $this->meta_data->page)) : (_JSHOP_NEW), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        parent::display($tpl);
    }

}