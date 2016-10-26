<?php 
/**
* @version      4.4.1 10.02.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<table>
   <tr>
     <td width="200">
       <?php echo _JSHOP_ACCOUNT_HOLDER;?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][acc_holder]" id="params_pm_debit_acc_holder" value="<?php print $params['acc_holder']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo _JSHOP_IBAN?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank_iban]" id="params_pm_debit_bank_iban" value="<?php print $params['bank_iban']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo _JSHOP_BIC_BIC?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank_bic]" id="params_pm_debit_bank_bic" value="<?php print $params['bank_bic']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo _JSHOP_BANK?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank]" id="params_pm_debit_bank" value="<?php print $params['bank']?>"/>
     </td>
   </tr>
</table>
<script type="text/javascript">
  function check_pm_debit(){
    var ar_focus=new Array();
    var error=0;
    unhighlightField('payment_form');
    if (isEmpty($F_("params_pm_debit_acc_holder"))) {
        ar_focus[ar_focus.length]="params_pm_debit_acc_holder";
        error=1;
    }
    if (isEmpty($F_("params_pm_debit_bank_iban"))) {
        ar_focus[ar_focus.length]="params_pm_debit_bank_iban";
        error=1;
    }
    if (isEmpty($F_("params_pm_debit_bank"))) {
        ar_focus[ar_focus.length]="params_pm_debit_bank";
        error=1;
    }
    if (error){
        $_(ar_focus[0]).focus();
        for (var i=0; i<ar_focus.length; i++ ){
           highlightField(ar_focus[i]);
        }
        return false;
    }
    jQuery('#payment_form').submit();
  }
 </script>