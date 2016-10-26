<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<table class = "jshop" id = "jshop_menu_order">
  <tr>
    <?php foreach($this->steps as $k=>$step){?>
      <td class = "jshop_order_step <?php print $this->cssclass[$k]?>">
        <?php print $step;?>
      </td>
    <?php }?>
  </tr>
</table>