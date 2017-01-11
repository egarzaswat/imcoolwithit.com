<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();
$jshopConfig = JSFactory::getConfig();

$modelUsersList = JSFactory::getModel('usersList', 'jshop');
$searchParams = $modelUsersList->searchParamsCurrentUser(JSFactory::getUserShop());
$latitude = $searchParams['latitude'];
$longitude = $searchParams['longitude'];
$lang = JSFactory::getLang()->lang;

$user_id = 0;
if (isset($_POST['user_id']) && (int)$_POST['user_id'] != 0) {
    $user_id = (int)$_POST['user_id'];
}

if(isset($_POST['category_id']) && (int)$_POST['category_id'] != 0) {
    $category_id = (int)$_POST['category_id'];
    $db = JFactory::getDBO();
    $query = "SELECT category_image, `name_{$lang}` as name "
           . "FROM {$db->quoteName('#__jshopping_categories')} "
           . "WHERE category_id = {$category_id}";
    $db->setQuery($query);
    $category = $db->loadAssocList();

    $query = "SELECT p.product_id, p.image, p.`name_{$lang}` as name, 3958.0*acos( sin(`latitude_{$lang}`/57.29577951)*sin(" . ($latitude/57.29577951) . ") + cos(`latitude_{$lang}`/57.29577951 )*cos(" . ($latitude/57.29577951 ) . ")*cos(`longitude_{$lang}`/57.29577951 - " . $longitude/57.29577951 . ") ) as radius FROM {$db->quoteName('#__jshopping_products_to_categories')} as ptc LEFT JOIN {$db->quoteName('#__jshopping_products')} as p ON ptc.product_id = p.product_id WHERE ptc.category_id = {$category_id} ORDER BY radius";
    $db->setQuery($query);
    $sponsors_with_category = $db->loadAssocList(); ?>


    <div class="lincup-offers-for-category">
        <div class="lincup-offers-category first-column"><?php print $category[0]['name']; ?></div>
        <div class="lincup-offers-items row">
            <?php foreach($sponsors_with_category as $key => $temp){
                    $link = '/' . JText::_('LINK_MEETING') . '?user=' . $user_id . '&sponsor=' . $temp['product_id']; ?>
                    <div class="padding-null <?php if($key%3 == 1){print 'dark-column';} ?> col-sm-4 col-xs-12">
                        <a <?php print 'href="' . $link . '"'; ?> class="sponsor" title="<?php print $temp['name']; ?>">
                            <img src="<?php print '/images/places/' . $temp['image']; ?>">
                        </a>
                    </div>
            <?php } ?>
        </div>
        <?php if ($user_id != 0) {
            $return_link = 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS') . '?user=' . $user_id;
        } else {
            $return_link = 'https://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_SPONSORS');} ?>

        <a <?php print 'href="' . $return_link . '"';?> class="all-offers">All Sponsors</a>
    </div>

<?php } ?>