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
$sponsors = $this->sponsors;
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

    <button id="export_answers_for_meet_ups" class="btn">Export Answers for Meet Ups</button>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="title" width="10">
                #
            </th>
            <th width="200" align="left">
                Sender
            </th>
            <th width="200" align="left">
                Recipient
            </th>
            <th width="200" align="left">
                Sponsor
            </th>
            <th width="200" align="left">
                Occurred Date
            </th>
            <th width="50" align="left">
                Review
            </th>
        </tr>
        </thead>
        <tbody id="meet_up_list" style="width: 1000px;">

        </tbody>
    </table>

<?php print $this->tmp_html_end ?>

<script type="text/javascript">
    function MeetUpsReload(){
        var data_post = {
            'sponsor'   : jQuery('#select_sponsor').val(),
            'date_from' : jQuery('#datepicker_from').val(),
            'date_to'   : jQuery('#datepicker_to').val(),
            'list'      : <?php print json_encode($list);?>
        };

        jQuery.ajax({
            type: "POST",
            url: 'components/com_jshopping/controllers/meet_ups_reload.php',
            data: data_post,
            success: function (data) {
                jQuery('#meet_up_list').html(data);
            },
            error: function (html) {
                alert('error');
            }
        });
    }

    var data_post = {
        'sponsor' : jQuery('#meet_up_list').val(),
        'list' : <?php print json_encode($list);?>
    };

    jQuery.ajax({
        type: "POST",
        url: 'components/com_jshopping/controllers/meet_ups_reload.php',
        data: data_post,
        success: function (data) {
            jQuery('#meet_up_list').html(data);
        },
        error: function (html) {
            alert('error');
        }
    });

    jQuery('#select_sponsor').val('0').prop('selected', true);

    jQuery('#select_sponsor').change(function () {
        MeetUpsReload();
    });

    jQuery('#datepicker_from').change(function () {
        MeetUpsReload();
    });

    jQuery('#datepicker_to').change(function () {
        MeetUpsReload();
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

        window.open('mpdf56/getpdf/meet_ups.php?sponsor='+jQuery('#select_sponsor').val()+'&date_from='+date_from+'&date_to='+date_to);
    });

    jQuery('#export_answers_for_meet_ups').click(function(){
        var date_from=0;
        if(jQuery('#datepicker_from').val() !== ''){
            date_from = jQuery('#datepicker_from').val();
        }

        var date_to=0;
        if(jQuery('#datepicker_to').val() !== ''){
            date_to = jQuery('#datepicker_to').val();
        }
        window.open('mpdf56/getpdf/answers_for_sponsors.php?sponsor='+jQuery('#select_sponsor').val()+'&date_from='+date_from+'&date_to='+date_to);
    });

</script>