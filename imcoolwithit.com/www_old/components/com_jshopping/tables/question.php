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

class jshopQuestion extends JTableAvto{

    function __construct( &$_db ){
        parent::__construct('#__questions_earn_tokens', 'id', $_db );
    }

    function getName($id){
        $db = JFactory::getDBO();
        $query = "SELECT question"." FROM `#__questions_earn_tokens` WHERE id = '".$db->escape($id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }

}
?>