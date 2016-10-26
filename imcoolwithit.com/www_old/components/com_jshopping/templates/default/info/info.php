<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="info-content padding-null col-xs-12">
    <?php if($this->header != ""){ ?>
        <h1 class="info-content-title"><?php print $this->header; ?></h1>
    <?php }

    if($this->content != ""){ ?>
        <div class="info-content-text"><?php print $this->content; ?></div>
    <?php } ?>
</div>