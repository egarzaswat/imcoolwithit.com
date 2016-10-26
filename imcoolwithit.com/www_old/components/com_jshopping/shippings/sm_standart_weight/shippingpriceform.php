<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$row = $template->sh_method_price;
?>
<tr><td>&nbsp;</td></tr>
<tr>
  <td class="key" style = "text-align:right; vertical-align:top">
    <b><?php echo _JSHOP_PRICE_DEPENCED_WEIGHT;?></b>
  </td>
  <td>
    <table class="adminlist" id="table_shipping_weight_price">
    <thead>
       <tr>
         <th>
           <?php echo _JSHOP_MINIMAL_WEIGHT;?> (<?php print sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo _JSHOP_MAXIMAL_WEIGHT;?> (<?php print sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo _JSHOP_PRICE;?> (<?php echo $template->currency->currency_code; ?>)
         </th>
         <th>
           <?php echo _JSHOP_PACKAGE_PRICE;?> (<?php echo $template->currency->currency_code; ?>)
         </th>         
         <th>
           <?php echo _JSHOP_DELETE;?>
         </th>
       </tr>                   
       </thead>
       <?php
       $key = 0;
       foreach ($row->prices as $key=>$value){?>
       <tr id='shipping_weight_price_row_<?php print $key?>'>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_weight_from[]" value = "<?php echo $value->shipping_weight_from;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_weight_to[]" value = "<?php echo $value->shipping_weight_to;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_price[]" value = "<?php echo $value->shipping_price;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_package_price[]" value = "<?php echo $value->shipping_package_price;?>" />
         </td>         
         <td style="text-align:center">
            <a class="btn btn-micro" href="#" onclick="delete_shipping_weight_price_row(<?php print $key?>);return false;">
                <i class="icon-delete"></i>
            </a>
         </td>
       </tr>
       <?php }?>    
    </table>
    <table class="adminlist"> 
    <tr>
        <td style="padding-top:5px;" align="right">
            <input type="button" class="btn" value="<?php echo _JSHOP_ADD_VALUE?>" onclick = "addFieldShPrice();">
        </td>
    </tr>
    </table>
    <script type="text/javascript"> 
        <?php print "var shipping_weight_price_num = $key;";?>
    </script>
</td>
</tr>