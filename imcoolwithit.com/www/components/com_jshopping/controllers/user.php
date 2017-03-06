<?php
/**
 * @version      4.8.0 18.12.2014
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerUser extends JControllerLegacy{

    function __construct($config = array()){
        parent::__construct( $config );
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerUser', array(&$this));
    }

    function display($cachable = false, $urlparams = false){
        $this->myaccount();
    }

    function login(){
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();

        $user = JFactory::getUser();
        if ($user->id){
            $view_name = "user";
            $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("logout");
            $view->display();
            return 0;
        }

        if (JRequest::getVar('return')){
            $return = JRequest::getVar('return');
        }else{
            $return = $session->get('return');
        }

        $show_pay_without_reg = $session->get("show_pay_without_reg");

        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("login");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_LOGIN);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_LOGIN;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }

        $country = JSFactory::getTable('country', 'jshop');
        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option',  '0', _JSHOP_REG_SELECT, 'country_id', 'name' );
        $select_countries = JHTML::_('select.genericlist', array_merge($option_country, $list_country),'country','id = "country" class = "inputbox" size = "1"','country_id','name' );
        foreach ($jshopConfig->user_field_title as $key => $value) {
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name' );

        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name');

        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['register'];

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayLogin', array() );
        if ($jshopConfig->show_registerform_in_logintemplate){
            $dispatcher->trigger('onBeforeDisplayRegister', array());
        }
        if ($jshopConfig->show_registerform_in_logintemplate && $config_fields['birthday']['display']){
            JHTML::_('behavior.calendar');
        }
        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("login");
        $view->assign('href_register', SEFLink('index.php?option=com_jshopping&controller=user&task=register',1,0, $jshopConfig->use_ssl));
        $view->assign('href_lost_pass', SEFLInk('index.php?option=com_users&view=reset',0,0, $jshopConfig->use_ssl));
        $view->assign('return', $return);
        $view->assign('Itemid', JRequest::getVar('Itemid'));
        $view->assign('config', $jshopConfig);
        $view->assign('show_pay_without_reg', $show_pay_without_reg);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('select_titles', $select_titles);
        $view->assign('select_countries', $select_countries);
        $view->assign('config_fields', $config_fields);
        $view->assign('live_path', JURI::base());
        $view->assign('urlcheckdata', SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1", 1, 1, $jshopConfig->use_ssl));
        $dispatcher->trigger('onBeforeDisplayLoginView', array(&$view));
        if ($jshopConfig->show_registerform_in_logintemplate){
            $dispatcher->trigger('onBeforeDisplayRegisterView', array(&$view));
        }
        $view->display();
    }

    function loginsave(){
        $jshopConfig = JSFactory::getConfig();
        $mainframe = JFactory::getApplication();
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeLoginSave', array());
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = base64_decode($return);
            if (!JURI::isInternal($return)) {
                $return = '';
            }
        }

        $options = array();
        $options['remember'] = JRequest::getBool('remember', false);
        $options['return'] = $return;

        $credentials = array();
        $credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
        $credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

        $dispatcher->trigger( 'onBeforeLogin', array(&$options, &$credentials) );

        $error = $mainframe->login($credentials, $options);

        setNextUpdatePrices();

        if ((!JError::isError($error)) && ($error !== FALSE)){
            if ( ! $return ) {
                $return = JURI::base();
            }
            $dispatcher->trigger('onAfterLogin', array(&$options, &$credentials));
            $mainframe->redirect( $return );
        }else{
            $dispatcher->trigger('onAfterLoginEror', array(&$options, &$credentials));
            $mainframe->redirect( SEFLink('index.php?option=com_jshopping&controller=user&task=login&return='.base64_encode($return),0,1,$jshopConfig->use_ssl) );
        }
    }

    function check_user_exist_ajax(){
        $dispatcher = JDispatcher::getInstance();
        $mes = array();
        $username = JRequest::getVar("username");
        $email = JRequest::getVar("email");
        $dispatcher->trigger('onBeforeUserCheck_user_exist_ajax', array(&$mes, &$username, &$email));
        $db = JFactory::getDBO();
        if ($username){
            $query = "SELECT id FROM `#__users` WHERE username = '".$db->escape($username)."'";
            $db->setQuery($query);
            $db->query();
            if ($db->getNumRows()){
                $mes[] = sprintf(_JSHOP_USER_EXIST, $username);
            }
        }
        if ($email){
            $query = "SELECT id FROM `#__users` WHERE email = '".$db->escape($email). "'";
            $db->setQuery($query);
            $db->query();
            if ($db->getNumRows()){
                $mes[] = sprintf(_JSHOP_USER_EXIST_EMAIL, $email);
            }
        }
        $dispatcher->trigger('onAfterUserCheck_user_exist_ajax', array(&$mes, &$username, &$email));
        if (count($mes)==0){
            print "1";
        }else{
            print implode("\n",$mes);
        }
        die();
    }

    function register(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();

        $session = JFactory::getSession();
        if (JRequest::getInt('lrd')){
            $adv_user = (object)$session->get('registrationdata');
        }else{
            $adv_user = new stdClass();
        }

        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("register");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_REGISTRATION);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_REGISTRATION;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }

        $usersConfig = JComponentHelper::getParams( 'com_users' );
        if ($usersConfig->get('allowUserRegistration') == '0') {
            JError::raiseError( 403, JText::_( 'Access Forbidden' ));
            return;
        }

        if (!$adv_user->country) $adv_user->country = $jshopConfig->default_country;
        $country = JSFactory::getTable('country', 'jshop');
        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option',  '0', _JSHOP_REG_SELECT, 'country_id', 'name' );
        $select_countries = JHTML::_('select.genericlist', array_merge($option_country, $country->getAllCountries()),'country','id = "country" class = "inputbox" size = "1"','country_id','name', $adv_user->country);
        foreach($jshopConfig->user_field_title as $key => $value){
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name', $adv_user->title);

        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name', $adv_user->client_type);

        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['register'];

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayRegister', array(&$adv_user));

        filterHTMLSafe($adv_user, ENT_QUOTES);

        if ($config_fields['birthday']['display']){
            JHTML::_('behavior.calendar');
        }
        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("register");
        $view->assign('config', $jshopConfig);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('select_titles', $select_titles);
        $view->assign('select_countries', $select_countries);
        $view->assign('config_fields', $config_fields);
        $view->assign('user', $adv_user);
        $view->assign('live_path', JURI::base());
        $view->assign('urlcheckdata', SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1",1,1));
        $dispatcher->trigger('onBeforeDisplayRegisterView', array(&$view));
        $view->display();
    }

    function registersave(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $config = JFactory::getConfig();
        $db = JFactory::getDBO();
        $params = JComponentHelper::getParams('com_users');
        $lang = JFactory::getLanguage();
        $lang->load('com_users');
        $post = JRequest::get('post');

        $dispatcher = JDispatcher::getInstance();

        if ($params->get('allowUserRegistration')==0){
            JError::raiseError( 403, JText::_('Access Forbidden'));
            return;
        }

        $usergroup = JSFactory::getTable('usergroup', 'jshop');
        $default_usergroup = $usergroup->getDefaultUsergroup();
        $post['username'] = $post['u_name'];
        $post['password2'] = $post['password_2'];
        if ($post['f_name']=="") $post['f_name'] = $post['email'];
        $post['name'] = $post['f_name'].' '.$post['l_name'];
        if ($post['birthday']) $post['birthday'] = getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);

        $post['lang'] = $jshopConfig->getLang();

        $dispatcher->trigger('onBeforeRegister', array(&$post, &$default_usergroup));

        $row = JSFactory::getTable('userShop', 'jshop');
        $row->bind($post);
        $row->usergroup_id = $default_usergroup;
        $row->password = $post['password'];
        $row->password2 = $post['password2'];

        if (!$row->check("register")){
            $session = JFactory::getSession();
            $registrationdata = JRequest::get('post');
            $session->set('registrationdata', $registrationdata);
            JError::raiseWarning('', $row->getError());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=register&lrd=1",1,1, $jshopConfig->use_ssl));
            return 0;
        }
        if ($post["u_name"]==""){
            $post["u_name"] = $post['email'];
            $row->u_name = $post["u_name"];
        }
        if ($post["password"]==""){
            $post["password"] = substr(md5('up'.time()), 0, 8);
        }
        $user = new JUser;
        $data = array();
        $data['groups'][] = $params->get('new_usertype', 2);
        $data['email'] = $post['email'];
        $data['password'] = $post['password'];
        $data['password2'] = $post['password2'];
        $data['name'] = $post['f_name'].' '.$post['l_name'];
        $data['username'] = $post["u_name"];
        $useractivation = $params->get('useractivation');
        $sendpassword = $params->get('sendpassword', 1);

        if (($useractivation == 1) || ($useractivation == 2)){
            jimport('joomla.user.helper');
            $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
            $data['block'] = 1;
        }

        $user->bind($data);
        if (!$user->save()){
            JError::raiseWarning('', $user->getError());
            saveToLog('error.log', 'Error registration-'.$user->getError());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=register",1,1,$jshopConfig->use_ssl));
            return 0;
        }

        $row->user_id = $user->id;
        $row->number = $row->getNewUserNumber();
        unset($row->password);
        unset($row->password2);
        if (!$db->insertObject($row->getTableName(), $row, $row->getKeyName())){
            saveToLog('error.log', $db->getErrorMsg());
            JError::raiseWarning('', "Error insert in table ".$row->getTableName());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=register",1,1,$jshopConfig->use_ssl));
            return 0;
        }

        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = JUri::base();

        if ($useractivation == 2){
            $uri = JURI::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);

            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        }else if ($useractivation == 1){
            $uri = JURI::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);

            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword) {
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
                    $data['siteurl'],
                    $data['username']
                );
            }
        } else {
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            if ($sendpassword){
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            }else{
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl']
                );
            }
        }

        $dispatcher->trigger('onBeforeRegisterSendMailClient', array(&$post, &$data, &$emailSubject, &$emailBody));

        $mailer = JFactory::getMailer();
        $mailer->setSender(array($data['mailfrom'], $data['fromname']));
        $mailer->addRecipient($data['email']);
        $mailer->setSubject($emailSubject);
        $mailer->setBody($emailBody);
        $mailer->isHTML(false);
        $dispatcher->trigger('onBeforeRegisterMailerSendMailClient', array(&$mailer, &$post, &$data, &$emailSubject, &$emailBody));
        $mailer->Send();

        if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)){
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            $emailBodyAdmin = JText::sprintf(
                'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
                $data['name'],
                $data['username'],
                $data['siteurl']
            );

            $query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';
            $db->setQuery( $query );
            $rows = $db->loadObjectList();
            $mode = false;
            foreach($rows as $rowadm){
                $dispatcher->trigger('onBeforeRegisterSendMailAdmin', array(&$post, &$data, &$emailSubject, &$emailBodyAdmin, &$rowadm, &$mode));
                $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $rowadm->email, $emailSubject, $emailBodyAdmin, $mode);
            }
        }

        $dispatcher->trigger('onAfterRegister', array(&$user, &$row, &$post, &$useractivation));

        if ( $useractivation == 2 ){
            $message  = JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
        } elseif ( $useractivation == 1 ){
            $message  = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
        } else {
            $message = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
        }

        $return = SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl);

        $this->setRedirect($return, $message);
    }

    public function activate(){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        $uParams = JComponentHelper::getParams('com_users');
        $lang =  JFactory::getLanguage();
        $lang->load( 'com_users' );
        jimport('joomla.user.helper');

        if ($user->get('id')) {
            $this->setRedirect('index.php');
            return true;
        }

        if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) {
            JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
            return false;
        }

        $model = JSFactory::getTable('userShop', 'jshop');
        $token = JRequest::getVar('token', null, 'request', 'alnum');

        if ($token === null || strlen($token) !== 32) {
            JError::raiseError(403, JText::_('JINVALID_TOKEN'));
            return false;
        }

        $return = $model->activate($token);

        JDispatcher::getInstance()->trigger('onAfterUserActivate', array(&$model, &$token, &$return));

        if ($return === false) {
            $this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect('index.php');
            return false;
        }

        $useractivation = $uParams->get('useractivation');

        if ($useractivation == 0){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }elseif ($useractivation == 1){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }elseif ($return->getParam('activate')){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }else{
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }
        return true;
    }

    function settings(){
        checkUserLogin();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();

        $adv_user = JSFactory::getUserShop();
        $adv_user->birthday = JSFactory::getAge($adv_user->birthday);

        appendPathWay(_JSHOP_EDIT_DATA);

        $meta_data = JSFactory::getMetaData('settings');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        if (!$adv_user->country) $adv_user->country = $jshopConfig->default_country;
        if (!$adv_user->d_country) $adv_user->d_country = $jshopConfig->default_country;

        $adv_user->d_birthday = getDisplayDate($adv_user->d_birthday, $jshopConfig->field_birthday_format);

        $sex_options = '<select name="sex_options" required="required">';
        foreach($jshopConfig->user_sex as $key => $value){
            if($key == $adv_user->sex){
                $sex_options .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $sex_options .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $sex_options .= '</select>';

        $looking_for_options = '<select name="looking_for_options" required="required">';
        foreach($jshopConfig->user_looking_for as $key => $value){
            if($key == $adv_user->looking_for){
                $looking_for_options .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                $looking_for_options .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $looking_for_options .= '</select>';

        $distance_options = '<select name="distance_for_options" required="required">';
        foreach($jshopConfig->user_distance as $key => $value){
            if($key == $adv_user->distance){
                $distance_options .= '<option selected value="' . $key . '">' . $value . '</option>';
            } else {
                if($adv_user->distance == 0 && $key == 120){
                    $distance_options .= '<option selected value="' . $key . '">' . $value . '</option>';
                } else {
                    $distance_options .= '<option value="' . $key . '">' . $value . '</option>';
                }
            }
        }
        $distance_options .= '</select>';

        /*$select_looking_for = '';
        foreach($jshopConfig->looking_for as $key => $value){
            if($key == $adv_user->looking_for){
                $select_looking_for .= '<input  type="radio" id="radio_looking' . $key . '" name="looking_for" value="' . $key . '" checked required="required">';
            } else {
                $select_looking_for .= '<input type="radio" id="radio_looking' . $key . '" name="looking_for" value="' . $key . '" required="required">';
            }

            $select_looking_for .= '<label for="radio_looking' . $key . '">' . $value . '</label>';
        }*/

        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayEditUser', array(&$adv_user));

        filterHTMLSafe( $adv_user, ENT_QUOTES);

        if ($config_fields['birthday']['display'] || $config_fields['d_birthday']['display']){
            JHTML::_('behavior.calendar');
        }

        $user_shop = JSFactory::getTable('userShop', 'jshop');
        $user_shop->load($adv_user->user_id);
        $registered = $user_shop->register_activate ? true : false;

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("editaccount");
        $view->assign('title', JText::_('SETTINGS_PAGE_EDIT_DATA'));
        $view->assign('sex_options', $sex_options);
        $view->assign('looking_for_options', $looking_for_options);
        $view->assign('distance_options', $distance_options);

        $view->assign('registered',$registered);
        $view->assign('user', $adv_user);
        $dispatcher->trigger('onBeforeDisplayEditAccountView', array(&$view));
        $view->display();
    }

    function edit_photos(){
        checkUserLogin();
        $adv_user = JSFactory::getUserShop();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        appendPathWay(_JSHOP_EDIT_DATA);
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayEditUser', array(&$adv_user));
        filterHTMLSafe( $adv_user, ENT_QUOTES);

        $meta_data = JSFactory::getMetaData('edit_account');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $img_big = JSFactory::existImage($conf->path_user_image_big, $adv_user->photosite);
        $adv_user->photosite = JSFactory::existImage($conf->path_user_image_big, $adv_user->photosite);

        $image_avatar = array(
            'path_to_load'  => $conf->path_user_image_big,
            'src_big'       => $img_big,
            'src'           => $adv_user->photosite,
            'avatar_w'      => $conf->size_avatar_big_w,
            'avatar_h'      => $conf->size_avatar_big_h,
        );

        $modelUser = JSFactory::getModel('user', 'jshop');

        $images = $modelUser->getUserAlbum(JSFactory::getUser()->user_id);

        $limit_upload_images = true;
        if(count($images) >= $conf->limit_upload_photo){
            $limit_upload_images = false;
        }

        $public_images = array();
        $private_images = array();

        foreach($images as $temp){
            if($temp->private == 0){
                array_push($public_images, $temp);
            } else {
                array_push($private_images, $temp);
            }
        }

        $images_album = array(
            'path_to_load'  => $conf->path_albums_image . "user_" . JSFactory::getUser()->user_id . "/",
            'path_to_album' => $conf->path_albums_image . "user_" . JSFactory::getUser()->user_id . "/",
            'path_to_thumb' => $conf->path_albums_image . "user_" . JSFactory::getUser()->user_id . "/thumb/",
            'images'        => $public_images,
            'private_images'=> $private_images
        );

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("edit_photos");
        $view->assign('user', $adv_user);
        $view->assign('link_Q_n_A', 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_COMPLETE_PROFILE'));
        $view->assign('image_avatar', $image_avatar);
        $view->assign('images_album', $images_album);
        $view->assign('permission_upload_photo', $conf->permission_upload_photo);
        $view->assign('limit_upload_images', $limit_upload_images);
        $view->assign('count_limit_upload_images', $conf->limit_upload_photo);
        $view->assign('available_images_count', $conf->limit_upload_photo - count($images));
//        $view->assign('menu', $menu);
        $dispatcher->trigger('onBeforeDisplayEditAccountView', array(&$view));
        $view->display();
    }

    function accountsave(){
        checkUserLogin();
        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();

        $user_shop = JSFactory::getTable('userShop', 'jshop');
        $post = JRequest::get('post');
        if (!isset($post['password'])) $post['password'] = '';
        if (!isset($post['password_2'])) $post['password_2'] = '';
        if ($post['d_birthday']) $post['d_birthday'] = getJsDateDB($post['d_birthday'], $jshopConfig->field_birthday_format);
        $post['lang'] = $jshopConfig->getLang();

        unset($post['user_id']);
        unset($post['usergroup_id']);
        $user_shop->load($user->id);
        $user_shop->bind($post);
        $user_shop->password = $post['password'];
        $user_shop->password2 = $post['password_2'];

        if(!$user_shop->register_activate):

            $uploadPhoto = $this->uploadPhotoWhitFB($user_shop->photo);
            $user_shop->photosite = $uploadPhoto;

        endif;

        $user_shop->register_activate = 1;

        if (!$user_shop->check("editaccount")) {
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT'));
//            JError::raiseWarning('',$user_shop->getError());
//            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",0,1,$jshopConfig->use_ssl));
//            return 0;
        }
        unset($user_shop->password);
        unset($user_shop->password2);

        if (!$user_shop->store()){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT'));
//            JError::raiseWarning(500,_JSHOP_REGWARN_ERROR_DATABASE);
//            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",0,1,$jshopConfig->use_ssl));
//            return 0;
        }

        $user = new JUser($user->id);
        if (!$jshopConfig->not_update_user_joomla){
            if ($user_shop->email){
                $user->email = $user_shop->email;
            }
            if ($user_shop->f_name || $user_shop->l_name){
                $user->name = $user_shop->f_name.' '.$user_shop->l_name;
            }
        }
        if ($post['password']!=''){
            $data = array("password"=>$post['password'], "password2"=>$post['password']);
            $user->bind($data);
        }



        $user->save();

        $data = array();
        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $app->setUserState('com_users.edit.profile.data', $data);

        $message = JFactory::getApplication()->enqueueMessage(_JSHOP_ACCOUNT_UPDATE, 'success');
        header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT'));
//        $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",0,1,$jshopConfig->use_ssl), $message);
    }

    function orders(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $user = JFactory::getUser();
        $order = JSFactory::getTable('order', 'jshop');

        appendPathWay(_JSHOP_MY_ORDERS);
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("myorders");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_MY_ORDERS;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);

        $orders = $order->getOrdersForUser($user->id);
        $total = 0;
        foreach($orders as $key=>$value){
            $orders[$key]->order_href = SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$value->order_id,0,0,$jshopConfig->use_ssl);
            $total += $value->order_total / $value->currency_exchange;
        }

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayListOrder', array(&$orders) );

        $view_name = "order";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("listorder");
        $view->assign('orders', $orders);
        $view->assign('image_path', $jshopConfig->live_path."images");
        $view->assign('total', $total);
        $dispatcher->trigger('onBeforeDisplayOrdersView', array(&$view));
        $view->display();
    }

    function order(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        $dispatcher = JDispatcher::getInstance();

        appendPathWay(_JSHOP_MY_ORDERS, SEFLink('index.php?option=com_jshopping&controller=user&task=orders',0,0,$jshopConfig->use_ssl));

        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("myorder-detail");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_MY_ORDERS;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);

        $order_id = JRequest::getInt('order_id');
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $dispatcher->trigger('onAfterLoadOrder', array(&$order, &$user));

        appendPathWay(_JSHOP_ORDER_NUMBER.": ".$order->order_number);

        if ($user->id!=$order->user_id){
            JError::raiseError( 500, "Error order number. You are not the owner of this order");
        }

        $order->items = $order->getAllItems();
        $order->weight = $order->getWeightItems();
        $order->status_name = $order->getStatus();
        $order->history = $order->getHistory();
        if ($jshopConfig->client_allow_cancel_order && $order->order_status!=$jshopConfig->payment_status_for_cancel_client && !in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client) ){
            $allow_cancel = 1;
        }else{
            $allow_cancel = 0;
        }

        if ($order->weight==0 && $jshopConfig->hide_weight_in_cart_weight0){
            $jshopConfig->show_weight_order = 0;
        }

        $order->birthday = getDisplayDate($order->birthday, $jshopConfig->field_birthday_format);
        $order->d_birthday = getDisplayDate($order->d_birthday, $jshopConfig->field_birthday_format);

        $shipping_method =JSFactory::getTable('shippingMethod', 'jshop');
        $shipping_method->load($order->shipping_method_id);

        $name = $lang->get("name");
        $description = $lang->get("description");
        $order->shipping_info = $shipping_method->$name;

        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
        $order->payment_name = $pm_method->$name;
        if ($pm_method->show_descr_in_email) $order->payment_description = $pm_method->$description;  else $order->payment_description = "";

        $country = JSFactory::getTable('country', 'jshop');
        $country->load($order->country);
        $field_country_name = $lang->get("name");
        $order->country = $country->$field_country_name;

        $d_country = JSFactory::getTable('country', 'jshop');
        $d_country->load($order->d_country);
        $field_country_name = $lang->get("name");
        $order->d_country = $d_country->$field_country_name;

        $jshopConfig->user_field_client_type[0]="";
        $order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];

        $order->delivery_time_name = '';
        $order->delivery_date_f = '';
        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = JSFactory::getAllDeliveryTime();
            $order->delivery_time_name = $deliverytimes[$order->delivery_times_id];
            if ($order->delivery_time_name==""){
                $order->delivery_time_name = $order->delivery_time;
            }
        }
        if ($jshopConfig->show_delivery_date && !datenull($order->delivery_date)){
            $order->delivery_date_f = formatdate($order->delivery_date);
        }

        $order->order_tax_list = $order->getTaxExt();
        $show_percent_tax = 0;
        if (count($order->order_tax_list)>1 || $jshopConfig->show_tax_in_product) $show_percent_tax = 1;
        if ($jshopConfig->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if (($jshopConfig->hide_tax || count($order->order_tax_list)==0) && $order->order_discount==0 && $order->order_payment==0 && $jshopConfig->without_shipping) $hide_subtotal = 1;

        $text_total = _JSHOP_ENDTOTAL;
        if (($jshopConfig->show_tax_in_product || $jshopConfig->show_tax_product_in_cart) && (count($order->order_tax_list)>0)){
            $text_total = _JSHOP_ENDTOTAL_INKL_TAX;
        }

        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');

        if ($jshopConfig->order_display_new_digital_products){
            $product = JSFactory::getTable('product', 'jshop');
            foreach($order->items as $k=>$v){
                $product->product_id = $v->product_id;
                $product->setAttributeActive(unserialize($v->attributes));
                $files = $product->getSaleFiles();
                $order->items[$k]->files = serialize($files);
            }
        }

        $dispatcher->trigger('onBeforeDisplayOrder', array(&$order));

        $view_name = "order";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("order");
        $view->assign('order', $order);
        $view->assign('config', $jshopConfig);
        $view->assign('text_total', $text_total);
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('image_path', $jshopConfig->live_path . "images");
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('allow_cancel', $allow_cancel);
        $dispatcher->trigger('onBeforeDisplayOrderView', array(&$view));
        $view->display();
    }

    function cancelorder(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        $mainframe = JFactory::getApplication();

        if (!$jshopConfig->client_allow_cancel_order) return 0;

        $order_id = JRequest::getInt('order_id');

        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);

        appendPathWay(_JSHOP_ORDER_NUMBER.": ".$order->order_number);

        if ($user->id!=$order->user_id){
            JError::raiseError( 500, "Error order number");
        }
        $status = $jshopConfig->payment_status_for_cancel_client;

        if ($order->order_status==$status || in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client)){
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=order&order_id=".$order_id,0,1,$jshopConfig->use_ssl));
            return 0;
        }

        $checkout = JSFactory::getModel('checkout', 'jshop');
        $checkout->changeStatusOrder($order_id, $status, 1);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterUserCancelOrder', array(&$order_id));

        $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=order&order_id=".$order_id,0,1,$jshopConfig->use_ssl), _JSHOP_ORDER_CANCELED);
    }

    function myaccount(){
        checkUserLogin();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $meta_data = JSFactory::getMetaData('myaccount');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelUser = JSFactory::getModel('user', 'jshop');
        $adv_user = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('user_id', 'u_name', 'birthday', 'city', 'state', 'photosite', 'block', 'user_reviews', 'looking_for', 'age_look_from', 'age_look_to', 'distance', 'zip', 'longitude', 'latitude', 'sex', 'user_reviews', 'last_visit', 'register_activate'), true);

        if($adv_user->register_activate == 0){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_ACCOUNT'));
        }

        if($adv_user->block == 0){
            $adv_user->age = JSFactory::getAge($adv_user->birthday);
            $adv_user->photosite = JSFactory::existImage($conf->path_user_image_big, $adv_user->photosite);

            if(is_null($adv_user->height) || $adv_user->height == ""){
                $adv_user->height = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->status) || $adv_user->status == ""){
                $adv_user->status = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->ethnicity) || $adv_user->ethnicity == ""){
                $adv_user->ethnicity = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->body) || $adv_user->body == ""){
                $adv_user->body = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->profession) || $adv_user->profession == ""){
                $adv_user->profession = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->religion) || $adv_user->religion == ""){
                $adv_user->religion = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->kids) || $adv_user->kids == ""){
                $adv_user->kids = JText::_('UNKNOWN');
            }

            if(is_null($adv_user->user_about) || $adv_user->user_about == ""){
                $adv_user->user_about = JText::_('MY_COOL_DEFAULT');
            }

            if(is_null($adv_user->look_qualites) || $adv_user->look_qualites == ""){
                $adv_user->look_qualites = JText::_('MY_QUALITIES_DEFAULT');
            }

            if(is_null($adv_user->recommend) || $adv_user->recommend == ""){
                $adv_user->recommend = JText::_('MY_RECOMMEND_DEFAULT');
            }

            if(is_null($adv_user->few_places) || $adv_user->few_places == ""){
                $adv_user->few_places = JText::_('MY_FEW_PLACES_DEFAULT');
            }

            $adv_user->last_visit = JSFactory::getDateDiffFormat($adv_user->last_visit);
            $adv_user->user_reviews = ($adv_user->user_reviews > 5) ? 5 : $adv_user->user_reviews;


            if($adv_user->sex == 2){
                $adv_user->sex = JText::_('MALE');
            }
            if($adv_user->sex == 1){
                $adv_user->sex = JText::_('FEMALE');
            }

            $modelVisitors = JSFactory::getModel('visitors', 'jshop');
            $count_new_visitors = $modelVisitors->getProfileCountNewVisitors();

            $modelFriends = JSFactory::getModel('friends', 'jshop');
            $count_new_tokens = $modelFriends->getCountReceivedTokens();

            $modelMessages = JSFactory::getModel('messaging', 'jshop');
            $count_new_messages = $modelMessages->getCountNewMessages();

            $adv_user->images_album = array(
                'path_to_album'  => $conf->path_albums_image . "user_" . $adv_user->user_id . "/",
                'path_to_thumb'  => $conf->path_albums_image . "user_" . $adv_user->user_id . "/thumb/",
                'images'        => $modelUser->getUserAlbumProfile($adv_user->user_id)
            );

            $view_name = "user";
            $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("myaccount");
            $view->assign('user', $adv_user);
            $view->assign('count_new_visitors', $count_new_visitors);
            $view->assign('count_new_tokens', $count_new_tokens);
            $view->assign('count_new_messages', $count_new_messages);
            $view->assign('title', $meta_data['header']);
            $view->assign('href_user_group_info', SEFLink('index.php?option=com_jshopping&controller=user&task=groupsinfo'));
            $view->assign('href_show_orders', 'index.php?option=com_jshopping&controller=user&task=orders');
            $view->assign('link_Q_n_A', 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_COMPLETE_PROFILE'));
            $view->assign('link_private_photos', 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PRIVATE_PICTURES') . '?user=' . JSFactory::getUser()->user_id);
            $view->assign('link_honesty_reviews', 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_HONESTY_REVIEWS') . '?user=' . JSFactory::getUser()->user_id);
            $view->assign('verified', $modelUser->isVerification(JSFactory::getUser()->user_id));
            $view->display();
        } else {
            $adv_user->photosite = $conf->path_user_image_big . "block.jpg";


            $view_name = "user";
            $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("myaccount");
            $view->assign('user', $adv_user);
            $view->display();
        }
    }

    function groupsinfo(){
        $jshopConfig = JSFactory::getConfig();
        setMetaData(_JSHOP_USER_GROUPS_INFO, "", "");

        $group = JSFactory::getTable('userGroup', 'jshop');
        $list = $group->getList();

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayGroupsInfo', array());

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("groupsinfo");
        $view->assign('rows', $list);
        $dispatcher->trigger('onBeforeDisplayGroupsInfoView', array(&$view));
        $view->display();
    }

    function logout(){
        $mainframe = JFactory::getApplication();
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLogout', array() );

        $error = $mainframe->logout();

        $session = JFactory::getSession();
        $session->set('user_shop_guest', null);
        $session->set('cart', null);

        if (!JError::isError($error)){
            if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
                $return = base64_decode($return);
                if (!JURI::isInternal($return)) {
                    $return = '';
                }
            }

            setNextUpdatePrices();

            $dispatcher->trigger( 'onAfterLogout', array() );

            if ( $return && !( strpos( $return, 'com_user' )) ) {
                $mainframe->redirect( $return );
            }else{
                $mainframe->redirect(JURI::base());
            }
        }
    }

    function profile(){
        checkUserLogin();
        $currentUser = JSFactory::getUserShop();
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();

        $userId = JRequest::getInt('user');
        if($userId == JSFactory::getUser()->user_id){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
            exit;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');
        if($modelUser->existUser($userId) == false){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('user_page');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $searchParams = $modelUsersList->searchParamsCurrentUser($currentUser);

        $modelUser = JSFactory::getModel('user', 'jshop');
        $userData = $modelUser->getDataUser($userId, array('user_id', 'u_name', 'birthday', 'city', 'state', 'photosite', 'block', 'user_reviews', 'looking_for', 'age_look_from', 'age_look_to', 'distance', 'zip', 'longitude', 'latitude', 'sex', 'user_reviews', 'last_visit'), true);

        /*$userData = JSFactory::getTable('userShop', 'jshop');
        $userData->load($userId);*/

        $distance = $modelUsersList->calculateDistance($searchParams['latitude'], $searchParams['longitude'], $userData->latitude, $userData->longitude);
        $userData->age = JSFactory::getAge($userData->birthday);
        if(is_null($userData->height) || $userData->height == ""){
            $userData->height = JText::_('UNKNOWN');
        }
        if(is_null($userData->status) || $userData->status == ""){
            $userData->status = JText::_('UNKNOWN');
        }
        if(is_null($userData->body) || $userData->body == ""){
            $userData->body = JText::_('UNKNOWN');
        }
        if(is_null($userData->user_about) || $userData->user_about == ""){
            $userData->user_about = JText::_('USER_ABOUT_DEFAULT');
        }
        $userData->sex = ($userData->sex == 2) ? JText::_('MALE') : JText::_('FEMALE');
        $userData->distance = $distance;
        $userData->last_visit = JSFactory::getDateDiffFormat($userData->last_visit);
        $userData->currentAge = JSFactory::getAge($userData->birthday);

//        $menu = JSFactory::getContentMenu();

        $conf = new JConfig();

        if($userData->block == 0){
            $modelBookmark = JSFactory::getModel('bookmarks', 'jshop');
            $my_bookmark_list = $modelBookmark->getAllMyBookmarks(JSFactory::getUser()->user_id);

            $userData->add_to_bookmarks = true;
            foreach($my_bookmark_list as $key => $value){
                if($value->reciper == $userData->user_id){
                    $userData->add_to_bookmarks = false;
                }
            }

            $modelFriends = JSFactory::getModel('friends', 'jshop');

            if($modelFriends->getIsFrieds($userData->user_id)){
                $user_is_friends = true;
                header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_FULL_USER_PAGE') . '?user=' . $userData->user_id);
                exit;
            } else {
                $user_is_friends = false;
            }

            if($modelFriends->getIsIFiledClaim($userData->user_id)){
                $is_i_filed_claim = true;
            } else {
                $is_i_filed_claim = false;
            }

            if($modelFriends->getIsAccept($userData->user_id)){
                $user_is_accept = true;
            } else {
                $user_is_accept = false;
            }

            if(!$user_is_friends){
                $referrer = $modelFriends->getReferrer($userData->user_id);
                if($referrer){
                    $email_referrer = $modelFriends->getReferrerEmail($referrer);
                }
            }

            $count_my_tokens = $modelUser->getCountUserTokens(JSFactory::getUser()->user_id);
            $count_tokens_add_to_friends = $conf->count_tokens_add_to_friends;
            if($count_my_tokens < $count_tokens_add_to_friends){
                $isset_tokens_add_to_friends = false;
            } else {
                $isset_tokens_add_to_friends = true;
            }

            $modelUsersList = JSFactory::getModel('usersList', 'jshop');
            $searchParams = $modelUsersList->searchParamsCurrentUser($currentUser);
            $users_list_id = $modelUsersList->usersList($searchParams, -1, -1, array('user_id'), array(), true);

            $active_user = 0;
            foreach($users_list_id as $key=>$value){
                if($value->user_id == $userData->user_id){
                    $active_user = $key;
                    break;
                }
            }

            if(isset($users_list_id[$active_user+1])){
                $next_users = 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $users_list_id[$active_user+1]->user_id;
            } else {
                if(isset($users_list_id[$active_user-1])){
                    $next_users = 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $users_list_id[$active_user-1]->user_id;
                } else {
                    $next_users = 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST');
                }
            }

            $userData->photosite = JSFactory::existImage($conf->path_user_image_big, $userData->photosite);

            $link_full_profile = JText::_('LINK_FULL_USER_PAGE') . '?user=' . $userData->user_id;
            $link_send_message = JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $userData->user_id;
            $link_earn_tokens= JText::_('LINK_EARN_TOKENS');
            $link_all_users= JText::_('LINK_USERS_LIST');

            $view_name = "user";
            $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("userpage");
            $view->assign('config',$jshopConfig);
            if(isset($email_referrer)){
                $view->assign('email_referrer',$email_referrer);
            }
            $view->assign('userData',$userData);
            $view->assign('user_is_accept',$user_is_accept);
            $view->assign('user_is_friends',$user_is_friends);
            $view->assign('is_i_filed_claim',$is_i_filed_claim);
            $view->assign('isset_tokens_add_to_friends',$isset_tokens_add_to_friends);
            $view->assign('next_users',$next_users);
            $view->assign('link_all_users',$link_all_users);
            $view->assign('my_user_id', JSFactory::getUser()->user_id);
            $view->assign('link_full_profile', $link_full_profile);
            $view->assign('link_send_message', $link_send_message);
            $view->assign('link_earn_tokens', $link_earn_tokens);
//            $view->assign('menu', $menu);
            $view->display();
        } else {
            $userData->photosite = $conf->path_user_image_big . "block.jpg";

            $view_name = "user";
            $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("userpage");
            $view->assign('config',$jshopConfig);
            $view->assign('userData',$userData);
//            $view->assign('menu',$menu);
            $view->display();
        }


    }

    function full_profile()
    {
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();
        $conf = new JConfig();

        $user_id = JRequest::getInt('user');
        $modelUser = JSFactory::getModel('user', 'jshop');

        if (!$modelUser->existUser($user_id)) {
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $data_user = $modelUser->getDataUser($user_id, array('user_id', 'u_name', 'birthday', 'city', 'state', 'photosite', 'block', 'user_reviews', 'longitude', 'latitude', 'sex', 'last_visit'), true);
        if ($data_user->block != 0) {
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }

        $visible = (strtotime(JSFactory::getUser()->invisible_to) > strtotime(date("Y-m-d H:i:s"))) ? false : true;

        if($visible){
            $modelVisitors = JSFactory::getModel('visitors', 'jshop');
            $modelVisitors->addVisitor($user_id);

            if (JSFactory::isUserOffline($user_id)){
                $Config = JSFactory::getConfig();
                $modelNotes = JSFactory::getModel('notifications', 'jshop');
                $modelNotes->addNote(JSFactory::getUser()->user_id, $user_id, JSFactory::getUser()->user_id, JSFactory::getUser()->u_name, $Config->notifications[7]);
            }
        }

        $modelFriends = JSFactory::getModel('friends', 'jshop');
        $isFriends = $modelFriends->getIsFrieds($user_id) && !$modelFriends->getIsAccept($user_id) && ($user_id != JSFactory::getUser()->user_id);
        if($modelFriends->getIsIFiledClaim($user_id)){
            $is_i_filed_claim = true;
        } else {
            $is_i_filed_claim = false;
        }
        if($modelFriends->getIsAccept($user_id)){
            $user_is_accept = true;
        } else {
            $user_is_accept = false;
        }
        if(!$isFriends){
            $referrer = $modelFriends->getReferrer($user_id);
            if($referrer){
                $email_referrer = $modelFriends->getReferrerEmail($referrer);
            }
        }

        $modelBookmark = JSFactory::getModel('bookmarks', 'jshop');
        $my_bookmark_list = $modelBookmark->getAllMyBookmarks(JSFactory::getUser()->user_id);

        $add_to_bookmarks = true;
        foreach($my_bookmark_list as $key => $value){
            if($value->reciper == $user_id){
                $add_to_bookmarks = false;
            }
        }

        $count_my_tokens = $modelUser->getCountUserTokens(JSFactory::getUser()->user_id);
        $count_tokens_add_to_friends = $conf->count_tokens_add_to_friends;
        if($count_my_tokens < $count_tokens_add_to_friends){
            $isset_tokens_add_to_friends = false;
        } else {
            $isset_tokens_add_to_friends = true;
        }
        /*if (!$isFriends) {
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }*/

        $meta_data = JSFactory::getMetaData('user_full_profile');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $currentUser = JSFactory::getUserShop();
        $searchParams = $modelUsersList->searchParamsCurrentUser($currentUser);
        $distance = $modelUsersList->calculateDistance($searchParams['latitude'], $searchParams['longitude'], $data_user->latitude, $data_user->longitude);

        $data_user->distance = $distance;
        $data_user->user_reviews = ($data_user->user_reviews > 5) ? 5 : $data_user->user_reviews;
        $data_user->last_visit = JSFactory::getDateDiffFormat($data_user->last_visit);
        $data_user->age = JSFactory::getAge($data_user->birthday);
        $data_user->sex = ($data_user->sex == 2) ? JText::_('MALE') : JText::_('FEMALE');
        $data_user->photosite = JSFactory::existImage($conf->path_user_image_big, $data_user->photosite);
        $count_reviews = $data_user->user_reviews;

        $links = array(
            'questions'     => ($user_id != JSFactory::getUser()->user_id) ? JText::_('LINK_USER_QUESTIONS') . '?user=' . $user_id : JText::_('LINK_COMPLETE_PROFILE'),
            'private'       => ($user_id != JSFactory::getUser()->user_id) ? JText::_('LINK_USER_SHOW_PRIVATE_PICTURES') . '?user=' . $user_id : JText::_('LINK_USER_PRIVATE_PICTURES') . '?user=' . $user_id,
            'honesty_reviews' => ($user_id != JSFactory::getUser()->user_id) ? JText::_('LINK_USER_HONESTY_REVIEWS') . '?user=' . $user_id : JText::_('LINK_USER_HONESTY_REVIEWS') . '?user=' . JSFactory::getUser()->user_id,
            'send_message'     => ($user_id != JSFactory::getUser()->user_id) ? JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $user_id : JText::_('LINK_MESSAGING_SENT'),
            'lincup'     => ($user_id != JSFactory::getUser()->user_id) ? JText::_('LINK_SPONSORS') . "?user=" . $user_id : JText::_('LINK_SPONSORS')
        );

        $data_user->images_album = array(
            'path_to_album'  => $conf->path_albums_image . "user_" . $user_id . "/",
            'path_to_thumb'  => $conf->path_albums_image . "user_" . $user_id . "/thumb/",
            'images'        => $modelUser->getUserAlbumProfile($user_id)
        );
        if(is_null($data_user->height) || $data_user->height == ""){
            $data_user->height = JText::_('UNKNOWN');
        }
        if(is_null($data_user->status) || $data_user->status == ""){
            $data_user->status = JText::_('UNKNOWN');
        }
        if(is_null($data_user->ethnicity) || $data_user->ethnicity == ""){
            $data_user->ethnicity = JText::_('UNKNOWN');
        }
        if(is_null($data_user->body) || $data_user->body == ""){
            $data_user->body = JText::_('UNKNOWN');
        }
        if(is_null($data_user->profession) || $data_user->profession == ""){
            $data_user->profession = JText::_('UNKNOWN');
        }
        if(is_null($data_user->religion) || $data_user->religion == ""){
            $data_user->religion = JText::_('UNKNOWN');
        }
        if(is_null($data_user->kids) || $data_user->kids == ""){
            $data_user->kids = JText::_('UNKNOWN');
        }
        if(is_null($data_user->user_about) || $data_user->user_about == ""){
            $data_user->user_about = JText::_('YOUR_COOL_DEFAULT');
        }
        if(is_null($data_user->look_qualites) || $data_user->look_qualites == ""){
            $data_user->look_qualites = JText::_('YOUR_QUALITIES_DEFAULT');
        }
        if(is_null($data_user->recommend) || $data_user->recommend == ""){
            $data_user->recommend = JText::_('YOUR_RECOMMEND_DEFAULT');
        }
        if(is_null($data_user->few_places) || $data_user->few_places == ""){
            $data_user->few_places = JText::_('YOUR_FEW_PLACES_DEFAULT');
        }

        $exist_private = $modelUser->getUserAlbumPrivate($user_id);
        if(count($exist_private)>0){
            $exist_private = true;
        } else {
            $exist_private = false;
        }

        $email_verification =$modelUser->isVerification($user_id);

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("full_user_page");
        $view->assign('data',$data_user);
        $view->assign('count_reviews',$count_reviews);
        if(isset($email_referrer)){
            $view->assign('email_referrer',$email_referrer);
        }
        $view->assign('isFriends',$isFriends);
        $view->assign('visible',$visible);
        $view->assign('add_to_bookmarks',$add_to_bookmarks);
        $view->assign('links',$links);
        $view->assign('user_is_accept',$user_is_accept);
        $view->assign('is_i_filed_claim',$is_i_filed_claim);
        $view->assign('isset_tokens_add_to_friends',$isset_tokens_add_to_friends);
//        $view->assign('menu',$menu);
        $view->assign('exist_private',$exist_private);
        $view->assign('email_verification',$email_verification);
        $view->display();

    }

    function show_private_pictures(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $adv_user = JSFactory::getUser()->user_id;

        $user_id = JRequest::getInt('user');
        if($user_id == $adv_user){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
            exit;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');
        if(!$modelUser->existUser($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelFriends = JSFactory::getModel('friends', 'jshop');
        if( !$modelFriends->getIsFrieds($user_id) && !$modelFriends->getIsAccept($user_id) ){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }

        $meta_data = JSFactory::getMetaData('show_private_pictures');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $count_tokens = $modelUser->getCountUserTokens($adv_user);

        if($count_tokens >= $conf->count_tokens_view_private_photos){
            $permission = true;
        } else{
            $permission = false;
        }
        $user_email = $modelUser->getDataUser($user_id, array('u_name', 'email'));

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("show_private_pictures");
//        $view->assign('menu',$menu);
        $view->assign('adv_id', $user_id);
        $view->assign('adv_email', $user_email->email);
        $view->assign('adv_name', $user_email->u_name);
        $view->assign('count_tokens', $conf->count_tokens_view_private_photos);
        $view->assign('permission', $permission);
        $view->assign('link_earn_tokens', JText::_('LINK_EARN_TOKENS'));
        $view->assign('adv_user', $adv_user);
        $view->assign('link', 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PRIVATE_PICTURES') . '?user=' . $user_id);
        $view->display();
    }

    function private_pictures(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $user_id = JRequest::getInt('user');
        $modelUser = JSFactory::getModel('user', 'jshop');

        if(!$modelUser->existUser($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $modelFriends = JSFactory::getModel('friends', 'jshop');
        if( !$modelFriends->getIsFrieds($user_id) && !$modelFriends->getIsAccept($user_id) && ($user_id != JSFactory::getUser()->user_id) ){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }

        $pos = strpos($_SERVER['HTTP_REFERER'], JText::_('LINK_USER_SHOW_PRIVATE_PICTURES'));
        if( ($pos == false) && ($user_id != JSFactory::getUser()->user_id) ){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_SHOW_PRIVATE_PICTURES') . '?user=' . $user_id);
            exit;
        }

        $meta_data = JSFactory::getMetaData('user_private_pictures');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $photos = $modelUser->getUserAlbumPrivate($user_id);
        $path_to_album  = $conf->path_albums_image . "user_" . $user_id . "/";
        $path_to_thumb  = $conf->path_albums_image . "user_" . $user_id . "/thumb/";

        $user_email = $modelUser->getDataUser($user_id, array('u_name', 'email'));
        $my_name = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('u_name'));

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("private_pictures");
        $view->assign('photos', $photos);
        $view->assign('adv_id', $user_id);
        $view->assign('adv_email', $user_email->email);
        $view->assign('adv_name', $user_email->u_name);
        $view->assign('my_name', $my_name->u_name);
        $view->assign('my_id', JSFactory::getUser()->user_id);
        $view->assign('title', $meta_data['header']);
        $view->assign('path_to_album', $path_to_album);
        $view->assign('path_to_thumb', $path_to_thumb);
        $view->display();
    }

    function honesty_reviews(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $user_id = JRequest::getInt('user');

        $modelUser = JSFactory::getModel('user', 'jshop');
        if(!$modelUser->existUser($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }
        $member_info = $modelUser->getDataUser($user_id, array('u_name', 'user_reviews'));
        $member_info->user_reviews = ($member_info->user_reviews > 5) ? 5 : $member_info->user_reviews;

        $meta_data = JSFactory::getMetaData('honesty_reviews');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("honesty_reviews");
        $view->assign('member_name', $member_info->u_name);
        $view->assign('member_reviews', $member_info->user_reviews);
        $view->assign('max_reviews', $conf->maximum_reviews);
        $view->display();
    }

    function accept(){
        checkUserLogin();
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();
        $params = $mainframe->getParams();

        $user_id = JRequest::getInt('user');

        if( !isset($user_id) || is_null($user_id) || $user_id == 0 ){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
        }

        $modelFriends = JSFactory::getModel('friends', 'jshop');
        if(!$modelFriends->getIsFrieds($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }

        $modelUser = JSFactory::getModel('user', 'jshop');

        if($modelUser->isBlockUser($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_ERROR'));
            exit;
        }

        $meta_data = JSFactory::getMetaData('accept_user');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

        $my_photo = $modelUser->getDataUser(JSFactory::getUser()->user_id, array('photosite'));
        $my_photo = JSFactory::existImage($conf->path_user_image_medium, $my_photo->photosite);
        $my_link = '/';

        $user = $modelUser->getDataUser($user_id, array('u_name', 'photosite'));
        $user_photo = JSFactory::existImage($conf->path_user_image_medium, $user->photosite);
        $user_link = JText::_('LINK_USER_PAGE') . '?user=' . $user_id;

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("accept_user");
        $view->assign('my_photo',$my_photo);
        $view->assign('my_link',$my_link);
        $view->assign('user_name',$user->u_name . '`s');
        $view->assign('user_photo',$user_photo);
        $view->assign('user_link',$user_link);
        $view->assign('message_link', JText::_('LINK_MESSAGING_VIEW') . '?friend=' . $user_id);
        $view->assign('profile_link', JText::_('LINK_FULL_USER_PAGE') . '?user=' . $user_id);
        $view->assign('lincup_link', JText::_('LINK_SPONSORS') . "?user=" . $user_id);
//        $view->assign('menu', $menu);
        $view->display();
    }

    function add_to_friends(){
        checkUserLogin();
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $params = $mainframe->getParams();

        $meta_data = JSFactory::getMetaData('add_to_friends');
        setMetaData($meta_data['title'], $meta_data['keywords'], $meta_data['description'], $params);

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("add_to_friends");
//        $view->assign('menu', $menu);
        $view->display();
    }

    function uploadPhotoWhitFB($photoFB){
        $jshopConfig = JSFactory::getConfig();
        $photo_path_fb = $jshopConfig->img_facebook;
        $photo_path_fb_live = $jshopConfig->img_facebook_path;

        $parseImgUrl = pathinfo($photoFB);
        $name = explode('/', $parseImgUrl['dirname']);
        $name_img = 'user_'.$name[3].'.jpg';

        $url = $photoFB;
        $data = file_get_contents($url);
        $uploadPhoto = $photo_path_fb.$name_img;
        $uploadPhotoPath = 'components/com_jshopping/files/img_facebook/'.$name_img;

        $file = fopen($uploadPhoto, 'w+');
        fputs($file, $data);
        fclose($file);

        return $uploadPhotoPath;
    }

    function bookmarks(){
        checkUserLogin();
        $jshopConfig = JSFactory::getConfig();
        $currentUser = JSFactory::getUserShop();
        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $userIdAddToBookmarks = JRequest::getInt('user_id');
        $addUserToBookmark = $modelUsersList->addUserToBookmark($currentUser->user_id, $userIdAddToBookmarks);

        if($addUserToBookmark == 0):
            $message = JFactory::getApplication()->enqueueMessage('This user already been in bookmarks', 'success');
        else:
            $message = JFactory::getApplication()->enqueueMessage('User was add in bookmarks', 'success');
        endif;

        $userBookmarks = $modelUsersList->getAllMyBookmarks($currentUser->user_id);

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("user_bookmarks");
        $view->assign('config',$jshopConfig);
        $view->assign('message',$message);
        $view->display();
    }

    function questions(){
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        $jshopConfig = JSFactory::getConfig();
        $conf = new JConfig();

        $user_id = JRequest::getInt('user');
        $modelUser = JSFactory::getModel('user', 'jshop');
        if(!$modelUser->existUser($user_id)){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }

/*        $modelFriends = JSFactory::getModel('friends', 'jshop');
        if( !$modelFriends->getIsFrieds($user_id) && !$modelFriends->getIsAccept($user_id) ){
            header('Location: ' . 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_PAGE') . '?user=' . $user_id);
            exit;
        }*/

        $meta_data = JSFactory::getMetaData('user_profile_questions');
        setMetaData($meta_data['title'], $meta_data['keyword'], $meta_data['description'], $params);

        $user_data = $modelUser->getDataUser($user_id, array('u_name', 'photosite', 'block'));

        if($user_data->block != 0){
            $user_data->photosite = $conf->path_user_image_medium . "block.jpg";
        } else {
            $user_data->photosite = JSFactory::existImage($conf->path_user_image_medium, $user_data->photosite);
        }

        $modelMyprofile = JSFactory::getModel('myprofile', 'jshop');

        $list_profile_answers = $modelMyprofile->getProfileAnswers($user_id);
        $list = $modelMyprofile->getProfileQuestions();
        $list_questions = array();

        foreach($list as $temp){
            $list_questions[$temp->question_id]['question'] = $temp->question;
            $list_questions[$temp->question_id]['question'] = $temp->question;
            $list_questions[$temp->question_id]['answers'][$temp->answer_id]['value'] = $temp->answer;
            if(in_array(array($temp->question_id, $temp->answer_id), $list_profile_answers)){
                $list_questions[$temp->question_id]['answers'][$temp->answer_id]['checked'] = 1;
            } else {
                $list_questions[$temp->question_id]['answers'][$temp->answer_id]['checked'] = 0;
            }
        }

//        $menu = JSFactory::getContentMenu();

        $view_name = "user";
        $view_config = array("template_path"=>$jshopConfig->template_path.$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("user_questions");
        $view->assign('user_photo', $user_data->photosite);
        $view->assign('user_id', $user_id);
        $view->assign('user_name', $user_data->u_name);
        $view->assign('questions', $list_questions);
//        $view->assign('menu', $menu);
        $view->display();
    }

/*
    function removeBookmark(){
        $currentUser = JSFactory::getUserShop();
        $modelUsersList = JSFactory::getModel('usersList', 'jshop');
        $userIdRemoveToBookmarks = JRequest::getInt('user_id');
        $userTable = JSFactory::getTable('userShop', 'jshop');

        $modelUsersList->removeBookmark($currentUser->user_id, $userIdRemoveToBookmarks);

        $userBookmarks = $modelUsersList->getAllMyBookmarks($currentUser->user_id);
        $userBookmarksList = array();

        foreach($userBookmarks as $key=>$userBook):
            $userTable->load($userBook->recieper);

            $userBookmarksList[$key]['user_id']  = $userTable->user_id;
            $userBookmarksList[$key]['name']     = $userTable->u_name;
            $userBookmarksList[$key]['sex']      = $userTable->title;
            $userBookmarksList[$key]['photo']    = $userTable->photosite;

            $userBookmarksList[$key]['currentAge'] = $modelUsersList->currentAge($userTable->birthday);

            $objLastVisit = new DateTime($userTable->last_visit);
            $userBookmarksList[$key]['last_visit'] = $modelUsersList->lastVisit($objLastVisit);

        endforeach;

        $html = '';

        foreach($userBookmarksList as $userBookmark):

            $html .= '<div class="userBlockBookmark">';
            $html .= '<div class="removeUser bookmark_'.$userBookmark['user_id'].'" onclick="removeBookmark('.$userBookmark['user_id'].')">';
            $html .= '<span>x</span>';
            $html .= '</div>';

            $html .= '<div class="userImgBookmark">';
            $html .= '<img src="'.$userBookmark['photo'].'" />';
            $html .= '</div>';

            $html .= '<div class="userInfoBookmark">';
            $html .= '<div class="userNameAgeBookmark">';
            $html .= ' '.$userBookmark['name'].',  ';
            $html .= '<span>'.$userBookmark['currentAge'].'</span>';
            $html .= '</div>';

            $html .= '<div class="lastVisitBookmark">';
            $html .= '<span>'.Jtext::_('Last visit: ').'</span>';
            $html .= ' '.$userBookmark['last_visit'].' ';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';

        endforeach;

        $html .= '<div style="clear:both"></div>';

        echo $html;

        die();

    }
    */
}