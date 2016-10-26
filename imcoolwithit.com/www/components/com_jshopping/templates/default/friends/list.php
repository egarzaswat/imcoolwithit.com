<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="friends col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::_('CONNECTIONS_TITLE'); ?></h1>

        <?php if (count($this->data) < 1) { ?>
            <div class="no-records-found">
                <?php print JText::_('NO_USERS_FOUND'); ?>
            </div>
        <?php } else {
            foreach ($this->data as $key => $user) { ?>
                <a class="users-list-link" href="<?php print $user['user_link']; ?>">
                    <img class="user-image" src="<?php print $user['photo']; ?>"/>
                    <div class="users-list-info">
                        <span class="username"><?php print $user['name']; ?></span>
                        <span class="info">
                            <span class="localisation">
                                <?php print JText::sprintf('LOCATION', $user['distance']); ?>
                                <span class="yellow">|</span>
                                <?php print $user['sex']; ?>
                            </span>
                            <span class="last-visit"><?php print JText::sprintf('LAST_VISIT', $user['last_visit']); ?></span>
                        </span>
                    </div>
                </a>
            <?php }
        } ?>

        <div><?php print $this->pagination; ?></div>

    </div>

</div>