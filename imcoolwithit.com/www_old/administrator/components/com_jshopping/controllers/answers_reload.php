<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

function calculateAnswers($list, $offer = 0, $date_from = 0, $date_to = 0){

        $mod_list = array();

        foreach ($list as $temp) {
            $occured_date = date("y.m.d", strtotime($temp['date']));
            if ( ($temp['offer'] == $offer || $offer == 0) && ($date_from == 0 || (strtotime($occured_date) >= strtotime($date_from)) ) && ($date_to == 0 || (strtotime($occured_date) <= strtotime($date_to)) ) ) {
                $mod_list[$temp['question']]['name'] = $temp['question_name'];
                $mod_list[$temp['question']]['sum']++;
                $mod_list[$temp['question']]['answers'][$temp['answer']]['name'] = $temp['answer_name'];
                $mod_list[$temp['question']]['answers'][$temp['answer']]['sum']++;
            }
        }

        foreach ($list as $temp) {
            $occured_date = date("y.m.d", strtotime($temp['date']));
            if ( ($temp['offer'] == $offer || $offer == 0) && ($date_from == 0 || (strtotime($occured_date) >= strtotime($date_from)) ) && ($date_to == 0 || (strtotime($occured_date) <= strtotime($date_to)) ) ) {
                $mod_list[$temp['question']]['answers'][$temp['answer']]['per'] = round((($mod_list[$temp['question']]['answers'][$temp['answer']]['sum'] / $mod_list[$temp['question']]['sum']) * 100),2);
            }
        }
        $i = 0;

        foreach ($mod_list as $key => $row) { ?>
            <tr>
                <td>
                    <?php print $i + 1; ?>
                </td>
                <td>
                    <a href="index.php?option=com_jshopping&controller=attributes&task=edit&attr_id=<?php print $key; ?>"><?php print $row['name']; ?></a>
                </td>
                <td>
                    <?php foreach ($row['answers'] as $key_ => $item) { if(!first) { print ", ";} $first = false;?>
                        <a href="index.php?option=com_jshopping&controller=attributesvalues&task=show&attr_id=<?php print $key ?>"><?php print $item['name']; ?></a>
                        <?php print $item['per'] . "%," ?>
                    <?php } ?>
                </td>
            </tr>
            <?php $i++;
        }
}

if(isset($_POST['date_from']) && $_POST['date_from'] != 0) {
    $date_from = date("y.m.d", strtotime($_POST['date_from']));
} else {
    $date_from = 0;
}

if(isset($_POST['date_to']) && $_POST['date_to'] != 0) {
    $date_to = date("y.m.d", strtotime($_POST['date_to']));
} else {
    $date_to = 0;
}

if($_POST['list'] != null && $_POST['sponsor'] != null) {
    calculateAnswers($_POST['list'], $_POST['sponsor'], $date_from, $date_to);
}

?>
