<?php
/**
 * @version      4.9.0 13.08.2013
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();
displaySubmenuOptions();
$list = $this->list;
$offers = $this->offers;
$count = count($list);
$i = 0;
?>

<link rel="stylesheet" href="/administrator/components/com_jshopping/css/jqueryui.css">
<script src="/administrator/components/com_jshopping/js/jquery-1.10.2.js"></script>
<script src="/administrator/components/com_jshopping/js/jquery-ui.js"></script>
<script>
    $(function() {
        $( "#datepicker_from" ).datepicker();
        $( "#datepicker_from" ).datepicker( "option", "dateFormat", "dd\.mm\.yy" );
        $( "#datepicker_to" ).datepicker();
        $( "#datepicker_to" ).datepicker( "option", "dateFormat", "dd\.mm\.yy" );
    });
</script>

<?php print $this->tmp_html_start ?>
    <select id="select_offer">
        <option value="0" selected="selected">All</option>
        <?php foreach ($offers as $key => $value) { ?>
        <option value="<?php print $value->offer?>"><?php print $value->offer_name?></option>
        <?php } ?>
    </select>

    <div class="date_from_to">
        <?php print JText::_('DATE_FROM_TEXT'); ?> <input type="text" id="datepicker_from">
    </div>
    <div class="date_from_to">
        <?php print JText::_('DATE_TO_TEXT'); ?> <input type="text" id="datepicker_to">
    </div>

    <button id="export_data" class="btn">Export</button>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="title" width="10">
                #
            </th>
            <th width="500" align="left">
                <?php print _JSHOP_ATTRIBUTES_NAME; ?>
            </th>
            <th align="left">
                <?php print _JSHOP_OPTIONS; ?>
            </th>
        </tr>
        </thead>
        <tbody id="questions_list" style="width: 1000px;">

        </tbody>
    </table>

<?php print $this->tmp_html_end ?>

<script type="text/javascript">
    function answersReload(){
        var date_from_v=0;
        if(jQuery('#datepicker_from').val() !== ''){
            date_from_v = jQuery('#datepicker_from').val();
        }

        var date_to_v=0;
        if(jQuery('#datepicker_to').val() !== ''){
            date_to_v = jQuery('#datepicker_to').val();
        }


        var data_post = {
            'sponsor'     : jQuery('#select_offer').val(),
            'date_from' : date_from_v,
            'date_to'   : date_to_v,
            'list'      : <?php print json_encode($list);?>
        };

        jQuery.ajax({
            type: "POST",
            url: 'components/com_jshopping/controllers/answers_reload.php',
            data: data_post,
            success: function (data) {
                jQuery('#questions_list').html(data);
            },
            error: function (html) {
                alert('error');
            }
        });
    }

    jQuery('#select_offer').val('0').prop('selected', true);

    answersReload();

    jQuery('#select_offer').change(function () {
        answersReload();
    });

    jQuery('#datepicker_from').change(function () {
        answersReload();
    });

    jQuery('#datepicker_to').change(function () {
        answersReload();
    });

    jQuery('#export_data').click(function(){
        var date_from=0;
        if(jQuery('#datepicker_from').val() !== ''){
            date_from = jQuery('#datepicker_from').val();
        }

        var date_to=0;
        if(jQuery('#datepicker_to').val() !== ''){
            date_to = jQuery('#datepicker_to').val();
        }

        window.open('mpdf56/getpdf/answers_for_offers.php?offer='+jQuery('#select_offer').val()+'&date_from='+date_from+'&date_to='+date_to);
    });
</script>