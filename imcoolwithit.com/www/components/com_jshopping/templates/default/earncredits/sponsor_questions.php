<?php
defined('_JEXEC') or die('Restricted access');
$data = $this->data;
$user = $this->user;
$this->block_name = 'LincUp Review';
?>

<div class="lincup-questions col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::sprintf('LINCUP_QUESTIONS_TITLE', $data['sponsor_name']); ?></h1>
            <div class="block-right">
                <img src="<?php print $data['sponsor_image']; ?>">
            </div>
        </div>

        <div class="earn-tokens-info">
            <span><?php print JText::sprintf('LINCUP_QUESTIONS_INFO', $data['sponsor_name']); ?></span>
        </div>

        <form id="lincup-questions" class="earn-tokens-list">
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

            <div class="submit-block">
                <input type="button" class="submit-button" value="<?php print JText::_('EARN_TOKENS_SUBMIT'); ?>">
                <span class="earn-tokens text-none-select hidden-xs">
                    <span class="tokens-icon">
                        <img src="/templates/protostar/images/system/token.png">
                    </span>
                    <span><?php print JText::sprintf('SURVEY_SUBMIT', $this->tokens_count); ?></span>
                </span>
            </div>
        </form>
    </div>

</div>

<script type="text/javascript">

    jQuery('.earn-tokens-submit .submit-button').click(function () {

        jQuery(this).attr('disabled', true);

        var names = {};
        jQuery(':radio').each(function() { // find unique names
            names[jQuery(this).attr('name')] = true;
        });

        var count = 0;
        jQuery.each(names, function() { // then count them
            count++;
        });

        if(jQuery('input:radio:checked').length == count) {
            var link = '<?php print $this->link_meet_ups; ?>';
            var data_post = {
                'answers': jQuery('#lincup-questions').serialize(),
                'meet_up': <?php print $this->meet_up;?>
            };

            jQuery.ajax({
                type: "POST",
                url: '/components/com_jshopping/controllers/save_data/save_sponsor_answers.php',
                data: data_post,
                success: function (data) {
                    if(data=='success'){
                        jQuery('.lincup-questions').html('<?php include_once('complete_box.php'); ?>');
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