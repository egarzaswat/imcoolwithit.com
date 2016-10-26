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




if(isset($_GET['sponsor']) && $_GET['sponsor'] != 0) {
    $sponsor = $_GET['sponsor'];
} else {
    $sponsor = 0;
}

if(isset($_GET['date_from']) && $_GET['date_from'] != 0) {
    $segments = explode('.', $_GET['date_from']);
    $date_from = $segments[2] . '-' . $segments[1] . '-' . $segments[0];
} else {
    $date_from = 0;
}

if(isset($_GET['date_to']) && $_GET['date_to'] != 0) {
    $segments = explode('.', $_GET['date_to']);
    $date_to = $segments[2] . '-' . $segments[1] . '-' . $segments[0];
} else {
    $date_to = 0;
}

$meet_ups = JSFactory::getModel('meetups');
$meet_ups_answers = $meet_ups->getStatisticsMeetUpReview($sponsor, $date_from, $date_to);

$statistics = array();
foreach($meet_ups_answers as $key => $value){
    $statistics[$value->question]['name'] = $value->question_name;
    $statistics[$value->question]['sum']++;
    $statistics[$value->question]['answers'][$value->answer]['name'] = $value->answer_name;
    $statistics[$value->question]['answers'][$value->answer]['sum']++;
}

foreach ($meet_ups_answers as $value) {
    $statistics[$value->question]['answers'][$value->answer]['per'] = round((($statistics[$value->question]['answers'][$value->answer]['sum'] / $statistics[$value->question]['sum']) * 100),2);
}


if(isset($_GET['sponsor']) && $_GET['sponsor'] != 0) {
    $offer = JSFactory::getModel('products');
    $image_sponsor = $offer->getImageProduct($_GET['sponsor']);
    $image_sponsor = "/images/places/" . $image_sponsor;
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
    <h1>Answers For Sponsors: &nbsp;&nbsp;&nbsp;';

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

$html .= '</h1>
</div>';

$users_ans = $meet_ups->getAllUserMeetAnswers($sponsor, $date_from, $date_to);
$html .= '<div class="row">'
    . '<div class="col col30 border">'
    . $users_ans . "  people voted"
    . '</div>'
    .'</div>';
$html .= '<div class="line_separator"></div>';



$i = 0;
foreach($statistics as $key => $value){
    $html .= ($i+1) . ". " . $value['name'] . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $first = true;
    foreach ($value['answers'] as $key_ => $item) {
        if(!$first) {
            $html .= ", &nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $first = false;
        $html .= $item['name'] . " - ";
        $html .= $item['per'] . "%";
    }
    $html .= '</td>
            </tr> <hr>';
    $i++;
}

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








