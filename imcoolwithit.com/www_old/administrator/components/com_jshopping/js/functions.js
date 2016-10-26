var jstriggers = {
    addAttributValueHtml: '',
    addAttributValue2Html: '',
};

function Round(value, numCount){
    var ret = parseFloat(Math.round(value * Math.pow(10, numCount)) / Math.pow(10, numCount)).toString();
    return (isNaN(ret)) ? (0) : (ret);
}

function changeCategory(){
    var catid = jQuery("#category_parent_id").val();
    var url = 'index.php?option=com_jshopping&controller=categories&task=sorting_cats_html&catid='+catid+"&ajax=1";
    function showResponse(data){
        jQuery('#ordering').html(data);
    }
    jQuery.get(url, showResponse);
}

function verifyStatus(orderStatus, orderId, message, extended, limit){
   if (extended == 0){
       var statusNewId = $F_('select_status_id' + orderId);
       if (statusNewId == orderStatus){
            alert (message);
            return;
       } else {
         var isChecked = ($_('order_check_id_' + orderId ).checked) ? ('&notify=1') : ('');
         location.href = 'index.php?option=com_jshopping&controller=orders&task=update_status&js_nolang=1&order_id=' + orderId + '&order_status=' + statusNewId + limit + isChecked;
       }
   } else {
       var statusNewId = $F_('order_status');
       if (statusNewId == orderStatus){
              alert (message);
              return;
       } else {
         var isChecked = ($_('notify').checked) ? ('&notify=1') : ('&notify=0');
         var includeComment = ($_('include').checked) ? ('&include=1') : ('&include=0');

         location.href = 'index.php?option=com_jshopping&controller=orders&task=update_one_status&js_nolang=1&order_id=' + orderId + '&order_status=' + statusNewId + isChecked + includeComment + '&comments=' + encodeURIComponent($F_('comments'));
       }
   }
}

function updatePrice(display_price_admin){
    var repl = new RegExp("\,", "i");
    var percent = $_('product_tax_id')[$_('product_tax_id').selectedIndex].text;
    var pattern = /(\d*\.?\d*)%\)$/
    pattern.test(percent);
    percent = RegExp.$1; 
    var price2 = $F_('product_price2');
    if (display_price_admin==0){
        $_('product_price').value = Round(price2 * (1 + percent / 100), product_price_precision);
    }else{
        $_('product_price').value = Round(price2 / (1 + percent / 100), product_price_precision);
    }
    reloadAddPriceValue();
}

function updatePrice2(display_price_admin){
    var repl = new RegExp("\,", "i");
    var percent = $_('product_tax_id')[$_('product_tax_id').selectedIndex].text;
    var pattern = /(\d*\.?\d*)%\)$/
    pattern.test(percent);
    percent = RegExp.$1; 
    var price = $F_('product_price');
    if (display_price_admin==0){
        $_('product_price2').value = Round (price / (1 + percent / 100), product_price_precision);
    }else{
        $_('product_price2').value = Round (price * (1 + percent / 100), product_price_precision);
    }
    reloadAddPriceValue();
}

function addNewPrice(){
    add_price_num++;
    var html;    
    html = '<tr id="add_price_'+add_price_num+'">';
    html += '<td><input type = "text" class="small3" name = "quantity_start[]" id="quantity_start_'+add_price_num+'" value = "" /></td>';
    html += '<td><input type = "text" class="small3" name = "quantity_finish[]" id="quantity_finish_'+add_price_num+'" value = "" /></td>';
    html += '<td><input type = "text" class="small3" name = "product_add_discount[]" id="product_add_discount_'+add_price_num+'" value = "" onkeyup="productAddPriceupdateValue('+add_price_num+')" /></td>';
    html += '<td><input type = "text" class="small3" id="product_add_price_'+add_price_num+'" value = "" onkeyup="productAddPriceupdateDiscount('+add_price_num+')" /></td>';    
    html += '<td align="center"><a href="#" class="btn btn-micro" onclick="delete_add_price('+add_price_num+');return false;"><i class="icon-delete"></i></a></td>';
    html += '</tr>';
    jQuery("#table_add_price").append(html);
}

function delete_add_price(num){
    jQuery("#add_price_"+num).remove();
}

