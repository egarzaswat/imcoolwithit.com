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
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
<tr>
   <td style="width:250px;" class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_USER_ID;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[user_id]" size="45" value = "<?php echo $params['user_id']?>" />
   </td>
 </tr>
 <tr>
   <td style="width:250px;" class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_PROJECT_ID;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[project_id]" size="45" value = "<?php echo $params['project_id']?>" />     
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_PROJECT_PASSWORD;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[project_password]" size="45" value = "<?php echo $params['project_password']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_NOFITY_PASSWORD;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[notify_password]" size="45" value = "<?php echo $params['notify_password']?>" />
   </td>
 </tr>
 
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_END;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );     
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_PENDING;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_FAILED;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
     ?>
   </td>
 </tr>
 <tr>
    <td class="key">&nbsp;</td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_RETURN_URL;?>
   </td>
   <td>
     <?php 
     print JURI::root(). "index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_sofortueberweisung";
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_NOTIFI_URL;?>
   </td>
   <td>
     <?php 
     print JURI::root(). "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_sofortueberweisung&no_lang=1";
     ?>
   </td>
 </tr>         
 <tr>
   <td class="key">
     <?php echo _JSHOP_SOFORTUEBERWEISUNG_CANCEL_URL;?>
   </td>
   <td>
     <?php 
     print JURI::root(). "index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_sofortueberweisung";
     ?>
   </td>
 </tr>
 
</table>
</fieldset>
</div>
<div class="clr"></div>