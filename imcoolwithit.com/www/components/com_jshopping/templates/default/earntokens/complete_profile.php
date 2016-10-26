<?php 
    defined('_JEXEC') or die('Restricted access');
    $conf = new JConfig();
    $this->block_name = 'Q & A’s';
?>

<div class="complete-profile col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('COMPLETE_PROFILE_TITLE'); ?></h1>

        <div class="complete-profile-info">
			<span class="earn-tokens-tokens text-none-select">
				<span class="tokens-count"><?php print $this->tokens_count; ?></span>
			</span>
			<?php print JText::sprintf('COMPLETE_PROFILE_INFO', $this->tokens_count); ?>
        </div>

        <form id="complete-profile-questions" class="earn-tokens-list">
            <?php foreach ($this->questions as $key => $value) { ?>
                <div class="earn-tokens-item">
                    <span class="earn-tokens-question"><?php print $value['question']; ?></span>
                    <span class="earn-tokens-answers">
                        <?php foreach ($value['answers'] as $key_answer => $value_answer) { ?>
                            <input id="<?php print $key_answer; ?>" type="radio" name="<?php print $key; ?>"
                                   value="<?php print $key_answer; ?>"
                                   <?php if ($value_answer['checked'] == 1) { print 'checked="checked"'; } ?> />
                            <label for="<?php print $key_answer; ?>"><?php print $value_answer['value']; ?></label>
                        <?php } ?>
                    </span>
                </div>
            <?php } ?>
            <div class="earn-tokens-submit">
				<?php if($this->start_complete_profile) {?>
					<input type="button" class="submit-button" value="<?php print JText::_('EARN_TOKENS_EDIT'); ?>">
				<?php } else { ?>
					<input type="button" class="submit-button" value="<?php print JText::_('EARN_TOKENS_SUBMIT'); ?>">
				<?php } ?>
            </div>
        </form>

    </div>

</div>

<script type="text/javascript">

    jQuery('.earn-tokens-submit .submit-button').click(function () {

        jQuery(this).attr('disabled', true);

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/save_complete_profile.php',
            data: jQuery('#complete-profile-questions').serialize(),
            success: function (data){
                if(data=='success'){
                    jQuery(location).attr('href', '/');
                } else if (data=='add_tokens'){
                    jQuery('.complete-profile').html('<?php include_once('complete_box.php'); ?>');
                    jQuery('.close-page').click(function(){
                        jQuery(location).attr('href', '<?php print '/' . JText::_('LINK_EARN_TOKENS'); ?>');
                    });
                } else {
                    jQuery('.earn-tokens-submit .submit-button').addClass('error');
                    setTimeout(function () {
                        jQuery('.earn-tokens-submit .submit-button').attr('disabled', false).removeClass('error');
                    }, 3000);
                }
            },
            error: function (html) {
                jQuery('.earn-tokens-submit .submit-button').addClass('error');
                setTimeout(function () {
                    jQuery('.earn-tokens-submit .submit-button').attr('disabled', false).removeClass('error');
                }, 3000);
            }
        });
    });

</script>