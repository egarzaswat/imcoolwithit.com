<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="messaging col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">

        <h1 class="title col-xs-12"><?php print JText::sprintf('MESSAGING_TITLE', $this->friend_data->u_name); ?></h1>

        <div class="interlocutor col-xs-12">
            <a class="interlocutor-link" href="<?php print $this->friend_data->link; ?>">
                <img class="user-image" src="<?php print_r($this->friend_data->photo); ?>">
            </a>
        </div>

        <div class="messaging-list padding-null col-xs-12">
            <span class="load-old-messages"><?php echo JText::_('SHOW_OLD_MESSAGES'); ?></span>
            <div id="load-messages"></div>
        </div>

        <?php if($this->friend_data->permission_write) { ?>
            <form id="messageForm" class="messaging-form padding-null col-xs-12">
                <div class="smiles-list">
                    <span class="sm_adore smile"></span>
                    <span class="sm_aggressive smile"></span>
                    <span class="sm_angry smile"></span>
                    <span class="sm_badly smile"></span>
                    <span class="sm_beuptonogood smile"></span>
                    <span class="sm_beaten smile"></span>
                    <span class="sm_beer smile"></span>
                    <span class="sm_bigsmile smile"></span>
                    <span class="sm_bomb smile"></span>
                    <span class="sm_boring smile"></span>
                    <span class="sm_call smile"></span>
                    <span class="sm_cocktail smile"></span>
                    <span class="sm_coffee smile"></span>
                    <span class="sm_cold smile"></span>
                    <span class="sm_cool smile"></span>
                    <span class="sm_cry smile"></span>
                    <span class="sm_crying smile"></span>
                    <span class="sm_despair smile"></span>
                    <span class="sm_disappointment smile"></span>
                    <span class="sm_disgust smile"></span>
                    <span class="sm_dizzy smile"></span>
                    <span class="sm_easymoney smile"></span>
                    <span class="sm_exclamation smile"></span>
                    <span class="sm_expressionless smile"></span>
                    <span class="sm_facepalm smile"></span>
                    <span class="sm_frown smile"></span>
                    <span class="sm_frustrated smile"></span>
                    <span class="sm_furious smile"></span>
                    <span class="sm_giggle smile"></span>
                    <span class="sm_happy smile"></span>
                    <span class="sm_hi smile"></span>
                    <span class="sm_hug smile"></span>
                    <span class="sm_hungry smile"></span>
                    <span class="sm_hypnotic smile"></span>
                    <span class="sm_hysterical smile"></span>
                    <span class="sm_idea smile"></span>
                    <span class="sm_impish smile"></span>
                    <span class="sm_kiss smile"></span>
                    <span class="sm_kissed smile"></span>
                    <span class="sm_lol smile"></span>
                    <span class="sm_laugh smile"></span>
                    <span class="sm_monocle smile"></span>
                    <span class="sm_movie smile"></span>
                    <span class="sm_music smile"></span>
                    <span class="sm_nerd smile"></span>
                    <span class="sm_ninja smile"></span>
                    <span class="sm_party smile"></span>
                    <span class="sm_pirate smile"></span>
                    <span class="sm_pudently smile"></span>
                    <span class="sm_question smile"></span>
                    <span class="sm_rage smile"></span>
                    <span class="sm_rose smile"></span>
                    <span class="sm_sad smile"></span>
                    <span class="sm_satisfied smile"></span>
                    <span class="sm_scared smile"></span>
                    <span class="sm_shock smile"></span>
                    <span class="sm_sick smile"></span>
                    <span class="sm_singing smile"></span>
                    <span class="sm_sleep smile"></span>
                    <span class="sm_smile smile"></span>
                    <span class="sm_smoking smile"></span>
                    <span class="sm_snotty smile"></span>
                    <span class="sm_sorry smile"></span>
                    <span class="sm_stars smile"></span>
                    <span class="sm_stop smile"></span>
                    <span class="sm_stressed smile"></span>
                    <span class="sm_struggle smile"></span>
                    <span class="sm_study smile"></span>
                    <span class="sm_surprise smile"></span>
                    <span class="sm_sweat smile"></span>
                    <span class="sm_sweetangel smile"></span>
                    <span class="sm_thinking smile"></span>
                    <span class="sm_thumbsdown smile"></span>
                    <span class="sm_thumbsup smile"></span>
                    <span class="sm_waiting smile"></span>
                    <span class="sm_whistling smile"></span>
                    <span class="sm_wink smile"></span>
                    <span class="sm_woo smile"></span>
                    <span class="sm_wornout smile"></span>
                    <span class="sm_yawn smile"></span>
                </div>
                <?php if (!isset($friend_id) || $friend_id == 0 || !$modelUser->existUser($friend_id)) { ?>
                    <div class="form-elements row">
                        <div class="padding-null col-xs-9">
                            <div class="input-text">
                                <input type="text" class="message-send" name="message-send" placeholder="<?php echo JText::_('MESSAGE_PLACEHOLDER'); ?>"  AUTOCOMPLETE="off">
                                <span class="show-smiles"></span>
                            </div>
                        </div>
                        <div class="send-text col-xs-3">
                            <input class="submit-button" type="submit" name="sendMessage" value="<?php print JText::_('MESSAGE_SEND'); ?>">
                        </div>
                    </div>
               <?php } ?>
            </form>
        <?php } ?>

        <div class="page-footer col-xs-12"></div>

    </div>

