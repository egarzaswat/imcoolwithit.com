<?php 
/**
* @version      4.8.0 18.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php
$config_fields = $this->config_fields;
include(dirname(__FILE__)."/register.js.php");
?>
<div class="jshop" id="comjshop_register">
    <?php if (!isset($hideheaderh1)) : ?>
    <h1><?php print _JSHOP_REGISTRATION;?></h1>
    <?php endif; ?>
    
    <form action = "<?php print SEFLink('index.php?option=com_jshopping&controller=user&task=registersave',1,0, $this->config->use_ssl)?>" class = "form-validate form-horizontal" method = "post" name = "loginForm" onsubmit = "return validateRegistrationForm('<?php print $this->urlcheckdata?>', this.name)" autocomplete="off" enctype="multipart/form-data">
    <?php echo $this->_tmpl_register_html_1?>
    <div class = "jshop_register">
        <?php if ($config_fields['title']['display']) : ?>
        <div class = "control-group">
            <div class = "control-label name">
                <?php print _JSHOP_REG_TITLE?> <?php if ($config_fields['title']['require']) : ?><span>*</span><?php endif; ?>
            </div>
            <div class = "controls">
                <?php print $this->select_titles ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['f_name']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_F_NAME ?> <?php if ($config_fields['f_name']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "f_name" id = "f_name" value = "<?php print $this->user->f_name?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['l_name']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_L_NAME ?> <?php if ($config_fields['l_name']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "l_name" id = "l_name" value = "<?php print $this->user->l_name?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['m_name']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_M_NAME ?> <?php if ($config_fields['m_name']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "m_name" id = "m_name" value = "<?php print $this->user->m_name?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['firma_name']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_FIRMA_NAME ?> <?php if ($config_fields['firma_name']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "firma_name" id = "firma_name" value = "<?php print $this->user->firma_name?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['client_type']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_CLIENT_TYPE ?> <?php if ($config_fields['client_type']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <?php print $this->select_client_types;?>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['firma_code']['display']) : ?>
        <div class = "control-group" id = 'tr_field_firma_code' <?php if ($config_fields['client_type']['display']) : ?>style="display:none;"<?php endif; ?>>
          <div class = "control-label name">
            <?php print _JSHOP_FIRMA_CODE ?> <?php if ($config_fields['firma_code']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "firma_code" id = "firma_code" value = "<?php print $this->user->firma_code?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['tax_number']['display']) : ?>
        <div class = "control-group" id = 'tr_field_tax_number' <?php if ($config_fields['client_type']['display']) : ?>style="display:none;"<?php endif; ?>>
          <div class = "control-label name">
            <?php print _JSHOP_VAT_NUMBER ?> <?php if ($config_fields['tax_number']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "tax_number" id = "tax_number" value = "<?php print $this->user->tax_number?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['email']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_EMAIL ?> <?php if ($config_fields['email']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "email" id = "email" value = "<?php print $this->user->email?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['email2']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_EMAIL2 ?> <?php if ($config_fields['email2']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "email2" id = "email2" value = "<?php print $this->user->email2?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['birthday']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_BIRTHDAY?> <?php if ($config_fields['birthday']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">            
            <?php echo JHTML::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'input', 'size'=>'25', 'maxlength'=>'19'));?>
          </div>
        </div>
        <?php endif; ?>
    </div>        
    <?php echo $this->_tmpl_register_html_2?>
    <div class = "jshop_register">
        <?php if ($config_fields['home']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_HOME ?> <?php if ($config_fields['home']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "home" id = "home" value = "<?php print $this->user->home?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['apartment']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_APARTMENT ?> <?php if ($config_fields['apartment']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "apartment" id = "apartment" value = "<?php print $this->user->apartment?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['street']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_STREET_NR ?> <?php if ($config_fields['street']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "street" id = "street" value = "<?php print $this->user->street?>" class = "input" />
            <?php if ($config_fields['street_nr']['display']){?>
            <input type="text" name="street_nr" id="street_nr" value="<?php print $this->user->street_nr?>" class="input" />
            <?php }?>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['zip']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_ZIP ?> <?php if ($config_fields['zip']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "zip" id = "zip" value = "<?php print $this->user->zip?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['city']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_CITY ?> <?php if ($config_fields['city']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "city" id = "city" value = "<?php print $this->user->city?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['state']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_STATE ?> <?php if ($config_fields['state']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "state" id = "state" value = "<?php print $this->user->state?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['country']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_COUNTRY ?> <?php if ($config_fields['country']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <?php print $this->select_countries ?>
          </div>
        </div>
        <?php endif; ?>
    </div>
    <?php echo $this->_tmpl_register_html_3?>
    <div class = "jshop_register">
        <?php if ($config_fields['phone']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_TELEFON ?> <?php if ($config_fields['phone']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "phone" id = "phone" value = "<?php print $this->user->phone?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['mobil_phone']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_MOBIL_PHONE ?> <?php if ($config_fields['mobil_phone']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "mobil_phone" id = "mobil_phone" value = "<?php print $this->user->mobil_phone?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['fax']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_FAX ?> <?php if ($config_fields['fax']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "fax" id = "fax" value = "<?php print $this->user->fax?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        
        <?php if ($config_fields['ext_field_1']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_EXT_FIELD_1 ?> <?php if ($config_fields['ext_field_1']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "ext_field_1" id = "ext_field_1" value = "<?php print $this->user->ext_field_1?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['ext_field_2']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_EXT_FIELD_2 ?> <?php if ($config_fields['ext_field_2']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "ext_field_2" id = "ext_field_2" value = "<?php print $this->user->ext_field_2?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['ext_field_3']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_EXT_FIELD_3 ?> <?php if ($config_fields['ext_field_3']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "ext_field_3" id = "ext_field_3" value = "<?php print $this->user->ext_field_3?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
    </div>
    <?php echo $this->_tmpl_register_html_4?>
    <div class = "jshop_register last_register_block">
        <?php if ($config_fields['u_name']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_USERNAME ?> <?php if ($config_fields['u_name']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "text" name = "u_name" id = "u_name" value = "<?php print $this->user->u_name?>" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['password']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_PASSWORD ?> <?php if ($config_fields['password']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "password" name = "password" id = "password" value = "" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['password_2']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <?php print _JSHOP_PASSWORD_2 ?> <?php if ($config_fields['password_2']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class = "controls">
            <input type = "password" name = "password_2" id = "password_2" value = "" class = "input" />
          </div>
        </div>
        <?php endif; ?>
        <?php if ($config_fields['privacy_statement']['display']) : ?>
        <div class = "control-group">
          <div class = "control-label name">
            <a class="privacy_statement" href="#" onclick="window.open('<?php print SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
            <?php print _JSHOP_PRIVACY_STATEMENT?> <?php if ($config_fields['privacy_statement']['require']) : ?><span>*</span><?php endif; ?>
            </a>            
          </div>
          <div class = "controls">
            <input type="checkbox" name="privacy_statement" id="privacy_statement" value="1" />
          </div>
        </div>
        <?php endif; ?>
    </div>
    <?php echo $this->_tmpl_register_html_5?>
    <div class = "control-group box_button">
        <div class = "controls">
            <div class="requiredtext">* <?php print _JSHOP_REQUIRED?></div>
            <?php echo $this->_tmpl_register_html_6?>
            <input type="submit" value = "<?php print _JSHOP_SEND_REGISTRATION ?>" class = "btn btn-primary button" />
        </div>
    </div>
    <?php echo JHtml::_('form.token');?>
    </form>
</div>