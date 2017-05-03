<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/login_helper.php';

$params->def('greeting', 1);

if(stristr($_SERVER['REQUEST_URI'], '/login')){
    $layout = 'login';
} else if(stristr($_SERVER['REQUEST_URI'], '/join')){
    $layout = 'join_now';
} else {
    $document = JFactory::getDocument();
    $pathToJS = JURI::root().'components/com_jshopping/js/';
    $document->addScript($pathToJS.'signup_email.js');
    if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false){
        $document->addScript($pathToJS.'signup_zipcode.js');
    }
    $layout = 'sign_up';
}

require JModuleHelper::getLayoutPath('mod_mylogin', $layout);
