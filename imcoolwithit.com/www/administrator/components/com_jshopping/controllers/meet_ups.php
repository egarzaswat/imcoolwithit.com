<?php

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerMeet_Ups extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'review',   'review' );
        checkAccessController("meet_ups");
        addSubmenu("meet_ups");
    }

    function display($cachable = false, $urlparams = false){
        $this->displayList();
    }

    function displayList(){
        $meet_ups = JSFactory::getModel('meetups');
        $list = $meet_ups->getOccurredMeetUps();
        $sponsors = $meet_ups->getAllSponsors();

        $view = $this->getView("meet_ups", 'html');
        $view->setLayout("list");
        $view->assign('list', $list);
        $view->assign('sponsors', $sponsors);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayMeetUps', array(&$view));
        $view->displayList();
    }

    function review(){
        $meet_up = JRequest::getInt('meet_up');
//
//        if($meet_up == 0){
//            $this->ShowStatisticsReviews();
//            return false;
//        }

        $meet_ups = JSFactory::getModel('meetups');
        $reviews = $meet_ups->getMeetUpReview($meet_up);

        $view = $this->getView("meet_ups", 'html');
        $view->setLayout("reviews");
        $view->assign('reviews', $reviews);

        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayMeetUps', array(&$view));
        $view->displayList();
    }

//    function ShowStatisticsReviews(){
//        $meet_ups = JSFactory::getModel('meetups');
//        $sponsors = $meet_ups->getAllSponsors();
////        $reviews = $meet_ups->getStatisticsMeetUpReview();
//
//        $view = $this->getView("meet_ups", 'html');
//        $view->setLayout("statistics_reviews");
//        $view->assign('sponsors', $sponsors);
//
//        $dispatcher = JDispatcher::getInstance();
//        $dispatcher->trigger('onBeforeDisplayMeetUps', array(&$view));
//        $view->displayList();
//    }

}