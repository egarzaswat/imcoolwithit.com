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
<div class = "jshop pagelogin" id="comjshop">
    <h1><?php print _JSHOP_LOGIN ?></h1>

    <?php if ($this->config->shop_user_guest && $this->show_pay_without_reg) : ?>
        <span class = "text_pay_without_reg"><?php print _JSHOP_ORDER_WITHOUT_REGISTER_CLICK?> <a href="<?php print SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,0, $this->config->use_ssl);?>"><?php print _JSHOP_HERE?></a></span>
    <?php endif; ?>

    <?php echo $this->tmpl_login_html_1?>
    <div class = "row-fluid">
        <div class = "span6 login_block">
			<?php echo $this->tmpl_login_html_2?>
            <div class="small_header"><?php print _JSHOP_HAVE_ACCOUNT ?></div>
            <div class="logintext"><?php print _JSHOP_PL_LOGIN ?></div>

            <form method="post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=user&task=loginsave', 1,0, $this->config->use_ssl)?>" name="jlogin" class="form-horizontal">
                <div class="control-group">
                    <div class="control-label">
                        <label id="username-lbl" for="jlusername">
                            <?php print _JSHOP_USERNAME ?>:
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" id="jlusername" name="username" value="" class="inputbox" />
                    </div>
                </div>

                <div class="control-group rowpasword">
                    <div class="control-label">
                        <label id="password-lbl" class="" for="jlpassword"><?php print _JSHOP_PASSWORT ?>:</label>
                    </div>
                    <div class="controls">
                        <input type="password" id="jlpassword" name="passwd" value="" class="inputbox" />
                    </div>
                </div>

                <div class="control-group checkbox rowremember">
                    <div class="controls">
                        <input type="checkbox" name="remember" id="remember_me" value="yes" />
                        <label for = "remember_me"><?php print _JSHOP_REMEMBER_ME ?></label>
                    </div>
                </div>

                <div class="control-group rowbutton">
                    <div class="controls">
                        <input type="submit" class="btn btn-primary button" value="<?php print _JSHOP_LOGIN ?>" />
                    </div>
                </div>

                <div class="control-group rowlostpassword">
                    <div class="controls">
                        <a href = "<?php print $this->href_lost_pass ?>"><?php print _JSHOP_LOST_PASSWORD ?></a>
                    </div>
                </div>

                <input type = "hidden" name = "return" value = "<?php print $this->return ?>" />
                <?php echo JHtml::_('form.token');?>
				<?php echo $this->tmpl_login_html_3?>
            </form>
        </div>
        <div class = "span6 register_block">
			<?php echo $this->tmpl_login_html_4?>
            <span class="small_header"><?php print _JSHOP_HAVE_NOT_ACCOUNT ?></span>
            <div class="logintext"><?php print _JSHOP_REGISTER ?></div>
            <?php if (!$this->config->show_registerform_in_logintemplate){?>
                <div class="block_button_register">
                    <input type="button" class="btn button" value="<?php print _JSHOP_REGISTRATION ?>" onclick="location.href='<?php print $this->href_register ?>';" />
                </div>
            <?php }else{?>
                <?php $hideheaderh1 = 1; include(dirname(__FILE__)."/register.php"); ?>
            <?php }?>
			<?php echo $this->tmpl_login_html_5?>
        </div>
    </div>
	<?php echo $this->tmpl_login_html_6?>
</div>