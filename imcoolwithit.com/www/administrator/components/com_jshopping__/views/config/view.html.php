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

class JshoppingViewConfig extends JViewLegacy
{
    function display($tpl=null){
        $layout = $this->getLayout();
        $title = _JSHOP_EDIT_CONFIG;
        
        $exttitle = '';
        switch ($layout){
            case 'general': $exttitle = _JSHOP_GENERAL_PARAMETERS; break;
            case 'categoryproduct': $exttitle = _JSHOP_CAT_PROD; break;
            case 'checkout': $exttitle = _JSHOP_CHECKOUT; break;
            case 'fieldregister': $exttitle = _JSHOP_REGISTER_FIELDS; break;
            case 'currency': $exttitle = _JSHOP_CURRENCY_PARAMETERS; break;
            case 'image': $exttitle = _JSHOP_IMAGE_VIDEO_PARAMETERS; break;
            case 'storeinfo': $exttitle = _JSHOP_STORE_INFO; break;
            case 'adminfunction': $exttitle = _JSHOP_SHOP_FUNCTION; break;
            case 'otherconfig': $exttitle = _JSHOP_OC; break;
        }
        if ($exttitle!=''){
            $title .= ' / '.$exttitle;
        }
        
        JToolBarHelper::title($title, 'generic.png' );
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::custom('panel', 'home', 'home', _JSHOP_PANEL, false);
        JToolBarHelper::divider();
        if (JFactory::getUser()->authorise('core.admin')){
            JToolBarHelper::preferences('com_jshopping');        
            JToolBarHelper::divider();
        }
        parent::display($tpl);
	}
    
    function displayListSeo($tpl=null){        
        JToolBarHelper::title( _JSHOP_SEO, 'generic.png' );
        JToolBarHelper::addNew("seoedit");
        JToolBarHelper::custom('panel', 'home', 'home', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayEditSeo($tpl=null){
        $title = _JSHOP_SEO;        
        if (defined("_JSHP_SEOPAGE_".$this->row->alias)) $titleext = constant("_JSHP_SEOPAGE_".$this->row->alias); else $titleext = $this->row->alias;
        if ($titleext) $title.=' / '.$titleext;
        JToolBarHelper::title($title, 'generic.png' );
        JToolBarHelper::save("saveseo");
        JToolBarHelper::spacer();
        JToolBarHelper::apply("applyseo");
        JToolBarHelper::spacer();
        JToolBarHelper::cancel("seo");
        JToolBarHelper::spacer();
        JToolBarHelper::custom('panel', 'home', 'home', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayListStatictext($tpl=null){
        
        JToolBarHelper::title( _JSHOP_STATIC_TEXT, 'generic.png' );
        JToolBarHelper::addNew("statictextedit");
        JToolBarHelper::custom('panel', 'home', 'home', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayEditStatictext($tpl=null){
        $title = _JSHOP_STATIC_TEXT;        
        if (defined("_JSHP_STPAGE_".$this->row->alias)) $titleext = constant("_JSHP_STPAGE_".$this->row->alias); else $titleext = $this->row->alias;
        if ($titleext) $title.=' / '.$titleext;
        JToolBarHelper::title($title, 'generic.png' );
        JToolBarHelper::save("savestatictext");
        JToolBarHelper::spacer();
        JToolBarHelper::apply("applystatictext");
        JToolBarHelper::spacer();
        JToolBarHelper::cancel("statictext");
        JToolBarHelper::spacer();
        JToolBarHelper::custom('panel', 'home', 'home', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
}
?>