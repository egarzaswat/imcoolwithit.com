<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php print _JSHOP_HI?> <?php print $this->order->f_name;?> <?php print $this->order->l_name;?>,
<?php printf(_JSHOP_YOUR_ORDER_STATUS_CHANGE, $this->order->order_number);?>

<?php print _JSHOP_NEW_STATUS_IS?>: <?php print $this->order_status?>
<?php if ($this->order_detail){?>
<?php print _JSHOP_ORDER_DETAILS?>: <?php print $this->order_detail?>
<?php }?>
 
<?php print $this->vendorinfo->company_name?> 
<?php print $this->vendorinfo->adress?> 
<?php print $this->vendorinfo->zip?> <?php print $this->vendorinfo->city?> 
<?php print $this->vendorinfo->country?> 
<?php print _JSHOP_CONTACT_PHONE?>: <?php print $this->vendorinfo->phone?> 
<?php print _JSHOP_CONTACT_FAX?>: <?php print $this->vendorinfo->fax?>