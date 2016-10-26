<?php 
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if (!empty($this->text)){?>
<?php echo $this->text;?>
<?php }else{?>
<p><?php print _JSHOP_THANK_YOU_ORDER?></p>
<?php }?>