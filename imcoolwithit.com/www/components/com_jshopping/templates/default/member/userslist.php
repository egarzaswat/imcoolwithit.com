<?php
    defined('_JEXEC') or die('Restricted access');
    $usersList   = $this->usersList;
?>
<div class="user_list row">
    <div class="back col-sm-2 col-xs-5">
        <span class="back_action"></span>
    </div>
    <div class="user_list_content col-lg-8 col-sm-10 col-xs-12">
        <div class="row">
            <div class="col-sm-12 col-xs-7 col-sm-offset-0 col-xs-offset-4">
                <a class="settings" href="index.php?option=com_jshopping&controller=member&task=editaccount" title="<?php echo JText::_('MY_PROFILE_SETTINGS'); ?>"></a>
            </div>

        </div>

        <div class="clr"></div>
        <?php
        if(count($usersList)<1){
        ?>
            <div class="page_content no_records_found">
                <?php print JText::_('NO_USERS_FOUND'); ?>
            </div>
        <?php
        }
        foreach($usersList as $key=>$user): ?>
            <div class="padding_null col-sm-6 col-xs-12">
                <a class="color_2 item_block link_user" href="<?php echo $user['userLink']?>">
                    <span class="name"><?php echo $user['name'] . ", " . $user['currentAge'];?></span>
                    <span><?php echo JText::_('Distance: ') . $user['distance']; ?></span>
                    <span><?php echo JText::_('Last visit: '). $user['lastVisit']; ?></span>
                    <img src="<?php echo $user['photosite'];?>"/>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-xs-12">
        <?php print $this->pagination; ?>
    </div>
</div>
<!--<div class="col-lg-8 col-sm-10 col-sm-offset-2">-->
<!--    --><?php //print $this->menu; ?>
<!--</div>-->