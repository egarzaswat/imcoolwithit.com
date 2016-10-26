<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopImportExport extends JTable {
    
    var $id = null;
    var $name = null;
    var $alias = null;
    var $description = null;
    var $params = null;
    var $endstart = null;
    var $steptime = null;
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_import_export', 'id', $_db );
    }        
}
?>