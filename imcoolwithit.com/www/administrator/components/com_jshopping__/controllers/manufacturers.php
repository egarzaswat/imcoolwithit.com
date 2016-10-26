<?php
/**
* @version      4.10.1 22.07.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerManufacturers extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );        

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("manufacturers");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false) {
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $context = "jshopping.list.admin.manufacturers";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $manufacturer = JSFactory::getModel("manufacturers");
        $rows = $manufacturer->getAllManufacturers(0, $filter_order, $filter_order_Dir);        
        $view=$this->getView("manufacturer", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayManufacturers', array(&$view));
        $view->displayList();
    }

    function edit() {
        $db = JFactory::getDBO();
        $man_id = JRequest::getInt("man_id");
        $manufacturer = JSFactory::getTable('manufacturer', 'jshop');
        $manufacturer->load($man_id);
        $edit = ($man_id)?(1):(0);
        
        if (!$man_id){
            $manufacturer->manufacturer_publish = 1;
        }
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $nofilter = array();
        JFilterOutput::objectHTMLSafe( $manufacturer, ENT_QUOTES, $nofilter);

        $view=$this->getView("manufacturer", 'html');
        $view->setLayout("edit");
        $view->assign('manufacturer', $manufacturer);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('etemplatevar', '');
        $view->assign('multilang', $multilang);
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditManufacturers', array(&$view));        
        $view->displayEdit();
    }

    function save(){
        $jshopConfig = JSFactory::getConfig();
        
        require_once ($jshopConfig->path.'lib/image.lib.php');
        require_once ($jshopConfig->path.'lib/uploadfile.class.php');
        
        
        $dispatcher = JDispatcher::getInstance();
        
        $apply = JRequest::getVar("apply");
        $_alias = JSFactory::getModel("alias");
        $db = JFactory::getDBO();
        $man = JSFactory::getTable('manufacturer', 'jshop');        
        $man_id = JRequest::getInt("manufacturer_id");

        $post = JRequest::get("post");
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]=="") $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            $post['alias_'.$lang->language] = JApplication::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, 0, $man_id)){
                $post['alias_'.$lang->language] = "";
                JError::raiseWarning("",_JSHOP_ERROR_ALIAS_ALREADY_EXIST);
            }
            $post['description_'.$lang->language] = JRequest::getVar('description'.$lang->id,'','post',"string", 2);
            $post['short_description_'.$lang->language] = JRequest::getVar('short_description_'.$lang->language,'','post',"string", 2);
        }
        
        if (!$post['manufacturer_publish']){
            $post['manufacturer_publish'] = 0;
        }
        
        $dispatcher->trigger( 'onBeforeSaveManufacturer', array(&$post) );
        
        if (!$man->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
            return 0;
        }
        
        if (!$man_id){
            $man->ordering = null;
            $man->ordering = $man->getNextOrder();            
        }        
        
        $upload = new UploadFile($_FILES['manufacturer_logo']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($jshopConfig->image_manufs_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){            
            if ($post['old_image']){
                @unlink($jshopConfig->image_manufs_path."/".$post['old_image']);
            }
            $name = $upload->getName();
            @chmod($jshopConfig->image_manufs_path."/".$name, 0777);
            
            if($post['size_im_category'] < 3){
                if($post['size_im_category'] == 1){
                    $category_width_image = $jshopConfig->image_category_width; 
                    $category_height_image = $jshopConfig->image_category_height;
                }else{
                    $category_width_image = JRequest::getInt('category_width_image'); 
                    $category_height_image = JRequest::getInt('category_height_image');
                }

                $path_full = $jshopConfig->image_manufs_path."/".$name;
                $path_thumb = $jshopConfig->image_manufs_path."/".$name;

                if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)) {
                    JError::raiseWarning("",_JSHOP_ERROR_CREATE_THUMBAIL);
                    saveToLog("error.log", "SaveManufacturer - Error create thumbail");
                }
                @chmod($jshopConfig->image_manufs_path."/".$name, 0777);    
                unset($img);
            }
            
            $man->manufacturer_logo = $name;
            
        }else{
            if ($upload->getError() != 4){
                JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_IMAGE);
                saveToLog("error.log", "SaveManufacturer - Error upload image. code: ".$upload->getError());
            }
        }
        
        
        if (!$man->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
            return 0;
        }
        
        $dispatcher->trigger( 'onAfterSaveManufacturer', array(&$man) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers&task=edit&man_id=".$man->manufacturer_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
        }
        
    }

    function remove(){
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        $jshopConfig = JSFactory::getConfig();
        $text = array();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveManufacturer', array(&$cid) );
        foreach ($cid as $key => $value) {
            $manuf = JSFactory::getTable('manufacturer', 'jshop');
            $manuf->load($value);
            $manuf->delete();
            
            $text[]= sprintf(_JSHOP_MANUFACTURER_DELETED, $value);
            if ($manuf->manufacturer_logo){
                @unlink($jshopConfig->image_manufs_path.'/'.$manuf->manufacturer_logo);
            }            
        }
        $dispatcher->trigger( 'onAfterRemoveManufacturer', array(&$cid) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers", implode("</li><li>",$text));
    }
    
    function publish(){
        $this->publishManufacturer(1);
    }
    
    function unpublish(){
        $this->publishManufacturer(0);
    }

    function publishManufacturer($flag) {
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishManufacturer', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__jshopping_manufacturers`
                       SET `manufacturer_publish` = '" . $db->escape($flag) . "'
                       WHERE `manufacturer_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
        
        $dispatcher->trigger( 'onAfterPublishManufacturer', array(&$cid, &$flag) );
        
        $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
    }
    
    function delete_foto(){
        $id = JRequest::getInt("id");
        $jshopConfig = JSFactory::getConfig();
        $manuf = JSFactory::getTable('manufacturer', 'jshop');
        $manuf->load($id);
        @unlink($jshopConfig->image_manufs_path.'/'.$manuf->manufacturer_logo);
        $manuf->manufacturer_logo = "";
        $manuf->store();        
        die();
    }
    
    function order(){        
        $id = JRequest::getInt("id");
        $move = JRequest::getInt("move");        
        $manuf = JSFactory::getTable('manufacturer', 'jshop');
        $manuf->load($id);
        $manuf->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
    }
    
    function saveorder(){
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
        
        foreach ($cid as $k=>$id){
            $table = JSFactory::getTable('manufacturer', 'jshop');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = JSFactory::getTable('manufacturer', 'jshop');
        $table->ordering = null;
        $table->reorder();        
                
        $this->setRedirect("index.php?option=com_jshopping&controller=manufacturers");
    }

}
?>		