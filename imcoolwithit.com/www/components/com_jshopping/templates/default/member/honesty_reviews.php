<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="member-honesty-reviews col-sm-6 col-sm-offset-3 col-xs-12" style="margin-top: 45px;">

    <div class="page-popup row">

        <h1 class="title"><?php print JText::sprintf('USER_HONESTY_REVIEWS_FULL', $this->member_name); ?></h1>
        <a href="/" class="close-page">X</a>

        <span class="member-honesty-reviews-info text"><?php print JText::sprintf('USER_HONESTY_REVIEWS_FULL_TEXT', $this->member_name, $this->member_reviews, $this->max_reviews); ?></span>
    </div>

</div>