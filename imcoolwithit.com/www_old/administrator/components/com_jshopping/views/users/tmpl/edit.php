<?php
/**
* @version      4.5.0 10.02.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$user=$this->user;
$lists=$this->lists;
$config_fields=$this->config_fields;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=users" method="post" name="adminForm" id="adminForm" autocomplete="off">
<?php print $this->tmp_html_start?>
<ul class="nav nav-tabs">    
    <li class="active"><a href="#firstpage1" data-toggle="tab"><?php echo _JSHOP_GENERAL;?></a></li>
    <li><a href="#secondpage2" data-toggle="tab"><?php echo 'Other data';?></a></li>
</ul>

<div id="editdata-document" class="tab-content">

<div id="firstpage1" class="tab-pane active">
    <div class="col100">
    <fieldset class="adminform">
    <table class="admintable">

        <tr>
            <td class="key">
                <?php echo _JSHOP_USERNAME;?>*
            </td>
            <td>
                <input type="text" class="inputbox" name="u_name" value="<?php echo $user->u_name ?>" />
            </td>
        </tr>

        <tr>
          <td class="key">
            <?php echo _JSHOP_EMAIL?>*
          </td>
          <td>
            <input type="text" class="inputbox" name="email" value="<?php echo $user->email ?>" />
          </td>
        </tr>

        <tr>
            <td class="key">
                <?php echo _JSHOP_NEW_PASSWORD ?>
            </td>
            <td>
                <input class="inputbox" type="password" name="password" id="password" size="40" value=""/>
            </td>
        </tr>

        <tr>
            <td class="key">
                <?php echo _JSHOP_PASSWORD_2 ?>
            </td>
            <td>
                <input class="inputbox" type="password" name="password2" id="password2" size="40" value=""/>
            </td>
        </tr>

        <tr>
            <td class="key">
                <?php echo _JSHOP_NUMBER?>
            </td>
            <td>
                <input type="text" class="inputbox" name="number" value="<?php echo $user->number?>" disabled="disabled" />
            </td>
        </tr>

<?php /* ?>
        <?php if (JFactory::getUser()->authorise('core.admin', 'com_jshopping')){?>
        <tr>
            <td class="key">
                <?php echo _JSHOP_BLOCK_USER ?>
            </td>
            <td>
                <?php echo $this->lists['block']; ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
          <td class="key">
            <?php echo _JSHOP_USERGROUP_NAME;?>*
          </td>
          <td>
            <?php echo $lists['usergroups'];?>
          </td>
        </tr>
<?php */ ?>

    </table>
    </fieldset>
    </div>
    <div class="clr"></div>
<?php $pkey = "etemplatevar0";if ($this->$pkey){print $this->$pkey;}?>
</div>

<div id="secondpage2" class="tab-pane">
    <div class="col100">
    <fieldset class="adminform">
    <table class="admintable">
    <?php if ($config_fields['title']['display']){?>
    <tr>
        <td class="key">
            <?php echo _JSHOP_USER_TITLE ?>
        </td>
        <td>
            <?php echo $lists['select_titles'];?>
        </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['f_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_USER_FIRSTNAME;?>
      </td>
      <td>
        <input type="text" class="inputbox" name="f_name" value="<?php echo $user->f_name ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['l_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_USER_LASTNAME;?>
      </td>
      <td>
        <input type="text" class="inputbox" name="l_name" value="<?php echo $user->l_name ?>" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['m_name']['display']){?>
	<tr>
	  <td class="key">
		<?php print _JSHOP_M_NAME ?>
	  </td>
	  <td>
		<input type = "text" name = "m_name" id = "m_name" value = "<?php print $user->m_name ?>" class = "inputbox" />
	  </td>
	</tr>
	<?php } ?>
    <?php if ($config_fields['firma_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_FIRMA_NAME;?>
      </td>
      <td>
        <input type="text" class="inputbox" name="firma_name" value="<?php echo $user->firma_name ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['client_type']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_CLIENT_TYPE;?>
      </td>
      <td>
        <?php print $lists['select_client_types'];?>
      </td>
    </tr>
    <?php } ?>

    <?php if ($config_fields['firma_code']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_FIRMA_CODE ?> 
      </td>
      <td>
        <input type="text" name="firma_code" id="firma_code" value="<?php print $user->firma_code ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['tax_number']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_VAT_NUMBER ?>
      </td>
      <td>
        <input type="text" name="tax_number" id="tax_number" value="<?php print $user->tax_number ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['birthday']['display']){?>
	<tr>
	  <td class="key">
		<?php print _JSHOP_BIRTHDAY?>
	  </td>
	  <td>
		<?php echo JHTML::_('calendar', $user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19'));?>
	  </td>
	</tr>
<?php } ?>
    <?php if ($config_fields['home']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_FIELD_HOME?>
      </td>
      <td>
        <input type="text" class="inputbox" name="home" value="<?php echo $user->home ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['apartment']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_FIELD_APARTMENT?>
      </td>
      <td>
        <input type="text" class="inputbox" name="apartment" value="<?php echo $user->apartment ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['street']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_STREET_NR?>
      </td>
      <td>
        <input type="text" class="inputbox" name="street" value="<?php echo $user->street ?>" />
        <?php if ($config_fields['street_nr']['display']){?>
        <input type = "text" class = "inputbox" name = "street_nr" value = "<?php echo $user->street_nr ?>" />
        <?php }?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['city']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_CITY?>
      </td>
      <td>
        <input type="text" class="inputbox" name="city" value="<?php echo $user->city ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['zip']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_ZIP?>
      </td>
      <td>
        <input type="text" class="inputbox" name="zip" value="<?php echo $user->zip ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['state']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_STATE?>
      </td>
      <td>
        <input type="text" class="inputbox" name="state" value="<?php echo $user->state ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['country']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_COUNTRY?>
      </td>
      <td>
        <?php echo $lists['country'];?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['phone']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_TELEFON?>
      </td>
      <td>
        <input type="text" class="inputbox" name="phone" value="<?php echo $user->phone ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['mobil_phone']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_MOBIL_PHONE ?>
      </td>
      <td>
        <input type="text" name="mobil_phone" id="mobil_phone" value="<?php print $user->mobil_phone ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['fax']['display']){?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_FAX?>
      </td>
      <td>
        <input type="text" class="inputbox" name="fax" value="<?php echo $user->fax ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_1']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_EXT_FIELD_1 ?>
      </td>
      <td>
        <input type="text" name="ext_field_1" id="ext_field_1" value="<?php print $user->ext_field_1 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_2']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_EXT_FIELD_2 ?>
      </td>
      <td>
        <input type="text" name="ext_field_2" id="ext_field_2" value="<?php print $user->ext_field_2 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_3']['display']){?>
    <tr>
      <td class="key">
        <?php print _JSHOP_EXT_FIELD_3 ?>
      </td>
      <td>
        <input type="text" name="ext_field_3" id="ext_field_3" value="<?php print $user->ext_field_3 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>

    </table>
    </fieldset>
    </div>
    <div class="clr"></div>
<?php $pkey = "etemplatevar1";if ($this->$pkey){print $this->$pkey;}?>
</div>

</div>
<?php $pkey = "etemplatevarend";if ($this->$pkey){print $this->$pkey;}?>
<input type="hidden" name="task" value="">
<input type="hidden" name="user_id" value="<?php print $user->user_id?>">
<?php print $this->tmp_html_end?>
</form>
</div>