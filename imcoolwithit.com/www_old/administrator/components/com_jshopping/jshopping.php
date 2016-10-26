<?php
	defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_jshopping')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JTable::addIncludePath(JPATH_COMPONENT_SITE.'/tables');
require_once(JPATH_COMPONENT_SITE."/lib/factory.php");
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/functions.php');

$ajax = JRequest::getInt('ajax');
$adminlang = JFactory::getLanguage();
if (!JRequest::getVar("js_nolang")){
    JSFactory::loadAdminLanguageFile();
}
$db = JFactory::getDBO();
$jshopConfig = JSFactory::getConfig();
$jshopConfig->setLang($jshopConfig->getFrontLang());

if ($jshopConfig->adminLanguage!=$adminlang->getTag()){
	$config = new jshopConfig($db);
	$config->id = $jshopConfig->load_id;
	$config->adminLanguage = $adminlang->getTag();
	if (!$config->store()) {
		JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
		return 0;
	}
}
if (!$ajax){
    installNewLanguages();
}else{
    header('Content-Type: text/html;charset=UTF-8');
}

JPluginHelper::importPlugin('jshopping');
JPluginHelper::importPlugin('jshoppingadmin');
JPluginHelper::importPlugin('jshoppingmenu');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onAfterLoadShopParamsAdmin', array());

JHtml::_('behavior.framework');
JHtml::_('bootstrap.framework');
$document = JFactory::getDocument();
$document->addScript($jshopConfig->live_path.'js/functions.js');
$document->addScript($jshopConfig->live_admin_path.'js/functions.js');
$document->addStyleSheet($jshopConfig->live_admin_path.'css/style.css');

$controller = JRequest::getCmd('controller');
if (!$controller) $controller = "panel";
$dispatcher->trigger('onAfterGetControllerAdmin', array(&$controller));

if (file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
    require_once( JPATH_COMPONENT.'/controllers/'.$controller.'.php' );
else
    JError::raiseError( 403, JText::_('Access Forbidden') );

$classname = 'JshoppingController'.$controller;
$controller = new $classname();
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>