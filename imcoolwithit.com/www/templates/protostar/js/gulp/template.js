!function(o){function e(){o(".site-popup").css("display","flex")}function n(){o(".site-popup").css("display","none")}function t(){if(navigator.geolocation)navigator.geolocation.getCurrentPosition(i);else{var o={message:"Geolocation is not supported by this browser.",location_save:!1};jQuery.ajax({type:"POST",url:"/components/com_jshopping/controllers/save_data/save_location.php",data:o,success:function(o){}})}}function i(o){var e={latitude:o.coords.latitude,longitude:o.coords.longitude,message:"Successfully load",location_save:!0};jQuery.ajax({type:"POST",url:"/components/com_jshopping/controllers/save_data/save_location.php",data:e,success:function(o){"success"===o&&Cookies.set("update_geo",now.getTime()+6e5)}})}function s(){now=new Date;var o=now.getTime(),e=Cookies.get("update_geo");void 0===e||e<o?(t(),clearTimeout(c),c=setTimeout(function(){s()},6e5)):(clearTimeout(c),c=setTimeout(function(){s()},e-o))}o(document).ready(function(){o(".back_action").click(function(){history.go(-1)}),o(".open-signup").click(function(){o(".home-sign-up-popup .buttons-popup").css("display","none"),o(".home-sign-up-popup .form-popup").css("display","flex")}),o(".home-top-content .join-button").click(function(){e()}),o(".home-sign-up-popup .close-site-popup").click(function(){n()})}),o(window).resize(function(){o(".back_action").click(function(){history.go(-1)})});var c;s()}(jQuery);