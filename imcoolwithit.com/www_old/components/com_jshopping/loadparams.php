<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$session =JFactory::getSession();
$ajax = JRequest::getInt('ajax');


if (!JRequest::getInt('no_lang')){
    JSFactory::loadLanguageFile();
}
$jshopConfig = JSFactory::getConfig();
setPrevSelLang($jshopConfig->getLang());

//reload price for new currency
if (JRequest::getInt('id_currency')){
    header("Cache-Control: no-cache, must-revalidate");
    updateAllprices();
    $back = JRequest::getVar('back');
    $mainframe =JFactory::getApplication();
    if ($back!='') $mainframe->redirect($back);
}

$user = JFactory::getUser();

$js_update_all_price = $session->get('js_update_all_price');
$js_prev_user_id = $session->get('js_prev_user_id');
if ($js_update_all_price || ($js_prev_user_id!=$user->id)){
    updateAllprices();
    $session->set('js_update_all_price', 0);
}
$session->set("js_prev_user_id", $user->id);

if (!$ajax){
    installNewLanguages();
    $document = JFactory::getDocument();
    $viewType = $document->getType();
    if ($viewType=="html"){
        JSFactory::loadCssFiles();
        JSFactory::loadJsFiles();
    }
}else{
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onAfterLoadShopParams', array());

?>