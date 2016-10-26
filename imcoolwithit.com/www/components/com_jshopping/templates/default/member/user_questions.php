<?php
    defined('_JEXEC') or die('Restricted access');
?>

<div class="user-questions col-sm-10 col-sm-offset-1 col-xs-12">

    <div class="page-content row">

        <h1 class="title"><?php print JText::sprintf('USER_QUESTIONS_TITLE', $this->user_name); ?></h1>

        <div class="photo">
            <a href="<?php print JText::_('LINK_FULL_USER_PAGE') . '?user=' . $this->user_id; ?>"><img class="user-image" src="<?php print $this->user_photo; ?>"></a>
        </div>

        <form id="complete-profile-questions" class="earn-tokens-list">
            <?php foreach ($this->questions as $key => $value) { ?>
                <div class="earn-tokens-item">
                    <span class="earn-tokens-question"><?php print $value['question']; ?></span>
                    <span class="earn-tokens-answers">
                        <?php foreach ($value['answers'] as $key_answer => $value_answer) { ?>
                            <input id="<?php print $key_answer; ?>" type="radio" name="<?php print $key; ?>"
                                   value="<?php print $key_answer; ?>" <?php if ($value_answer['checked'] == 1) {
                                print 'checked="checked"';} ?> disabled="disabled">
                            <label for="<?php print $key_answer; ?>"><?php print $value_answer['value']; ?></label>
                        <?php } ?>
                    </span>
                </div>
            <?php } ?>
        </form>

    </div>

</div>