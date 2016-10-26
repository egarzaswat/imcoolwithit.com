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
<div id="comjshop">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <div class="jshop address_block">
        <?php 
        $config_fields = $this->config_fields;
        include(dirname(__FILE__)."/adress.js.php");
        ?>
        <form action = "<?php print $this->action ?>" method = "post" name = "loginForm" onsubmit = "return validateCheckoutAdressForm('<?php print $this->live_path ?>', this.name)" class = "form-horizontal" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_address_start?>
            <div class = "jshop_register">
                <?php if ($config_fields['title']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_REG_TITLE ?> <?php if ($config_fields['title']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php print $this->select_titles ?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['f_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_F_NAME ?> <?php if ($config_fields['f_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "f_name" id = "f_name" value = "<?php print $this->user->f_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['l_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_L_NAME ?> <?php if ($config_fields['l_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "l_name" id = "l_name" value = "<?php print $this->user->l_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['m_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_M_NAME ?> <?php if ($config_fields['m_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "m_name" id = "m_name" value = "<?php print $this->user->m_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['firma_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_FIRMA_NAME ?> <?php if ($config_fields['firma_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "firma_name" id = "firma_name" value = "<?php print $this->user->firma_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['client_type']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_CLIENT_TYPE ?> <?php if ($config_fields['client_type']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php print $this->select_client_types;?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['firma_code']['display']){?>
                <div class = "control-group" id='tr_field_firma_code' <?php if ($config_fields['client_type']['display'] && $this->user->client_type!="2"){?>style="display:none;"<?php } ?>>
                    <div class = "control-label name">
                    <?php print _JSHOP_FIRMA_CODE ?> <?php if ($config_fields['firma_code']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "firma_code" id = "firma_code" value = "<?php print $this->user->firma_code ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['tax_number']['display']){?>
                <div class = "control-group" id='tr_field_tax_number' <?php if ($config_fields['client_type']['display'] && $this->user->client_type!="2"){?>style="display:none;"<?php } ?>>
                    <div class = "control-label name">
                    <?php print _JSHOP_VAT_NUMBER ?> <?php if ($config_fields['tax_number']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "tax_number" id = "tax_number" value = "<?php print $this->user->tax_number ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['email']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EMAIL ?> <?php if ($config_fields['email']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "email" id = "email" value = "<?php print $this->user->email ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['email2']['display']){?>
                <div class = "control-group">
                  <div class = "control-label name">
                    <?php print _JSHOP_EMAIL2 ?> <?php if ($config_fields['email2']['require']){?><span>*</span><?php } ?>
                  </div>
                  <div class = "controls">
                    <input type = "text" name = "email2" id = "email2" value = "<?php print $this->user->email ?>" class = "input" />
                  </div>
                </div>
                <?php } ?> 
                <?php if ($config_fields['birthday']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_BIRTHDAY ?> <?php if ($config_fields['birthday']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php echo JHTML::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'input', 'size'=>'25', 'maxlength'=>'19'));?>            
                    </div>
                </div>
                <?php } ?>
                <?php echo $this->_tmpl_address_html_2?>
                <?php if ($config_fields['home']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_HOME ?> <?php if ($config_fields['home']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "home" id = "home" value = "<?php print $this->user->home ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['apartment']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_APARTMENT ?> <?php if ($config_fields['apartment']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "apartment" id = "apartment" value = "<?php print $this->user->apartment ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>        
                <?php if ($config_fields['street']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_STREET_NR ?> <?php if ($config_fields['street']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "street" id = "street" value = "<?php print $this->user->street ?>" class = "input" />
                    <?php if ($config_fields['street_nr']['display']){?>
                    <input type="text" name="street_nr" id="street_nr" value="<?php print $this->user->street_nr?>" class="input" />
                    <?php }?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['zip']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_ZIP ?> <?php if ($config_fields['zip']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "zip" id = "zip" value = "<?php print $this->user->zip ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['city']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_CITY ?> <?php if ($config_fields['city']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "city" id = "city" value = "<?php print $this->user->city ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['state']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_STATE ?> <?php if ($config_fields['state']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "state" id = "state" value = "<?php print $this->user->state ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['country']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_COUNTRY ?> <?php if ($config_fields['country']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php print $this->select_countries ?>
                    </div>
                </div>
                <?php } ?>
                <?php echo $this->_tmpl_address_html_3?>
                <?php if ($config_fields['phone']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_TELEFON ?> <?php if ($config_fields['phone']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "phone" id = "phone" value = "<?php print $this->user->phone ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['mobil_phone']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_MOBIL_PHONE ?> <?php if ($config_fields['mobil_phone']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "mobil_phone" id = "mobil_phone" value = "<?php print $this->user->mobil_phone ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['fax']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_FAX ?> <?php if ($config_fields['fax']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "fax" id = "fax" value = "<?php print $this->user->fax ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['ext_field_1']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_1 ?> <?php if ($config_fields['ext_field_1']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "ext_field_1" id = "ext_field_1" value = "<?php print $this->user->ext_field_1 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['ext_field_2']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_2 ?> <?php if ($config_fields['ext_field_2']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "ext_field_2" id = "ext_field_2" value = "<?php print $this->user->ext_field_2 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['ext_field_3']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_3 ?> <?php if ($config_fields['ext_field_3']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "ext_field_3" id = "ext_field_3" value = "<?php print $this->user->ext_field_3 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php echo $this->_tmpl_address_html_4?>
            </div>
            
            <?php if ($this->count_filed_delivery > 0){?>
            <div class = "control-group other_delivery_adress">
                <div class = "control-label name">
                    <?php print _JSHOP_DELIVERY_ADRESS ?>
                </div>
                <div class = "controls">
                    <input type = "radio" name = "delivery_adress" id = "delivery_adress_1" value = "0" <?php if (!$this->delivery_adress) {?> checked = "checked" <?php } ?> onclick = "jQuery('#div_delivery').hide()" />
                    <label for = "delivery_adress_1"><?php print _JSHOP_NO ?></label>
                    <input type = "radio" name = "delivery_adress" id = "delivery_adress_2" value = "1" <?php if ($this->delivery_adress) {?> checked = "checked" <?php } ?> onclick = "jQuery('#div_delivery').show()" />
                    <label for = "delivery_adress_2"><?php print _JSHOP_YES ?></label>
                </div>
            </div>
            <?php }?>
            
            <div id = "div_delivery" class = "jshop_register" style = "padding-bottom:0px;<?php if (!$this->delivery_adress){ ?>display:none;<?php } ?>">
                <?php if ($config_fields['d_title']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_REG_TITLE ?> <?php if ($config_fields['d_title']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php print $this->select_d_titles ?>
                    </div>
                </div>        
                <?php } ?>
                <?php if ($config_fields['d_f_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_F_NAME ?> <?php if ($config_fields['d_f_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_f_name" id = "d_f_name" value = "<?php print $this->user->d_f_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_l_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_L_NAME ?> <?php if ($config_fields['d_l_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_l_name" id = "d_l_name" value = "<?php print $this->user->d_l_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_m_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_M_NAME ?> <?php if ($config_fields['d_m_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_m_name" id = "d_m_name" value = "<?php print $this->user->d_m_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_firma_name']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_FIRMA_NAME ?> <?php if ($config_fields['d_firma_name']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_firma_name" id = "d_firma_name" value = "<?php print $this->user->d_firma_name ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_email']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EMAIL ?> <?php if ($config_fields['d_email']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_email" id = "d_email" value = "<?php print $this->user->d_email ?>" class = "input" />
                    <?php if ($config_fields['d_street_nr']['display']){?>
                    <input type="text" name="d_street_nr" id="d_street_nr" value="<?php print $this->user->d_street_nr?>" class="inputbox" />
                    <?php }?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_birthday']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_BIRTHDAY ?> <?php if ($config_fields['d_birthday']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php echo JHTML::_('calendar', $this->user->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format, array('class'=>'input', 'size'=>'25', 'maxlength'=>'19'));?>
                    </div>
                </div>
                <?php } ?>
                <?php echo $this->_tmpl_address_html_5?>
                <?php if ($config_fields['d_home']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_HOME ?> <?php if ($config_fields['d_home']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_home" id = "d_home" value = "<?php print $this->user->d_home ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_apartment']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_APARTMENT ?> <?php if ($config_fields['d_apartment']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_apartment" id = "d_apartment" value = "<?php print $this->user->d_apartment ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                
                <?php if ($config_fields['d_street']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_STREET_NR ?> <?php if ($config_fields['d_street']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_street" id = "d_street" value = "<?php print $this->user->d_street ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_zip']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_ZIP ?> <?php if ($config_fields['d_zip']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_zip" id = "d_zip" value = "<?php print $this->user->d_zip ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_city']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_CITY ?> <?php if ($config_fields['d_city']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_city" id = "d_city" value = "<?php print $this->user->d_city ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_state']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_STATE ?> <?php if ($config_fields['d_state']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_state" id = "d_state" value = "<?php print $this->user->d_state ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_country']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_COUNTRY ?> <?php if ($config_fields['d_country']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <?php print $this->select_d_countries ?>
                    </div>
                </div>
                <?php } ?>
                <?php echo $this->_tmpl_address_html_6?>
                <?php if ($config_fields['d_phone']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_TELEFON ?> <?php if ($config_fields['d_phone']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_phone" id = "d_phone" value = "<?php print $this->user->d_phone ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_mobil_phone']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_MOBIL_PHONE ?> <?php if ($config_fields['d_mobil_phone']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_mobil_phone" id = "d_mobil_phone" value = "<?php print $this->user->d_mobil_phone ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_fax']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_FAX ?> <?php if ($config_fields['d_fax']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_fax" id = "d_fax" value = "<?php print $this->user->d_fax ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_ext_field_1']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_1 ?> <?php if ($config_fields['d_ext_field_1']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_ext_field_1" id = "d_ext_field_1" value = "<?php print $this->user->d_ext_field_1 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_ext_field_2']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_2 ?> <?php if ($config_fields['d_ext_field_2']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_ext_field_2" id = "d_ext_field_2" value = "<?php print $this->user->d_ext_field_2 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>
                <?php if ($config_fields['d_ext_field_3']['display']){?>
                <div class = "control-group">
                    <div class = "control-label name">
                    <?php print _JSHOP_EXT_FIELD_3 ?> <?php if ($config_fields['d_ext_field_3']['require']){?><span>*</span><?php } ?>
                    </div>
                    <div class = "controls">
                    <input type = "text" name = "d_ext_field_3" id = "d_ext_field_3" value = "<?php print $this->user->d_ext_field_3 ?>" class = "input" />
                    </div>
                </div>
                <?php } ?>                    
                <?php echo $this->_tmpl_address_html_7?>
            </div>
            <?php if ($config_fields['privacy_statement']['display']){?>
            <div class="jshop_register jshop_block_privacy_statement">
                <div class = "control-group">
                    <div class = "control-label name">
                        <a class="privacy_statement" href="#" onclick="window.open('<?php print SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
                        <?php print _JSHOP_PRIVACY_STATEMENT?> <?php if ($config_fields['privacy_statement']['require']){?><span>*</span><?php } ?>
                        </a>            
                    </div>
                    <div class = "controls">
                        <input type="checkbox" name="privacy_statement" id="privacy_statement" value="1" />
                    </div>
                </div>
            </div>
            <?php } ?>    
            <?php print $this->_tmp_ext_html_address_end?>
            
            <div class = "control-group box_button">
                <div class = "controls">
                    <?php echo $this->_tmpl_address_html_8?>
                    <div class="requiredtext">* <?php print _JSHOP_REQUIRED?></div>
                    <?php echo $this->_tmpl_address_html_9?>
                    <input type = "submit" name = "next" value = "<?php print _JSHOP_NEXT ?>" class = "btn btn-primary button" />
                </div>
            </div>
        </form>
    </div>
</div>