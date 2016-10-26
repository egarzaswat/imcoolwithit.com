<?php
    defined('_JEXEC') or die('Restricted access');
    $userBookmarksList = $this->userBookmarksList;
?>

<div>
    <h1><?php echo JText::_('Your Saved Profiles'); ?></h1>
</div>

<div id="userBookmarks">

    <?php foreach($userBookmarksList as $userBookmark): ?>

        <div class="userBlockBookmark">

            <div class="removeUser bookmark_<?php echo $userBookmark['user_id']?>" onclick="removeBookmark(<?php echo $userBookmark['user_id']?>)">
                <span>x</span>
            </div>

            <div class="userImgBookmark">

                <img src="<?php echo $userBookmark['photo']; ?>" />

            </div>

            <div class="userInfoBookmark">

                <div class="userNameAgeBookmark">
                    <?php echo $userBookmark['name'].', '; ?>
                    <span><?php echo $userBookmark['currentAge']; ?></span>
                </div>

                <div class="lastVisitBookmark">
                    <span><?php echo Jtext::_('Last visit: '); ?></span>
                    <?php echo $userBookmark['last_visit']; ?>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

    <div class="clr"></div>

</div>