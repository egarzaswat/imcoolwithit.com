<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
displaySubmenuOptions();
$list = $this->list;
$count = count ($list);
$i = 0;
?>
<form action="index.php?option=com_jshopping&controller=questions" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="title" width="10">
                #
            </th>
            <th width="20">
                <input type="checkbox" name="checkall-toggle" value=""
                       title="<?php print JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
            </th>
            <th width="400" align="left">
                Question
            </th>
            <th width="100" align="left">
                Type
            </th>
            <th width="400" align="left">
                Answers
            </th>
            <th width="50" class="center">
                <?php print _JSHOP_EDIT; ?>
            </th>
        </tr>
        </thead>
        <?php foreach ($list as $key=>$row) { ?>
            <tr class="row<?php print $i % 2; ?>">
                <td>
                    <?php print $i + 1; ?>
                </td>
                <td>
                    <?php print JHtml::_('grid.id', $i, $key); ?>
                </td>
                <td>
                    <a href="index.php?option=com_jshopping&controller=questions&task=edit&question_id=<?php print $key; ?>"><?php print $row['question_name']; ?></a>
                </td>
                <td>
                    <?php print $row['question_type']; ?>
                </td>
                <td>
                    <a href="index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=<?php print $key ?>"><?php print _JSHOP_OPTIONS ?></a>&nbsp;&nbsp;
                    <?php print "(";
                    $first = true;
                    foreach($row['answers'] as $item) {
                        if(!$first) { print ", ";}
                        print $item['answer_name'];
                        $first = false;
                    }

                    print ")";?>

                </td>
                <td class="center">
                    <a class="btn btn-micro"
                       href='index.php?option=com_jshopping&controller=questions&task=edit&question_id=<?php print $key; ?>'>
                        <i class="icon-edit"></i>
                    </a>
                </td>
            </tr>
            <?php $i++; } ?>
    </table>

<input type="hidden" name="task" value="<?php echo JRequest::getVar('task', 0)?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>