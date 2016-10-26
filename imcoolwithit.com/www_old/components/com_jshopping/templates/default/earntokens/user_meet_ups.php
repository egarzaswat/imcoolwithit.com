<?php
defined('_JEXEC') or die('Restricted access');
$list = $this->list;
?>

<div class="lincup-review col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('LINCUP_REVIEW_TITLE'); ?></h1>

        <div class="earn-tokens-info">
            <img src="<?php print $this->lincup_review_logo; ?>">
            <span><?php print JText::_('LINCUP_REVIEW_INFO'); ?></span>
        </div>

        <div class="earn-tokens-list">
            <?php if (count($list) < 1) { ?>
                <div class="earn-tokens-item no-records-found">
                    <?php print JText::_('NO_LINCUP_FOUND'); ?>
                </div>
            <?php } else {
                foreach ($list as $temp) { ?>
                    <div class="earn-tokens-item row">
                        <a href="<?php print $temp->link; ?>">
                            <img src="<?php print $temp->image; ?>"/>
                            <span><?php print JSFactory::getDateFormatMonthYearNumber($temp->occurred_date); ?></span>
                        </a>
                        <span class="earn-tokens-tokens text-none-select">
                            <span><?php print $this->tokens_count; ?></span>
                        </span>
                    </div>
                <?php } ?>

                <?php print $this->pagination; ?>

            <?php } ?>
        </div>

        <div class="earn-tokens-footer"><span><?php print JText::_('POWERED_BY'); ?></span></div>

    </div>

</div>
