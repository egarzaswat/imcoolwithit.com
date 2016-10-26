<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopProductVideo extends JTable {

    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_videos', 'video_id', $_db);
    }
}
?>