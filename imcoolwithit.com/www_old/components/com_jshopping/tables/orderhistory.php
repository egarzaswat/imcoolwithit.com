<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopOrderHistory extends JTable {
    
    var $order_history_id = null;
    var $order_id = null;
    var $order_status_id = null;
    var $status_date_added = null;
    var $customer_notify = null;
    var $comments = null;

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_order_history', 'order_history_id', $_db );
    }

}
?>