function productAddPriceupdateValue(num){
    var price;
    var origin = jQuery("#product_price").val();
    if (origin=="") return 0;
    var discount = jQuery("#product_add_discount_"+num).val();
    if (discount=="") return 0;
    if (config_product_price_qty_discount==1)
        price = origin - discount;
    else
        price = origin - (origin * discount/100);
    jQuery("#product_add_price_"+num).val(price);
}

function productAddPriceupdateDiscount(num){
    var price;
    var origin = jQuery("#product_price").val();
    if (origin=="") return 0;
    var price = jQuery("#product_add_price_"+num).val();
    if (price=="") return 0;
    if (config_product_price_qty_discount==1)
        discount = origin - price;
    else
        discount = 100 - (price / origin * 100);
    jQuery("#product_add_discount_"+num).val(discount);
}

function reloadAddPriceValue(){
    var discount;
    var origin = jQuery("#product_price").val();
    jQuery("#attr_price").val(origin);
    
    if (origin=="") return 0;
    
    for(i=0;i<=add_price_num;i++){
        if (jQuery("#product_add_discount_"+i)){
            discount = jQuery("#product_add_discount_"+i).val();
            if (config_product_price_qty_discount==1)
                price = origin - discount;
            else
                price = origin - (origin * discount/100);
            jQuery("#product_add_price_"+i).val(price);
        }
    }
}

function updateEanForAttrib(){
    jQuery("#attr_ean").val(jQuery("#product_ean").val());
}

function addFieldShPrice(){
    shipping_weight_price_num++;
    var html;
    html = '<tr id="shipping_weight_price_row_'+shipping_weight_price_num+'">';
    html += '<td><input type = "text" class = "inputbox" name = "shipping_weight_from[]" value = "" /></td>';
    html += '<td><input type = "text" class = "inputbox" name = "shipping_weight_to[]" value = "" /></td>';
    html += '<td><input type = "text" class = "inputbox" name = "shipping_price[]" value = "" /></td>';
    html += '<td><input type = "text" class = "inputbox" name = "shipping_package_price[]" value = "" /></td>';
    html += '<td style="text-align:center"><a class="btn btn-micro" href="#" onclick="delete_shipping_weight_price_row('+shipping_weight_price_num+');return false;"><i class="icon-delete"></i></a></td>';
    html += '</tr>';
    jQuery("#table_shipping_weight_price").append(html);
}

function delete_shipping_weight_price_row(num){
    jQuery("#shipping_weight_price_row_"+num).remove();
}

function setDefaultSize(width, height, param){
   $_(param + '_width_image').value = width;
   $_(param + '_height_image').value = height;
   $_(param + '_width_image').disabled = true;
   $_(param + '_height_image').disabled = true;
}

function setOriginalSize(param){
   $_(param + '_width_image').disabled = true;
   $_(param + '_height_image').disabled = true;
   $_(param + '_width_image').value = 0;
   $_(param + '_height_image').value = 0;
}

function setManualSize(param){
   $_(param + '_width_image').disabled = false;
   $_(param + '_height_image').disabled = false;
}

function setFullOriginalSize(param){
   $_(param + '_width_image').disabled = true;
   $_(param + '_height_image').disabled = true;
   $_(param + '_width_image').value = 0;
   $_(param + '_height_image').value = 0;
}

function setFullManualSize(param){
   $_(param + '_width_image').disabled = false;
   $_(param + '_height_image').disabled = false;
}

function addAttributValue2(id){
    var value_id = jQuery("#attr_ind_id_tmp_"+id+"  :selected").val();
    var attr_value_text = jQuery("#attr_ind_id_tmp_"+id+"  :selected").text();
    var mod_price = jQuery("#attr_price_mod_tmp_"+id).val();
    var price = jQuery("#attr_ind_price_tmp_"+id).val();
    var existcheck = jQuery('#attr_ind_'+id+'_'+value_id).val();
    if (existcheck){
        alert(lang_attribute_exist);
        return 0;
    }    
    if (value_id=="0"){
        alert(lang_error_attribute);
        return 0;
    }
    html = "<tr id='attr_ind_row_"+id+"_"+value_id+"'>"; 
    hidden = "<input type='hidden' id='attr_ind_"+id+"_"+value_id+"' name='attrib_ind_id[]' value='"+id+"'>";
    hidden2 = "<input type='hidden' name='attrib_ind_value_id[]' value='"+value_id+"'>";
    tmpimg="";
    if (value_id!=0 && attrib_images[value_id]!=""){
        tmpimg ='<img src="'+folder_image_attrib+'/'+attrib_images[value_id]+'" style="margin-right:5px;" width="16" height="16" class="img_attrib">';
    }
    html+="<td>" + hidden + hidden2 + tmpimg + attr_value_text + "</td>";
    html+=jstriggers.addAttributValue2Html;
    html+="<td><a class='btn btn-micro' href='#' onclick=\"jQuery('#attr_ind_row_"+id+"_"+value_id+"').remove();return false;\"><i class=\"icon-delete\"></i></a></td>";
    html += "</tr>";    
    jQuery("#list_attr_value_ind_"+id).append(html);
}

