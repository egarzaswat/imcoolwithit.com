<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="surveys-questions col-sm-6 col-sm-offset-3 col-xs-12">
    <div class="page-content row">
        <h1 class="title"><?php print JText::_('SURVEYS_TITLE'); ?></h1>
        <div class="earn-tokens-info">
            <img src="<?php print $this->data['image']; ?>">
        </div>
        <div class="earn-tokens-list">
            <div class="earn-tokens-item">
                <span><?php print JText::sprintf('OFFER_CONFIRM_TEXT', $this->data['name_' . JSFactory::getLang()->lang]); ?></span>
                <span class="earn-tokens-tokens text-none-select">
                    <span class="tokens-count"><?php print $this->tokens_count?></span>
                    <span><?php print JText::sprintf('OFFER_CONFIRM_TOKENS', $this->tokens_count); ?></span>
                </span>
            </div>
        </div>
        <div class="questions_confirm row">
            <div class="padding_xs_null col-xs-4">
                <a class="submit-button" href="<?php print $this->data['link_out']; ?>">
                    <?php print JText::_('OFFER_CONFIRM_OUT_BUTTON'); ?>
                </a>
            </div>
            <div class="padding_xs_null col-xs-4">
                <img src="/images/earn_tokens/R_U_Interested_In.png"/>
            </div>
            <div class="padding_xs_null col-xs-4">
                <a class="submit-button" href="<?php print $this->data['link_in']; ?>">
                    <?php print JText::_('OFFER_CONFIRM_IN_BUTTON'); ?>
                </a>
            </div>
        </div>
        <div class="page-footer"></div>
    </div>
</div>
