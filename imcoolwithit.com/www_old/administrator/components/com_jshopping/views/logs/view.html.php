<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class JshoppingViewLogs extends JViewLegacy{
    
    function displayList($tpl = null){        
        JToolBarHelper::title( _JSHOP_LOGS, 'generic.png');
        parent::display($tpl);
	}
    
    function displayEdit($tpl = null){
        JToolBarHelper::title(_JSHOP_LOGS." / ".$this->filename, 'generic.png');
        JToolBarHelper::back();
        parent::display($tpl);
    }
}
?>