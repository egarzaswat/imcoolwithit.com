<?php
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewQuestions extends JViewLegacy
{

    function displayList($tpl = null)
    {
        JToolBarHelper::title(_JSHOP_LIST_QUESTIONS, 'generic.png');
        JToolBarHelper::addNew('edit', 'New question');
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        parent::display($tpl);
    }

    function displayEdit($tpl = null)
    {
        JToolBarHelper::title($temp = ($this->question->id) ? (_JSHOP_EDIT_ATTRIBUT . ' / ' . $this->question->question) : (_JSHOP_NEW_ATTRIBUT), 'generic.png');
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        parent::display($tpl);
    }

    function displayAnswersList($tpl = null)
    {
        JToolBarHelper::title(_JSHOP_LIST_ANSWERS . ' / '. $this->question->question, 'generic.png');
        JToolBarHelper::custom( "back", 'arrow-left', 'arrow-left', _JSHOP_RETURN_TO_ATTRIBUTES, false);
        JToolBarHelper::addNew('edit_answer', 'New answer');
        JToolBarHelper::deleteList('', 'remove_answer', 'JTOOLBAR_DELETE');
        JToolBarHelper::spacer();
        parent::display($tpl);
    }

    function displayEditAnswer($tpl = null)
    {
        JToolBarHelper::title($temp = ($this->answer->id) ? (_JSHOP_EDIT_ATTRIBUT . ' / ' . $this->answer->answer) : (_JSHOP_NEW_ATTRIBUT), 'generic.png');
        JToolBarHelper::save('save_answer');
        JToolBarHelper::spacer();
        JToolBarHelper::apply('apply_answer');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        parent::display($tpl);
    }

}