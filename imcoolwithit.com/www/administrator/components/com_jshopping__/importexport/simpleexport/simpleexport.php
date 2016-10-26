<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('');
jimport('joomla.filesystem.folder');

class IeSimpleExport extends IeController{
    
    function view(){
        $jshopConfig = JSFactory::getConfig();
        $ie_id = JRequest::getInt("ie_id");
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');
        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);
                
        $files = JFolder::files($jshopConfig->importexport_path.$_importexport->get('alias'), '.csv');    
        $count = count($files);
            
        JToolBarHelper::title(_JSHOP_EXPORT. ' "'.$name.'"', 'generic.png' ); 
        JToolBarHelper::custom("backtolistie", "arrow-left", 'arrow-left', _JSHOP_BACK_TO.' "'._JSHOP_PANEL_IMPORT_EXPORT.'"', false );
        JToolBarHelper::spacer();
        JToolBarHelper::save("save", _JSHOP_EXPORT);                
        
        include(dirname(__FILE__)."/list_csv.php");  
    }

    function save(){
        $mainframe = JFactory::getApplication();
        
        include_once(JPATH_COMPONENT_SITE."/lib/csv.io.class.php");
        
        $ie_id = JRequest::getInt("ie_id");
        if (!$ie_id) $ie_id = $this->get('ie_id');
        
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());        
        $params = JRequest::getVar("params");        
        if (is_array($params)){        
            $paramsstr = parseArrayToParams($params);
            $_importexport->set('params', $paramsstr);
        }                
        $_importexport->store();
        
        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);
        
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        
        $query = "SELECT prod.product_id, prod.product_ean, prod.product_quantity, prod.product_date_added, prod.product_price, tax.tax_value as tax, prod.`".$lang->get('name')."` as name, prod.`".$lang->get('short_description')."` as short_description,  prod.`".$lang->get('description')."` as description, cat.`".$lang->get('name')."` as cat_name
                  FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_categories` as cat on cat.category_id=categ.category_id
                  LEFT JOIN `#__jshopping_taxes` AS tax ON tax.tax_id = prod.product_tax_id              
                  GROUP BY prod.product_id";
        $db->setQuery($query);
        $products = $db->loadObjectList();
        
        $data = array();
        $head = array("product_id","ean","qty","date","price","tax","category","name","short_description","description");
        $data[] = $head;
        
        foreach($products as $prod){
            $row = array();
            $row[] = $prod->product_id;
            $row[] = $prod->product_ean;
            $row[] = $prod->product_quantity;
            $row[] = $prod->product_date_added;
            $row[] = $prod->product_price;        
            $row[] = $prod->tax;
            $row[] = utf8_decode($prod->cat_name);
            $row[] = utf8_decode($prod->name);
            $row[] = utf8_decode($prod->short_description);
            $row[] = utf8_decode($prod->description);
            $data[] = $row; 
        }
        
        
        $filename = $jshopConfig->importexport_path.$alias."/".$ie_params['filename'].".csv";
        
        $csv = new csv();
        $csv->write($filename, $data);
                
        if (!JRequest::getInt("noredirect")){
            $mainframe->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id, _JSHOP_COMPLETED);
        }
    }

    function filedelete(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $ie_id = JRequest::getInt("ie_id");
        $_importexport = JSFactory::getTable('ImportExport', 'jshop'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $file = JRequest::getVar("file");
        $filename = $jshopConfig->importexport_path.$alias."/".$file;
        @unlink($filename);
        $mainframe->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
    }
    
}
?>