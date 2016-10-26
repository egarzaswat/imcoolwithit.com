<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

abstract class ShippingFormRoot{
    
    var $_errormessage = "";
    
    abstract function showForm($shipping_id, $shippinginfo, $params);
    
    function check($params, $sh_method){
        return 1;
    }
    
    /**
    * Set message error check
    */
    function setErrorMessage($msg){
        $this->_errormessage = $msg;
    }
    
    /**
    * Get message error check
    */
    function getErrorMessage(){
        return $this->_errormessage;
    }
    
    /**
    * list display params name shipping saved to order
    */
    function getDisplayNameParams(){
        return array();
    }
    
    /**
    * exec before mail send
    */
    function prepareParamsDispayMail(&$order, &$sh_method){
    }

}