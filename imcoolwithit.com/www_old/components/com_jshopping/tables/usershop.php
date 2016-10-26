<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopUserShop extends JTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_users', 'user_id', $_db);
        JPluginHelper::importPlugin('jshoppingcheckout');
    }
    
	function isUserInShop($id) {
		$query = "SELECT user_id FROM `#__jshopping_users` WHERE `user_id`='".$this->_db->escape($id)."'";
		$this->_db->setQuery($query);
		$res = $this->_db->query();
		return $this->_db->getNumRows($res);
	}
    	
	function addUserToTableShop($user) {
		$this->u_name = $user->username;
		$this->email = $user->email;
		$this->user_id = $user->id;
        $number = $this->getNewUserNumber();

        $usergroup = JSFactory::getTable('usergroup', 'jshop');
        $default_usergroup = $usergroup->getDefaultUsergroup();

        // Insert facebook data
        $getParams = $user->getParam('params');
        $userFacebookData = unserialize($getParams);

		$query = "INSERT INTO `#__jshopping_users` SET
                    `usergroup_id`='".$default_usergroup."',
                    `title`='".$user->sex."',
                    `u_name`='".$this->_db->escape($user->username)."',
                    `f_name`='".$this->_db->escape($userFacebookData['first_name'])."',
                    `l_name`='".$this->_db->escape($userFacebookData['last_name'])."',
                    `sex`='".$this->_db->escape($user->sex)."',
                    `birthday`='".$user->birthday."',
                    `block`='".$user->block."',
                    `photo`='".$this->_db->escape($userFacebookData['photo'])."',
                    `profile`='".$this->_db->escape($userFacebookData['link'])."',
                    `fb_id`='".$this->_db->escape($userFacebookData['id'])."',
                    `email`='".$this->_db->escape($user->email)."',
                    `user_id`='".$this->_db->escape($user->id)."',
                    `about`='".$this->_db->escape($user->about)."',
                    `number`='".$this->_db->escape($number)."'";

		$this->_db->setQuery($query);
		$this->_db->query();
        JDispatcher::getInstance()->trigger('onAfterAddUserToTableShop', array(&$this));
	}

    function giveTokens($user) {
        $db = JFactory::getDBO();
        $query = "insert into `#__user_tokens` set `user_id`=" . $user->id;
        $db->setQuery($query);
        $db->query();
    }
    
    public function store($updateNulls = false){
        $tmp = $this->percent_discount;
        unset($this->percent_discount);
        JDispatcher::getInstance()->trigger('onBeforeStoreTableShop', array(&$this));
        $res = parent::store($updateNulls);
        $this->percent_discount = $tmp;
        return $res;
    }

	function check($type = null){
		jimport('joomla.mail.helper');
        $types = explode(".",$type);
        $type = $types[0];
        if (isset($types[1])){
            $type2 = $types[1];
        }else{
            $type2 = '';
        }

        $jshopConfig = JSFactory::getConfig();
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields[$type];
        JDispatcher::getInstance()->trigger('onBeforeCheckJshopUserShop', array(&$this, &$type, &$config_fields, &$type2));

        if ($config_fields['title']['require']){
            if (!intval($this->title)) {
                $this->_error = addslashes(_JSHOP_REGWARN_TITLE);
                return false;
            }
        }
                		
        if ($config_fields['f_name']['require']){
		    if(trim($this->f_name) == '') {
			    $this->_error = addslashes(_JSHOP_REGWARN_NAME);
			    return false;
		    }
        }

        if ($config_fields['l_name']['require']){
		    if(trim($this->l_name) == '') {
			    $this->_error = addslashes(_JSHOP_REGWARN_LNAME);
			    return false;
		    }
        }
		
		if ($config_fields['m_name']['require']){
            if(trim($this->m_name) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_MNAME);
                return false;
            }
        }
        
        if ($config_fields['firma_name']['require']){
            if(trim($this->firma_name) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_FIRMA_NAME);
                return false;
            }
        }
        
        if ($config_fields['client_type']['require']){
            if(trim($this->client_type) == 0) {
                $this->_error = addslashes(_JSHOP_REGWARN_CLIENT_TYPE);
                return false;
            }
        }

        if ($this->client_type==2 || !$config_fields['client_type']['display']){
            if ($config_fields['firma_code']['require']){
                if(trim($this->firma_code) == '') {
                    $this->_error = addslashes(_JSHOP_REGWARN_FIRMA_CODE);
                    return false;
                }
            }
            
            if ($config_fields['tax_number']['require']){
                if(trim($this->tax_number) == '') {
                    $this->_error = addslashes(_JSHOP_REGWARN_TAX_NUMBER);
                    return false;
                }
            }
        }        

		if ($config_fields['email']['require'] || $this->email!=''){
		    if ((trim($this->email=="")) || !JMailHelper::isEmailAddress($this->email)){
			    $this->_error = addslashes(_JSHOP_REGWARN_MAIL);
			    return false;
		    }
        }

        if ($config_fields['birthday']['require']){
            if(trim($this->birthday) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_BIRTHDAY);
                return false;
            }
        }		

		if ($type == "register"){
            if ($config_fields['u_name']['require']){
				if(trim($this->u_name) == '') {
					$this->_error = addslashes(_JSHOP_REGWARN_UNAME);
					return false;
				}
			}
			if ($this->u_name!=''){
				if (preg_match("#[<>\"'%;()&]#i", $this->u_name) || strlen(utf8_decode($this->u_name )) < 2) {
					$this->_error = sprintf(addslashes(_JSHOP_VALID_AZ09),addslashes(_JSHOP_USERNAME),2);
					return false;
				}
				// check for existing username
				$query = "SELECT id FROM #__users WHERE username = '".$this->_db->escape($this->u_name)."' AND id != ".(int)$this->user_id;
                JDispatcher::getInstance()->trigger('onBeforeCheckUserNameExistJshopUserShop', array(&$this, &$type, &$config_fields, &$type2, &$query));
				$this->_db->setQuery($query);
				$xid = intval($this->_db->loadResult());
				if($xid && $xid != intval($this->user_id)){
					$this->_error = addslashes(_JSHOP_REGWARN_INUSE);
					return false;
				}
			}
            if ($config_fields['password']['require'] && trim($this->password)==''){
                $this->_error = _JSHOP_REGWARN_PASSWORD;
                return false;
            }

            if (($this->password || $this->password2) && $config_fields['password_2']['display'] && $this->password!=$this->password2){
                $this->_error = _JSHOP_REGWARN_PASSWORD_NOT_MATCH;
                return false;
            }
		}
        
        if ($type=='editaccount'){
            if ($config_fields['password']['require']){
                if(trim($this->password) == '') {
                    $this->_error = addslashes(_JSHOP_REGWARN_PASSWORD);
                    return false;
                }
            }
            if (($this->password || $this->password2) && $config_fields['password_2']['display'] && $this->password!=$this->password2){
                $this->_error = _JSHOP_REGWARN_PASSWORD_NOT_MATCH;
                return false;
            }
        }
        
        if ($type2 == "edituser"){
			if ($config_fields['u_name']['require']){
				if(trim($this->u_name) == '') {
					$this->_error = addslashes(_JSHOP_REGWARN_UNAME);
					return false;
				}
            }
			if ($this->u_name!=''){
				if (preg_match("#[<>\"'%;()&]#i", $this->u_name) || strlen(utf8_decode($this->u_name )) < 2){
					$this->_error = sprintf(addslashes(_JSHOP_VALID_AZ09),addslashes(_JSHOP_USERNAME),2);
					return false;
				}
				// check for existing username
				$query = "SELECT id FROM #__users WHERE username = '".$this->_db->escape($this->u_name)."' AND id != ".(int)$this->user_id;
                JDispatcher::getInstance()->trigger('onBeforeCheckUserNameExistJshopUserShop', array(&$this, &$type, &$config_fields, &$type2, &$query));
				$this->_db->setQuery($query);
				$xid = intval($this->_db->loadResult());
				if($xid && $xid != intval($this->user_id)){
					$this->_error = addslashes(_JSHOP_REGWARN_INUSE);
					return false;
				}
            }
            if ($this->password && $this->password!=$this->password2){
                $this->_error = _JSHOP_REGWARN_PASSWORD_NOT_MATCH;
                return false;
            }
        }
        
		if ($this->email!=''){
			// check for existing email
			$query = "SELECT id FROM #__users WHERE email='".$this->_db->escape($this->email)."' AND id != ".(int)$this->user_id;
            JDispatcher::getInstance()->trigger('onBeforeCheckUserEmailExistJshopUserShop', array(&$this, &$type, &$config_fields, &$type2, &$query));
			$this->_db->setQuery($query);
			$xid = intval($this->_db->loadResult());
			if($xid && $xid != intval($this->id)){
				$this->_error = addslashes(_JSHOP_REGWARN_EMAIL_INUSE);
				return false;
			}
		}
		
        if ($config_fields['home']['require']){
            if(trim($this->home) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_HOME);
                return false;
            }
        }
        
        if ($config_fields['apartment']['require']){
            if(trim($this->apartment) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_APARTMENT);
                return false;
            }
        }
        
        if ($config_fields['street']['require']){
		    if(trim($this->street) == '') {
			    $this->_error = addslashes(_JSHOP_REGWARN_STREET);
			    return false;
		    }
        }
        
        if ($config_fields['street_nr']['require']){
            if(trim($this->street_nr) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_STREET);
                return false;
            }
        }
		
        if ($config_fields['zip']['require']){
		    if (trim($this->zip) == ""){
		        $this->_error = addslashes( _JSHOP_REGWARN_ZIP );
		        return false;
		    }
        }
        
        if ($config_fields['city']['require']){
		    if (trim($this->city) == ''){
		        $this->_error = addslashes( _JSHOP_REGWARN_CITY );
		        return false;
		    }
        }		
        
        if ($config_fields['state']['require']){
            if (trim($this->state) == ''){
                $this->_error = addslashes( _JSHOP_REGWARN_STATE ); //region
                return false;
            }
        }
        
        if ($config_fields['country']['require']){
		    if(!intval($this->country)) {
			    $this->_error = addslashes(_JSHOP_REGWARN_COUNTRY);
			    return false;
		    }
        }		
        	
        if ($config_fields['phone']['require']){	
		    if(trim($this->phone) == '') {
		        $this->_error = addslashes(_JSHOP_REGWARN_PHONE);
		        return false;
		    }
        }
        
        if ($config_fields['mobil_phone']['require']){    
            if(trim($this->mobil_phone) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_MOBIL_PHONE);
                return false;
            }
        }
        
        if ($config_fields['fax']['require']){    
            if(trim($this->fax) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_FAX);
                return false;
            }
        }
        
        if ($config_fields['ext_field_1']['require']){
            if(trim($this->ext_field_1) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_1);
                return false;
            }
        }
        
        if ($config_fields['ext_field_2']['require']){
            if(trim($this->ext_field_2) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_2);
                return false;
            }
        }
        
        if ($config_fields['ext_field_3']['require']){
            if(trim($this->ext_field_3) == '') {
                $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_3);
                return false;
            }
        }
        
		if ($type == "address" || $type == "editaccount") {
			if ($this->delivery_adress) {
                            
                if ($config_fields['d_title']['require']){
                    if(!intval($this->d_title)) {
                        $this->_error = addslashes(_JSHOP_REGWARN_TITLE_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_f_name']['require']){
				    if(trim($this->d_f_name) == '') {
					    $this->_error = addslashes(_JSHOP_REGWARN_NAME_DELIVERY);
					    return false;
				    }
                }

                if ($config_fields['d_l_name']['require']){
				    if(trim($this->d_l_name) == '') {
					    $this->_error = addslashes(_JSHOP_REGWARN_LNAME_DELIVERY);
					    return false;
				    }
                }
				
				if ($config_fields['d_m_name']['require']){
                    if(trim($this->d_m_name) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_MNAME_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_firma_name']['require']){
                    if(trim($this->d_firma_name) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_FIRMA_NAME_DELIVERY);
                        return false;
                    }
                }
                
                if (isset($config_fields['d_firma_code']) && $config_fields['d_firma_code']['require']){
                    if(trim($this->d_firma_code) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_FIRMA_CODE_DELIVERY);
                        return false;
                    }
                }
                
                if (isset($config_fields['d_tax_number']) && $config_fields['d_tax_number']['require']){
                    if(trim($this->d_tax_number) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_TAX_NUMBER_DELIVERY);
                        return false;
                    }
                }                

                if ($config_fields['d_email']['require']){
                    if ( (trim($this->d_email) == "") || ! JMailHelper::isEmailAddress($this->d_email)) {
                        $this->_error = addslashes(_JSHOP_REGWARN_MAIL_DELIVERY);
                        return false;
                    }
                }
				
				if ($config_fields['d_birthday']['require']){
                    if(trim($this->d_birthday) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_BIRTHDAY_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_home']['require']){
                    if(trim($this->d_home) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_HOME_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_apartment']['require']){
                    if(trim($this->d_apartment) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_APARTMENT_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_street']['require']){
				    if(trim($this->d_street) == '') {
					    $this->_error = addslashes(_JSHOP_REGWARN_STREET_DELIVERY);
					    return false;
				    }
                }
                
                if ($config_fields['d_street_nr']['require']){
                    if(trim($this->d_street_nr) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_STREET_DELIVERY);
                        return false;
                    }
                }
				
                if ($config_fields['d_zip']['require']){
                    if (trim($this->d_zip) == ""){
				        $this->_error = addslashes( _JSHOP_REGWARN_ZIP_DELIVERY );
				        return false;
				    }
                }
                
                if ($config_fields['d_city']['require']){
                    if (trim($this->d_city) == ''){
                        $this->_error = addslashes( _JSHOP_REGWARN_CITY_DELIVERY );
                        return false;
                    }
                }

                if ($config_fields['d_state']['require']){
				    if (trim($this->d_state) == ''){
				        $this->_error = addslashes( _JSHOP_REGWARN_STATE_DELIVERY );
				        return false;
				    }
                }
                
                if ($config_fields['d_country']['require']){
				    if(!intval($this->d_country)) {
                        $this->_error = addslashes(_JSHOP_REGWARN_COUNTRY_DELIVERY);
                        return false;
                    }
                }                                
				
                if ($config_fields['d_phone']['require']){
				    if(trim($this->d_phone) == '') {
				        $this->_error = addslashes(_JSHOP_REGWARN_PHONE_DELIVERY);
				        return false;
				    }
                }
                
                if ($config_fields['d_mobil_phone']['require']){    
                    if(trim($this->d_mobil_phone) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_MOBIL_PHONE_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_fax']['require']){    
                    if (trim($this->d_fax) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_FAX_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_ext_field_1']['require']){
                    if(trim($this->d_ext_field_1) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_1_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_ext_field_2']['require']){
                    if(trim($this->d_ext_field_2) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_2_DELIVERY);
                        return false;
                    }
                }
                
                if ($config_fields['d_ext_field_3']['require']){
                    if(trim($this->d_ext_field_3) == '') {
                        $this->_error = addslashes(_JSHOP_REGWARN_EXT_FIELD_3_DELIVERY);
                        return false;
                    }
                }
                                
			}
		}        

		return true;
	}
    
	function getCountryId($id_user) {
		$db = JFactory::getDBO(); 
		$query = "SELECT country FROM `#__jshopping_users` WHERE user_id = '" . $db->escape($id_user) . "'";
		$db->setQuery($query);
		return $db->loadResult();

	}
	
	function getDiscount(){
		$db = JFactory::getDBO(); 
		$query = "SELECT usergroup.usergroup_discount FROM `#__jshopping_usergroups` AS usergroup
				  INNER JOIN `#__jshopping_users` AS users ON users.usergroup_id = usergroup.usergroup_id
				  WHERE users.user_id = '".$db->escape($this->user_id)."' ";
		$db->setQuery($query);
		return floatval($db->loadResult());
	}
    
    function saveTypePayment($id){
        $this->payment_id = $id;
        $this->store();
        return 1;
    }
    
    function saveTypeShipping($id){
        $this->shipping_id = $id;
        $this->store();
        return 1;
    }
    
    function getError($i = null, $toString = true){
        return $this->_error;
    }
    
    function setError($error){
        $this->_error = $error;
    }
    
    function activate($token)
    {
        $config = JFactory::getConfig();
        $userParams = JComponentHelper::getParams('com_users');
        $db = JFactory::getDBO();

        // Get the user id based on the token.
        $db->setQuery(
            'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
            ' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
            ' AND '.$db->quoteName('block').' = 1' .
            ' AND '.$db->quoteName('lastvisitDate').' = '.$db->Quote($db->getNullDate())
        );
        $userId = (int) $db->loadResult();
        
        // Check for a valid user id.
        if (!$userId) {
            $this->setError(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));
            return false;
        }

        // Load the users plugin group.
        JPluginHelper::importPlugin('user');

        // Activate the user.
        $user = JFactory::getUser($userId);

        // Admin activation is on and user is verifying their email
        if (($userParams->get('useractivation') == 2) && !$user->getParam('activate', 0))
        {
            $uri = JURI::getInstance();

            // Compile the admin notification mail values.
            $data = $user->getProperties();
            $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
            $user->set('activation', $data['activation']);
            $data['siteurl']    = JUri::base();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);
            $data['fromname'] = $config->get('fromname');
            $data['mailfrom'] = $config->get('mailfrom');
            $data['sitename'] = $config->get('sitename');
            $user->setParam('activate', 1);
            $emailSubject    = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_SUBJECT',
                $data['name'],
                $data['sitename']
            );

            $emailBody = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY',
                $data['sitename'],
                $data['name'],
                $data['email'],
                $data['username'],
                $data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation']
            );

            // get all admin users
			$query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';

            $db->setQuery( $query );
            $rows = $db->loadObjectList();

            // Send mail to all superadministrators id
            foreach( $rows as $row )
            {
                $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);

                // Check for an error.
                if ($return !== true) {
                    $this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
                    return false;
                }
            }
        }

        //Admin activation is on and admin is activating the account
        elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0))
        {
            $user->set('activation', '');
            $user->set('block', '0');

            $uri = JURI::getInstance();

            // Compile the user activated notification mail values.
            $data = $user->getProperties();
            $user->setParam('activate', 0);
            $data['fromname'] = $config->get('fromname');
            $data['mailfrom'] = $config->get('mailfrom');
            $data['sitename'] = $config->get('sitename');
            $data['siteurl']    = JUri::base();
            $emailSubject    = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
                $data['name'],
                $data['sitename']
            );

            $emailBody = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
                $data['name'],
                $data['siteurl'],
                $data['username']
            );

            $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

            // Check for an error.
            if ($return !== true) {
                $this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
                return false;
            }
        }
        else
        {
            $user->set('activation', '');
            $user->set('block', '0');
        }

        // Store the user object.
        if (!$user->save()) {
            $this->setError(JText::sprintf('COM_USERS_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()));
            return false;
        }

        return $user;
    }
    
    function getNewUserNumber(){
        $number = $this->user_id;
        JDispatcher::getInstance()->trigger('onBeforeGetNewUserNumber', array(&$this, &$number));
        return $number;
    }

    function insertDataTimeToUser(){
        $user = JFactory::getUser();
        $userId = $user->id;
        $currentDataTime = date("Y-m-d H:i:s");

        $query = "UPDATE `#__jshopping_users` SET `last_visit` = '".$currentDataTime."' WHERE `user_id` = '".$userId."' ";

        $this->_db->setQuery($query);
        $this->_db->query();
    }

}
?>