<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>

<div id="main-page" class="tab-pane">
    <div class="col100">
     <table class="admintable" width="90%">
     <tr>
       <td class="key" style="width:180px;">
         <?php echo _JSHOP_PUBLISH;?>
       </td>
       <td>
         <input type="checkbox" name="product_publish" id="product_publish" value="1" <?php if ($row->product_publish) echo 'checked="checked"'?> />
       </td>
     </tr>

<!--     <tr>-->
<!--         <td class="key">-->
<!--             --><?php //echo 'Tokens';?>
<!--         </td>-->
<!--         <td>-->
<!--             <input type="text" id="tokens" name="tokens" value="--><?php //echo $row->tokens;?><!--" />-->
<!--         </td>-->
<!--     </tr>-->

<!--    --><?php //if (!$this->withouttax){?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //if ($jshopConfig->display_price_admin==0) echo _JSHOP_PRODUCT_NETTO_PRICE; else echo _JSHOP_PRODUCT_BRUTTO_PRICE;?>
<!--       </td>-->
<!--       <td>-->
<!--         <input type="text" id="product_price2" value="--><?php //echo $row->product_price2;?><!--" onkeyup="updatePrice(--><?php //print $jshopConfig->display_price_admin;?><!--)" />
<!--       </td>
//     </tr>
//     <?php //}?>


<!--     --><?php //if ($jshopConfig->admin_show_product_bay_price) { ?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_PRODUCT_BUY_PRICE;?>
<!--       </td>-->
<!--       <td>-->
<!--         <input type="text" name="product_buy_price" id="product_buy_price" value="--><?php //echo $row->product_buy_price?><!--" />-->
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //} ?>

<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo 'Code';?>
<!--       </td>-->
<!--       <td>-->
<!--         <input type="text" name="product_ean" id="product_ean" value="--><?php //echo $row->product_ean?><!--" onkeyup="updateEanForAttrib()"; />-->
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //if ($jshopConfig->stock){?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_QUANTITY_PRODUCT;?><!--*-->
<!--       </td>-->
<!--       <td>-->
<!--         <div id="block_enter_prod_qty" style="padding-bottom:2px;--><?php //if ($row->unlimited) print "display:none;";?><!--">-->
<!--             <input type="text" name="product_quantity" id="product_quantity" value="--><?php //echo $row->product_quantity?><!--" --><?php //if ($this->product_with_attribute){?><!--readonly="readonly"--><?php //}?><!-- />-->
<!--             --><?php //if ($this->product_with_attribute){ echo JHTML::tooltip(_JSHOP_INFO_PLEASE_EDIT_AMOUNT_FOR_ATTRIBUTE); } ?>
<!--         </div>-->
<!--         <div>-->
<!--            <input type="checkbox" name="unlimited" value="1" onclick="ShowHideEnterProdQty(this.checked)" --><?php //if ($row->unlimited) print "checked";?><!-- /> --><?php //print _JSHOP_UNLIMITED;?>
<!--         </div>-->
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //}?>

<!--     --><?php //if ($jshopConfig->use_different_templates_cat_prod) { ?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_TEMPLATE_PRODUCT;?>
<!--       </td>-->
<!--       <td>-->
<!--         --><?php //echo $lists['templates'];?>
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //} ?>

<!--     --><?php //if (!$this->withouttax){?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_TAX;?><!--*-->
<!--       </td>-->
<!--       <td>-->
<!--         --><?php //echo $lists['tax'];?>
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //}?>

     <tr style="display: none;">
       <td class="key">
         <?php echo _JSHOP_CATEGORIES;?>*
       </td>
       <td>
         <?php echo $lists['categories'];?>
       </td>
     </tr>