function addAttributValue(){
    attr_tmp_row_num++;
    var id=0;
    var ide=0;
    var value = "";
    var text = "";
    var html="";
    var hidden="";
    var field="";
    var count_attr_sel = 0;
    var tmpmass = {};
    var tmpimg = "";
    var selectedval = {};
    var num = 0;
    var current_index_list = [];
    var max_index_list = [];
    var combination = 1;
    var count_attributs = attrib_ids.length;
    var index = 0;
    var option = {};
            
    for (var i=0; i<count_attributs; i++){
        current_index_list[i] = 0;
        id = attrib_ids[i];
        ide = "value_id"+id;
        selectedval[id] = [];
        num = 0;
        jQuery("#"+ide+" :selected").each(function(j, selected){ 
          value = jQuery(selected).val(); 
          text = jQuery(selected).text();
          if (value!=0){
              selectedval[id][num] = {"text":text, "value":value};
              num++;
          }
        });

        if (selectedval[id].length==0){
            selectedval[id][0] = {"text":"-", "value":"0"};
        }else{
            count_attr_sel++;    
        }
        max_index_list[i] = selectedval[id].length;
        combination = combination * max_index_list[i];
    }
    
    var first_attr = jQuery("input:hidden","#list_attr_value tr:eq(1)");
    if (first_attr.length > 0) {
        for (var k=0; k<count_attributs; k++)
        {
            id = attrib_ids[k];
            if (first_attr[k].value==0) 
            {
                if (selectedval[id][0].value != 0) 
                {
                    alert(lang_error_attribute);
                    return 0;
                }
            }
            if (first_attr[k].value!=0) 
            {
                if (selectedval[id][0].value == 0) 
                {
                    alert(lang_error_attribute);
                    return 0;
                }
            }
        }
    }
    
    if (count_attr_sel==0){
        alert(lang_error_attribute);
        return 0;
    }
    
    var list_key = [];
    for(var j=0; j<combination; j++){
        list_key[j] = [];
        for (var i=0; i<count_attributs; i++){
            id = attrib_ids[i];
            num = current_index_list[i];
            list_key[j][i] = num;
        }
        
        index = 0;
        for (var i=0; i<count_attributs; i++){
            if (i==index){
                current_index_list[index]++;
                if (current_index_list[index] >= max_index_list[index]){
                    current_index_list[index] = 0;
                    index++;
                }
            }
        }
    }

    var entered_price = jQuery("#attr_price").val();
    var entered_count = jQuery("#attr_count").val();
    var entered_ean = jQuery("#attr_ean").val();    
    var entered_weight = jQuery("#attr_weight").val();
    var entered_weight_volume_units = jQuery("#attr_weight_volume_units").val();
    var entered_old_price = jQuery("#attr_old_price").val();
    var entered_buy_price = jQuery("#attr_buy_price").val();
    var count_added_rows = 0;
    for(var j=0; j<combination; j++){
        tmpmass = {};
        html = "<tr id='attr_row_"+attr_tmp_row_num+"'>";
        for (var i=0; i<count_attributs; i++){
            id = attrib_ids[i];
            num = list_key[j][i];
            option = selectedval[id][num];
            hidden = "<input type='hidden' name='attrib_id["+id+"][]' value='"+option.value+"'>";
            tmpimg="";
            if (option.value!=0 && attrib_images[option.value]!=""){
                tmpimg ='<img src="'+folder_image_attrib+'/'+attrib_images[option.value]+'" style="margin-right:5px;" width="16" height="16" class="img_attrib">';
            }
            html+="<td>" + hidden + tmpimg + option.text + "</td>";
            tmpmass[id] = option.value;
        }

        field="<input type='text' name='attrib_price[]' value='"+entered_price+"'>";
        html+="<td>"+field+"</td>";
        
        html+=jstriggers.addAttributValueHtml;
        
        if (use_stock=="1"){
            field="<input type='text' name='attr_count[]' value='"+entered_count+"'>";
            html+="<td>"+field+"</td>";
        }
        
        field="<input type='text' name='attr_ean[]' value='"+entered_ean+"'>";
        html+="<td>"+field+"</td>";
        
        field="<input type='text' name='attr_weight[]' value='"+entered_weight+"'>";
        html+="<td>"+field+"</td>";
        
        if (use_basic_price=="1"){
            field="<input type='text' name='attr_weight_volume_units[]' value='"+entered_weight_volume_units+"'>";
            html+="<td>"+field+"</td>";
        }
        
        field="<input type='text' name='attrib_old_price[]' value='"+entered_old_price+"'>";
        html+="<td>"+field+"</td>";
        
        if (use_bay_price=="1"){
            field="<input type='text' name='attrib_buy_price[]' value='"+entered_buy_price+"'>";
            html+="<td>"+field+"</td>";
        }
            
        html+="<td></td><td><input type='hidden' name='product_attr_id[]' value='0'><input type='checkbox' class='ch_attr_delete' value='"+attr_tmp_row_num+"'></td>";
        
        html+="</tr>";
        html+="";
        
        var existcheck = 0;
        for ( var k in attrib_exist ){
            var exist = 1; 
            for(var i=0; i<count_attributs; i++){
                id = attrib_ids[i];
                if (attrib_exist[k][id]!=tmpmass[id]) exist=0;
            }
            if (exist==1) {
                existcheck = 1;
                break;
            }
        }
        
        if (!existcheck){
            jQuery("#list_attr_value #attr_row_end").before(html);
            attrib_exist[attr_tmp_row_num] = tmpmass;
            attr_tmp_row_num++;
            count_added_rows++;
        }
    }
    
    if (count_added_rows==0){
        alert(lang_attribute_exist);
        return 0;
    }   
    return 1; 
}

