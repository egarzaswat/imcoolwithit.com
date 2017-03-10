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
 * HTML Article View class for the Content component
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class ContentViewInfo extends JViewLegacy
{
	public function display($tpl = null)
	{
        $app = JFactory::getApplication();
		$task = $this->get('Task');
        $this->data = $this->get('Data');
        JFactory::getDocument()->setDescription($this->data[0]->description);
        JFactory::getDocument()->setTitle($this->data[0]->title);
        JFactory::getDocument()->setMetaData('keywords', $this->data[0]->keywords);

        switch($task){
            case 'about': $this->setLayout('about'); break;
            case 'already_cool': $this->setLayout('already_cool'); break;
            case 'sponsors': $this->setLayout('sponsors'); break;
            case 'partners': $this->setLayout('partners'); break;
            case 'contact': $this->setLayout('contacts'); break;
            case 'terms' :
            case 'privacy':
            case 'faqs': $this->setLayout('mono'); break;

            default : $app->redirect("index.php?option=com_content&view=featured&Itemid=101"); break;

        }

		parent::display($tpl);
	}

}
