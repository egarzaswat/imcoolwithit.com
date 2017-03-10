<?php //var_dump($this->photos); ?>
<div class="user-photos">
    <?php foreach($this->photos as $key => $value){ ?>
        <div class="photo">
            <div class="block">
                <img src="<?php print $value['photo']; ?>"/>
            </div>
            <div class="actions-block">
                <?php if($value['like']){ ?>
                    <span class="liked"><img src="/templates/protostar/images/system/liked.png" /></span>
                <?php } else { ?>
                    <span class="like" data-photo="<?php print $value['id']; ?>"><img src="/templates/protostar/images/system/like.png" /></span>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">
    jQuery('.user-photos .actions-block .like').click(function () {
        var data_post = {
            'photo_id' : this.getAttribute('data-photo')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/like_photo.php',
            data: data_post,
            success: function () {
                location.reload();
            },
            error: function (html) {}
        });
    });
</script>