function deleteTmpRowAttrib(num){
    jQuery("#attr_row_"+num).remove();
    delete attrib_exist[num];
}
function selectAllListAttr(checked){
    jQuery(".ch_attr_delete").attr('checked', checked);
}
function deleteListAttr(){
    jQuery("#ch_attr_delete_all").attr('checked', false);
    jQuery(".ch_attr_delete").each(function(i){
        if (jQuery(this).is(':checked')){
            deleteTmpRowAttrib(jQuery(this).val());
        }
    });
}

function deleteFotoCategory(catid){
    var url = 'index.php?option=com_jshopping&controller=categories&task=delete_foto&catid='+catid;
    function showResponse(data){
        jQuery("#foto_category").hide();
    }
    jQuery.get(url, showResponse);
}

function deleteFotoProduct(id){
    var url = 'index.php?option=com_jshopping&controller=products&task=delete_foto&id='+id;
    function showResponse(data){
        jQuery("#foto_product_"+id).hide();
    }
    jQuery.get(url, showResponse);
}

function deleteVideoProduct(id){
    var url = 'index.php?option=com_jshopping&controller=products&task=delete_video&id='+id;
    function showResponse(data){
        jQuery("#video_product_"+id).hide();
    }
    jQuery.get(url, showResponse);
}

function deleteFileProduct(id, type){
    var url = 'index.php?option=com_jshopping&controller=products&task=delete_file&id='+id+"&type="+type;
    function showResponse(data){
        if (type=="demo"){
            jQuery("#product_demo_"+id).html("");
        }
        if (type=="file"){
            jQuery("#product_file_"+id).html("");
        }
        if (data=="1") jQuery(".rows_file_prod_"+id).hide();
    }
    jQuery.get(url, showResponse);
}

function deleteFotoManufacturer(id){
    var url = 'index.php?option=com_jshopping&controller=manufacturers&task=delete_foto&id='+id;
    function showResponse(data){
        jQuery("#image_manufacturer").hide();
    }
    jQuery.get(url, showResponse);
}

