<?php
/**
* @version      4.11.3 17.11.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');

class JshoppingControllerUpdate extends JControllerLegacy{
    
    function __construct( $config = array() ){
        $mainframe = JFactory::getApplication();
        parent::__construct( $config );
        checkAccessController("update");
        addSubmenu("update");
        $language = JFactory::getLanguage(); 
        $language->load('com_installer');
    }

    function display($cachable = false, $urlparams = false){		                
		$view=$this->getView("update", 'html');  
        $view->assign('etemplatevar1', '');
        $view->assign('etemplatevar2', '');
        $view->sidebar = JHtmlSidebar::render();
		$view->display(); 
    }

	function update() {       
        $installtype = JRequest::getVar('installtype');
        $jshopConfig = JSFactory::getConfig();
        $back = JRequest::getVar('back');

        if (!extension_loaded('zlib')){
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
        
        if ($installtype == 'package'){
            $userfile = JRequest::getVar('install_package', null, 'files', 'array' );
            if (!(bool) ini_get('file_uploads')) {
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if (!is_array($userfile) ) {
                JError::raiseWarning('', JText::_('No file selected'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if ( $userfile['error'] || $userfile['size'] < 1 ){
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = JFactory::getConfig();            
            $tmp_dest = $config->get('tmp_path').'/'.$userfile['name'];            
            $tmp_src = $userfile['tmp_name'];
            jimport('joomla.filesystem.file');
            $uploaded = JFile::upload($tmp_src, $tmp_dest, false, true); 
            $archivename = $tmp_dest;            
            $tmpdir = uniqid('install_');
            $extractdir = JPath::clean(dirname($archivename).'/'.$tmpdir);
            $archivename = JPath::clean($archivename);        
        }else {
            jimport('joomla.installer.helper');
            $url = JRequest::getVar('install_url');
            if (preg_match('/(sm\d+):(.*)/',$url, $matches)){
                $url = $jshopConfig->updates_server[$matches[1]]."/".$matches[2];
            }
            if (!$url) {
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $p_file = JInstallerHelper::downloadPackage($url);
            if (!$p_file) {
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = JFactory::getConfig();
            $tmp_dest = $config->get('tmp_path');
            $tmpdir = uniqid('install_');
            $extractdir = JPath::clean(dirname(JPATH_BASE).'/tmp/'.$tmpdir);
            $archivename = JPath::clean($tmp_dest.'/'.$p_file);              
        }
        saveToLog("install.log", "\nStart install: ".$archivename);
        $result = JArchive::extract($archivename, $extractdir);
        if ( $result === false ) {
            JError::raiseWarning('500', "Archive error");
            saveToLog("install.log", "Archive error");
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
		
		$pathinfo = pathinfo($archivename);
		$this->backup_folder = 'jsbk'.date('ymdHis').'_'.$pathinfo['filename'];
        
        if (file_exists($extractdir."/checkupdate.php")) include($extractdir."/checkupdate.php");                        
        if (file_exists($extractdir."/configupdate.php")) include($extractdir."/configupdate.php");
        
        if (isset($configupdate['version']) && !$this->checkVersionUpdate($configupdate['version'])){
            $this->setRedirect("index.php?option=com_jshopping&controller=update"); 
            return 0;
        }
        
        if (!$this->copyFiles($extractdir)){
            JError::raiseWarning(500, _JSHOP_INSTALL_THROUGH_JOOMLA);
            saveToLog("install.log", 'INSTALL_THROUGH_JOOMLA');
            $this->setRedirect("index.php?option=com_jshopping&controller=update"); 
            return 0;
        }
		
        if (file_exists($extractdir."/update.sql")){
            $db = JFactory::getDBO();
            $lines = file($extractdir."/update.sql");
            $fullline = implode(" ", $lines);
            $queryes = $db->splitSql($fullline);            
            foreach($queryes as $query){
                if (trim($query)!=''){
                    $db->setQuery($query);
                    $db->query();
                    if ($db->getErrorNum()) {
                        JError::raiseWarning(500, $db->stderr());
                        saveToLog("install.log", "Update - ".$db->stderr());
                    }
                }
            }            
        }
        
        if (file_exists($extractdir."/update.php")) include($extractdir."/update.php");
        
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterUpdateShop', array($extractdir) );
                
        @unlink($archivename);
		JFolder::delete($extractdir);
        
        $session = JFactory::getSession();
        $checkedlanguage = array();
        $session->set("jshop_checked_language", $checkedlanguage);        
        
        $msg = _JSHOP_COMPLETED;
        if (isset($configupdate['MASSAGE_COMPLETED'])){
            $msg = $configupdate['MASSAGE_COMPLETED'];
        }
        if ($back==''){
            $this->setRedirect("index.php?option=com_jshopping&controller=update", $msg); 
        }else{
            $this->setRedirect($back, $msg);
        }
    }
    
    function copyFiles($startdir, $subdir = ""){
        
        if ($subdir!="" && !file_exists(JPATH_ROOT.$subdir)){
            @mkdir(JPATH_ROOT.$subdir, 0755);
        }
        
        $files = JFolder::files($startdir.$subdir, '', false, false, array(), array());
        foreach($files as $file){
            if ($subdir=="" && ($file=="update.sql" || $file=="update.php" || $file=="checkupdate.php" || $file=="configupdate.php")){
                continue;
            }
            if ($subdir==""){
                $fileinfo = pathinfo($file);
                if (strtolower($fileinfo['extension'])=='xml'){
                    return 0;
                }
            }
            
			if (JSFactory::getConfig()->auto_backup_addon_files && file_exists(JPATH_ROOT.$subdir."/".$file)){
				JFolder::create(JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir);
				copy(JPATH_ROOT.$subdir."/".$file, JPATH_ROOT.'/tmp/'.$this->backup_folder.$subdir."/".$file);
			}
            if (@copy($startdir.$subdir."/".$file, JPATH_ROOT.$subdir."/".$file)){
                saveToLog("install.log", "Copy file: ".$subdir."/".$file);
            }else{
                JError::raiseWarning("", "Copy file: ".$subdir."/".$file." ERROR");
                saveToLog("install.log", "Copy file: ".$subdir."/".$file." ERROR");
            }
        }
        
        $folders = JFolder::folders($startdir.$subdir, '');
        foreach($folders as $folder){
            $dir = $subdir."/".$folder;            
            $this->copyFiles($startdir, $dir);
        }
        return 1;
    }
    
    function checkVersionUpdate($version){
        $jshopConfig = JSFactory::getConfig();
        
        $currentVersion = $jshopConfig->getVersion();
        $groupVersion = intval($currentVersion);
        
        if (isset($version[$groupVersion])){
            $min = $version[$groupVersion]['min'];
            $max = $version[$groupVersion]['max'];
            $min_cmp = version_compare($currentVersion, $min);
            $max_cmp = version_compare($currentVersion, $max);            
            if ($min_cmp<0){
                JError::raiseWarning("", sprintf(_JSHOP_MIN_VERSION_ERROR, $min));
                saveToLog("install.log", "Error: ".sprintf(_JSHOP_MIN_VERSION_ERROR, $min));
                return 0;
            }
            if ($max_cmp>0){
                JError::raiseWarning("", sprintf(_JSHOP_MAX_VERSION_ERROR, $max));
                saveToLog("install.log", "Error: ".sprintf(_JSHOP_MAX_VERSION_ERROR, $max));
                return 0;
            }
        }
        return 1;
    }

}