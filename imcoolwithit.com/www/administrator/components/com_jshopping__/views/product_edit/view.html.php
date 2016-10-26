<?php
/**
* @version      4.4.2 09.04.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewProduct_edit extends JViewLegacy{

    function display($tpl=null){
        $title = _JSHOP_NEW_PRODUCT;
        if ($this->edit){
            $title = _JSHOP_EDIT_PRODUCT;
            if (!$this->product_attr_id) $title .= ' "'.$this->product->name.'"';
        }
        JToolBarHelper::title($title, 'generic.png' );
        JToolBarHelper::save();
        if (!$this->product_attr_id){
            JToolBarHelper::spacer();
            JToolBarHelper::apply();
            JToolBarHelper::spacer();
            JToolBarHelper::cancel();
        }
        parent::display($tpl);
	}

    function editGroup($tpl=null){
        JToolBarHelper::title(_JSHOP_EDIT_PRODUCT, 'generic.png');
        JToolBarHelper::save("savegroup");
        JToolBarHelper::cancel();
        parent::display($tpl);
    }
}
?>