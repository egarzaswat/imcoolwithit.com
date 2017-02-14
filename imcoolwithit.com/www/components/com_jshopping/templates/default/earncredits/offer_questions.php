<?php
    defined('_JEXEC') or die('Restricted access');
    $data = $this->data;
    $this->block_name = 'Survey';
?>

<div class="surveys-questions col-sm-6 col-sm-offset-3 col-xs-12">
    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('SURVEYS_TITLE'); ?></h1>
        </div>

        <div class="earn-tokens-info">
            <img src="<?php print $data['offer_image']; ?>">
            <span><?php print JText::sprintf('SURVEY_INFO', $data['offer_name']); ?></span>
        </div>

        <?php if ($data['attributes'] == 'no_questions') { ?>
            <div class="no-records-found col-xs-10 col-xs-offset-1">
                <?php print JText::_('NO_QUESTIONS_FOUND'); ?>
            </div>
        <?php } else { ?>
            <form id="surveys-questions" class="earn-tokens-list">
                <?php foreach ($data['attributes'] as $attr) { ?>
                    <div class="earn-tokens-item">
                        <span class="earn-tokens-question"><?php print $attr['attr_name']; ?></span>
                        <span class="earn-tokens-answers">
                        <?php foreach ($attr['values'] as $key => $value) { ?>
                            <input id="<?php print $value['value_id']; ?>" type="radio"
                                   name="<?php print $attr['attr_id']; ?>" value="<?php print $value['value_id']; ?>"/>
                            <label for="<?php print $value['value_id']; ?>"><?php print $value['value_name']; ?></label>
                        <?php } ?>
                        </span>
                    </div>
                <?php } ?>

                <div class="earn-tokens-submit submit-block">
                    <input type="button" class="submit-button" value="<?php print JText::_('EARN_TOKENS_SUBMIT'); ?>" />
                    <span class="error">Whoops, you missed something.</span>
                    <span class="earn-tokens text-none-select hidden-xs">
                        <span class="tokens-icon">
                            <img src="/templates/protostar/images/system/token.png">
                        </span>
                        <span><?php print JText::sprintf('SURVEY_SUBMIT', $this->tokens_count); ?></span>
                    </span>
                </div>
            </form>
        <?php } ?>
    </div>

</div>

<script type="text/javascript">

    jQuery('.earn-tokens-submit .submit-button').click(function () {

        jQuery(this).attr('disabled', true);

        var names = {};
        jQuery(':radio').each(function() {
            names[jQuery(this).attr('name')] = true;
        });

        var count = 0;
        jQuery.each(names, function() {
            count++;
        });

        if(jQuery('input:radio:checked').length == count) {
            var link = '<?php print $this->link_offers; ?>';
            var data_post = {
                'answers': jQuery('#surveys-questions').serialize(),
                'offer_id': <?php print $this->offer_id;?>
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/save_offer_answers.php',
                data: data_post,
                success: function (data) {
                    if(data=='success'){
                        jQuery('.surveys-questions').html('<?php include_once('complete_box.php'); ?>');
                        jQuery('.close-page').click(function(){
                            jQuery(location).attr('href', link);
                        });
                    } else {
                        jQuery('.earn-tokens-submit .error').css('display', 'block');
                        setTimeout(function () {
                            jQuery('.earn-tokens-submit .submit-button').attr('disabled', false);
//                            jQuery('.earn-tokens-submit .error').hide();
                        }, 3000);
                    }
                },
                error: function (html) {
                    jQuery('.earn-tokens-submit .error').css('display', 'block');
                    setTimeout(function () {
                        jQuery('.earn-tokens-submit .submit-button').attr('disabled', false);
//                        jQuery('.earn-tokens-submit .error').hide();
                    }, 3000);
                }
            });
            return false;
        } else {
            jQuery('.earn-tokens-submit .error').css('display', 'block');
            setTimeout(function () {
                jQuery('.earn-tokens-submit .submit-button').attr('disabled', false);
//                jQuery('.earn-tokens-submit .error').hide();
            }, 3000);
        }
    });

</script>