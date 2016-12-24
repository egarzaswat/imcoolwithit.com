<?php
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>

<div class="profile col-sm-10 col-sm-offset-1 col-xs-12">

    <div id="hide-content">
        <div id="show-img">
            <div class="img-slider">
                <img id="gallery-img" src=""/>
                <span id="close-img">X</span>
                <span id="prev-img"></span>
                <span id="next-img"></span>
            </div>
        </div>
    </div>

    <div class="page-content row">

        <div class="profile-content-left col-sm-5 col-xs-12">
            <div class="photo">
                <?php if(strpos($this->user->photosite, 'no-image') !== false) { ?>
                    <a href="<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_PHOTOS');?>">
                <?php } ?>
                <img src="<?php echo $this->user->photosite; ?>" alt="<?php echo $this->user->photosite; ?>">
                <?php if(strpos($this->user->photosite, 'no-image') === false) { ?>
                    </a>
                <?php } ?>

                <?php if(count($this->user->images_album['images']) > 0) {?>
                    <span class="open-photo"></span>
                <?php } ?>
            </div>
            <div class="points">
                <?php foreach($this->user->images_album['images'] as $key => $value){ ?>
                    <span class="<?php print $key; ?>"></span>
                <?php } ?>
            </div>
            <a class="link-button" href="<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EDIT_PHOTOS');?>">Edit Photos</a>
            <span class="localisation">
                <?php print $this->user->city . ", " . $this->user->state; ?>
                <span class="yellow">|</span>
                <?php print $this->user->sex; ?>
            </span>
<!--            <span class="last-online">--><?php //print JText::_('LAST_ONLINE'); ?><!----><?php //print $this->user->last_visit; ?><!--</span>-->
            <div class="my-stats"><?php print JText::_('MY_STATS'); ?><span id="edit-stats" class="edit"></span></div>
            <span class="inf"><?php print JText::_('AGE'); ?> <span class="age"><?php print $this->user->age; ?></span></span>
            <span class="inf"><?php print JText::_('HEIGHT'); ?> <span class="height"><?php print $this->user->height; ?></span></span>
            <span class="inf"><?php print JText::_('STATUS'); ?> <span class="status"><?php print $this->user->status; ?></span></span>
            <span class="inf"><?php print JText::_('LOOKING_FOR'); ?> <span class="look"><?php print $this->user->looking_for; ?></span></span>
           <!-- <span class="inf"><?php /*print JText::_('ETHNICITY'); */?> <span class="ethnicity"><?php /*print $this->user->ethnicity; */?></span></span>
            <span class="inf"><?php /*print JText::_('BODY'); */?> <span class="body"><?php /*print $this->user->body; */?></span></span>
            <span class="inf"><?php /*print JText::_('PROFESSION'); */?> <span class="profession"><?php /*print $this->user->profession; */?></span></span>
            <span class="inf"><?php /*print JText::_('RELIGION'); */?> <span class="religion"><?php /*print $this->user->religion; */?></span></span>
            <span class="inf"><?php /*print JText::_('KIDS'); */?> <span class="kids"><?php /*print $this->user->kids; */?></span></span>-->
        </div>

        <div class="profile-content-right col-sm-7 col-xs-12">
            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_COOL'); ?><span id="edit-save" class="edit"></span></span>
                <span id="user_about" class="block-answer"><?php print $this->user->user_about; ?></span>
            </div>
            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_QUALITIES'); ?><span id="edit-save" class="edit"></span></span>
                <span id="look_qualites" class="block-answer"><?php print $this->user->look_qualites; ?></span>
            </div>
            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_RECOMMEND'); ?><span id="edit-save" class="edit"></span></span>
                <span id="recommend" class="block-answer"><?php print $this->user->recommend; ?></span>
            </div>
            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_FEW_PLACES'); ?><span id="edit-save" class="edit"></span></span>
                <span id="few_places" class="block-answer"><?php print $this->user->few_places; ?></span>
            </div>
        </div>

        <div class="user-options text-none-select">
            <a href="<?php print $this->link_Q_n_A; ?>" class="questions-answers" title="Questions & Answers"><?php print JText::_('USER_QUESTIONS'); ?></a>
            <a href="<?php print $this->link_honesty_reviews; ?>" class="honesty-reviews" title="(<?php print $this->user->user_reviews; ?>) Honesty Reviews"><span class="honesty-count"><?php print $this->user->user_reviews; ?></span><?php print JText::_('USER_HONESTY_REVIEWS'); ?></a>
            <a href="<?php print $this->link_private_photos; ?>" class="private-photos" title="View Private Photos"><?php print JText::_('USER_PRIVATE_PHOTOS'); ?></a>
            <?php if($this->verified){ ?>
                <span class="authenticated" title="Email Authenticated"><?php print JText::_('USER_AUTHENTICATED'); ?></span>
            <?php } else { ?>
                <span class="authenticated" title="Email Not Authenticated"><?php print JText::_('USER_AUTHENTICATED'); ?></span>
            <?php } ?>
        </div>

    </div>

