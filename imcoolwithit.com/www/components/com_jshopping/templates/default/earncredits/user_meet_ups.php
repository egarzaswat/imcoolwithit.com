<?php
defined('_JEXEC') or die('Restricted access');
$list = $this->list;
?>

<div class="lincup-review col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('LINCUP_REVIEW_TITLE'); ?></h1>
            <div class="block-right">
                <img src="<?php print $this->lincup_review_logo; ?>">
            </div>
        </div>

        <div class="earn-tokens-info">
            <span><?php print JText::_('LINCUP_REVIEW_INFO'); ?></span>
        </div>

        <div class="earn-tokens-list">
            <?php if (count($list) < 1) { ?>
                <div class="earn-tokens-item no-records-found">
                    <?php print JText::_('NO_LINCUP_FOUND'); ?>
                </div>
            <?php } else {
                foreach ($list as $temp) { ?>
                    <div class="earn-tokens-item">
                        <div class="left-block">
                            <a class="inf" href="<?php print $temp->link; ?>">
                                <img class="sp" src="<?php print $temp->image; ?>">
                            </a>
                        </div>
                        <div class="right-block">
                            <div class="tokens-image">
                                <img src="/templates/protostar/images/system/token.png">
                            </div>
                            <span class="tokens-text"><?php print JText::sprintf('QUESTIONS_EARN_TOKENS', $this->tokens_count); ?></span>
                        </div>
                        <div class="bot-inf">
                            <span><?php print JSFactory::getDateFormatMonthYearNumber($temp->occurred_date); ?></span>
                        </div>
                    </div>
                <?php } ?>

                <?php print $this->pagination; ?>
            <?php } ?>
        </div>
    </div>

</div>
