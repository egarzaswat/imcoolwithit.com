<?php //var_dump($this->photos); ?>
<div class="user-photos">
    <div class="user-data">
        <a href="/member/full_profile?user=<?php print $this->data_user->user_id; ?>">
            <img src="<?php print $this->data_user->photosite; ?>" />
            <span class="username"><?php print $this->data_user->u_name; ?></span>
        </a>
    </div>
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
    jQuery('.container-full.backg-gr').removeClass('backg-gr');
    jQuery('.user-photos .actions-block .like').click(function () {
        var data_post = {
            'photo_id' : this.getAttribute('data-photo'),
            'user_id' : '<?php print $this->data_user->user_id; ?>'
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