<?php
defined('_JEXEC') or die('Restricted access');
$data = $this->data;
?>

<div class="surveys col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('SURVEYS_TITLE'); ?></h1>

        <div class="earn-tokens-info">
            <img src="<?php print $this->surveys_logo; ?>">
            <span><?php print JText::_('SURVEYS_INFO'); ?></span>
        </div>

        <div class="earn-tokens-list">
            <?php if ($data == null) { ?>
                <div class="earn-tokens-item no-records-found">
                    <?php print JText::_('NO_SURVEYS_FOUND'); ?>
                </div>
            <?php } else {
                foreach ($data as $key => $temp) { ?>
                    <div class="earn-tokens-item">
                        <a href="<?php print $temp->link; ?>">
                            <img src="<?php print $temp->image; ?>">
                        </a>
                        <span class="earn-tokens-tokens text-none-select">
                            <span class="tokens-count"><?php print $temp->tokens; ?></span>
                        </span>
                    </div>
                <?php } ?>

                    <?php print $this->pagination; ?>

            <?php } ?>
        </div>

        <div class="earn-tokens-footer"><span><?php print JText::_('POWERED_BY'); ?></span></div>

    </div>

</div>
