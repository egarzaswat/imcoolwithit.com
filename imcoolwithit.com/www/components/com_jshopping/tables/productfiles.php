<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProductFiles extends JTable {
    
    var $id = null;
    var $product_id = null;
    var $demo = null;
    var $demo_descr = null;
    var $file = null;
    var $file_descr = null;
    var $ordering = null;
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_products_files', 'id', $_db );
    }
}
?>