<?php
    defined('_JEXEC') or die('Restricted access');
    $userData=$this->data;
?>

<div class="full-user-page col-sm-10 col-sm-offset-1 col-xs-12">
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
        <div class="profile-content-top padding-null col-xs-12">
            <h1 class="title"><?php print JText::sprintf('SAY_HELLO', $userData->u_name); ?></h1>
            <div class="external-links-top">
                <?php if($this->isFriends) { ?>
                    <a href="<?php print $this->links['send_message']; ?>"><?php print JText::_('FULL_PROFILE_SEND_MESSAGE'); ?></a>
                    <a href="<?php print $this->links['lincup']; ?>"><?php print JText::_('FULL_PROFILE_LINCUP'); ?></a>
                <?php } else if(!$this->user_is_accept && !$this->is_i_filed_claim && $this->isset_tokens_add_to_friends){ ?>
                    <span class="send-token" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('FULL_PROFILE_ADD_FRIEND'); ?><span class="token"></span></span>
                <?php } else if($this->user_is_accept && $this->isset_tokens_add_to_friends){ ?>
                    <span class="accept-token" data-user="<?php print $userData->user_id; ?>"><?php print JText::sprintf('FULL_PROFILE_ACCEPT_FRIEND', $userData->u_name); ?><span class="token"></span></span>
                <?php }
				else { ?>
				<font  class="token_sent">Friend request sent!</font>
				<?php } ?>
            </div>
        </div>
        <div class="profile-content-left col-sm-5 col-xs-12">
            <div class="photo">
                <img src="<?php echo $userData->photosite; ?>" alt="<?php echo $userData->photosite; ?>">
                <?php if(count($userData->images_album['images']) > 0) {?>
                    <span class="open-photo"></span>
                <?php } ?>
            </div>
            <div class="points">
                <?php foreach($userData->images_album['images'] as $key => $value){ ?>
                    <span class="<?php print $key; ?>"></span>
                <?php } ?>
            </div>
                <span class="localisation">
                    <?php print $userData->city . ", " . $userData->state . ", " . JText::sprintf('MILES_AWAY', $userData->distance); ?>
                    <span class="yellow">|</span>
                    <?php print $userData->sex; ?>
                </span>
            <span class="last-online"><?php print JText::_('LAST_ONLINE'); ?><?php print $userData->last_visit; ?></span>
            <div class="my-stats"><?php print JText::_('MY_STATS'); ?></div>
            <span class="inf"><?php print JText::_('AGE'); ?> <span class="age"><?php print $userData->age; ?></span></span>
            <span class="inf"><?php print JText::_('HEIGHT'); ?> <span class="height"><?php print $userData->height; ?></span></span>
            <span class="inf"><?php print JText::_('STATUS'); ?> <span class="status"><?php print $userData->status; ?></span></span>
            <span class="inf"><?php print JText::_('LOOKING_FOR'); ?> <span class="look"><?php print $userData->looking_for; ?></span></span>
            <span class="inf"><?php print JText::_('RELATIONSHIP_TYPE'); ?> <span class="type"><?php print $userData->relationship_type; ?></span></span>
            <!--<span class="inf"><?php /*print JText::_('ETHNICITY'); */?> <span class="ethnicity"><?php /*print $userData->ethnicity; */?></span></span>
            <span class="inf"><?php /*print JText::_('BODY'); */?> <span class="body"><?php /*print $userData->body; */?></span></span>
            <span class="inf"><?php /*print JText::_('PROFESSION'); */?> <span class="profession"><?php /*print $userData->profession; */?></span></span>
            <span class="inf"><?php /*print JText::_('RELIGION'); */?> <span class="religion"><?php /*print $userData->religion; */?></span></span>
            <span class="inf"><?php /*print JText::_('KIDS'); */?> <span class="kids"><?php /*print $userData->kids; */?></span></span>-->
        </div>

        <div class="profile-content-right col-sm-7 col-xs-12">
            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_COOL'); ?></span>
                <span class="block-answer"><?php print $userData->user_about; ?></span>
            </div>

            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_QUALITIES'); ?></span>
                <span class="block-answer"><?php print $userData->look_qualites; ?></span>
            </div>

            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_RECOMMEND'); ?></span>
                <span class="block-answer"><?php print $userData->recommend; ?></span>
            </div>

            <div class="block-info">
                <span class="block-question"><?php print JText::_('YOUR_FEW_PLACES'); ?></span>
                <span class="block-answer"><?php print $userData->few_places; ?></span>
            </div>
        </div>

        <div class="user-options">
            <a href="<?php print $this->links['questions']?>" class="questions-answers" title="Questions & Answers"><?php print JText::_('USER_QUESTIONS'); ?></a>
            <a href="<?php print $this->links['honesty_reviews']?>" class="honesty-reviews" title="(<?php print $userData->user_reviews; ?>) Honesty Reviews"><span class="honesty-count"><?php print $userData->user_reviews; ?></span><?php print JText::_('USER_HONESTY_REVIEWS'); ?></a>
            <?php if($this->exist_private){?>
                <?php if($this->isFriends){ ?>
                    <a href="<?php print $this->links['private']?>" class="private-photos" title="View Private Photos"><?php print JText::_('USER_PRIVATE_PHOTOS'); ?></a>
                <?php } else { ?>
                    <a id="privatephotos" class="private-photos" title="View Private Photos"><?php print JText::_('USER_PRIVATE_PHOTOS'); ?></a>
                <?php }?>

            <?php }?>
            <?php if($this->email_verification){ ?>
                <span class="authenticated" title="Email Authenticated"><?php print JText::_('USER_AUTHENTICATED'); ?></span>
            <?php } else { ?>
                <span class="authenticated" title="Email Not Authenticated"><?php print JText::_('USER_AUTHENTICATED'); ?></span>
            <?php } ?>
        </div>
        <div class="external-links-bottom">
            <?php if (!$this->visible) { ?>
                <span class="add-visited" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('FULL_PROFILE_VISITED'); ?></span>
            <?php } ?>
            <?php if ($this->isFriends) { ?>
                <span class="delete-friend" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('FULL_PROFILE_DELETE'); ?></span>
            <?php } else if(!$this->user_is_accept && !$this->is_i_filed_claim && $this->isset_tokens_add_to_friends){ ?>
                <span class="hide-user" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('FULL_PROFILE_HIDE'); ?></span>
            <?php } else if($this->user_is_accept && $this->isset_tokens_add_to_friends){ ?>
                <span class="reject-user" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('FULL_PROFILE_REJECT'); ?></span>
            <?php } ?>

            <?php if (!$this->add_to_bookmarks) { ?>
                <span class="delete-bookmark" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('DELETE_BOOKMARK'); ?></span>
            <?php } else { ?>
                <span class="add-bookmark" data-user="<?php print $userData->user_id; ?>"><?php print JText::_('SAVE_BOOKMARK'); ?></span>
            <?php } ?>
        </div>
    </div>