</div>

<?php $json_arr_images = json_encode($this->user->images_album['images']); ?>

<style>
    @media (max-width: 500px){
        .user-options:after{
            display: none;
        }
    }
</style>

<script type="text/javascript">

    if(jQuery(window).width() < 500){
        jQuery('.profile').css('margin-top', '-25px');
        jQuery('.profile .profile-content-left .photo img').css({'width':jQuery(window).width(), 'margin-left':'-15px', 'margin-top':'-15px'});
        jQuery('.container-full .container').css('padding', '0');
        jQuery('.user-options').css('right','0');
    }

    jQuery(window).resize(function(){
        if(jQuery(window).width() < 500){
            jQuery('.profile').css('margin-top', '-25px');
            jQuery('.profile .profile-content-left .photo img').css({'width':jQuery(window).width(), 'margin-left':'-15px', 'margin-top':'-15px'});
            jQuery('.container-full .container').css('padding', '0');
            jQuery('.user-options').css('right','0');
        } else {
            jQuery('.profile').css('margin-top', '0');
            jQuery('.profile .profile-content-left .photo img').css({'width': '100%', 'margin-left':'auto', 'margin-top':'0'});
            jQuery('.container-full .container').css('padding', 'inherit');
            jQuery('.user-options').css('right','-17px');
        }
    });
</script>

<?php if(strpos($this->user->photosite, 'no-image') === false) { ?>
    <script type="text/javascript">
        jQuery('.profile .profile-content-left .photo').click(function(){
            index = 0;
            showSliderPhoto();
            document.getElementById('hide-content').style.display='block';
            document.getElementById('show-img').style.display='block';
        });
    </script>
<?php } ?>

