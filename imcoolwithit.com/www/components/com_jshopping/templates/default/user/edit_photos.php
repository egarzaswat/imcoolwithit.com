<?php
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$pathToJS = JURI::root().'components/com_jshopping/js/';
$document->addScript($pathToJS.'ga.js');
$document->addScript($pathToJS.'zipcode.js');
$document->addScript($pathToJS.'zipcode.js');
$document->addScript($pathToJS.'js_crop_image/jquery.min.js');
$document->addScript($pathToJS.'js_crop_image/jquery.Jcrop.js');
?>

<script type="text/javascript">

    function initJcrop(){
        var width_img = <?php print $this->image_avatar['avatar_w']; ?>;
        var height_img = <?php print $this->image_avatar['avatar_h']; ?>;

        jQuery('#cropbox').Jcrop({
            aspectRatio: width_img/height_img,
            minSize: [width_img, height_img],
            onSelect: updateCoords,
            setSelect: [ 0, 0, width_img, height_img ]
        });
    }

    function updateCoords(c) {
        jQuery('#x_coord').val(c.x);
        jQuery('#y_coord').val(c.y);
        jQuery('#w_coord').val(c.w);
        jQuery('#h_coord').val(c.h);
        checkCoords();
    }

    function checkCoords(){
        if (parseInt(jQuery('#w_coord').val())) return true;
        alert('Please select a crop region then press submit.');
        return false;
    }

</script>

<div class="hidden_block">
    <div class="black_background"></div>
    <div class="image_crop">
        <img src="" id="cropbox" />
        <span class="submit_image">Save</span>
        <span class="cancel_crop">Cancel</span>
    </div>
    <input type="hidden" id="x_coord" name="x" />
    <input type="hidden" id="y_coord" name="y" />
    <input type="hidden" id="w_coord" name="w" />
    <input type="hidden" id="h_coord" name="h" />
</div>