</div>

<?php if(isset($this->email_referrer)){
    $email_ref = $this->email_referrer;
} else {
    $email_ref = '';
}?>


<?php if(!$this->isFriends){
    $page__box = '<div class="earn-tokens-box col-sm-8 col-sm-offset-2" style="margin-top: 50px;">';
    $page__box.= '<div class="page-popup row">';
    $page__box.= '<h1 class="title">' . JText::_('VIEW_PRIVATE_PHOTOS') . '</h1>';
    $page__box.= '<span class="close-page">X</span>';
    $page__box.= '<span class="earn-tokens-box-info page-info">' . JText::_('NOT_RFIENDS_TO_PRIVATE') . '</span>';
    $page__box.= '</div>';
    $page__box.= '</div>';
    ?>


    <script type="text/javascript">
        jQuery('#privatephotos').click(function(){
            var tmp = jQuery('.full-user-page').html();
            jQuery('.full-user-page').html('<?php print $page__box; ?>');
            window.setTimeout(function(){
                jQuery('.full-user-page').html(tmp);
            }, 3000);
        });
    </script>
<?php } ?>

<script type="text/javascript">
    var photos = <?php print json_encode($userData->images_album['images']); ?>;
    var index = 0;

    function showSliderPhoto(){
        console.log(index);
        document.getElementById('gallery-img').src = "<?php print $userData->images_album['path_to_album']; ?>" + photos[index].photo;
    }

    jQuery('.profile-content-left .photo').click(function(){
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='block';
        index = 0;
        showSliderPhoto();
    });

    jQuery('.profile-content-left .points span').click(function(){
        index = parseInt(jQuery(this).attr('class'));
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='block';
        showSliderPhoto();
    });

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

    jQuery('#close-img').click(function(){
        document.getElementById('hide-content').style.display='none';
        document.getElementById('show-img').style.display='none';
        document.getElementById('gallery-img').src='';
    });

    jQuery('.full-user-page .external-links-bottom .add-visited').click(function () {

        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/add_visited.php',
            data: data_post,
            success: function () {
                jQuery('.full-user-page .external-links-bottom .add-visited').hide();
            },
            error: function (html) {

            }
        });

    });

    jQuery('.full-user-page .external-links-bottom .delete-friend').click(function () {

        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        if (confirm("<?php print JText::_('CONFIRM_DELETE'); ?>")){
            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/delete_from_friends.php',
                data: data_post,
                success: function (data){
                    if(data == 'success'){
                        jQuery(this).html('<span class="success"><?php print JText::_("FRIENDS_DELETE_MESSAGE"); ?></span>');
                    } else {
                        jQuery(this).parent().html('<span class="error"><?php print JText::_("FRIENDS_DELETE_MESSAGE_ERROR"); ?></span>');
                    }
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                },
                error: function (html) {

                }
            });
        }
    });

    jQuery('.full-user-page .external-links-bottom .hide-user').click(function(){
        jQuery(this).attr('disabled',true);
        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/rejected_user.php',
            data: data_post,
            success: function(data){
                if(data == "success"){
                    jQuery(location).attr('href','<?php print 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST'); ?>');
                } else {

                }
            },
            error: function(data){

            }
        });
        return false;
    });

    jQuery('.full-user-page .external-links-bottom .reject-user').click(function(){
        jQuery(this).attr('disabled',true);
        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_rejected.php',
            data: data_post,
            success: function(data){
                if(data == "success"){
                    jQuery(location).attr('href','<?php print 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST'); ?>');
                } else {

                }
            },
            error: function(data){

            }
        });
        return false;
    });

    jQuery('.full-user-page .external-links-bottom .add-bookmark').click(function(){
        jQuery(this).attr('disabled',true);
        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/add_to_bookmark.php',
            data: data_post,
            success: function(data){
                location.reload();
            },
            error: function(data){

            }
        });
        return false;
    });

    jQuery('.full-user-page .external-links-bottom .delete-bookmark').click(function(){
        jQuery(this).attr('disabled',true);
        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        if (confirm("<?php print JText::_('CONFIRM_DELETE'); ?>")){
            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/delete_from_bookmark.php',
                data: data_post,
                success: function(data){
                    location.reload();
                },
                error: function(data){

                }
            });
        }
        return false;
    });

    jQuery('.full-user-page .external-links-top .send-token').click(function(){

        //jQuery('.full-user-page .external-links-top .send-token .token').removeClass('token').addClass('sending-token');
        //jQuery('.full-user-page .external-links-top .send-token .token').addClass('token-animation');

        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/add_to_friends.php',
            data: data_post,
            success: function(data){
                //setTimeout(function () {
                    jQuery(location).attr('href','<?php print 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ADD_TO_FRIENDS') . '?usr=' . $userData->u_name . '&id=' . $userData->user_id; ?>');
                //}, 3000);
            },
            error: function(data){
                console.log(data);
            }
        });
        return false;
    });

    jQuery('.full-user-page .external-links-top .accept-token').click(function(){

        //jQuery('.full-user-page .external-links-top .accept-token .token').removeClass('token').addClass('sending-token');
        //jQuery('.full-user-page .external-links-top .accept-token .token').addClass('token-animation');
        var link='<?php print 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ACCEPT'); ?>?user=' + this.getAttribute('data-user');
        var referer_email='<?php print $email_ref; ?>';

        var data_post = {
            'user_id' : this.getAttribute('data-user')
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/accept_to_friends.php',
            data: data_post,
            success: function(data){
                if(referer_email != ''){
                    sendEmailReferrer(referer_email);
                }
                //setTimeout(function () {
                    jQuery(location).attr('href', link);
                //}, 3000);
            },
            error: function(data){
                console.log(data);
            }
        });
        return false;
    });
</script>