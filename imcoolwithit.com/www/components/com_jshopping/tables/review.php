<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopReview extends JTable {

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_products_reviews', 'review_id', $_db );
    }

    // Static method
    // $type = null - product
    // $type = other - manufacturer
    function getAllowReview($type = null) {
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        if($type) {
            if(!$jshopConfig->allow_reviews_manuf) {
                return 0;
            }
        } else {
            if(!$jshopConfig->allow_reviews_prod) {
                return 0;
            }
        }
        if($jshopConfig->allow_reviews_only_registered && !$user->id) {
            return -1;
        }
        return 1;
    }

    function getText() {
        $jshopConfig = JSFactory::getConfig();
        // Not logged in
        if($this->getAllowReview() == -1) {
            return _JSHOP_REVIEW_NOT_LOGGED;
        } else {
            return '';
        }
    }
	
	function check(){
        $db = JFactory::getDBO();
        if (!$this->product_id){
            return 0;
        }
        if ($this->user_name==''){
            return 0;
        }
        if ($this->user_email==''){
            return 0;
        }
        if ($this->review==''){
            return 0;
        }        
        $query = "SELECT product_id FROM #__jshopping_products WHERE product_id=".intval($this->product_id);
        $db->setQuery($query);
        $pid = intval($db->loadResult());
        if (!$pid){
            return 0;
        }
        return 1;
    }

}

?>