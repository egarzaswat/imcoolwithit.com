<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

if(isset($_POST['latitude']) && $_POST['latitude'] != null && isset($_POST['longitude']) && $_POST['longitude'] != null) {
    $db = JFactory::getDBO();
    $query = "SELECT image, `name_" . $_POST['lang'] . "` as name, `short_description_" . $_POST['lang'] . "` as short_description, 3958.0*acos( sin(`latitude_" . $_POST['lang'] . "`/57.29577951)*sin(" . ($_POST['latitude']/57.29577951) . ") + cos(`latitude_" . $_POST['lang'] . "`/57.29577951 )*cos(" . ($_POST['latitude']/57.29577951 ) . ")*cos(`longitude_" . $_POST['lang'] . "`/57.29577951 - " . $_POST['longitude']/57.29577951 . ") ) as radius"
        ." FROM `#__jshopping_products` ORDER BY radius ASC limit 4";
    $db->setQuery($query);
    $sponsors = $db->loadAssocList(); ?>

    <div class="col-xs-12">
        <h1 class="font_header"><?php print $_POST['header'] ?></h1>
    </div>

    <?php
    foreach($sponsors as $temp){ ?>

        <div class="col-sm-3 col-xs-12">
            <img src="<?php print '/images/places/' . $temp['image']; ?>">
            <span><?php print $temp['name']; ?></span>
            <?php if (strlen($temp['short_description']) > 100) {
                $string = strip_tags($temp['short_description']);
                $string = substr($string, 0, 100);
                $string = rtrim($string, "!,.-");
                $string = substr($string, 0, strrpos($string, ' '));
                $temp['short_description'] = $string . "… ";
            }?>
            <br/>
            <span><?php print $temp['short_description']; ?></span>
        </div>

    <?php }
} ?>