</div>

<script type="text/javascript">

    var count_load_messages=5;
    jQuery(document).ready(function(){
        count_messages = 0;
        loadMessages(false);
        getCountMessages(1);
        setInterval(function () {
            getCountMessages(0);
        }, 3000);
    });

    jQuery('.messaging-form .form-elements .show-smiles').click(function(){
        jQuery('.messaging-form .smiles-list').toggle();
    });

    jQuery('.messaging-form .smiles-list .smile').click(function(){
        jQuery('.messaging-form .smiles-list').toggle();
        var data_post = {
            'class' : jQuery(this).attr('class')
        };
        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/smiles.php',
            data: data_post,
            success: function(data){
                jQuery("#messageForm .message-send").val(jQuery("#messageForm .message-send").val() + data).focus();
            }
        });
    });

    jQuery('#messageForm').submit(function(){
        count_load_messages=5;
        if(jQuery("#messageForm .message-send").val() != ""){
            var data_post = {
                'message' : jQuery("#messageForm .message-send").val(),
                'friend_id' : <?php echo $this->friend_data->user_id; ?>,
                'count_load_messages' : count_load_messages
            };
            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/send_update_messages.php',
                data: data_post,
                success: function(html){
                    jQuery('#load-messages').html(html).scrollTop(100000);
                    jQuery('#messageForm input[type=text]').attr('value', '');
//                    jQuery('.read_messages .messages_list .input_message').html('');
                },
                error: function(html){

                }
            });
        }
        return false;
    });

    jQuery('.load-old-messages').click(function(){
        count_load_messages = count_load_messages+10;
        loadMessages(true);
        return false;
    });

    function loadMessages(scroll){
        var data_post = {
            'older_message' : 1,
            'friend_id' : <?php echo $this->friend_data->user_id; ?>,
            'count_load_messages' : count_load_messages
        };
        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/send_update_messages.php',
            data: data_post,
            success: function(html){
                if(scroll){
                    jQuery('#load-messages').html(html).scrollTop(0);
                } else {
                    jQuery('#load-messages').html(html).scrollTop(1000000);
                }
            },

            error: function(html){

            }
        });
    }

    function getCountMessages(init){
        var data_post_get_count_messages = {
            'friend_id' : <?php echo $this->friend_data->user_id; ?>
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/get_count_chat_messages.php',
            data: data_post_get_count_messages,
            success: function(html){
                if(parseInt(html) != 'NaN'){
                    if(init == 0){
                        if(count_messages != html){
                            count_messages = html;
                            loadMessages();
                        }
                    } else {
                        if(html > count_load_messages){
                            jQuery('.load-old-messages').show();
                        }
                    }
                }
            },
            error: function(html){

            }
        });
    }

    jQuery('.messaging-form .form-elements .message-send').keyup(function(e){
        jQuery('.messaging-form .form-elements .message-send').animate({scrollLeft: 10000}, 0);
    });

</script>