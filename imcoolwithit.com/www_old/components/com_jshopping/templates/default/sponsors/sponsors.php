<?php 
    defined('_JEXEC') or die('Restricted access');
?>

<div class="lincup-offers col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title row"><?php print JText::_('LINCUP_OFFERS_TITLE'); ?></h1>

        <div class="lincup-offers-info row">
            <span class="zip-code">
                <?php print JText::_('SPONSORS_ZIP_CODE'); ?>
                <a href="<?php print JText::_('LINK_EDIT_ACCOUNT'); ?>"><?php print $this->zip; ?></a>
            </span>
            <?php print $this->content; ?>
        </div>

        <div class="lincup-offers-list row">
            <?php foreach ($this->data as $key => $value) { ?>
                <div class="padding-null <?php if ($key % 2 != 0) { print 'dark-column '; } ?>col-sm-4 col-xs-12">
                    <div id="category_<?php print $key; ?>" class="lincup-offers-category text-none-select <?php if ($key == 0) { print 'first-column'; } ?>">
                        <?php print $value->name; ?>
                    </div>
                    <div id="category_<?php print $key . '_show'; ?>" class="lincup-offers-items">
                        <?php if (sizeof($value->category_sponsors) != 0) { ?>
                            <?php foreach ($value->category_sponsors as $key_s => $value_s) { ?>
                                <a <?php print 'href="' . $value_s->link . '"'; ?> title="<?php print $value_s->name; ?>">
                                    <img src="<?php print $value_s->image; ?>">
                                </a>
                            <?php } ?>
                            <div class="more-offers" id="<?php print $value->category_id; ?>">
                                <span><?php print JText::_('SPONSORS_MORE_OFFERS'); ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="no-offers">
                                <span><?php print JText::_('SPONSORS_NO_OFFERS'); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>

</div>

<script type="text/javascript">

    jQuery('.lincup-offers-category').click(function () {
        var id = this.id;
        if(jQuery(document).width() <= 767){
            jQuery('#' + id + '_show').toggle();
        }
    });

    jQuery('.more-offers').click(function () {

        var data_post = {
            'category_id': this.id,
            'user_id': <?php print $this->user;?>
        };

        jQuery.ajax({
            type: "POST",
            url: '/components/com_jshopping/controllers/save_data/show_more_offers.php',
            data: data_post,
            success: function (data) {
                jQuery('.lincup-offers .page-content .lincup-offers-list').html(data);
            },
            error: function (html) {

            }
        });
    });

</script>