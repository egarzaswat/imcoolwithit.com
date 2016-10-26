<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="info-content community padding-null col-xs-12">
    <?php if($this->header != ""){ ?>
        <h1 class="info-content-title"><?php print $this->header; ?></h1>
        <span style="color: rgb(113, 109, 110); font-size: 35px; padding-left: 10px;">Coming Soon</span>
    <?php }

    if($this->content != ""){ ?>
        <div class="info-content-text"><?php print $this->content; ?></div>
        <img src="/images/home/community.png" alt="CoolWithIt Community">
    <?php } ?>
</div>