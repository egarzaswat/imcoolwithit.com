<?php
/**
* @version      4.7.0 08.09.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelLogs extends JModelLegacy{

    function getList(){        
        $jshopConfig = JSFactory::getConfig();
        $list = array();
        $dir = $jshopConfig->log_path;
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if (preg_match("/(.*)\.log/", $file, $matches)){
                $time = filemtime($dir.$file);
                $size = filesize($dir.$file);
                $list[] = array($file, $time, $size);
            }
        }
        closedir($dh);
        return $list;
    }
    
    function read($file){
        $jshopConfig = JSFactory::getConfig();        
        $dir = $jshopConfig->log_path;
        return file_get_contents($dir.$file);
    }
            
}
?>