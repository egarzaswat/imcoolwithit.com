<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="honesty-review col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('HONEST_REVIEW_TITLE'); ?></h1>

        <div class="earn-tokens-info">
            <img src="<?php print $this->honest_logo; ?>">
            <span><?php print JText::sprintf('HONEST_REVIEW_INFO', $this->tokens_count, $this->tokens_count); ?></span>
        </div>

        <div class="earn-tokens-list">
            <?php if (!(isset($this->items) && (count($this->items) > 0))) { ?>
                <div class="earn-tokens-item no-records-found">
                    <?php print JText::_('NO_LINCUP_FOUND'); ?>
                </div>
            <?php } else {
                foreach ($this->items as $key => $temp) { ?>
                    <div class="earn-tokens-item row">
                        <a href="<?php print $temp->link; ?>">
                            <img class="user-image" src="<?php print $temp->photosite; ?>">
                            <span class="earn-tokens-username"><?php print $temp->u_name; ?></span>
                            <span><?php print $temp->occurred_date; ?></span>
                        </a>
                        <span class="earn-tokens-tokens text-none-select">
                            <span class="tokens-count"><?php print $this->tokens_count; ?></span>
                            <span><?php print JText::sprintf('QUESTIONS_EARN_TOKENS', $this->tokens_count); ?></span>
                        </span>
                    </div>
                <?php } ?>

                <?php print $this->pagination; ?>

            <?php } ?>
        </div>

        <div class="earn-tokens-footer"><span><?php print JText::_('POWERED_BY'); ?></span></div>

    </div>

</div>