<script type="text/javascript">
    var photos = <?php echo $json_arr_images; ?>;
    var index = 0;

    function showSliderPhoto(){
        document.getElementById('gallery-img').src = "<?php print $this->user->images_album['path_to_album']; ?>" + photos[index].photo;
    }

    jQuery('#prev-img').click(function(){
        if(index == 0){
            index = photos.length-1;
        } else {
            index = index - 1;
        }
        showSliderPhoto();
    });

    jQuery('#next-img').click(function(){
        if(index == photos.length-1){
            index = 0;
        } else {
            index = index + 1;
        }
        showSliderPhoto();
    });

    jQuery('.profile .profile-content-left .points span').click(function(){
        index = parseInt(jQuery(this).attr('class'));
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='block';
        showSliderPhoto();
    });

    jQuery('#close-img').click(function(){
        document.getElementById('hide-content').style.display='none';
        document.getElementById('show-img').style.display='none';
        document.getElementById('gallery-img').src='';
    });

    jQuery('.my-stats #edit-stats').click(function(){
        jQuery(this.removeClass('edit'));
        jQuery('.profile-content-left').append('<span class="save">Save</span>');

        var height_value = (jQuery('.profile-content-left .height').html() === '<?php print JText::_('UNKNOWN'); ?>') ? "" : jQuery('.profile-content-left .height').html();

        var height_array = height_value.split("'");
        if(height_array[0] == "" || height_array[0] == "0"){
            height_array[0] = 0;
        } else {
            height_array[0] = parseInt(height_array[0]);
        }

        if(!height_array[1] || height_array[1] == "" || height_array[1] == "0"){
            height_array[1] = 0;
        } else {
            height_array[1] = parseInt(height_array[1]);
        }

        jQuery('.profile-content-left .height').html('<input type="number" class="height_input height_m" name="height_m" required="required" min="0" max="7" step="1" value="' + height_array[0] + '" /> Ft ' +
            '<input type="number" class="height_input height_c" name="height_c" required="required" min="0" max="11" step="1" value="' + height_array[1] + '" /> in');

        var status_value = jQuery('.profile-content-left .status');
        var status_field ='<select name="status">' +
            '<option selected value="">' + '<?php print JText::_('--'); ?>' + '</option>' +
            '<option' + ( (status_value.html() === 'Single')? ' selected ' : '' ) + ' value="Single" >Single</option>' +
            '<option' + ( (status_value.html() === 'Divorced')? ' selected ' : '' ) + ' value="Divorced" >Divorced</option>' +
            '<option' + ( (status_value.html() === 'Widowed')? ' selected ' : '' ) + ' value="Widowed" >Widowed</option>' +
            '<option' + ( (status_value.html() === 'Separated')? ' selected ' : '' ) + ' value="Separated" >Separated</option>' +
            '<option' + ( (status_value.html() === 'Complicated')? ' selected ' : '' ) + ' value="Complicated" >Complicated</option>' +
        '</select>';
        status_value.html(status_field);

        var looking_value = jQuery('.profile-content-left .look');
        var looking_field ='<select name="look">' +
            '<option disabled selected>' + '<?php print JText::_('UNKNOWN'); ?>' + '</option>' +
            '<option' + ( (looking_value.html() === '<?php print JText::_('MALE'); ?>')? ' selected ' : '' ) + ' value="2" ><?php print JText::_('MALE'); ?></option>' +
            '<option' + ( (looking_value.html() === '<?php print JText::_('FEMALE'); ?>')? ' selected ' : '' ) + ' value="1" ><?php print JText::_('FEMALE'); ?></option>' +
            '<option' + ( (looking_value.html() === '<?php print JText::_('EITHER'); ?>')? ' selected ' : '' ) + ' value="3" ><?php print JText::_('EITHER'); ?></option>' +
        '</select>';
        looking_value.html(looking_field);

/*        var ethnicity_value = jQuery('.profile-content-left .ethnicity');
        var ethnicity_field = '<select name="ethnicity">' +
            '<option disabled selected>' + '<?php print JText::_('UNKNOWN'); ?>' + '</option>' +
            '<option' + ( (ethnicity_value.html() === 'Who cares')? ' selected ' : '' ) + ' value="Who cares" >Who cares</option>' +
            '<option' + ( (ethnicity_value.html() === 'Caucasian')? ' selected ' : '' ) + ' value="Caucasian" >Caucasian</option>' +
            '<option' + ( (ethnicity_value.html() === 'Black')? ' selected ' : '' ) + ' value="Black" >Black</option>' +
            '<option' + ( (ethnicity_value.html() === 'Latin')? ' selected ' : '' ) + ' value="Latin" >Latin</option>' +
            '<option' + ( (ethnicity_value.html() === 'African-American')? ' selected ' : '' ) + ' value="African-American" >African-American</option>' +
            '<option' + ( (ethnicity_value.html() === 'Asian')? ' selected ' : '' ) + ' value="Asian" >Asian</option>' +
            '<option' + ( (ethnicity_value.html() === 'European')? ' selected ' : '' ) + ' value="European" >European</option>' +
            '<option' + ( (ethnicity_value.html() === 'Middle Eastern')? ' selected ' : '' ) + ' value="Middle Eastern" >Middle Eastern</option>' +
            '<option' + ( (ethnicity_value.html() === 'Mix of stuff')? ' selected ' : '' ) + ' value="Mix of stuff" >Mix of stuff</option>' +
            '<option' + ( (ethnicity_value.html() === 'Alien')? ' selected ' : '' ) + ' value="Alien" >Alien</option>' +
            '</select>';
        ethnicity_value.html(ethnicity_field);*/

/*        var body_value = jQuery('.profile-content-left .body');
        var body_field = '<select name="ethnicity">' +
            '<option disabled selected>' + '<?php print JText::_('UNKNOWN'); ?>' + '</option>' +
            '<option' + ( (body_value.html() === 'Thin')? ' selected ' : '' ) + ' value="Thin" >Thin</option>' +
            '<option' + ( (body_value.html() === 'Regular')? ' selected ' : '' ) + ' value="Regular" >Regular</option>' +
            '<option' + ( (body_value.html() === 'Fit')? ' selected ' : '' ) + ' value="Fit" >Fit</option>' +
            '<option' + ( (body_value.html() === 'A lil extra')? ' selected ' : '' ) + ' value="A lil extra" >A lil extra</option>' +
            '<option' + ( (body_value.html() === 'BBW')? ' selected ' : '' ) + ' value="BBW" >BBW</option>' +
            '</select>';
        body_value.html(body_field);

        var profession_value = jQuery('.profile-content-left .profession');
        profession_value.html('<input type="text" name="height" maxlength="20" required="required" value="' + ( (profession_value.html() === '<?php print JText::_('UNKNOWN'); ?>')? '' : profession_value.html() ) + '" />');

        var religion_value = jQuery('.profile-content-left .religion');
        var religion_field = '<select name="ethnicity">' +
            '<option disabled selected>' + '<?php print JText::_('UNKNOWN'); ?>' + '</option>' +
            '<option' + ( (religion_value.html() === 'Spiritual')? ' selected ' : '' ) + ' value="Spiritual" >Spiritual</option>' +
            '<option' + ( (religion_value.html() === 'Not-Spiritual')? ' selected ' : '' ) + ' value="Not-Spiritual" >Not-Spiritual</option>' +
            '<option' + ( (religion_value.html() === 'Christian')? ' selected ' : '' ) + ' value="Christian" >Christian</option>' +
            '<option' + ( (religion_value.html() === 'Jewish')? ' selected ' : '' ) + ' value="Jewish" >Jewish</option>' +
            '<option' + ( (religion_value.html() === 'Muslim')? ' selected ' : '' ) + ' value="Muslim" >Muslim</option>' +
            '<option' + ( (religion_value.html() === 'Atheist')? ' selected ' : '' ) + ' value="Atheist" >Atheist</option>' +
            '<option' + ( (religion_value.html() === 'Agnostic')? ' selected ' : '' ) + ' value="Agnostic" >Agnostic</option>' +
            '<option' + ( (religion_value.html() === 'Catholic')? ' selected ' : '' ) + ' value="Catholic" >Catholic</option>' +
            '<option' + ( (religion_value.html() === 'Hindu')? ' selected ' : '' ) + ' value="Hindu" >Hindu</option>' +
            '<option' + ( (religion_value.html() === 'Other')? ' selected ' : '' ) + ' value="Other" >Other</option>' +
            '</select>';
        religion_value.html(religion_field);

        var kids_value = jQuery('.profile-content-left .kids');
        var kids_field = '<select name="ethnicity">' +
            '<option disabled selected>' + '<?php print JText::_('UNKNOWN'); ?>' + '</option>' +
            '<option' + ( (kids_value.html() === 'No, but would like them')? ' selected ' : '' ) + ' value="No, but would like them" >No, but would like them</option>' +
            '<option' + ( (kids_value.html() === 'Yes, and would like more')? ' selected ' : '' ) + ' value="Yes, and would like more" >Yes, and would like more</option>' +
            '<option' + ( (kids_value.html() === 'No, and I do not want any')? ' selected ' : '' ) + ' value="No, and I do not want any" >No, and I do not want any</option>' +
            '<option' + ( (kids_value.html() === 'Yes, but I do not want any more')? ' selected ' : '' ) + ' value="Yes, but I do not want any more" >Yes, but I do not want any more</option>' +
            '<option' + ( (kids_value.html() === 'Not sure')? ' selected ' : '' ) + ' value="Not sure" >Not sure</option>' +
            '</select>';
        kids_value.html(kids_field);*/
    });

    jQuery('.profile-content-left .save').live('click', function(){
        jQuery('.my-stats #edit-stats').addClass('edit');
        jQuery(this).remove();
//        var height_regex = /\d{1,2}'\d{1,2}/;
//        var height_value = jQuery('.profile-content-left .height input').val();
//        var height = height_regex.test(height_value) ? height_value : '';


        var height = jQuery('.height_m').val() + "'" + jQuery('.height_c').val();
        console.debug(jQuery('.profile-content-left .status select').val());
        var msg = {
            'height' : height,
            'status' : jQuery('.profile-content-left .status select').val(),
            'looking_for' : jQuery('.profile-content-left .look select').val(),
            'ethnicity' : jQuery('.profile-content-left .ethnicity select').val(),
            'body' : jQuery('.profile-content-left .body select').val(),
            'profession' : jQuery('.profile-content-left .profession input').val(),
            'religion' : jQuery('.profile-content-left .religion select').val(),
            'kids' : jQuery('.profile-content-left .kids select').val()
        };


        console.log(msg);
        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/save_profile_info.php',
            data: msg,
            success: function(data) {
                if(data=='success'){
                    jQuery('.profile-content-left .height').text( (height === '') ? '<?php print JText::_('UNKNOWN'); ?>' : height);
                    jQuery('.profile-content-left .status').text( (jQuery('.profile-content-left .status select').val() === null) ? '<?php print JText::_(''); ?>' : jQuery('.profile-content-left .status select').val() );
                    var _look = '';
                    var f_look = jQuery('.profile-content-left .look select');
                    if(f_look.val() === null){
                        _look = '<?php print JText::_('UNKNOWN'); ?>';
                    }
                    if(f_look.val() == 1){
                        _look = '<?php print JText::_('FEMALE'); ?>';
                    }
                    if(f_look.val() == 2){
                        _look = '<?php print JText::_('MALE'); ?>';
                    }
                    if(f_look.val() == 3){
                        _look = '<?php print JText::_('EITHER'); ?>';
                    }
                    jQuery('.profile-content-left .look').text(_look);

//                    jQuery('.profile-content-left .look').text( (jQuery('.profile-content-left .look select').val() === null) ? '<?php //print JText::_('UNKNOWN'); ?>//' : jQuery('.profile-content-left .status select').val() );



                    /*jQuery('.profile-content-left .ethnicity').text( (jQuery('.profile-content-left .ethnicity select').val() === null) ? '<?php print JText::_('UNKNOWN'); ?>' : jQuery('.profile-content-left .ethnicity select').val() );
                    jQuery('.profile-content-left .body').text( (jQuery('.profile-content-left .body select').val() === null) ? '<?php print JText::_('UNKNOWN'); ?>' : jQuery('.profile-content-left .body select').val() );
                    jQuery('.profile-content-left .profession').text( (jQuery('.profile-content-left .profession input').val() === '')? '<?php print JText::_('UNKNOWN'); ?>' : jQuery('.profile-content-left .profession input').val() );
                    jQuery('.profile-content-left .religion').text( (jQuery('.profile-content-left .religion select').val() === null) ? '<?php print JText::_('UNKNOWN'); ?>' : jQuery('.profile-content-left .religion select').val() );
                    jQuery('.profile-content-left .kids').text( (jQuery('.profile-content-left .kids select').val() === null) ? '<?php print JText::_('UNKNOWN'); ?>' : jQuery('.profile-content-left .kids select').val() );*/
                } else {
                    console.log(data);
                }
            },
            error:  function(data){
                console.log(data);
            }
        });
    });

    jQuery('.profile-content-right .edit').click(function(){
        var edit_field = jQuery(this).parent().next();
        edit_field_html = '<textarea name="' + edit_field.attr('id') + '" maxlength="200" ';
        id_edit_field = edit_field.attr('id');

        if(edit_field.html() === '<?php print JText::_("MY_COOL_DEFAULT") ?>' || edit_field.html() === '<?php print JText::_("MY_QUALITIES_DEFAULT") ?>' || edit_field.html() === '<?php print JText::_("MY_RECOMMEND_DEFAULT") ?>' || edit_field.html() === '<?php print JText::_("MY_FEW_PLACES_DEFAULT") ?>'){
            edit_field_html = edit_field_html + 'placeholder="' + edit_field.html() + '" >';
        } else {
            edit_field_html = edit_field_html + 'placeholder="';

            if(id_edit_field === 'user_about'){
                edit_field_html = edit_field_html + '<?php print JText::_("MY_COOL_DEFAULT") ?>';
            }

            if(id_edit_field === 'look_qualites'){
                edit_field_html = edit_field_html + '<?php print JText::_("MY_QUALITIES_DEFAULT") ?>';
            }

            if(id_edit_field === 'recommend'){
                edit_field_html = edit_field_html + '<?php print JText::_("MY_RECOMMEND_DEFAULT") ?>';
            }

            if(id_edit_field === 'few_places'){
                edit_field_html = edit_field_html + '<?php print JText::_("MY_FEW_PLACES_DEFAULT") ?>';
            }
            edit_field_html = edit_field_html + '" >' + edit_field.html();
        }

        edit_field_html = edit_field_html + '</textarea>';
        edit_field.html(edit_field_html);
        jQuery(this).hide();
        jQuery(this).parent().append('<span class="save">Save</span>');
    });

    jQuery('.profile .profile-content-right .save').live('click', function(){
        var active_field = jQuery(this).parent().parent();
        var key = active_field.find('textarea').attr('name');
        var msg = {};
        msg[key] = active_field.find('textarea').val();

        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/save_profile_info.php',
            data: msg,
            success: function (data) {
                if (data == 'success') {
                    if(active_field.find('textarea').val() === ''){
                        active_field.find('.block-answer').html(active_field.find('textarea').attr('placeholder'))
                    } else {
                        active_field.find('.block-answer').text(active_field.find('textarea').val());
                    }
                } else {
                    console.log(data);
                }
            },
            error: function (data) {
                console.log(data);
            }
        });

        jQuery(this).parent().find('.edit').show();
        jQuery(this).remove();
    });

</script>