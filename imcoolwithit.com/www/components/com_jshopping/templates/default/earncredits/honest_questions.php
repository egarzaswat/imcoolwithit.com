<?php
defined('_JEXEC') or die('Restricted access');
$this->block_name = 'Honesty Review';
?>

<div class="honesty-questions col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::sprintf('HONEST_QUESTIONS_TITLE', $this->user_data->u_name); ?></h1>

        <div class="earn-tokens-info">
            <div>
                <a class="user-link" href="<?php print $this->user_data->link; ?>">
                    <img class="user-image" src="<?php print $this->user_data->photosite; ?>"/>
                </a>
                <span><?php print JText::_('HONEST_QUESTIONS_INFO'); ?></span>
            </div>
        </div>

        <form id="honesty-questions" class="earn-tokens-list">
            <?php foreach ($this->questions as $key => $value) { ?>
                <div class="earn-tokens-item">
                    <span class="earn-tokens-question"><?php print $value['question']; ?></span>
                        <span class="earn-tokens-answers">
                        <?php foreach ($value['answers'] as $key_answer => $value_answer) { ?>
                            <input id="<?php print $key_answer; ?>" type="radio"
                                   name="<?php print $key; ?>"
                                   value="<?php if (isset($value_answer['negative']) && $value_answer['negative'] == 1) {
                                       print '0';
                                   } else {
                                       print $key_answer;
                                   } ?>"/>
                            <label for="<?php print $key_answer; ?>"><?php print $value_answer['value']; ?></label>
                        <?php } ?>
                        </span>
                </div>
            <?php } ?>
            <div class="earn-tokens-submit">
                <input type="button" class="submit-button" value="<?php print JText::_('EARN_TOKENS_SUBMIT'); ?>" />
                <span class="earn-tokens-tokens text-none-select hidden-xs">
                    <span class="tokens-count"><?php print $this->tokens_count; ?></span>
                    <span><?php print JText::sprintf('SURVEY_SUBMIT', $this->tokens_count); ?></span>
                </span>
            </div>
        </form>

        <div class="earn-tokens-footer"></div>

    </div>

</div>

<script type="text/javascript">

    jQuery('.earn-tokens-submit .submit-button').click(function () {

        jQuery(this).attr('disabled', true);

        var names = {};
        jQuery(':radio').each(function () {
            names[jQuery(this).attr('name')] = true;
        });

        var count = 0;
        jQuery.each(names, function () {
            count++;
        });

        if (jQuery('input:radio:checked').length == count) {
            var link = '<?php print $this->link_honest; ?>';
            var data_post = {
                'answers': jQuery('#honesty-questions').serialize(),
                'user_id': '<?php print $this->user_data->user_id;?>',
                'meet_up_id': '<?php print $this->meet;?>'
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/save_user_answers.php',
                data: data_post,
                success: function (data) {
                    if(data=='success'){
                        jQuery('.honesty-questions').html('<?php include_once('complete_box.php'); ?>');
                        jQuery('.close-page').click(function(){
                            jQuery(location).attr('href', link);
                        });
                    } else {
                        jQuery('.earn-tokens-submit .submit-button').addClass('error');
                        setTimeout(function () {
                            jQuery('.earn-tokens-submit .submit-button').attr('disabled', false).removeClass('error');
                        }, 3000);
                    }
                },
                error: function (html) {
                }
            });
            return false;
        } else {
            jQuery('.earn-tokens-submit .submit-button').addClass('error');
            setTimeout(function () {
                jQuery('.earn-tokens-submit .submit-button').attr('disabled', false).removeClass('error');
            }, 3000);
        }
    });
</script>