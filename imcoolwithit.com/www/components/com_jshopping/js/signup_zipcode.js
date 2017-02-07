jQuery(function(){
        // OnKeyDown Function
    var timer;

    jQuery("#zip").keyup(function(){
        var zip_in = jQuery(this);

        clearTimeout(timer);
        timer = setTimeout(function(){

            if (zip_in.val().length < 5) {
                jQuery('.form_sign_up .submit-button').attr('disabled',true);
                jQuery('.zip-error').removeClass('form-success').addClass('form-error').html('Invalid Postal Code');
            } else if (zip_in.val().length > 5) {
                jQuery('.form_sign_up .submit-button').attr('disabled',true);
                jQuery('.zip-error').removeClass('form-success').addClass('form-error').html('Invalid Postal Code');
            } else if ((zip_in.val().length == 5)) {

                // Make HTTP Request
                jQuery.ajax({
                    url: "https://api.zippopotam.us/us/" + zip_in.val(),
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function(result){

                        // US Zip Code Records Officially Map to only 1 Primary Location
                        places = result['places'][0];
                        jQuery("#city").val(places['place name']);
                        jQuery("#cityDisable").val(places['place name']);
                        jQuery("#state").val(places['state']);
                        jQuery("#stateDisable").val(places['state']);
                        jQuery("#longitude").val(places['longitude']);
                        jQuery("#latitude").val(places['latitude']);

                        jQuery('.form_sign_up .submit-button').attr('disabled',false);
                        jQuery('.zip-error').removeClass('form-error').addClass('form-success').html('Found!!!');
                    },
                    error: function() {
                        jQuery('.form_sign_up .submit-button').attr('disabled',true);
                        jQuery('.zip-error').removeClass('form-success').addClass('form-error').html('Invalid Postal Code');
                    }
                });
            }
        }, 1000);

    });

});

(function(){
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();