function deleteFotoAttribValue(id){
    var url = 'index.php?option=com_jshopping&controller=attributesvalues&task=delete_foto&id='+id;
    function showResponse(data){
        jQuery("#image_attrib_value").hide();
    }
    jQuery.get(url, showResponse);
}

function deleteFotoLabel(id){
    var url = 'index.php?option=com_jshopping&controller=productlabels&task=delete_foto&id='+id;
    function showResponse(data){
        jQuery("#image_block").hide();
    }
    jQuery.get(url, showResponse);
}

function releted_product_search(start, no_id){
    var text = jQuery("#related_search").val();
    var url = 'index.php?option=com_jshopping&controller=products&task=search_related&start='+start+'&no_id='+no_id+'&text='+encodeURIComponent(text)+"&ajax=1";
    function showResponse(data){
        jQuery("#list_for_select_related").html(data);
    }
    jQuery.get(url, showResponse);
}

function add_to_list_relatad(id){
    var name = jQuery("#serched_product_"+id+" .name").html();
    var img =  jQuery("#serched_product_"+id+" .image").html();
    var html = '<div class="block_related" id="related_product_'+id+'">';
    html += '<div class="block_related_inner">';
    html += '<div class="name">'+name+'</div>';
    html += '<div class="image">'+img+'</div>';
    html += '<div style="padding-top:5px;"><input type="button" class="btn btn-danger btn-small" value="'+lang_delete+'" onclick="delete_related('+id+')"></div>';
    html += '<input type="hidden" name="related_products[]" value="'+id+'"/>';
    html += '</div>';
    html += '</div>';
    jQuery("#list_related").append(html);
}

function delete_related(id){
    jQuery("#related_product_"+id).remove();
}

function reloadProductExtraField(product_id){
    var catsurl = "";
    jQuery("#category_id :selected").each(function(j, selected){ 
      value = jQuery(selected).val(); 
      text = jQuery(selected).text();
      if (value!=0){
          catsurl += "&cat_id[]="+value;
      }
    });
    
    var url = 'index.php?option=com_jshopping&controller=products&task=product_extra_fields&product_id='+product_id+catsurl+"&ajax=1";
    function showResponse(data){
        jQuery("#extra_fields_space").html(data);
    }
    jQuery.get(url, showResponse);
}

function PFShowHideSelectCats(){
    var value = jQuery("input[name=allcats]:checked").val();
    if (value=="0"){
        jQuery("#tr_categorys").show();
    }else{
        jQuery("#tr_categorys").hide();
    }
}

function ShowHideEnterProdQty(checked){
    if (checked){
        jQuery("#block_enter_prod_qty").hide();
    }else{
        jQuery("#block_enter_prod_qty").show();
    }
}

function editAttributeExtendParams(id){
    window.open('index.php?option=com_jshopping&controller=products&task=edit&product_attr_id='+id,'windowae','width=1000, height=760, scrollbars=yes,status=no,toolbar=no,menubar=no,resizable=yes,location=yes');
}

