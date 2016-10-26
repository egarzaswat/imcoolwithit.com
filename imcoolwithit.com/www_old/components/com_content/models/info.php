<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content Component Article Model
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class ContentModelInfo extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_content.article';

    public function getTask(){
        $app = JFactory::getApplication('site');
        $this->set('task_data', $app->input->get('task'));

        return $app->input->get('task');
    }

    public function getData(){
        $db = JFactory::getDBO();
        $query = "select *"." from `#__meta_data` where `page`='" . $this->get('task_data') . "'";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
