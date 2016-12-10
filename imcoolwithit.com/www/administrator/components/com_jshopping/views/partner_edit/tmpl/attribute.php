<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div id="attribs-page" class="tab-pane">

<?php if ( (count($lists['all_independent_attributes'])+count($lists['all_attributes']))>0 ): ?>
    <script type="text/javascript">
        var lang_error_attribute="<?php print _JSHOP_ERROR_ADD_ATTRIBUTE; ?>";
        var lang_attribute_exist="<?php print _JSHOP_ATTRIBUTE_EXIST; ?>";
        var folder_image_attrib="<?php print $jshopConfig->image_attributes_live_path?>";
        var use_basic_price="<?php print $jshopConfig->admin_show_product_basic_price?>";
        var use_bay_price="<?php print $jshopConfig->admin_show_product_bay_price?>";
        var use_stock="<?php print intval($jshopConfig->stock)?>";
        var attrib_images=new Object();
        <?php foreach($lists['attribs_values'] as $k=>$v){?>
            attrib_images[<?php print $v->value_id?>]="<?php print $v->image?>";
        <?php }?>
    </script>
<?php endif; ?>


<?php
    if (count($lists['all_independent_attributes'])):

       foreach($lists['all_independent_attributes'] as $ind_attr): ?>

        <div style="padding-top:20px;">
        <table class="table table-striped" id="list_attr_value_ind_<?php print $ind_attr->attr_id?>">
            <thead>
                <tr>
                    <th width="600"><?php print $ind_attr->name?></th>
<!--                    <th>--><?php //print _JSHOP_DELETE; ?><!--</th>-->
                </tr>
            </thead>

            <?php
            if (isset($lists['ind_attribs_gr'][$ind_attr->attr_id]) && is_array($lists['ind_attribs_gr'][$ind_attr->attr_id])):

                foreach($lists['ind_attribs_gr'][$ind_attr->attr_id] as $ind_attr_val): ?>

                    <tr id='attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>'>

                        <td>
                            <?php if ($lists['attribs_values'][$ind_attr_val->attr_value_id]->image!=''){?>
                                <img src='<?php print $jshopConfig->image_attributes_live_path."/".$lists['attribs_values'][$ind_attr_val->attr_value_id]->image?>' align='left' hspace='5' width='16' height='16' style='margin-right:5px;' class='img_attrib'>
                            <?php }?>

                            <input type='hidden' id='attr_ind_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>' name='attrib_ind_id[]' value='<?php print $ind_attr_val->attr_id?>'>
                            <input type='hidden' name="attrib_ind_value_id[]" value='<?php print $ind_attr_val->attr_value_id?>'>

                            <?php print $lists['attribs_values'][$ind_attr_val->attr_value_id]->name;?>
                        </td>

                        <td>
                            <a class="btn btn-micro" href='#' onclick="jQuery('#attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>').remove();return false;">
                                <i class="icon-delete"></i>
                            </a>
                        </td>

                    </tr>

                <?php endforeach;
            endif;
            ?>
        </table>
        </div>

        <div style="padding-top:5px;" class="input-inline">
            <table cellpadding="2" class="table">
                <tr>
                    <td width="150"><?php print $ind_attr->values_select;?></td>
                    <td><?php print $ind_attr->submit_button;?></td>
                </tr>
            </table>
        </div>

    <?php endforeach; ?>

   <br/><br/>
   <?php endif; ?>

</div>