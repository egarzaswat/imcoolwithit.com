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
$sponsors = $this->sponsors;
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
<select id="select_sponsor">
    <option value="0" selected="selected"><?php print JText::_('JGLOBAL_ALL_SPONSORS'); ?></option>
    <?php foreach ($sponsors as $key => $value) { ?>
        <option value="<?php print $value->sponsor?>"><?php print $value->sponsor_name?></option>
    <?php } ?>
</select>

<div class="date_from_to">
    <?php print JText::_('DATE_FROM_TEXT'); ?> <input type="text" id="datepicker_from">
</div>
<div class="date_from_to">
    <?php print JText::_('DATE_TO_TEXT'); ?> <input type="text" id="datepicker_to">
</div>

<button id="export_meet_ups" class="btn">Export</button>

<div class="load_reviews"></div>


<script type="text/javascript">
    function StatisticsReload(){
        var data_post = {
            'sponsor'   : jQuery('#select_sponsor').val(),
            'date_from' : jQuery('#datepicker_from').val(),
            'date_to'   : jQuery('#datepicker_to').val()
        };

        jQuery.ajax({
            type: "POST",
            url: 'components/com_jshopping/controllers/answers_calculate.php',
            data: data_post,
            success: function (data) {
                jQuery('#meet_up_list').html(data);
            },
            error: function (html) {
                alert('error');
            }
        });
    }

    jQuery('#select_sponsor').val('0').prop('selected', true);

    jQuery('#select_sponsor').change(function () {
        StatisticsReload();
    });

    jQuery('#datepicker_from').change(function () {
        StatisticsReload();
    });

    jQuery('#datepicker_to').change(function () {
        StatisticsReload();
    });

    jQuery('#export_meet_ups').click(function(){
        var date_from=0;
        if(jQuery('#datepicker_from').val() !== ''){
            date_from = jQuery('#datepicker_from').val();
        }

        var date_to=0;
        if(jQuery('#datepicker_to').val() !== ''){
            date_to = jQuery('#datepicker_to').val();
        }

        jQuery(location).attr('href', 'mpdf56/getpdf/meet_ups.php?sponsor='+jQuery('#select_sponsor').val()+'&date_from='+date_from+'&date_to='+date_to);
    });

</script>
