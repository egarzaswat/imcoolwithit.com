<?php
/**
* @version      4.8.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class jshopProductExtraOptions extends JTableAvto{

    function __construct( &$_db ){
        parent::__construct('#__products_offer_extra_options', 'product_id', $_db );
    }

}
?>