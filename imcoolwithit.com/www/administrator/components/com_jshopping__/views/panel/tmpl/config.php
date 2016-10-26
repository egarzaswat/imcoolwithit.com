<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php print $this->tmp_html_start?>
<table width="100%">
<tr>
    <td>
        <div id="cpanel">
        <?php displayConfigPanelIco();?>
        </div>
    </td>
</tr>
</table>
<?php print $this->tmp_html_end?>
</div>