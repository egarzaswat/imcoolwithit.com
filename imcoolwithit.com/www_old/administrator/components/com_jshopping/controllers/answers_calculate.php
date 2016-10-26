<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/administrator/includes/helper.php' );
require_once ( JPATH_BASE .'/administrator/includes/toolbar.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );

JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/administrator/components/com_jshopping/models');

JFactory::getApplication('site')->initialise();
$jshopConfig = JSFactory::getConfig();





if(isset($_POST['sponsor']) && $_POST['sponsor'] != null) {
    $sponsor = $_POST['sponsor'];
} else {
    $sponsor = 0;
}

if(isset($_POST['date_from']) && $_POST['date_from'] != null && $_POST['date_from'] != '') {
    $segments = explode('.', $_POST['date_from']);
    $date_from = $segments[2] . '-' . $segments[1] . '-' . $segments[0];
} else {
    $date_from = 0;
}

if(isset($_POST['date_to']) && $_POST['date_to'] != null && $_POST['date_to'] != '') {
    $segments = explode('.', $_POST['date_to']);
    $date_to = $segments[2] . '-' . $segments[1] . '-' . $segments[0];
} else {
    $date_to = 0;
}

$meet_ups = JSFactory::getModel('meetups');
$meet_ups_answers = $meet_ups->getStatisticsMeetUpReview($sponsor, $date_from, $date_to);

$i=1;