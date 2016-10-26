<?php
/**
* @version      4.4.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
 <tr>
   <td style="width:250px;" class="key">
     <?php echo _JSHOP_TESTMODE;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[testmode]', 'class = "inputbox" size = "1"', $params['testmode']);
     echo " ".JHTML::tooltip(_JSHOP_PAYPAL_TESTMODE_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo _JSHOP_PAYPAL_EMAIL;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[email_received]" size="45" value = "<?php echo $params['email_received']?>" />
     <?php echo JHTML::tooltip(_JSHOP_PAYPAL_EMAIL_DESCRIPTION);?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_END;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );
     echo " ".JHTML::tooltip(_JSHOP_PAYPAL_TRANSACTION_END_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_PENDING;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
     echo " ".JHTML::tooltip(_JSHOP_PAYPAL_TRANSACTION_PENDING_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_FAILED;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
     echo " ".JHTML::tooltip(_JSHOP_PAYPAL_TRANSACTION_FAILED_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_CHECK_DATA_RETURN;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[checkdatareturn]', 'class = "inputbox"', $params['checkdatareturn']);     
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_OVERRIDING_ADDRESSES?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[address_override]', 'class = "inputbox"', $params['address_override']);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_NOTIFY_URL_SEF?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[notifyurlsef]', 'class = "inputbox"', $params['notifyurlsef']);
     ?>
   </td>
 </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>