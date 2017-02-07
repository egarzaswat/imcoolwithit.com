jQuery(function(){

    jQuery(document).ready(function(){
        isValid();
    });

        // OnKeyDown Function
    jQuery("#zip").keyup(function(){
        isValid();
    });

    function isValid(){
        var zip_in = jQuery("#zip");

        if (zip_in.val().length<5){
            jQuery('.container-fluid button').attr('disabled',true);
            jQuery('.container-fluid button:last').attr('disabled',false);
            jQuery('.zip_error').html('Invalid Postal Code');
            jQuery("#city").val('');
            jQuery("#state").val('');
            jQuery("#longitude").val('');
            jQuery("#latitude").val('');
        }else if(zip_in.val().length>5){
            jQuery('.container-fluid button').attr('disabled',true);
            jQuery('.zip_error').html('Invalid Postal Code');
            jQuery("#city").val('');
            jQuery("#state").val('');
            jQuery("#longitude").val('');
            jQuery("#latitude").val('');
        }else if ((zip_in.val().length == 5)){

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
                    jQuery("#state").val(places['state']);
                    jQuery("#longitude").val(places['longitude']);
                    jQuery("#latitude").val(places['latitude']);
                    jQuery('.zip_error').html('');
                    jQuery('.container-fluid button').attr('disabled',false);
                },
                error: function() {
                    jQuery('.zip_error').html('Invalid Postal Code');

                    jQuery("#city").val('');
                    jQuery("#state").val('');
                    jQuery("#longitude").val('');
                    jQuery("#latitude").val('');
                }
            });
        }
    }

});

(function(){
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();