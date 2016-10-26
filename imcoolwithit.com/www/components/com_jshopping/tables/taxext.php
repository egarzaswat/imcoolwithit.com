<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopTaxExt extends JTable {    
    var $id = null;
    var $tax_id = null;
    var $zones = null;
    var $tax = null;
    var $firma_tax = null;    
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_taxes_ext', 'id', $_db );
    }
    
    function setZones($zones){
        $this->zones = serialize($zones);
    }
    
    function getZones(){
        if ($this->zones!=""){
            return unserialize($this->zones);
        }else{
            return array();
        }
    }
}
?>