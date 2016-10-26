<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopOrderStatus extends JTableAvto {
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_order_status', 'status_id', $_db );
    }
    
    function getName($status_id) {
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get('name')."` as name FROM `#__jshopping_order_status` WHERE status_id = '" . $this->_db->escape($status_id) . "'";
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }    
}
?>