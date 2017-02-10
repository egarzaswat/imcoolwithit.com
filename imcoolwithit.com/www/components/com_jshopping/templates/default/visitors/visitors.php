<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="visitors col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1><?php print $this->title; ?></h1>
        </div>

        <?php if (count($this->visitors_list) < 1) { ?>
            <div class="no-records-found">
                <?php print JText::_('NO_USERS_FOUND'); ?>
            </div>
        <?php } else {
        foreach ($this->visitors_list as $key=>$user) { ?>
            <a class="users-list-link" href="<?php print $user['user_link']; ?>">
                <img class="user-image" src="<?php print $user['photo']; ?>"/>
                <div class="users-list-info">
                    <span class="username"><?php print $user['name']; ?></span>
                    <span class="last-visit"><?php echo JText::_('VISITED') . $user['visited']; ?></span>
                    <span class="distance"><?php echo JText::sprintf('DISTANCE_AWAY', $user['distance']); ?></span>
                </div>
            </a>
        <?php }
        } ?>

        <?php print $this->pagination; ?>
    </div>

</div>