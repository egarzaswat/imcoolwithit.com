<?php
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
$lang = JFactory::getLanguage()->getTag();
if ($lang=='de-DE'){
	$urladv = 'https://www.trustedshops.de/shopbetreiber/bestellen.html?audit=0&voucher=TS-SOH-ERG-14&a_aid=8cbdddc2';
}else{
	$urladv = 'https://www.trustedshops.eu/merchants/order.html?audit=0&voucher=TS-SOH-ERG-14&a_aid=8cbdddc2';
}
?>
<div class="payment_advert" style="margin-top: 30px;">    
    <a align="middle" href="<?php print $urladv?>" target="_blank"><div style="font-size:16px;line-height:60px;float:left"><?php print _TEXT_TRUSTED_SHOPS?></div>&nbsp;&nbsp; <img src="https://b2b.trustedshops.com/affiliate/accounts/default1/banners/bf9db5ec.gif" alt="" title="" width="468" height="60" /></a>
</div>