function addOrderItemRow(){
    end_number_order_item++;
    var i = end_number_order_item;
    var html = '<tr valign="top" id="order_item_row_'+i+'">';
    html+='<td><input type="text" name="product_name['+i+']" value="" size="44" />';
    html+='<a class="modal btn" rel="{handler: \'iframe\', size: {x: 800, y: 600}}" href="index.php?option=com_jshopping&controller=product_list_selectable&tmpl=component&e_name='+i+'">'+lang_load+'</a><br />';
    if (admin_show_attributes){
        html+='<textarea rows="2" cols="24" name="product_attributes['+i+']"></textarea><br />';
    }
    if (admin_show_freeattributes){
        html+='<textarea rows="2" cols="24" name="product_freeattributes['+i+']"></textarea><br />';
    }   
    html+='<input type="hidden" name="product_id['+i+']" value="" />';    
    html+='<input type="hidden" name="delivery_times_id['+i+']" value="" />';
    html+='<input type="hidden" name="thumb_image['+i+']" value="" />';
    if (admin_order_edit_more){
        html+='<div>'+lang_weight+' <input type="text" name="weight['+i+']" value="" /></div>';
        html+='<div>'+lang_vendor+' ID <input type="text" name="vendor_id['+i+']" value="" /></div>';
    }else{
        html+='<input type="hidden" name="weight['+i+']" value="" />';
        html+='<input type="hidden" name="vendor_id['+i+']" value="" />';
    }
    html+='</td>';
    html+='<td><input type="text" name="product_ean['+i+']" class="middle" value="" /></td>';
    html+='<td><input type="text" name="product_quantity['+i+']" class="small3" value="" onkeyup="updateOrderSubtotalValue();"/></td>';
    html+='<td width="20%">';
    html+='<div class="price">'+lang_price+': <input type="text" class="small3" name="product_item_price['+i+']" value="" onkeyup="updateOrderSubtotalValue();"/></div>';
    if (!hide_tax){
    html+='<div class="tax">'+lang_tax+': <input type="text" class="small3" name="product_tax['+i+']" value="" />%</div>';
    }
	html+='<input type="hidden" name="order_item_id['+i+']" value="" /></td>';    
    html+='<td><a class="btn btn-micro" href="#" onclick="jQuery(\'#order_item_row_'+i+'\').remove();updateOrderSubtotalValue();return false;"><i class="icon-delete"></i></a></td>';
    html+='</tr>';
    jQuery("#list_order_items").append(html);
    
    SqueezeBox.initialize({});
    SqueezeBox.assign($$('a.modal'), {
        parse: 'rel'
    });
}
function loadProductInfoRowOrderItem(pid, num, currency_id){
	var url = 'index.php?option=com_jshopping&controller=products&task=loadproductinfo&product_id='+pid+'&currency_id='+currency_id+'&ajax=1';
    jQuery.getJSON(url, function(json){
        jQuery("input[name=product_id\\["+num+"\\]]").val(json.product_id);
        jQuery("input[name=product_name\\["+num+"\\]]").val(json.product_name);
        jQuery("input[name=product_ean\\["+num+"\\]]").val(json.product_ean);
        jQuery("input[name=product_item_price\\["+num+"\\]]").val(json.product_price);
        
        jQuery("input[name=product_tax\\["+num+"\\]]").val(json.product_tax);
        jQuery("input[name=weight\\["+num+"\\]]").val(json.product_weight);
        jQuery("input[name=delivery_times_id\\["+num+"\\]]").val(json.delivery_times_id);
        jQuery("input[name=vendor_id\\["+num+"\\]]").val(json.vendor_id);
        jQuery("input[name=thumb_image\\["+num+"\\]]").val(json.thumb_image);
        
        jQuery("input[name=product_quantity\\["+num+"\\]]").val(1);
		updateOrderSubtotalValue();
    });
}

function addOrderTaxRow(){
    var html="<tr>";
    html+='<td class="right"><input type="text" class="small3" name="tax_percent[]"/> %</td>';
    html+='<td class="left"><input type="text" class="small3" name="tax_value[]" onkeyup="updateOrderTotalValue();"/></td>';
    html+='</tr>';
    jQuery("#row_button_add_tax").before(html);
}

function updateOrderSubtotalValue() {
	var result = 0;
	var regExp = /product_item_price\[(\d+)\]/i;
	jQuery("input[name^=product_item_price]").each(function(){
		var myArray = regExp.exec(jQuery(this).attr("name"));
		var value = myArray[1];
		var price = parseFloat(jQuery(this).val());
		if (isNaN(price)) price = 0;
		var quantity = parseFloat(jQuery("input[name=product_quantity\\["+value+"\\]]").val().replace(',', '.'));
		if (isNaN(quantity)) quantity = 0;
		result += price * quantity;
	});
	
	jQuery("input[name=order_subtotal]").val(result);
	updateOrderTotalValue();
}

