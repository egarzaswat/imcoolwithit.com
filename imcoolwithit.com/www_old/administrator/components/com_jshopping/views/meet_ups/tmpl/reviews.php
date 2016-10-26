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
$list = $this->reviews;
$count = count($list);
$i = 0;
?>

<?php print $this->tmp_html_start ?>

<table class="table table-striped">
    <thead>
    <tr>
        <th class="title" width="10">
            #
        </th>
        <th width="200" align="left">
            User
        </th>
        <th width="200" align="left">
            Question
        </th>
        <th width="200" align="left">
            Answer
        </th>
        <th width="200" align="left">
            Date
        </th>
    </tr>
    </thead>
    <tbody id="meet_up_list" style="width: 1000px;">
    <?php
    $i = 0;
    foreach ($list as $key => $row) {
        ?>
        <tr>
            <td>
                <?php print $i + 1; ?>
            </td>
            <td>
                <?php print $row->u_name; ?>
            </td>
            <td>
                <a href="index.php?option=com_jshopping&controller=attributes&task=edit&attr_id=<?php print $row->question; ?>"><?php print $row->question_name; ?></a>
            </td>
            <td>
                <a href="index.php?option=com_jshopping&controller=attributesvalues&task=show&attr_id=<?php print $row->answer; ?>"><?php print $row->answer_name; ?></a>
            </td>
            <td>
                <?php print $row->date ?>
            </td>
        </tr>
        <?php $i++;
    } ?>
    </tbody>
</table>

<?php print $this->tmp_html_end ?>
