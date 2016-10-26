<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../' ));
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


if($_GET['sponsor'] != null && $_GET['sponsor'] != 0) {
    $sponsor = JSFactory::getModel('products');
    $image_sponsor = $sponsor->getImageProduct($_GET['sponsor']);
    $image_sponsor = "/images/places/" . $image_sponsor;
}

if($_GET['date_from'] != null && $_GET['date_from'] != 0){
    $date_from = date("y.m.d", strtotime($_GET['date_from']));
}

if($_GET['date_to'] != null && $_GET['date_to'] != 0){
    $date_to = date("y.m.d", strtotime($_GET['date_to']));
}


$html = '
<div class="header">
    <div class="logo"></div>';

    if(isset($image_sponsor)){
        $html .= '<div class="image">
        <img src="' .
            $image_sponsor .
            '" height="100">
    </div>';
    }

    $html .= '</div>
<div class="content">
    <h1>Meet Ups:</h1>
</div>
';

$meet_ups = JSFactory::getModel('meetups');

if($_GET['sponsor'] != null && $_GET['sponsor'] != 0) {
    $list = $meet_ups->getOccurredMeetUps($_GET['sponsor']);
} else {
    $list = $meet_ups->getOccurredMeetUps();
}


$stat = array();

// Функция сравнения
function cmp($a, $b) {
    if ($a->sponsor_name == $b->sponsor_name) {
        return 0;
    }
    return ($a->sponsor_name < $b->sponsor_name) ? -1 : 1;
}
// Сортируем и выводим получившийся массив

uasort($list, 'cmp');

$list = array_values($list);

$html_mass = "";

foreach($list as $key => $value){
    $occured_date = date("y.m.d", strtotime($value->occurred_date));
    if( (!isset($date_from) || (strtotime($occured_date) >= strtotime($date_from)) ) && (!isset($date_to) || (strtotime($occured_date) <= strtotime($date_to))) ){
        if(!isset($stat[$value->sponsor_name])){
            $stat[$value->sponsor_name] = 1;
        } else {
            $stat[$value->sponsor_name]++;
        }

        if($_GET['sponsor'] != null && $_GET['sponsor'] != 0) {
            $html_mass .= '<div class="row">'
                . '<div class="col_number col35px">'
                    . ($key+1) . "."
                . '</div>'
                . '<div class="col col20">'
                    . $value->sender_name
                . '</div>'
                . '<div class="col col20">'
                    . $value->recipient_name
                . '</div>'
                . '<div class="col col20">'
                    . $value->occurred_date
                . '</div>'
            . '</div><hr>';
        } else {
            $html_mass .= '<div class="row">'
                . '<div class="col_number col35px">'
                    . ($key+1) . "."
                . '</div>'
                . '<div class="col col15">'
                    . $value->sender_name
                . '</div>'
                . '<div class="col col15">'
                    . $value->recipient_name
                . '</div>'
                . '<div class="col col30">'
                    . $value->occurred_date
                . '</div>'
                . '<div class="col col30">'
                    . $value->sponsor_name
                . '</div>'
                . '</div><hr>';
        }
    }
}
$html .= 'Statistics: &nbsp;&nbsp;&nbsp;';

if($date_from != 0 || $date_to != 0){
    $html .= '(';
}
if($date_from != 0){
    $html .= '&nbsp;&nbsp;&nbsp; From: ' . $date_from . ' &nbsp;&nbsp;&nbsp;';
}
if($date_to != 0){
    $html .= 'To: ' . $date_to . '&nbsp;&nbsp;&nbsp;';
}
if($date_from != 0 || $date_to != 0){
    $html .= ')';
}

$html .= '<br>';

ksort($stat);
$i=1;
foreach($stat as $key => $value){
    $html .= '<div class="row">'
        . '<div class="col col35px border">'
            . $i . "."
        . '</div>'
        . '<div class="col col30 border">'
            . $key
        . '</div>'
        . '<div class="col col35px border">'
            . $value
        . '</div>'
    .'</div>';
    $i++;
}

$html .= '<div class="line_separator"></div>' . $html_mass;

$html .= '<div class="footer">© 2014-2016 SK III, LLC All Rights Reserved. Patents Pending.</div>';

include("../mpdf.php");

$mpdf=new mPDF('c');

$mpdf->SetDisplayMode('fullpage');

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/answers_for_offers.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

$mpdf->Output();


?>