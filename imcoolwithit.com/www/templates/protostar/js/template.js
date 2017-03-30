/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function($)
{
	$(document).ready(function()
	{
        // $('.container-full').css('min-height', $(window).height()-205);
        // $('.homepage .container-full').css('min-height', $(window).height()-120);
        $('.back_action').click(function(){
            history.go(-1);
        });
	});

    $(window).resize(function()
    {
        // $('.container-full').css('min-height', $(window).height()-205);
        // $('.homepage .container-full').css('min-height', $(window).height()-120);
        $('.back_action').click(function(){
            history.go(-1);
        });
    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            var data_post = {
                'message' : "Geolocation is not supported by this browser.",
                'location_save' : false
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/save_location.php',
                data: data_post,
                success: function (data) {

                }
            });
        }
    }

    function showPosition(position) {
        var data_post = {
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'message' : "Successfully load",
            'location_save' : true
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/save_location.php',
            data: data_post,
            success: function (data) {
                if(data === 'success'){
                    Cookies.set('update_geo', now.getTime() + (1000 * 60 *10));
                }
            }
        });
    }

    var timer;

    function updateLocation(){
        now = new Date();
        var realtime = now.getTime();
        var update_geo = Cookies.get('update_geo');

        if(update_geo === undefined || update_geo < realtime){
            getLocation();

            clearTimeout(timer);
            timer = setTimeout(function(){
                updateLocation();
            }, (1000 * 60 *10) );
        } else {
            clearTimeout(timer);
            timer = setTimeout(function(){
                updateLocation();
            }, (update_geo - realtime) );
        }
    }

    updateLocation();
})(jQuery);