<div class="edit-photos col-sm-8 col-sm-offset-2 col-xs-12">
    <div id="hide-content">
        <div id="show-img">
            <div class="img-slider">
                <img id="gallery-img" src=""/>
                <span id="close-img"></span>
                <span id="prev-img" class="controls" data-index="#"></span>
                <span id="next-img" class="controls" data-index="#"></span>
            </div>
        </div>
    </div>

    <div id="hide-fb-content">
        <div class="page-content row" id="show-fb-img"></div>
    </div>

    <div class="page-content row">
        <h1 class="title col-xs-12"><?php print JText::_('EDIT_PHOTOS_TITLE')?></h1>
        <div class="edit-photos-left col-sm-5 col-xs-12">
            <div class="type-settings">
                <span class="set-private-photo"><span class="flaticon-tool686"></span><?php print JText::_('EDIT_PHOTOS_SET_PRIVATE'); ?></span>
                <span class="delete-photo"><span class="flaticon-garbage21"></span><?php print JText::_('EDIT_PHOTOS_DELETE'); ?></span>
            </div>
            <div class="type-upload">
                <img src="<?php print $this->image_avatar['src']; ?>"/>
                <?php if($this->limit_upload_images){ ?>
                    <?php if ($this->permission_upload_photo == 1) { ?>
                        <div class="type-upload-buttons">
                            <input id="upload-photo" class="upload-photo" type="FILE" name="imgupload_to_album">
                            <label class="upload-photo" for="upload-photo"><?php print JText::_('EDIT_PHOTOS_UPLOAD_PHOTO'); ?></label>
                            <?php if($_SESSION['fb_1618118001736353_user_id'] && $_SESSION['fb_1618118001736353_access_token']) {?>
                                <span id="upload-from-facebook" class="upload-photo"><?php print JText::_('EDIT_PHOTOS_UPLOAD_FACEBOOK'); ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <span class="limit-upload"><?php print JText::sprintf('EDIT_PHOTOS_LIMIT_UPLOAD', $this->count_limit_upload_images); ?></span>
                <?php } ?>
            </div>
        </div>
        <div class="edit-photos-right col-sm-7 col-xs-12">

            <div class="album public-photos">
                <?php if (count($this->images_album['images']) != 0) { ?>
                    <div class="album-title"><?php print JText::_('EDIT_PHOTOS_PUBLIC_PHOTOS'); ?></div>
                    <ul>
                        <?php foreach ($this->images_album['images'] as $key => $value) { ?>
                            <li>
                                <div class="visible-xs-block visible-sm-block actions-block">
                                    <input type="button" class="private<?php if ($value->private) {
                                        print " active";
                                    } ?>" data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                    <input type="button" class="delete"
                                           data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                    <input type="button" class="rotate"
                                           data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                </div>

                                <input type="button" class="visible-xs-block visible-sm-block set-avatar"
                                       data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>" value="<?php print JText::_('EDIT_PHOTOS_SET_AS_PROFILE_PIC') ?>"/>
                                <img
                                    src="<?php print $this->images_album['path_to_thumb'] . $value->photo; ?>"
                                    data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>

            <div class="album private-photos">
                <?php if (count($this->images_album['private_images']) != 0) { ?>
                    <div class="album-title"><?php print JText::_('EDIT_PHOTOS_YOUR_PRIVATE_PHOTOS'); ?></div>
                    <ul>
                        <?php foreach ($this->images_album['private_images'] as $key => $value) { ?>
                            <li>
                                <div class="visible-xs-block visible-sm-block actions-block">
                                    <input type="button" class="private<?php if ($value->private) {print " active";} ?>"
                                           data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                    <input type="button" class="delete"
                                           data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                    <input type="button" class="rotate"
                                           data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                                </div>
                                <!--                                <input type="button" class="visible-xs-block visible-sm-block set-avatar"
                                       data-source="<?php /*print $this->images_album['path_to_album'] . $value->photo; */?>" value="Set Avatar"/>-->
                                <img
                                    src="<?php print $this->images_album['path_to_thumb'] . $value->photo; ?>"
                                    data-source="<?php print $this->images_album['path_to_album'] . $value->photo; ?>"/>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <div class="page-footer col-xs-12"></div>
    </div>
</div>

<div class="loading-block">
    <img src="/templates/protostar/images/system/loading.gif">
</div>

<script type="text/javascript">

    function savePhotoIbBase(image) {
        var msg = {
            'image_name': image,
            'user': '<?php print JSFactory::getUser()->user_id; ?>'
        };

        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/save_in_base.php',
            data: msg,
            success: function (data) {
                location.reload();
            },
            error: function (data) {

            }
        });
    }

    jQuery('.submit_image').click(function () {
        if (!parseInt(jQuery('#w_coord').val())) {
            return false;
        } else {
            var msg = {
                'x': jQuery('#x_coord').val(),
                'y': jQuery('#y_coord').val(),
                'w': jQuery('#w_coord').val(),
                'h': jQuery('#h_coord').val(),
                'image': jQuery('#cropbox').attr('src')
            };

            jQuery.ajax({
                type: 'POST',
                url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/crop_photo.php',
                data: msg,
                success: function (data) {
                    jQuery('submit_image').html('Success!');
                },
                error: function (data) {

                }
            });

            setTimeout(function () {
                var msg_resize = {
                    'image': jQuery('#cropbox').attr('src')
                };

                jQuery.ajax({
                    type: 'POST',
                    url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/resize_photo.php',
                    data: msg_resize,
                    success: function (data) {
                        savePhotoIbBase(data);
                    },
                    error: function (data) {

                    }
                });
            }, 1000);
        }
    });

    jQuery('.cancel_crop').click(function(){
        jQuery('.hidden_block .image_crop img').attr("src", '');
        initJcrop();
        jQuery('.hidden_block').hide();
    });

    jQuery('.form_content .upload_image').on('change', function () {
        var path_to_photo = '<?php print $this->image_avatar['path_to_load']; ?>';
        var InputFiles = this.files;
        var data = new FormData();
        data.append('file', InputFiles[0]);
        data.append('user', '<?php print JSFactory::getUser()->u_name; ?>');
        data.append('path', path_to_photo);

        jQuery.ajax({
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/upload_photo.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                if(data == 'Error format photo!'){
                    alert('<?php print JText::_('ERROR_FORMAT_PHOTO'); ?>');
                } else if(data == 'Error size photo!') {
                    alert('<?php print JText::_('ERROR_SIZE_PHOTO'); ?>');
                } else {
                    jQuery('.hidden_block .image_crop img').attr("src", path_to_photo + data);
                    initJcrop();
                    jQuery('.hidden_block').show();
                }
            },
            error: function (data) {
                jQuery('.form_image').html(data);
            }
        });
    });

    jQuery('.type-upload-buttons #upload-photo').on('change', function () {
        jQuery('.loading-block').css('display', 'flex');
        var InputFiles = this.files;
        var data = new FormData();
        data.append('file', InputFiles[0]);
        data.append('user', '<?php print JSFactory::getUser()->user_id; ?>');
        data.append('user_name', '<?php print JSFactory::getUser()->u_name; ?>');
        data.append('path', '<?php print $this->images_album['path_to_load']; ?>');
        data.append('private', '0');

        var countTotalPhoto = <?php print count($this->images_album['images']) + count($this->images_album['private_images']); ?>

        jQuery.ajax({
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/upload_photo_to_album.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data_) {
                if(data_.indexOf('Error') == -1){
                    if(countTotalPhoto > 0){
                        location.reload();
                    } else {
                        var data = new FormData();
                        data.append('image', '<?php print $this->images_album['path_to_album']; ?>' + data_);

                        jQuery.ajax({
                            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/upload_photo_from_album.php',
                            data: data,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success: function (data) {
                                jQuery('.hidden_block .image_crop img').attr("src", '<?php print $this->image_avatar['path_to_load']; ?>' + data);
                                initJcrop();
                                jQuery('.hidden_block').show();
                            },
                            error: function (data) {
                                jQuery('.form_image').html(data);
                            }
                        });
                    }
                } else {
                    alert(data_);
                }
            },
            error: function (data_) {
                jQuery('.form_image').html(data_);
            }
        });
    });

    function deleteImageFromAlbum(photo) {

        var msg = {
            'image': photo
        };

        if (confirm("<?php print JText::_('EDIT_ACCOUNT_DELETE_CONFIRM'); ?>")) {
            jQuery.ajax({
                type: 'POST',
                url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/delete_photo.php',
                data: msg,
                success: function (data) {
                    if (data == 'success') {
                        location.reload();
                    } else {
                        jQuery('.edit_account_content h1').fadeOut().delay(350);
                        jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                        jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
                    }
                },
                error: function (data) {
                    jQuery('.edit_account_content h1').fadeOut().delay(350);
                    jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                    jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
                }
            });
        }
    }

    function rotateImageFromAlbum(photo) {

        var msg = {
            'image': photo
        };

        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/rotate_photo.php',
            data: msg,
            success: function (data) {
                if (data == 'success') {
                    location.reload();
                } else {
                    jQuery('.edit_account_content h1').fadeOut().delay(350);
                    jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                    jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
                }
            },
            error: function (data) {
                jQuery('.edit_account_content h1').fadeOut().delay(350);
                jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
            }
        });
    }

    function setPrivatePhoto(photo) {

        var msg = {
            'image': photo
        };

        jQuery.ajax({
            type: 'POST',
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/set_private_photo.php',
            data: msg,
            success: function (data) {
                if (data == 'success') {
                    location.reload();
                } else {
                    jQuery('.edit_account_content h1').fadeOut().delay(350);
                    jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                    jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
                }
            },
            error: function (data) {
                jQuery('.edit_account_content h1').fadeOut().delay(350);
                jQuery('.edit_account_content .error').html('<?php print JText::_('SETTINGS_SAVED_ERROR'); ?>').fadeIn().delay(3000).fadeOut().delay(300);
                jQuery('.edit_account_content h1:not(.error)').delay(3500).fadeIn();
            }
        });
    }

    jQuery('.edit-photos-right .album ul li .delete').click(function(){
        deleteImageFromAlbum(this.getAttribute('data-source'));
    });


    jQuery('.edit-photos-right .album ul li .rotate').click(function(){
        rotateImageFromAlbum(this.getAttribute('data-source'));
    });

    jQuery('.edit-photos-right .album ul li .private').click(function(){
        setPrivatePhoto(this.getAttribute('data-source'));
    });

    jQuery('.edit-photos-right .album ul li .set-avatar').click(function(){

        var path_to_photo = '<?php print $this->image_avatar['path_to_load']; ?>';
        console.debug(path_to_photo);

        var data = new FormData();
        data.append('image', this.getAttribute('data-source'));

        jQuery.ajax({
            url: '/components/com_jshopping/controllers/save_data/upload_crop_photo/upload_photo_from_album.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                jQuery('.hidden_block .image_crop img').attr("src", path_to_photo + data);
                initJcrop();
                jQuery('.hidden_block').show();
            },
            error: function (data) {
                jQuery('.form_image').html(data);
            }
        });
    });

    jQuery('.type-upload-buttons #upload-from-facebook').click(function(){
        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/show_facebook_albums.php',
            success: function (data) {
                jQuery('#hide-fb-content').show();
                jQuery('#show-fb-img').html(data).show();
                var img_div=jQuery('.page-content .fb-album .image-style');
                jQuery(img_div).css('height', jQuery(img_div).width()+'px');
            },
            error: function (html) {
            }
        });

    });

    jQuery('.public-photos ul li img').click(function(){
        var index = $(this).parent('li').index();
        document.getElementById('prev-img').addClass('public').removeClass('private').setAttribute('data-index', index);
        document.getElementById('next-img').addClass('public').removeClass('private').setAttribute('data-index', index+2);
        document.getElementById('gallery-img').src=this.getAttribute('data-source');
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='inline-block';
        $('span.controls').trigger('click');
    });

    jQuery('.private-photos ul li img').click(function(){
        var index = $(this).parent('li').index();
        document.getElementById('prev-img').addClass('private').removeClass('public').setAttribute('data-index', index);
        document.getElementById('next-img').addClass('private').removeClass('public').setAttribute('data-index', index+2);
        document.getElementById('gallery-img').src=this.getAttribute('data-source');
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='inline-block';
        $('span.controls').trigger('click');
    });

    jQuery('.controls').click(function(){
        var index = $(this).attr('data-index');
        var newPrevIndex = parseInt(index)-1;
        var newNextIndex = parseInt(newPrevIndex)+2;
        var src;
        var total;
        if($(this).hasClass('public')){
            src = $('.public-photos ul li:nth-child('+ index +') img').attr('data-source');
            total = $('.public-photos ul li').length + 1;
            if(total === newNextIndex){ newNextIndex = 1; }
            if(newPrevIndex === 0){ newPrevIndex = total-1; }
        }
        if($(this).hasClass('private')){
            src = $('.private-photos ul li:nth-child('+ index +') img').attr('data-source');
            total = $('.private-photos ul li').length + 1;
            if(total === newNextIndex){ newNextIndex = 1; }
            if(newPrevIndex === 0){ newPrevIndex = total-1; }
        }
        document.getElementById('gallery-img').src=src;

        /*//hide next button
         if(total === newNextIndex){ $('#next_img').hide(); }else{ $('#next_img').show() }
         //hide previous button
         if(newPrevIndex === 0){ $('#prev_img').hide(); }else{ $('#prev_img').show() }*/

        document.getElementById('prev-img').setAttribute('data-index', newPrevIndex);
        document.getElementById('next-img').setAttribute('data-index', newNextIndex);
    });

    jQuery('#close-img').click(function(){
        document.getElementById('hide-content').style.display='none';
        document.getElementById('show-img').style.display='none';
        document.getElementById('gallery-img').src='';
    });

</script>