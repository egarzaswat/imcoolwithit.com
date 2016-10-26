<?php
/**
* @version      4.11.0 20.09.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

class JshoppingControllerAttributesValues extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct($config);
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("attributesvalues");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
		$attr_id = JRequest::getInt("attr_id");
		$db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.attr_values";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "value_ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$attributValues = JSFactory::getModel("AttributValue");
		$rows = $attributValues->getAllValues($attr_id, $filter_order, $filter_order_Dir);
		$attribut = JSFactory::getModel("attribut");

		$attr_name = $attribut->getName($attr_id);
		$view=$this->getView("attributesvalues", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);        
        $view->assign('attr_id', $attr_id);
        $view->assign('config', $jshopConfig);
        $view->assign('attr_name', $attr_name);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAttributesValues', array(&$view));
		$view->displayList(); 
	}
	
	function edit() {
		$value_id = JRequest::getInt("value_id");
		$attr_id = JRequest::getInt("attr_id");
        
		$jshopConfig = JSFactory::getConfig();
		$db = JFactory::getDBO();		
        
        $attributValue = JSFactory::getTable('attributValue', 'jshop');
        $attributValue->load($value_id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;	
        
        JFilterOutput::objectHTMLSafe($attributValue, ENT_QUOTES);
		
		$view=$this->getView("attributesvalues", 'html');
        $view->setLayout("edit");		
        $view->assign('attributValue', $attributValue);        
        $view->assign('attr_id', $attr_id);        
        $view->assign('config', $jshopConfig);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('etemplatevar', '');
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditAtributesValues', array(&$view));
		$view->displayEdit();
	}
    
	function save() {
        $jshopConfig = JSFactory::getConfig();
        require_once ($jshopConfig->path.'lib/uploadfile.class.php');
        
        $dispatcher = JDispatcher::getInstance();
        
        $db = JFactory::getDBO();
		$value_id = JRequest::getInt("value_id");
		$attr_id = JRequest::getInt("attr_id");
        
        $post = JRequest::get("post");
        $attributValue = JSFactory::getTable('attributValue', 'jshop');
        
        $dispatcher->trigger( 'onBeforeSaveAttributValue', array(&$post) );
        
        $upload = new UploadFile($_FILES['image']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($jshopConfig->image_attributes_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            if ($post['old_image']){
                @unlink($jshopConfig->image_attributes_path."/".$post['old_image']);
            }
            $post['image'] = $upload->getName();
            @chmod($jshopConfig->image_attributes_path."/".$post['image'], 0777);
        }else{
            if ($upload->getError() != 4){
                JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_IMAGE);
                saveToLog("error.log", "SaveAttributeValue - Error upload image. code: ".$upload->getError());
            }
        }
        
        if (!$value_id){
            $query = "SELECT MAX(value_ordering) AS value_ordering FROM `#__jshopping_attr_values` where attr_id='".$db->escape($attr_id)."'";
            $db->setQuery($query);
            $row = $db->loadObject();
            $post['value_ordering'] = $row->value_ordering + 1;
        }
        
        if (!$attributValue->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id);
            return 0;
        }
                
        if (!$attributValue->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id);
            return 0;
        }
                
        $dispatcher->trigger( 'onAfterSaveAttributValue', array(&$attributValue) );
                
		if ($this->getTask()=='apply'){ 
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&task=edit&attr_id=".$attr_id."&value_id=".$attributValue->value_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id);
        }
	}
	
	
	function remove(){
		$cid = JRequest::getVar("cid");
		$attr_id = JRequest::getInt("attr_id");
        $dispatcher = JDispatcher::getInstance();		
		$model = JSFactory::getModel("AttributValue");
        
        $dispatcher->trigger('onBeforeRemoveAttributValue', array(&$cid));
        
		$text = '';
		foreach($cid as $key=>$value){
            $model->delete(intval($value));
			$text = _JSHOP_ATTRIBUT_VALUE_DELETED;
		}
        
        $dispatcher->trigger('onAfterRemoveAttributValue', array(&$cid));
		
		$this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id, $text);
	}
	
	
	function order(){
		$order = JRequest::getVar("order");
		$cid = JRequest::getInt("id");
		$number = JRequest::getInt("number");
		$attr_id = JRequest::getInt("attr_id");
		$db = JFactory::getDBO();
		switch ($order) {
			case 'up':
				$query = "SELECT value_id, value_ordering FROM `#__jshopping_attr_values`					   
					   WHERE value_ordering < '" . $number . "' AND attr_id = '".$attr_id."' ORDER BY value_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT value_id, value_ordering FROM `#__jshopping_attr_values`					   
					   WHERE value_ordering > '" . $number . "' AND attr_id = '".$attr_id."' ORDER BY value_ordering ASC
					   LIMIT 1";
		}
		$db->setQuery($query);
		$row = $db->loadObject();
        
		$query1 = "UPDATE `#__jshopping_attr_values` SET value_ordering = '".$row->value_ordering."' WHERE value_id = '".$cid."'";
		$query2 = "UPDATE `#__jshopping_attr_values` SET value_ordering = '".$number."' WHERE value_id = '".$row->value_id."'";
        
		$db->setQuery($query1);
		$db->query();
		
		$db->setQuery($query2);
		$db->query();
		
		$this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id, $text);
	}
    
    function saveorder(){
        $cid = JRequest::getVar('cid', array(), 'post', 'array');
        $order = JRequest::getVar('order', array(), 'post', 'array');
        $attr_id = JRequest::getInt("attr_id");

        foreach($cid as $k=>$id){
            $table = JSFactory::getTable('attributValue', 'jshop');
            $table->load($id);
            if ($table->value_ordering!=$order[$k]){
                $table->value_ordering = $order[$k];
                $table->store();
            }
        }

        $this->setRedirect("index.php?option=com_jshopping&controller=attributesvalues&attr_id=".$attr_id);
    }
    
    function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
    
    function delete_foto(){
        $jshopConfig = JSFactory::getConfig();
        
        $id = JRequest::getInt("id");
        $attributValue = JSFactory::getTable('attributValue', 'jshop');
        $attributValue->load($id);
        @unlink($jshopConfig->image_attributes_path."/".$attributValue->image);
        $attributValue->image = "";
        $attributValue->store();
        die();               
    }    
}