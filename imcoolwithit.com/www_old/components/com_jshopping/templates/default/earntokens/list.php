<?php
    defined('_JEXEC') or die('Restricted access');
?>
<div class="earn-tokens col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title row"><?php print JText::_('EARN_TOKENS'); ?></h1>

        <div class="earn-more-tokens row"><span><?php print JText::_('EARN_MORE_TOKENS'); ?></span></div>

        <div class="earn-tokens-block row">
            <?php foreach ($this->data as $item) { ?>
                <div class="earn-tokens-item col-sm-4 col-xs-12">
                    <a href="<?php print $item['link']; ?>">
                        <img src="<?php print $item['image']; ?>" alt="<?php print $item['text']; ?>"/>
                    </a>
                    <span><?php print $item['text']; ?></span>
                </div>
            <?php } ?>
        </div>

    </div>

</div>
