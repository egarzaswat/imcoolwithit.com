<?php
/**
* @version      4.11.0 31.05.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();

class JshoppingControllerUsers extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );
        $this->registerTask('add',   'edit');
        $this->registerTask('apply', 'save');
        checkAccessController("users");
        addSubmenu("users");
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshopping.list.admin.users";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 'text_search', '' );
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "u_name", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $users = JSFactory::getModel("users");
        
        $total = $users->getCountAllUsers($text_search);
        
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);
        $rows = $users->getAllUsers($pageNav->limitstart, $pageNav->limit, $text_search, $filter_order, $filter_order_Dir);
        
        $view=$this->getView("users", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('pageNav', $pageNav);
        $view->assign('text_search', $text_search);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->sidebar = JHtmlSidebar::render();
		
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayUsers', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $me =  JFactory::getUser();
        $user_id = JRequest::getInt("user_id");
        $user = JSFactory::getTable('userShop', 'jshop');
        $user->load($user_id);
        $user->loadDataFromEdit();
		
        $user_site = new JUser($user_id);
		
		$lists['country'] = JshopHelpersSelects::getCountry($user->country);
		$lists['d_country'] = JshopHelpersSelects::getCountry($user->d_country, 'class = "inputbox endes"', 'd_country');
		$lists['select_titles'] = JshopHelpersSelects::getTitle($user->title);
		$lists['select_d_titles'] = JshopHelpersSelects::getTitle($user->d_title, 'class = "inputbox endes"', 'd_title');
		$lists['select_client_types'] = JshopHelpersSelects::getClientType($user->client_type);

        $usergroups = JSFactory::getModel("userGroups")->getAllUsergroups();
        $lists['usergroups'] = JHTML::_('select.genericlist', $usergroups, 'usergroup_id', 'class = "inputbox" size = "1"', 'usergroup_id', 'usergroup_name', $user->usergroup_id);
        $lists['block'] = JHTML::_('select.booleanlist',  'block', 'class="inputbox" size="1"', $user_site->get('block') );  
        
        filterHTMLSafe($user, ENT_QUOTES);
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('editaccount');
        
		JHTML::_('behavior.calendar');
		
        $view=$this->getView("users", 'html');
        $view->setLayout("edit");
		$view->assign('config', $jshopConfig);
        $view->assign('user', $user);  
        $view->assign('me', $me);       
        $view->assign('user_site', $user_site);
        $view->assign('lists', $lists);
        $view->assign('etemplatevar', '');
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        JDispatcher::getInstance()->trigger('onBeforeEditUsers', array(&$view));
        $view->displayEdit();        
    }
    
    function save() {
        $apply = JRequest::getVar("apply");
        JSFactory::loadLanguageFile();        
        $dispatcher = JDispatcher::getInstance();        
        $user_id = JRequest::getInt("user_id");
        $post = JRequest::get("post");
		
		$dispatcher->trigger('onBeforeSaveUser', array(&$post));
        
		if ($user_id){
			$model = JSFactory::getModel('useredit', 'jshop');
			$model->setUserId($user_id);
		}else{
			$model = JSFactory::getModel('userregister', 'jshop');
		}
		$model->setAdminRegistration(1);
		$model->setData($post);
		if (!$model->check('editaccountadmin.edituser')){
			JError::raiseWarning('', $model->getError());
			$this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_id);
			return 0;
		}
		if (!$model->save()){
			JError::raiseWarning('', $model->getError());            
			$this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_id);
			return 0;
		}
		
		$user_shop = $model->getUser();
		$user = $model->getUserJoomla();
		        
        $dispatcher->trigger('onAfterSaveUser', array(&$user, &$user_shop));
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_shop->user_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=users");
        }
    }
    
    function remove(){
        $mainframe = JFactory::getApplication();
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $me = JFactory::getUser();
        
        $dispatcher = JDispatcher::getInstance();
          
        if (JFactory::getUser()->authorise('core.admin', 'com_jshopping')){ 
            $dispatcher->trigger('onBeforeRemoveUser', array(&$cid));
            foreach($cid as $id){
                if ($me->get('id')==(int)$id) {
                    JError::raiseWarning("", JText::_('You cannot delete Yourself!'));
                    continue;
                }
                $user = JUser::getInstance((int)$id);
                $user->delete();
                $mainframe->logout((int)$id);
                $user_shop = JSFactory::getTable('userShop', 'jshop');
                $user_shop->delete((int)$id); 
            }
            $dispatcher->trigger( 'onAfterRemoveUser', array(&$cid) );
        }
        $this->setRedirect("index.php?option=com_jshopping&controller=users");
    }
    
    function publish(){
        $this->blockUser(0);
        $this->setRedirect('index.php?option=com_jshopping&controller=users');
    }
    
    function unpublish(){
        $this->blockUser(1);
        $this->setRedirect('index.php?option=com_jshopping&controller=users');
    }
    
    function blockUser($flag) {        
        $db = JFactory::getDBO();
        
        $dispatcher = JDispatcher::getInstance();
        $cid = JRequest::getVar("cid");
        $dispatcher->trigger( 'onBeforePublishUser', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $query = "UPDATE `#__users` SET `block` = '".$db->escape($flag)."' WHERE `id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->query();
        }
                
        $dispatcher->trigger( 'onAfterPublishUser', array(&$cid, &$flag) );
    }
    
    function get_userinfo() {
        $db = JFactory::getDBO();
        $id = JRequest::getInt('user_id');
        if(!$id){
            print '{}';
            die;
        }
        
        $query = 'SELECT * FROM `#__jshopping_users` WHERE `user_id`='.$id;
        $db->setQuery($query);
        $user = $db->loadAssoc();
        echo json_encode((array)$user);
        die();
    }    
    
}