<?php
/**
 * @version      4.9.0 24.07.2013
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerMeta_data extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        addSubmenu("meta_data");
    }

    function display($cachable = false, $urlparams = false){
        $metaData = JSFactory::getTable('metaData', 'jshop');
        $list = $metaData->getAllMetaData();


        $view = $this->getView("meta_data", 'html');
        $view->setLayout("list");
        $view->assign('list', $list);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayMetaData', array(&$view));
        $view->displayList();
    }

    function edit() {
        $id = JRequest::getInt("id");

        $metadata = JSFactory::getTable('metaData', 'jshop');
        $metadata->load($id);

        JFilterOutput::objectHTMLSafe($metadata, ENT_QUOTES);

        $view=$this->getView("meta_data", 'html');
        $view->setLayout("edit");
        $view->assign('meta_data', $metadata);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditMetaData', array(&$view, &$metadata));
        $view->displayEdit();
    }

    function save(){
        $id = JRequest::getInt('id');
        $dispatcher = JDispatcher::getInstance();

        $metadata = JSFactory::getTable('metaData', 'jshop');
        $post = JRequest::get("post");
        $post['content'] = JRequest::getVar( 'content', '', 'post', 'string', JREQUEST_ALLOWHTML);

        $dispatcher->trigger( 'onBeforeSaveMetaData', array(&$post) );

        if (!$metadata->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=meta_data");
            return 0;
        }

        if (!$metadata->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=meta_data");
            return 0;
        }

        if (!$id){
            $id = $metadata->id;
        }

        $dispatcher->trigger( 'onAfterSaveQuestion', array(&$metadata) );

        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=meta_data&task=edit&id=".$id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=meta_data");
        }

    }

    function remove() {
        $cid = JRequest::getVar("cid");
        $db = JFactory::getDBO();

        $dispatcher = JDispatcher::getInstance();

        $dispatcher->trigger( 'onBeforeRemoveMetaData', array(&$cid) );

        $text = '';
        foreach ($cid as $key => $value) {
            $value = intval($value);
            $query = "DELETE "."FROM `#__meta_data` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();

            $text = _JSHOP_META_DATA_DELETED;
        }

        $dispatcher->trigger( 'onAfterRemoveMetaData', array(&$cid) );

        $this->setRedirect("index.php?option=com_jshopping&controller=meta_data", $text);
    }

}