<?php
/**
* @version      4.7.0 31.05.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerUsers extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
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
        
        $user_site = new JUser($user_id);

        $_countries = JSFactory::getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $lists['country'] = JHTML::_('select.genericlist', $countries,'country','class = "inputbox" size = "1"','country_id','name', $user->country);
        $lists['d_country'] = JHTML::_('select.genericlist', $countries,'d_country','class = "inputbox endes" size = "1"','country_id','name', $user->d_country); 
        $user->birthday = getDisplayDate($user->birthday, $jshopConfig->field_birthday_format);
        $user->d_birthday = getDisplayDate($user->d_birthday, $jshopConfig->field_birthday_format);
        $option_title = array();
        foreach($jshopConfig->user_field_title as $key => $value){
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }    
        $lists['select_titles'] = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name', $user->title );
        $lists['select_d_titles'] = JHTML::_('select.genericlist', $option_title,'d_title','class = "inputbox endes"','title_id','title_name', $user->d_title );
        
        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $lists['select_client_types'] = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" ','id','name', $user->client_type);

        $_usergroups = JSFactory::getModel("userGroups");
        $usergroups = $_usergroups->getAllUsergroups();
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
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditUsers', array(&$view));
        $view->displayEdit();
        
    }
    
    function save() {
		$jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $apply = JRequest::getVar("apply");
        JSFactory::loadLanguageFile();
        
        $dispatcher = JDispatcher::getInstance();
        
        $user_shop = JSFactory::getTable('userShop', 'jshop');
        $user_id = JRequest::getInt("user_id");
        $post = JRequest::get("post");

        if (!$user_id){
            $params = JComponentHelper::getParams('com_users');
            $user_shop->bind($post);
            $user_shop->password = $post['password'];
            $user_shop->password2 = $post['password2'];            
            if (!$user_shop->check("editaccountadmin.edituser")){
                JError::raiseWarning('', $user_shop->getError());
                $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit");
                return 0;
            }
            $user = new JUser;
            $data = array();
            $data['groups'][] = $params->get('new_usertype', 2);
            $data['email'] = $post['email'];
            $data['password'] = $post['password'];
            $data['password2'] = $post['password2'];
            $data['name'] = $post['f_name'].' '.$post['l_name'];
            $data['username'] = $post["u_name"];
            $user->bind($data);
            if (!$user->save()){
                JError::raiseWarning('', $user->getError());
                $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit");
                return 0;
            }
            $user_id = $user->id;
            unset($user);
            $user_shop->user_id = $user_id;
            $user_shop->number = $user_shop->getNewUserNumber();
            unset($user_shop->password);
            unset($user_shop->password2);
            if (!$db->insertObject($user_shop->getTableName(), $user_shop, $user_shop->getKeyName())){
                JError::raiseWarning('', "Error insert in table ".$user_shop->getTableName());
                $this->setRedirect("index.php?option=com_jshopping&controller=users");
                return 0;
            }
            $post['user_id'] = $user_id;
            if (!$post['number']) $post['number'] = $user_shop->number;
        }

        $user_shop->load($user_id);
        
        $dispatcher->trigger( 'onBeforeSaveUser', array(&$post) );
        if ($post['birthday']) $post['birthday'] = getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        if ($post['d_birthday']) $post['d_birthday'] = getJsDateDB($post['d_birthday'], $jshopConfig->field_birthday_format);
        
        $user_shop->bind($post);
        $user_shop->password = JRequest::getVar('password');
        $user_shop->password2 = JRequest::getVar('password2');
        
		if (!$user_shop->check("editaccountadmin.edituser")){
            JError::raiseWarning("", $user_shop->getError());
            $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_shop->user_id);
            return 0;
        }
        
        unset($user_shop->password);
        unset($user_shop->password2);
        if (!$user_shop->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_shop->user_id);
            return 0;
        }
        
        $user = new JUser($user_id);
        $data['email']      = JRequest::getVar("email");
        $data['password']   = JRequest::getVar("password");
        $data['password2']  = JRequest::getVar("password2");
        $data['name']       = JRequest::getVar("f_name");
        $data['username']   = JRequest::getVar("u_name");
        $data['block']      = JRequest::getVar("block");
        
        $user->bind($data);
        $user->save(true);

        $dispatcher->trigger( 'onAfterSaveUser', array(&$user, &$user_shop) );
        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=users&task=edit&user_id=".$user_shop->user_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=users");
        }
    }
    
    function remove(){
        $mainframe = JFactory::getApplication();
        $cid = JRequest::getVar( 'cid', array(), '', 'array' );
        $me = JFactory::getUser();
        
        $dispatcher = JDispatcher::getInstance();
          
        if (JFactory::getUser()->authorise('core.admin', 'com_jshopping')){ 
            $dispatcher->trigger( 'onBeforeRemoveUser', array(&$cid) );
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

            $query = "UPDATE `#__jshopping_users` SET `block` = '".$db->escape($flag)."' WHERE `user_id` = '" . $db->escape($value) . "'";
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
?>