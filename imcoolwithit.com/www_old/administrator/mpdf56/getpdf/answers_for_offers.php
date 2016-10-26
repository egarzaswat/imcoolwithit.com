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

if($_GET['offer'] != null && $_GET['offer'] != 0) {
    $offer = JSFactory::getModel('products');
    $image_offer = $offer->getImageProduct($_GET['offer']);
    $image_offer = "/images/places/" . $image_offer;
}

if(isset($_GET['date_from']) && $_GET['date_from'] != 0) {
    $date_from = date("y.m.d", strtotime($_GET['date_from']));
} else {
    $date_from = 0;
}

if(isset($_GET['date_to']) && $_GET['date_to'] != 0) {
    $date_to = date("y.m.d", strtotime($_GET['date_to']));
} else {
    $date_to = 0;
}

$html = '
<div class="header">
    <div class="logo"></div>';

    if(isset($image_offer)){
        $html .= '<div class="image">
        <img src="' .
            $image_offer .
            '" height="100">
    </div>';
    }

    $html .= '</div>
<div class="content">
    <h1>Answers For Offers</h1>
</div>
';


function calculateAnswers($list, $offer = 0, $html, $date_from, $date_to){
    $mod_list = array();

    foreach ($list as $temp) {
        $occured_date = date("y.m.d", strtotime($temp->date));
        if ( ($temp->offer == $offer || $offer == 0) && ($date_from == 0 || (strtotime($occured_date) >= strtotime($date_from)) ) && ($date_to == 0 || (strtotime($occured_date) <= strtotime($date_to)) )  ) {
            $mod_list[$temp->question]['name'] = $temp->question_name;
            $mod_list[$temp->question]['sum']++;
            $mod_list[$temp->question]['answers'][$temp->answer]['name'] = $temp->answer_name;
            $mod_list[$temp->question]['answers'][$temp->answer]['sum']++;
        }
    }

    foreach ($list as $temp) {
        $occured_date = date("y.m.d", strtotime($temp->date));
        if ( ($temp->offer == $offer || $offer == 0) && ($date_from == 0 || (strtotime($occured_date) >= strtotime($date_from)) ) && ($date_to == 0 || (strtotime($occured_date) <= strtotime($date_to)) )  ) {
            $mod_list[$temp->question]['answers'][$temp->answer]['per'] = round((($mod_list[$temp->question]['answers'][$temp->answer]['sum'] / $mod_list[$temp->question]['sum']) * 100),2);
        }
    }

    $i = 0;

    foreach ($mod_list as $key => $row) {
        $html .= ($i+1) . ". " . $row['name'] . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $first = true;
                foreach ($row['answers'] as $key_ => $item) {
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
    return $html;
}

$answers = JSFactory::getModel('answers');
$list = $answers->getAllAnswers();


$html .= 'Statistics: ';
if($date_from !=0 || $date_to !=0){
    $html .= '(';
}
if($date_from !=0){
    $html .= '&nbsp;&nbsp;&nbsp; From: ' . $date_from . ' &nbsp;&nbsp;&nbsp;';
}
if($date_to !=0){
    $html .= 'To: ' . $date_to . '&nbsp;&nbsp;&nbsp;';
}
if($date_from !=0 || $date_to !=0){
    $html .= ')';
}
$html .= '<br>';
if($_GET['offer'] != null) {
    $users_ans = $answers->getAllUserAnswers($_GET['offer'], $_GET['date_from'], $_GET['date_to']);
    $html .= '<div class="row">'
        . '<div class="col col30 border">'
        . $users_ans . "  people voted"
        . '</div>'
        .'</div>';
    $html .= '<div class="line_separator"></div>';
    $html = calculateAnswers($list, $_GET['offer'], $html, $date_from, $date_to);
} else {
    $users_ans = $answers->getAllAnswers(0, $_GET['date_from'], $_GET['date_to']);

    $html .= '<div class="row">'
        . '<div class="col col30 border">'
        . $users_ans . "  people voted"
        . '</div>'
        .'</div>';
    $html .= '<div class="line_separator"></div>';
    $html = calculateAnswers($list, 0, $html, $date_from, $date_to);
}

$html .= '<div class="footer">© 2014-2016 SK III, LLC All Rights Reserved.  Patents Pending.</div>';


include("../mpdf.php");

$mpdf=new mPDF('c');

$mpdf->SetDisplayMode('fullpage');

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/answers_for_offers.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

$mpdf->Output();

exit;

?>