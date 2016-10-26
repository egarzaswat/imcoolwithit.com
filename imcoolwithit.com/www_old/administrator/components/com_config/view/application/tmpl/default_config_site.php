<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$this->name = JText::_('COM_CONFIG_CONFIG_SITE_SETTINGS_TOKENS');
$this->fieldsname = 'config_site_tokens';
echo JLayoutHelper::render('joomla.content.options_default', $this);

$this->name = JText::_('COM_CONFIG_CONFIG_SITE_SETTINGS_EXPIRES');
$this->fieldsname = 'config_site_expires';
echo JLayoutHelper::render('joomla.content.options_default', $this);

$this->name = JText::_('COM_CONFIG_CONFIG_SITE_SETTINGS_PAGINATION');
$this->fieldsname = 'config_site_pagination';
echo JLayoutHelper::render('joomla.content.options_default', $this);

$this->name = JText::_('COM_CONFIG_CONFIG_SITE_SETTINGS_IMAGES');
$this->fieldsname = 'config_site_images';
echo JLayoutHelper::render('joomla.content.options_default', $this);

$this->name = JText::_('COM_CONFIG_CONFIG_SITE_SETTINGS_PERMISSION');
$this->fieldsname = 'config_site_permission';
echo JLayoutHelper::render('joomla.content.options_default', $this);