<!--         --><?php //if ($this->category_id == 1) { $extra = $this->product_extra_options; ?>
         <?php if (false) { $extra = $this->product_extra_options; ?>

             <tr>
                 <td class="key">
                     <?php print _JSHOP_PERMANENT;?>*
                 </td>
                 <td>
                     <input type="checkbox" name="permanent" id="permanent" value="1" <?php if ($extra->permanent) print 'checked="checked"'?> />
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_EXPIRES;?>*
                 </td>
                 <td>
                     <input type="date" name="expires" id="expires" value="<?php print $extra->expires;?>"/>
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_MALE;?>*
                 </td>
                 <td>
                     <input type="checkbox" name="male" id="male" value="1" <?php if ($extra->male) print 'checked="checked"'?> />
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_FEMALE;?>*
                 </td>
                 <td>
                     <input type="checkbox" name="female" id="female" value="1" <?php if ($extra->female) print 'checked="checked"'?> />
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_FROM_AGE;?>*
                 </td>
                 <td>
                     <input type="text" name="from_age" id="from_age" value="<?php print $extra->from_age;?>"/>
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_TO_AGE;?>*
                 </td>
                 <td>
                     <input type="text" name="to_age" id="to_age" value="<?php print $extra->to_age;?>"/>
                 </td>
             </tr>
             <tr>
                 <td class="key">
                     <?php print _JSHOP_DISTANCE;?>*
                 </td>
                 <td>
                     <input type="text" name="distance" id="distance" value="<?php print $extra->distance;?>"/>
                 </td>
             </tr>

         <?php }?>

<!--     --><?php //if ($jshopConfig->admin_show_vendors && $this->display_vendor_select) { ?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_VENDOR;?>
<!--       </td>-->
<!--       <td>-->
<!--         --><?php //echo $lists['vendors'];?>
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //}?>
<!---->
<!--     --><?php //if ($jshopConfig->admin_show_delivery_time) { ?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_DELIVERY_TIME;?>
<!--       </td>-->
<!--       <td>-->
<!--         --><?php //echo $lists['deliverytimes'];?>
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //}?>

<!--     --><?php //if ($jshopConfig->admin_show_product_labels) { ?>
<!--     <tr>-->
<!--       <td class="key">-->
<!--         --><?php //echo _JSHOP_LABEL;?>
<!--       </td>-->
<!--       <td>-->
<!--         --><?php //echo $lists['labels'];?>
<!--       </td>-->
<!--     </tr>-->
<!--     --><?php //}?>

     <?php if ($jshopConfig->admin_show_product_basic_price) { ?>
     <tr>
       <td class="key"><br/><?php echo _JSHOP_BASIC_PRICE;?></td>
     </tr>
     <tr>
       <td class="key">
         <?php echo _JSHOP_WEIGHT_VOLUME_UNITS;?>
       </td>
       <td>
         <input type="text" name="weight_volume_units" value="<?php echo $row->weight_volume_units?>" />
       </td>
     </tr>
     <tr>
       <td class="key">
         <?php echo _JSHOP_UNIT_MEASURE;?>
       </td>
       <td>
         <?php echo $lists['basic_price_units'];?>
       </td>
     </tr>
     <?php }?>
     <?php if ($jshopConfig->return_policy_for_product){?>
     <tr>
       <td class="key">
         <?php echo _JSHOP_RETURN_POLICY_FOR_PRODUCT;?>
       </td>
       <td>
         <?php echo $lists['return_policy'];?>
       </td>
     </tr>
     <?php if (!$jshopConfig->no_return_all){?>
     <tr>
       <td class="key">
         <?php echo _JSHOP_NO_RETURN;?>
       </td>
       <td>
         <input type="hidden" name="options[no_return]"  value="0" />
         <input type="checkbox" name="options[no_return]" value="1" <?php if ($row->product_options['no_return']) echo 'checked = "checked"';?> />
       </td>
     </tr>
     <?php }?>
     <?php }?>
     <?php $pkey='plugin_template_info'; if ($this->$pkey){ print $this->$pkey;}?>
   </table>
   </div>
   <div class="clr"></div>
</div>
