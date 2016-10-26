<?php
/**
* @version      4.4.1 10.02.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class pm_debit extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        if (!isset($params['acc_holder'])) $params['acc_holder'] = '';
        if (!isset($params['bank_iban'])) $params['bank_iban'] = '';
        if (!isset($params['bank_bic'])) $params['bank_bic'] = '';
        if (!isset($params['bank'])) $params['bank'] = '';
    	include(dirname(__FILE__)."/paymentform.php");
    }

    function getDisplayNameParams(){
        $names = array('acc_holder' => _JSHOP_ACCOUNT_HOLDER, 'bank_iban' => _JSHOP_IBAN, 'bank_bic' => _JSHOP_BIC_BIC, 'bank' => _JSHOP_BANK );
        return $names;
    }
}
?>