<?php
defined('_JEXEC') or die('Restricted access');
$data = $this->data;
?>

<div class="surveys col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('SURVEYS_TITLE'); ?></h1>
            <div class="block-right">
                <img src="<?php print $this->surveys_logo; ?>">
            </div>
        </div>

        <div class="earn-tokens-info">
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
                        <div class="left-block">
                            <a class="inf" href="<?php print $temp->link; ?>">
                                <img class="sp" src="<?php print $temp->image; ?>">
                            </a>
                        </div>
                        <div class="right-block">
                            <div class="tokens-image">
                                <img src="/templates/protostar/images/system/token.png">
                            </div>
                            <span class="tokens-text"><?php print JText::sprintf('QUESTIONS_EARN_TOKENS', $temp->tokens); ?></span>
                        </div>
                        <div class="bot-inf">
                            <span class="earn-tokens-username"><?php print $temp->u_name; ?></span>
                            <span><?php print $temp->occurred_date; ?></span>
                        </div>
                    </div>
                <?php } ?>
                    <?php print $this->pagination; ?>
            <?php } ?>
        </div>
    </div>

</div>
