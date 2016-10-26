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
class JshoppingViewImport_export_list extends JViewLegacy{
    function display($tpl=null){
        JToolBarHelper::title(_JSHOP_PANEL_IMPORT_EXPORT, 'generic.png');
        parent::display($tpl);
	}
}
?>