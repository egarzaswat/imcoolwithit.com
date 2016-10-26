<?php
/**
 * @version      4.9.0 24.07.2013
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerQuestions extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        $this->registerTask( 'apply_answer', 'save_answer');
        checkAccessController("questions");
        addSubmenu("questions");
    }

    function display($cachable = false, $urlparams = false){
        $questions = JSFactory::getModel("questions");

        $list = $questions->getAllQuestions();
        $display_data = array();

        foreach($list as $temp){
            $display_data[$temp->question_id]['question_name'] = $temp->question_name;
            $display_data[$temp->question_id]['question_type'] = $temp->type;
            $display_data[$temp->question_id]['answers'][$temp->answer_id]['answer_name'] = $temp->answer_name;
        }

        $view = $this->getView("questions", 'html');
        $view->setLayout("list");
        $view->assign('list', $display_data);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayQuestions', array(&$view));
        $view->displayList();
    }

    function edit() {
        $question_id = JRequest::getInt("question_id");

        $question = JSFactory::getTable('question', 'jshop');
        $question->load($question_id);

        $categories = array();
        $categories[] = JHTML::_('select.option', 'complete_profile', 'Complete Profile', 'id','value');
        $categories[] = JHTML::_('select.option', 'honest_review', 'Honest Review', 'id','value');

        $category = JHTML::_('select.radiolist', $categories, 'type','onclick=""','id','value', $question->type);

        JFilterOutput::objectHTMLSafe($question, ENT_QUOTES);

        $view=$this->getView("questions", 'html');
        $view->setLayout("edit");
        $view->assign('question', $question);
        $view->assign('category', $category);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditQuestion', array(&$view, &$question));
        $view->displayEdit();
    }

    function save(){
        $question_id = JRequest::getInt('id');
        $dispatcher = JDispatcher::getInstance();

        $question = JSFactory::getTable('question', 'jshop');
        $post = JRequest::get("post");

        $dispatcher->trigger( 'onBeforeSaveQuestion', array(&$post) );

        if (!$question->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=questions");
            return 0;
        }

        if (!$question->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=questions");
            return 0;
        }

        if (!$question_id){
            $question_id = $question->id;
        }

        $dispatcher->trigger( 'onAfterSaveQuestion', array(&$question) );

        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=edit&question_id=".$question_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=questions");
        }

    }

    function remove() {
        $cid = JRequest::getVar("cid");
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        $dispatcher = JDispatcher::getInstance();

        $dispatcher->trigger( 'onBeforeRemoveQuestion', array(&$cid) );

        $text = '';
        foreach ($cid as $key => $value) {
            $value = intval($value);
            $query = "DELETE "."FROM `#__questions_earn_tokens` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();

            $query = "SELECT * "."FROM `#__answers_earn_tokens` where `id_question` = '".$db->escape($value)."' ";
            $db->setQuery($query);
            $answers = $db->loadObjectList();
            foreach ($answers as $answer){
                @unlink($jshopConfig->image_attributes_path."/".$answer->image);
            }
            $query = "DELETE "."FROM `#__answers_earn_tokens` where `id_question` = '".$db->escape($value)."' ";
            $db->setQuery($query);
            $db->query();

            $text = _JSHOP_ATTRIBUT_DELETED;
        }

        $dispatcher->trigger( 'onAfterRemoveQuestion', array(&$cid) );

        $this->setRedirect("index.php?option=com_jshopping&controller=questions", $text);
    }

    function show_answers(){
        $questions = JSFactory::getModel("questions");
        $question_id = JRequest::getInt('question_id');

        $question = JSFactory::getTable('question', 'jshop');
        $question->load($question_id);


        $list = $questions->getAnswers($question_id);

        $view = $this->getView("questions", 'html');
        $view->setLayout("show_answers");
        $view->assign('question', $question);
        $view->assign('list', $list);
        $view->assign('question_id', $question_id);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAnswers', array(&$view));
        $view->displayAnswersList();
    }

    function edit_answer(){

        $question_id = JRequest::getInt("question_id");
        $answer_id = JRequest::getInt("answer_id");

        $answer = JSFactory::getTable('answer', 'jshop');
        $answer->load($answer_id);

        $categories[] = JHTML::_('select.option', 0, 'Positive', 'id','value');
        $categories[] = JHTML::_('select.option', 1, 'Negative', 'id','value');
        if (!isset($answer->negative)) $answer->negative = 0;
        $negative = JHTML::_('select.radiolist', $categories, 'negative','onclick=""','id','value', $answer->negative);

        JFilterOutput::objectHTMLSafe($answer, ENT_QUOTES);

        $view=$this->getView("questions", 'html');
        $view->setLayout("edit_answer");
        $view->assign('answer', $answer);
        $view->assign('question_id', $question_id);
        $view->assign('negative', $negative);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditAnswer', array(&$view, &$answer));
        $view->displayEditAnswer();
    }

    function save_answer(){
        $answer_id = JRequest::getInt('id');
        $question_id = JRequest::getInt('id_question');
        $dispatcher = JDispatcher::getInstance();
        $answer = JSFactory::getTable('answer', 'jshop');
        $post = JRequest::get("post");
        $post['negative'] = $post['negative'] == 0 ? false : true;

        $dispatcher->trigger( 'onBeforeSaveAnswer', array(&$post) );

        if (!$answer->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=".$question_id);
            return 0;
        }

        if (!$answer->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=".$question_id);
            return 0;
        }

        if (!$answer_id){
            $answer_id = $answer->id;
        }

        $dispatcher->trigger( 'onAfterSaveAnswer', array(&$answer) );

        if ($this->getTask()=='apply_answer'){
            $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=edit_answer&answer_id=".$answer_id."&question_id=".$question_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=".$question_id);
        }

    }

    function remove_answer() {
        $cid = JRequest::getVar("cid");
        $question_id = JRequest::getInt('question_id');
        $db = JFactory::getDBO();

        $dispatcher = JDispatcher::getInstance();

        $dispatcher->trigger( 'onBeforeRemoveAnswer', array(&$cid) );

        $text = '';
        foreach ($cid as $key => $value) {
            $value = intval($value);
            $query = "DELETE "."FROM `#__answers_earn_tokens` WHERE `id` = '".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();

            $text = _JSHOP_ATTRIBUT_DELETED;
        }

        $dispatcher->trigger( 'onAfterRemoveAnswer', array(&$cid) );

        $this->setRedirect("index.php?option=com_jshopping&controller=questions&task=show_answers&question_id=".$question_id, $text);
    }

}