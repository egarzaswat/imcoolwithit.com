<?php
/**
* @version      4.7.0 10.10.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerPartners extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
            JPluginHelper::importPlugin('jshoppingcheckout');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerPartnres', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $partner = JRequest::getInt('partner');
        if( !isset($partner) || $partner == 0 ){
            $this->displayList();
        } else {
            $this->displayPartner($partner);
        }
    }

    function displayList(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();


        $meta_data = JSFactory::getMetaData('partners');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelPartners = JSFactory::getModel('partners', 'jshop');

        $partners = $modelPartners->getPartnersList();


        $session = JFactory::getSession();
        $get_cook = $session->get('declined_partners');
        if(!$get_cook){
            $get_cook = array();
        }

        $display_data = array();
        foreach($partners as $key => $value){
            if(!in_array($value->product_id, $get_cook)){
                $display_data[$key] = $value;
                $display_data[$key]->image = JSFactory::existImage($jshopConfig->image_product_path_site_small, $display_data[$key]->image);
                $display_data[$key]->link = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_PARTNERS') . '?partner=' . $value->product_id;
            }
        }

        $view_name = "partners";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('list');
        $view->assign('title', $meta_data['header']);
        $view->assign('content', $meta_data['content']);
        $view->assign('partners', $display_data);
        $view->display();
    }

    function displayPartner($id){
        $jshopConfig = JSFactory::getConfig();
        $modelPartner = JSFactory::getModel('partners', 'jshop');
        if(!$modelPartner->existPartner($id)){
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $partner_data = $modelPartner->getPartnerData($id, array('product_id', 'title_en-GB', 'name_en-GB', 'image', 'short_description_en-GB', 'city_en-GB', 'state_en-GB', 'product_ean', 'tokens'));
        $partner_data = $partner_data[0];
        $partner_data['name'] = $partner_data['name_en-GB'];
        $partner_data['title'] = $partner_data['title_en-GB'];
        $partner_data['description'] = $partner_data['short_description_en-GB'];
        $partner_data['city'] = $partner_data['city_en-GB'];
        $partner_data['state'] = $partner_data['state_en-GB'];
        $partner_data['image'] = JSFactory::existImage($jshopConfig->image_product_path_site, $partner_data['image']);

        $view_name = "partners";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout('partner');
        $view->assign('title', $partner_data['title']);
        $view->assign('partner', $partner_data);
        $view->assign('link_another_sponsor', 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_PARTNERS'));
        $view->display();
    }

}
?>