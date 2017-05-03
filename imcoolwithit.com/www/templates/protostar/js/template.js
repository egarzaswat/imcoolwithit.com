/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function($)
{
    function showSitePopup() {
        $('.site-popup').css("display", "flex");
    }

    function hideSitePopup() {
        $('.site-popup').css("display", "none");
    }

    $(document).ready(function()
	{
        // $('.container-full').css('min-height', $(window).height()-205);
        // $('.homepage .container-full').css('min-height', $(window).height()-120);
        $('.back_action').click(function(){
            history.go(-1);
        });


        $('.open-signup').click(function () {
            $('.home-sign-up-popup .buttons-popup').css("display", "none");
            $('.home-sign-up-popup .form-popup').css("display", "flex");
        });

        $('.home-top-content .join-button').click(function () {
            showSitePopup();
        });
        $('.home-sign-up-popup .close-site-popup').click(function () {
            hideSitePopup();
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


(function (b, l, e, g, h, f) {
    1 !== parseInt(e.msDoNotTrack || b.doNotTrack || e.doNotTrack, 10) && b.addEventListener("load", function () {
        var r = (new Date).getTime();
        b.galite = b.galite || {};
        var m = new XMLHttpRequest, n = "https://www.google-analytics.com/collect?cid=" + (l.uid = l.uid || Math.random() + "." + Math.random()) + "&v=1&tid=" + galite.UA + "&dl=" + f(h.location.href) + "&ul=en-us&de=UTF-8", a = function (b) {
            var d = "", c;
            for (c in b) {
                if (void 0 === b[c])return !1;
                d += f(b[c])
            }
            return d
        }, p = {
            dt: [h.title], sd: [g.colorDepth, "-bit"], sr: [g.availHeight,
                "x", g.availWidth], vp: [innerWidth, "x", innerHeight], dr: [h.referrer]
        }, k;
        for (k in p) {
            var q = k + "=" + a(p[k]);
            q && (n += "&" + q)
        }
        a = function (b, d) {
            var c = "", a;
            for (a in d)c = "&" + a + "=" + f(d[a]);
            return function () {
                var a = n + c + (galite.anonymizeIp ? "&aip=1" : "") + "&t=" + f(b) + "&z=" + (new Date).getTime();
                if (e.sendBeacon) e.sendBeacon(a); else try {
                    m.open("GET", a, !1), m.send()
                } catch (t) {
                    (new Image).src = a
                }
            }
        };
        setTimeout(a("pageview", null), 100);
        b.addEventListener("unload", a("timing", {
            utc: "JS Dependencies", utv: "unload", utt: (new Date).getTime() -
            r
        }))
    })
})(window, localStorage, navigator, screen, document, encodeURIComponent);