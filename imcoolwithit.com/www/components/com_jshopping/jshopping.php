<?php
/**
* @version      4.7.0 27.09.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');


    /*$instance = JUser::getInstance(408);
    $session = JFactory::getSession();
    $session->set('user', $instance);*/

JSFactory::getLinkPermission();

JTable::addIncludePath(JPATH_COMPONENT.'/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_COMPONENT.'/models');
require_once(JPATH_COMPONENT_SITE."/lib/factory.php");
JSFactory::getComponentHeader();

print '<div class="container-full"><div class="container">';

$controller = getJsFrontRequestController();
require("loadparams.php");


$a = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
$aa = file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php');

if (file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
    require_once(JPATH_COMPONENT.'/controllers/'.$controller.'.php');
else
    JError::raiseError(403, JText::_('Access Forbidden'));

$classname = 'JshoppingController' . $controller;
$controller = new $classname();



$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

displayTextJSC();
dataTimeLastVisitUser();

print '</div></div>';
?>