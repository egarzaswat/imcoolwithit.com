<?php
/**
* @version      4.4.2 09.04.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class pm_sofortueberweisung extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__)."/paymentform.php");
    }

	//function call in admin
	function showAdminFormParams($params){
	  $array_params = array('user_id', 'project_id', 'project_password', 'notify_password', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  } 
	  $orders = JSFactory::getModel('orders', 'JshoppingModel'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");
	}

	function checkTransaction($params, $order, $act){
        
        if ($params['user_id'] != $_POST['user_id']){
            return array(0, 'Error user_id. Order ID '.$order->order_id);
        } 
        if ($order->order_total != $_POST['amount']){
            return array(0, 'Error amount. Order ID '.$order->order_id);
        }
        if ($order->currency_code_iso != $_POST['currency_id']){
            return array(0, 'Error currency_id. Order ID '.$order->order_id);            
        }
        
        if ($params['notify_password']){
            $params['project_password'] = $params['notify_password'];
        }
        
        $data = array( 
          'transaction' => $_POST['transaction'], 
          'user_id' => $_POST['user_id'], 
          'project_id' => $_POST['project_id'], 
          'sender_holder' => $_POST['sender_holder'], 
          'sender_account_number' => $_POST['sender_account_number'], 
          'sender_bank_code' => $_POST['sender_bank_code'],
          'sender_bank_name' => $_POST['sender_bank_name'], 
          'sender_bank_bic' => $_POST['sender_bank_bic'], 
          'sender_iban' => $_POST['sender_iban'], 
          'sender_country_id' => $_POST['sender_country_id'], 
          'recipient_holder' => $_POST['recipient_holder'], 
          'recipient_account_number' => $_POST['recipient_account_number'], 
          'recipient_bank_code' => $_POST['recipient_bank_code'], 
          'recipient_bank_name' => $_POST['recipient_bank_name'], 
          'recipient_bank_bic' => $_POST['recipient_bank_bic'], 
          'recipient_iban' => $_POST['recipient_iban'], 
          'recipient_country_id' => $_POST['recipient_country_id'], 
          'international_transaction' => $_POST['international_transaction'], 
          'amount' => $_POST['amount'], 
          'currency_id' => $_POST['currency_id'], 
          'reason_1' => $_POST['reason_1'], 
          'reason_2' => $_POST['reason_2'], 
          'security_criteria' => $_POST['security_criteria'], 
          'user_variable_0' => $_POST['user_variable_0'], 
          'user_variable_1' => $_POST['user_variable_1'], 
          'user_variable_2' => $_POST['user_variable_2'], 
          'user_variable_3' => $_POST['user_variable_3'], 
          'user_variable_4' => $_POST['user_variable_4'], 
          'user_variable_5' => $_POST['user_variable_5'], 
          'created' => $_POST['created'], 
          'project_password' => $params['project_password'] 
        );
        
        $data_implode = implode('|', $data); 
        $hash = sha1($data_implode);        
        
        $return = 0;
        
        if ($_POST['security_criteria']){
            if ($_POST['hash']==$hash){
                $return = 1;
            }else{
                saveToLog("paymentdata.log", "Error hash. ".$hash);
            }
        }
        
    return array($return, "");    
	}

	function showEndForm($params, $order){
        $jshopConfig = JSFactory::getConfig();        
	    $item_name = sprintf(_JSHOP_PAYMENT_NUMBER, $order->order_number);
        
        $data = array( 
                      $params['user_id'], // user_id 
                      $params['project_id'], // project_id 
                      '',    // sender_holder 
                      '',    // sender_account_number 
                      '',    // sender_bank_code 
                      '',    // sender_country_id 
                      $order->order_total,    // amount 
                      $order->currency_code_iso,    // currency_id, mandatory parameter at hash calculation 
                      $item_name,  // reason_1 
                      '',    // reason_2 
                      $order->order_id,    // user_variable_0 
                      '',    // user_variable_1 
                      '',    // user_variable_2 
                      '',    // user_variable_3 
                      '',    // user_variable_4 
                      '',    // user_variable_5 
                      $params['project_password']  // project_password 
                    );
        $data_implode = implode('|', $data); 
        $hash = sha1($data_implode);

		$datajshopping = JApplicationHelper::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");
		
        ?>
        <form id="paymentform" action="https://www.sofortueberweisung.de/payment/start" name = "paymentform" method = "post">
        <input type='hidden' name='user_id' value='<?php print $params['user_id']?>' />
        <input type='hidden' name='project_id' value='<?php print $params['project_id']?>' />
        <input type="hidden" name="user_variable_0" value="<?php print $order->order_id?>">
        <input type='hidden' name='reason_1' value='<?php print $item_name?>' />
        <input type='hidden' name='amount' value='<?php print $order->order_total?>'/>
        <input type="hidden" name="currency_id" value="<?php print $order->currency_code_iso?>" />
        <input type='hidden' name='hash' value='<?php print $hash?>' />
		<input type='hidden' name='interface_version' value='joomshopping_<?php print $datajshopping['version']?>' />
        </form>
        <?php print _JSHOP_REDIRECT_TO_PAYMENT_PAGE ?>
        <br>
        <script type="text/javascript">document.getElementById('paymentform').submit();</script>
        <?php
        die();
	}
    
    function getUrlParams($pmconfigs){
        $params = array(); 
        $params['order_id'] = JRequest::getInt("user_variable_0");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = 0;
    return $params;
    }
}
?>