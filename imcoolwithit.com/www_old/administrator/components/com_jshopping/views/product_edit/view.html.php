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

        $post = JRequest::get('post');
        $cat_id = $post["category_id"][0];

        $category_id = ($cat_id) ? $cat_id : $_GET['category_id'];

        if($category_id == 1) $title = 'Offers';
        if($category_id == 3) $title = 'Coffee or Milk Shake';
        if($category_id == 4) $title = 'Restaurants';
        if($category_id == 5) $title = 'Movies & Events';

        if ($this->edit){

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