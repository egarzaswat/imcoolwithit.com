<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$document = JFactory::getDocument();
$pathToJS = JURI::root().'components/com_jshopping/js/';
$document->addScript($pathToJS.'ga.js');

?>

<div class="general_content padding_xs_null col-xs-12">
    <div class="text_column col-sm-8 col-sm-offset-2 col-xs-12">
        <div class="text_block">
            <div class="text_info page-content">
                <?php print $this->data[0]->content ?>
            </div>
            <form class="enter_zip_code" action="" method="post">
                <input class="zip_code" type="text" name="zip" id="zip" placeholder="Enter Zip Code:" required="required" pattern="[0-9]*" autocomplete="off"/>
                <span class="zip_error"></span>
            </form>
        </div>
    </div>
    <div class="sponsors col-xs-12"></div>
</div>

<script type="text/javascript">

    window.onload = function () {
        // OnKeyDown Function
        jQuery("#zip").keyup(function () {
            var zip_in = jQuery(this);

            if (zip_in.val().length < 5) {
                jQuery('.zip_error').html('Invalid Postal Code');
            } else if (zip_in.val().length > 5) {
                jQuery('.zip_error').html('Invalid Postal Code');
            } else if ((zip_in.val().length == 5)) {

                // Make HTTP Request
                jQuery.ajax({
                    url: "https://api.zippopotam.us/us/" + zip_in.val(),
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (result) {

                        // US Zip Code Records Officially Map to only 1 Primary Location
                        places = result['places'][0];
                        jQuery('.zip_error').html('Found!!!');

                        var data_post = {
                            'longitude' : places['longitude'],
                            'latitude' : places['latitude'],
                            'lang' : '<?php print JSFactory::getLang()->lang; ?>',
                            'header' : '<?php print $this->data[0]->header; ?>'
                        };

                        jQuery.ajax({
                            type: "POST",
                            url: '/components/com_content/views/info/sponsors_load.php',
                            data: data_post,
                            success: function (data) {
                                jQuery('.general_content .sponsors').html(data);
                            }
                        });
                    },
                    error: function () {
                        jQuery('.zip_error').html('Invalid Postal Code');
                    }
                });
            }
        });
    };

    (function(){
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

</script>
