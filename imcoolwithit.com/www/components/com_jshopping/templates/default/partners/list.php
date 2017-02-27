<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-offers partners-page col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::_('DATING_DEALS'); ?></h1>
        </div>

        <div class="lincup-offers-info row">
            <?php print $this->content; ?>
        </div>
        <div class="href row">
            <a href="/info">Learn More</a>
        </div>

        <div class="lincup-offers-list row">
            <?php foreach ($this->partners as $key => $value) { ?>

                <div class="lincup-offers-items padding-null col-sm-4 col-xs-12" style="display: block;">
                    <a <?php print 'href="' . $value->link . '"'; ?> title="<?php print $value->name; ?>">
                        <img src="<?php print $value->image; ?>">
                    </a>
                </div>

            <?php } ?>
        </div>

    </div>

</div>