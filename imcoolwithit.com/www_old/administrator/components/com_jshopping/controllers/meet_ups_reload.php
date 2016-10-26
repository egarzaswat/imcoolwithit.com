<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$list = $_POST['list'];
$sponsor = $_POST['sponsor'];

if($_POST['date_from'] != null && $_POST['date_from'] != ""){
    $date_from = date("y.m.d", strtotime($_POST['date_from']));
}

if($_POST['date_to'] != null && $_POST['date_to'] != ""){
    $date_to = date("y.m.d", strtotime($_POST['date_to']));
}
$i = 0;

foreach ($list as $temp) {
    $occured_date = date("y.m.d", strtotime($temp['occurred_date']));
    if ( ($temp['sponsor'] == $sponsor || $sponsor == 0) && (!isset($date_from) || (strtotime($occured_date) >= strtotime($date_from)) ) && (!isset($date_to) || (strtotime($occured_date) <= strtotime($date_to)) ) ) { ?>

        <tr>
            <td>
                <?php print $i + 1; ?>
            </td>
            <td>
                <?php print $temp['sender_name']; ?>
            </td>
            <td>
                <?php print $temp['recipient_name']; ?>
            </td>
            <td>
                <?php print $temp['sponsor_name']; ?>
            </td>
            <td>
                <?php print $temp['occurred_date']; ?>
            </td>
            <td>
                <a class="review btn" href="<?php print "/administrator/index.php?option=com_jshopping&controller=meet_ups&task=review&meet_up=" . $temp['id']?>">See Review</a>
            </td>
        </tr>
    <?php $i++; }
}

?>
