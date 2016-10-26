<?php
/**
* @version      3.19.0 06.09.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

class jshopPaymentTrx extends JTable{
    function __construct(&$_db){
        parent::__construct('#__jshopping_payment_trx', 'id', $_db);
    }
}
?>