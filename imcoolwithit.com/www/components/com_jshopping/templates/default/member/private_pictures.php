<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="private-photos col-sm-8 col-sm-offset-2 col-xs-12">

    <div id="hide-content">
        <div id="show-img">
            <div class="img-slider">
                <img id="gallery-img" src=""/>
                <span id="close-img">X</span>
                <span id="prev-img" class="controls" data-index="#"></span>
                <span id="next-img" class="controls" data-index="#"></span>
            </div>
        </div>
    </div>

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print JText::sprintf('PRIVATE_PHOTOS_TITLE', $this->adv_name);?></h1>
        </div>

        <div class="gallery">
            <?php if(count($this->photos) > 0){ ?>
                <ul>
                    <?php foreach ($this->photos as $key => $value) { ?>
                        <li>
                            <img class="thumb" src="<?php print $this->path_to_thumb . $value->photo; ?>"
                                 data-source="<?php print $this->path_to_album . $value->photo; ?>"/>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <div class="no-private-photos">
                    <?php print JText::_('NO_PRIVATE_PHOTOS');?>
                </div>

            <?php } ?>
        </div>
    </div>

</div>

<script type="text/javascript">

    jQuery('.gallery ul li img').click(function(){
        var index = jQuery(this).parent('li').index();
        document.getElementById('prev-img').addClass('private').removeClass('public').setAttribute('data-index', index);
        document.getElementById('next-img').addClass('private').removeClass('public').setAttribute('data-index', index+2);
        document.getElementById('gallery-img').src=this.getAttribute('data-source');
        document.getElementById('hide-content').style.display='block';
        document.getElementById('show-img').style.display='inline-block';
        jQuery('span.controls').trigger('click');
    });

    jQuery('.controls').click(function(){
        var index = jQuery(this).attr('data-index');
        var newPrevIndex = parseInt(index)-1;
        var newNextIndex = parseInt(newPrevIndex)+2;
        var src = jQuery('.gallery ul li:nth-child('+ index +') img').attr('data-source');
        var total = jQuery('.gallery ul li').length + 1;
        if(total === newNextIndex){ newNextIndex = 1; }
        if(newPrevIndex === 0){ newPrevIndex = total-1; }
        document.getElementById('gallery-img').src=src;
        document.getElementById('prev-img').setAttribute('data-index', newPrevIndex);
        document.getElementById('next-img').setAttribute('data-index', newNextIndex);
    });

    jQuery('#close-img').click(function(){
        document.getElementById('hide-content').style.display='none';
        document.getElementById('show-img').style.display='none';
        document.getElementById('gallery-img').src='';
    });
</script>