function updateOrderTotalValue() {
	var result = 0;
	var subtotal = parseFloat(jQuery("input[name=order_subtotal]").val());
	if (isNaN(subtotal)) subtotal = 0;
	var discount = parseFloat(jQuery("input[name=order_discount]").val());
	if (isNaN(discount)) discount = 0;
	var shipping = parseFloat(jQuery("input[name=order_shipping]").val());
	if (isNaN(shipping)) shipping = 0;
    var opackage = parseFloat(jQuery("input[name=order_package]").val());
    if (isNaN(opackage)) opackage = 0;
	var payment = parseFloat(jQuery("input[name=order_payment]").val());
	if (isNaN(payment)) payment = 0;
	result = subtotal - discount + shipping+opackage + payment;
	
	if (jQuery("#display_price option:selected").val() == 1) {
		jQuery("input[name^=tax_value]").each(function(){
			var tax_value = parseFloat(jQuery(this).val());
			if (isNaN(tax_value)) tax_value = 0;
			result += tax_value;
		});
	}
	
	jQuery("input[name=order_total]").val(result);
}

function changeVideoFileField(obj) {
	isChecked = jQuery(obj).is(':checked');
	var td_inputs = jQuery(obj).parents('td:first');
	if (isChecked) {
		td_inputs.find("input[name^='product_video_']").val('').hide();
		td_inputs.find("textarea[name^='product_video_code_']").show();
	} else {
		td_inputs.find("textarea[name^='product_video_code_']").val('').hide();
		td_inputs.find("input[name^='product_video_']").show();
	}
}

function updateAllVideoFileField() {
	jQuery("table.admintable input[name^='product_insert_code_']").each(function(){
		changeVideoFileField(this);
	});
}
function userEditenableFields(val){
    if (val==1){
        jQuery('.endes').removeAttr("disabled");    
    }else{
        jQuery('.endes').attr('disabled','disabled'); 
    }
}

function setBillingShippingFields(user){
    for(var field in user){
        jQuery(".jshop_address [name='" + field + "']").val(user[field]);
    }
}

function updateBillingShippingForUser(user_id){
    if (user_id > 0) {
        var data = {};
        data['user_id'] = user_id;
        if (userinfo_ajax){
            userinfo_ajax.abort();
        }
        userinfo_ajax = jQuery.ajax({
            url: userinfo_link,
            dataType: "json",
            data: data,
            type: "post",    
            success: function (json) {
                setBillingShippingFields(json);
            }
        });
    } else {
        setBillingShippingFields(userinfo_fields);
    }
}

function changeCouponType(){
    var val = jQuery("input[name=coupon_type]:checked").val();    
    if (val==0){
        jQuery("#ctype_percent").show();
        jQuery("#ctype_value").hide();
    }else{
        jQuery("#ctype_percent").hide();
        jQuery("#ctype_value").show();
    }
}

function setImageFromFolder(position, filename){
	jQuery("input[name='product_folder_image_" + position + "']").val(filename);
	jQuery("#sbox-overlay").click();
};

var product_images_prevAjaxQuery = null;
function product_images_request(position, url, filter){
	var data = {};
	data['position'] = position;
    data['filter'] = filter;
	if (product_images_prevAjaxQuery){
		product_images_prevAjaxQuery.abort();
	}
	product_images_prevAjaxQuery = jQuery.ajax({
	    url: url, 
        dataType: 'html',
        data : data, 
        beforeSend: function() {
			jQuery('#product_images').empty();
            jQuery('.sbox-content-string').append('<div id="product_images-overlay"></div>');
        },
		success: function(html){
			jQuery('#product_images-overlay').remove();
			jQuery('#product_images').html(html).fadeIn();
		}
    });
}

function SqueezeBox_init(product_images_width, product_images_height){
    if (!product_images_width) product_images_width = 640;
    if (!product_images_height) product_images_height = 480;
	SqueezeBox.initialize();
	SqueezeBox.setOptions({size: {x: product_images_width, y: product_images_height}}).setContent('string', '');
	SqueezeBox.applyContent('<div id="product_images" style="display: none; height: ' + product_images_height + 'px; overflow: scroll;"></div>');
	jQuery('.sbox-content-string').append('<div id="product_images-overlay"></div>');
}

function changeProductField(obj) {
	isChecked = jQuery(obj).is(':checked');
	var div_inputs = jQuery(obj).parents('div:first');
	if (isChecked) {
		div_inputs.find("input.product_image").val('').hide();
		div_inputs.find(".product_folder_image").show();
	} else {
		div_inputs.find("input[name^='product_folder_image_']").val('').hide();
		div_inputs.find("input[name^='select_image_']").val(_JSHOP_IMAGE_SELECT).hide();
		div_inputs.find("input.product_image").show();
	}
}