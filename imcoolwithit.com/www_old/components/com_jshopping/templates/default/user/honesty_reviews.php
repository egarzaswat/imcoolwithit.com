<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="member-honesty-reviews col-sm-6 col-sm-offset-3 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::sprintf('USER_HONESTY_REVIEWS_FULL', $this->member_name); ?></h1>

        <span class="member-honesty-reviews-info"><?php print JText::sprintf('USER_HONESTY_REVIEWS_FULL_TEXT', $this->member_name, $this->member_reviews, $this->max_reviews); ?></span>

        <span class="page-footer"></span>

    </div>

</div>