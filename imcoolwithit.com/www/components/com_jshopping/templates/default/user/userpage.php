<?php
    defined('_JEXEC') or die('Restricted access');
    $userData = $this->userData;
?>

<div class="user-page  col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::sprintf('SAY_HELLO', $userData->u_name); ?></h1>

        <div class="user-photo col-xs-12">
            <a href="<?php print $this->link_full_profile; ?>">
                <img src="<?php print $userData->photosite; ?>" alt="<?php print $userData->u_name; ?>"/>
            </a>
        </div>

        <div class="user-short-info col-xs-12">
            <span class="localisation">
                <?php print $userData->city . ", " . $userData->state . ", " . JText::sprintf('MILES_AWAY', $userData->distance); ?>
                <span class="yellow">|</span>
                <?php print $userData->sex; ?>
            </span>
            <span class="last-online"><?php print JText::_('LAST_ONLINE'); ?><?php print $userData->last_visit; ?></span>
        </div>

        <div class="user-long-info col-xs-12">
            <div class="info-block row">
                <div class="left-info-block">
                    <div>
                        <span class="info-title"><?php print JText::_('AGE'); ?></span>
                        <span class="info-value"><?php print $userData->age; ?></span>
                    </div>
                    <div>
                        <span class="info-title"><?php print JText::_('HEIGHT'); ?></span>
                        <span class="info-value"><?php print $userData->height; ?></span>
                    </div>
                    <div>
                        <span class="info-title"><?php print JText::_('BODY'); ?></span>
                        <span class="info-value"><?php print $userData->body; ?></span>
                    </div>
                    <div>
                        <span class="info-title"><?php print JText::_('STATUS'); ?></span>
                        <span class="info-value"><?php print $userData->status; ?></span>
                    </div>
                </div>
                <div class="right-info-block">
                    <span class="info-title"><?php print JText::sprintf('USER_ABOUT', $userData->u_name); ?></span>
                    <span class="info-value"><?php print $userData->user_about; ?></span>
                </div>
            </div>
        </div>

        <div class="user-actions col-xs-12">
            <?php if ($this->user_is_accept) { ?>
                <div class="accept-invite">
                    <input type="submit" data-user="<?php print $userData->user_id; ?>" class="refuse"
                           title="<?php print JText::_('NOT_INTERESTED'); ?>">
                    <span class="separator"></span>
                    <?php if ($this->isset_tokens_add_to_friends) { ?>
                        <input type="submit" data-user="<?php print $userData->user_id; ?>" class="accept"
                               title="<?php print JText::_('COOL'); ?>">
                        <input type="text" data-user="<?php print $userData->user_id; ?>" class="accept-animation"
                               title="<?php print JText::_('COOL'); ?>" style="display: none;">
                    <?php } else { ?>
                        <input type="text" data-user="<?php print $userData->user_id; ?>" class="accept"
                               title="<?php print JText::_('COOL'); ?>">
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="send-invite">
                    <input type="submit" data-user="<?php print $userData->user_id; ?>" class="refuse"
                           title="<?php print JText::_('NOT_INTERESTED'); ?>">
                    <span class="separator"></span>
                    <?php if (!$this->is_i_filed_claim && $this->isset_tokens_add_to_friends && !$this->user_is_friends) { ?>
                        <input type="submit" data-user="<?php print $userData->user_id; ?>" class="accept"
                               title="<?php print JText::_('COOL'); ?>">
                        <input type="text" data-user="<?php print $userData->user_id; ?>" class="accept-animation"
                               title="<?php print JText::_('COOL'); ?>" style="display: none;">
                    <?php } else { ?>
                        <input type="text" data-user="<?php print $userData->user_id; ?>" class="accept" style="opacity: 0.5;"
                               title="<?php print JText::_('SEND_COOL'); ?>">
                    <?php } ?>
                </div>
            <?php } ?>
            <span class="full-profile-link">
                <a href="<?php print $this->link_full_profile; ?>"><?php print JText::_('FULL_PROFILE_LINK'); ?></a>
            </span>
            <?php if (!$userData->add_to_bookmarks) { ?>
                <input type="submit" data-user="<?php print $userData->user_id; ?>" class="delete-bookmark btn btn-primary"
                       value="<?php print JText::_('DELETE_BOOKMARK'); ?>">
            <?php } else { ?>
                <input type="submit" data-user="<?php print $userData->user_id; ?>" class="add-bookmark btn btn-primary"
                       value="<?php print JText::_('SAVE_BOOKMARK'); ?>">
            <?php } ?>
        </div>

    </div>

</div>

<?php if(isset($this->email_referrer)){
    $email_ref = $this->email_referrer;
} else {
    $email_ref = '';
}?>

<?php if($userData->block == 0) { ?>
    <script type="text/javascript">
        function sendEmailReferrer(email){
            var link='<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ACCEPT') . '?user=' . $userData->user_id; ?>';
            var data_post = {
                'email' : email
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/send_email_refer_tokens.php',
                data: data_post,
                success: function(data){
                    setTimeout(function () {
                        jQuery(location).attr('href',link);
                    }, 3000);
                },
                error: function(data){

                }
            });
        }

        jQuery('.user-actions .add-bookmark[type="submit"]').click(function(){
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

        jQuery('.user-actions .delete-bookmark[type="submit"]').click(function(){
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

        jQuery('.send-invite .accept[type="submit"]').click(function(){
            jQuery(this).attr('disabled',true).removeClass('accept').addClass('accept-animation-finish').hide();
            jQuery('.send-invite .accept-animation').show();
//            jQuery(this).attr('disabled',true).addClass('token-fall');
            var data_post = {
                'user_id' : this.getAttribute('data-user')
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/add_to_friends.php',
                data: data_post,
                success: function(data){
                    setTimeout(function () {
                        jQuery('.send-invite .accept-animation-finish').show();
                        jQuery('.send-invite .accept-animation').hide();
                        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ADD_TO_FRIENDS').'?usr='.$userData->u_name; ?>');
                    }, 1700);
                },
                error: function(data){

                }
            });
            return false;
        });

        jQuery('.send-invite .refuse[type="submit"]').click(function(){
            jQuery(this).attr('disabled',true);
//            jQuery(this).attr('disabled',true).addClass('token-reject');
            var data_post = {
                'user_id' : this.getAttribute('data-user')
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/rejected_user.php',
                data: data_post,
                success: function(data){
                    if(data == "success"){
                        jQuery(location).attr('href','<?php print $this->next_users; ?>');
                        /*setTimeout(function () {
                         jQuery(location).attr('href','<?php print $this->next_users; ?>');
                         }, 3000);*/
                    }
                },
                error: function(data){

                }
            });
            return false;
        });

        jQuery('.accept-invite .accept[type="submit"]').click(function(){
            jQuery(this).attr('disabled',true).removeClass('accept').addClass('accept-animation-finish').hide();
            jQuery('.accept-invite .accept-animation').show();
//            jQuery(this).attr('disabled',true).addClass('token-fall');
            var link='<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USER_ACCEPT'); ?>?user=' + this.getAttribute('data-user');
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
                    setTimeout(function () {
                        jQuery('.accept-invite .accept-animation-finish').show();
                        jQuery('.accept-invite .accept-animation').hide();
                        jQuery(location).attr('href',link);
                    }, 1700);
                },
                error: function(data){

                }
            });
            return false;
        });

        jQuery('.accept-invite .refuse[type="submit"]').click(function(){
            jQuery(this).attr('disabled',true);
//            jQuery(this).attr('disabled',true).addClass('token-reject');
            var data_post = {
                'user_id' : this.getAttribute('data-user')
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/accept_rejected.php',
                data: data_post,
                success: function(data){
                    jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST'); ?>');
                    /*setTimeout(function () {
                        jQuery(location).attr('href','<?php print 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_TOKENS_RECEIVED'); ?>');
                    }, 3000);*/
                },
                error: function(data){

                }
            });
            return false;
        });
    </script>
<?php } ?>