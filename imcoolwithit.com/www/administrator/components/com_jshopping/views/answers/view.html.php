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

class JshoppingViewAnswers extends JViewLegacy{

    function displayList($tpl=null){        
        JToolBarHelper::title( _JSHOP_LIST_ANSWERS, 'generic.png' );
        JToolBarHelper::spacer();
        parent::display($tpl);
    }
}