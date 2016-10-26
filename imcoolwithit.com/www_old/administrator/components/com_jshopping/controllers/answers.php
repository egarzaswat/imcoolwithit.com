<?php

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerAnswers extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        checkAccessController("answers");
        addSubmenu("answers");
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.answers";

        $answers = JSFactory::getModel('answers');
        $list = $answers->getAllAnswers();
        $offers = $answers->getAllOffers();

        $view = $this->getView("answers", 'html');
        $view->setLayout("list");
        $view->assign('list', $list);
        $view->assign('offers', $offers);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAnswers', array(&$view));
        $view->displayList();
    }

}