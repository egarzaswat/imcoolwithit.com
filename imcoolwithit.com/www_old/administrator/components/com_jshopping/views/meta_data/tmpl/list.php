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
<form action="index.php?option=com_jshopping&controller=meta_data" method="post" name="adminForm" id="adminForm">
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
            <th width="150" align="left">
                Page
            </th>
            <th width="150" align="left">
                Alias
            </th>
            <th width="150" align="left">
                Title
            </th>
            <th width="150" align="left">
                Keywords
            </th>
            <th width="400" align="left">
                Description
            </th>
            <th width="400" align="left">
                Header
            </th>
            <th width="50" class="center">
                <?php print _JSHOP_EDIT; ?>
            </th>
        </tr>
        </thead>
        <?php foreach ($list as $row) { ?>
            <tr class="row<?php print $i % 2; ?>">
                <td>
                    <?php print $i + 1; ?>
                </td>
                <td>
                    <?php print JHtml::_('grid.id', $i, $row->id); ?>
                </td>
                <td>
                    <?php print $row->page; ?>
                </td>
                <td>
                    <?php print $row->alias; ?>
                </td>
                <td>
                    <?php print $row->title; ?>
                </td>
                <td>
                    <?php print $row->keywords; ?>
                </td>
                <td>
                    <?php print $row->description; ?>
                </td>
                <td>
                    <?php print $row->header; ?>
                </td>
                <td class="center">
                    <a class="btn btn-micro"
                       href='index.php?option=com_jshopping&controller=meta_data&task=edit&id=<?php print $row->id; ?>'>
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