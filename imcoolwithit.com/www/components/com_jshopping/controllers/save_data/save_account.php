<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../' ));
require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/lib/factory.php' );
require_once ( JPATH_BASE .'/components/com_jshopping/controllers/member.php' );
JTable::addIncludePath(JPATH_BASE . '/components/com_jshopping/tables');
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_jshopping/models');
JFactory::getApplication('site')->initialise();
$current_user = JSFactory::getUser()->user_id;
$jshopConfig = JSFactory::getConfig();
$conf = new JConfig();

// Return HTML
function answer()
{
    global $success;

    if($success){
        echo 'success';
    } else {
        echo 'error';
    }
}

function img_thumb_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);
    if ($size === false) return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio       = max($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    if($use_x_ratio){
        $new_width   = $width;
        $new_height  = floor($size[1] * $ratio);
    } else {
        $new_width   = floor($size[0] * $ratio);
        $new_height  = $height;
    }

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;
}

function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);
    if ($size === false) return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    if( ($width < $size[0]) || ($height < $size[1]) ){
        if ($size[1] >= $size[0]){
            $base_size = $height;
        } else {
            $base_size = $width;
        }

        if($base_size == $width){
            $new_width   = $width;
            $new_height  = $width*($size[1]/$size[0]);
        } else {
            $new_height  = $height;
            $new_width   = $height*($size[0]/$size[1]);
        }
    } else {
        $new_width = $size[0];
        $new_height = $size[1];
    }

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($new_width, $new_height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;
}

/*function uploadPhotoWhitFB($photoFB){
    $jshopConfig = JSFactory::getConfig();
    $photo_path_fb = $jshopConfig->img_facebook;

    $parseImgUrl = pathinfo($photoFB);
    $name = explode('/', $parseImgUrl['dirname']);
    $name_img = 'user_'.$name[3].'.jpg';

    $url = $photoFB;
    $data = file_get_contents($url);
    $uploadPhoto = $photo_path_fb."/".$name_img;
    $uploadPhotoPath = 'images/user_photo/'.$name_img;

    $file = fopen($uploadPhoto, 'w+');
    fputs($file, $data);
    fclose($file);

    return $uploadPhotoPath;
}*/

function uploadPhotoWhitFB($photoFB, $username){
    $jshopConfig = JSFactory::getConfig();
    $config = new JConfig();
    $photo_path_fb = $jshopConfig->img_facebook;

    $name_img = $username . '_' . date("YmdHis") . '.jpg';

    $photo_avatar = $photoFB . '?width='.$config->size_avatar_big_w.'&height='.$config->size_avatar_big_h;

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    $data = file_get_contents($photo_avatar, false, stream_context_create($arrContextOptions));
    $uploadPhoto = $photo_path_fb."/".$name_img;
    $file = fopen($uploadPhoto, 'w+');
    fputs($file, $data);
    fclose($file);

    $img_info = getimagesize($uploadPhoto);

    if( $img_info[0] >= $config->size_avatar_big_w && $img_info[1] >= $config->size_avatar_big_h ){
        img_resize($uploadPhoto, $uploadPhoto, $config->size_avatar_big_w, $config->size_avatar_big_h);
    } else {
        img_resize($uploadPhoto, $uploadPhoto, $config->size_avatar_medium_w, $config->size_avatar_medium_h);
    }
    $uploadPhoto_medium = $photo_path_fb."/medium/".$name_img;
    img_resize($uploadPhoto, $uploadPhoto_medium, $config->size_avatar_medium_w, $config->size_avatar_medium_h);
    $uploadPhoto_small = $photo_path_fb."/small/".$name_img;
    img_resize($uploadPhoto, $uploadPhoto_small, $config->size_avatar_small_w, $config->size_avatar_small_h);

    $user = JSFactory::getUser()->user_id;
    $filename = 'facebook_' . md5(microtime() . rand(0, 9999)) . '.jpg';
    $path = JPATH_BASE . $config->path_albums_image . "user_" . $user . "/";
    $path_thumb = JPATH_BASE . $config->path_albums_image . "user_" . $user . "/thumb/";

    if (!is_dir($path)){
        mkdir($path);
        copy(JPATH_BASE . $config->path_albums_image . 'index.html', $path . 'index.html');
    }

    if (!is_dir($path_thumb)){
        mkdir($path_thumb);
        copy(JPATH_BASE . $config->path_albums_image . 'index.html', $path_thumb . 'index.html');
    }

    $photo_album = $photoFB . '?width=' .$config->size_album_photo_big_w . '&height=' . $config->size_album_photo_big_h;

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );

    $data = file_get_contents($photo_album, false, stream_context_create($arrContextOptions));
    $uploadPhoto = $path . $filename;
    $file = fopen($uploadPhoto, 'w+');
    fputs($file, $data);
    fclose($file);

    img_resize($path . $filename, $path . $filename, $config->size_album_photo_big_w, $config->size_album_photo_big_h);
    img_thumb_resize($path . $filename, $path_thumb . $filename, $config->size_album_photo_small_w, $config->size_album_photo_small_h);


    $db = JFactory::getdbo();
    $query = "INSERT "
        . "INTO `#__users_photos` (`user_id`, `photo`, `private`, `avatar`, `date`) "
        . "VALUES ({$user}, '{$filename}', 0, 1, '" . date("y-m-d h:i:s") . "')";
    $db->setquery($query);
    $db->query();

    /*$data_medium = file_get_contents($photoFB_medium);
    $uploadPhoto_medium = $photo_path_fb."/medium/".$name_img;
    $file = fopen($uploadPhoto_medium, 'w+');
    fputs($file, $data_medium);
    fclose($file);

    $data_small = file_get_contents($photoFB_small);
    $uploadPhoto_small = $photo_path_fb."/small/".$name_img;
    $file = fopen($uploadPhoto_small, 'w+');
    fputs($file, $data_small);
    fclose($file);

    $photoFB_big = $photoFB . '?width='.$config->size_avatar_big_w.'&height='.$config->size_avatar_big_h;
    $photoFB_medium = $photoFB . '?width='.$config->size_avatar_medium_w.'&height='.$config->size_avatar_medium_h;
    $photoFB_small = $photoFB . '?width='.$config->size_avatar_small_w.'&height='.$config->size_avatar_small_h;*/

    return $name_img;
}

function setSettings($username, $sex, $birthday, $postal_code, $looking_for, $age_look_from, $age_look_to, $distance, $city, $state, $longitude, $latitude, $user_id){
    $db = JFactory::getDbo();
    $year_birthday=JSFactory::getYearBirthday($birthday);

    $modelUser = JSFactory::getModel('user', 'jshop');
    $user_birthday = $modelUser->getDataUser($user_id, array('birthday'));
    $user_birthday_arr = explode('-', $user_birthday->birthday);
    $user_birthday = $year_birthday . "-" . $user_birthday_arr[1] . "-" . $user_birthday_arr[2];

    if(JSFactory::getAge($user_birthday) < $birthday){
        $user_birthday = ($year_birthday - 1) . "-" . $user_birthday_arr[1] . "-" . $user_birthday_arr[2];
    }
    if(JSFactory::getAge($user_birthday) > $birthday){
        $user_birthday = ($year_birthday + 1) . "-" . $user_birthday_arr[1] . "-" . $user_birthday_arr[2];
    }

    $user_shop = JSFactory::getTable('userShop', 'jshop');
    $user_shop->load($user_id);
    if(!$user_shop->register_activate):
        if($user_shop->fb_id != ""){
            $uploadPhoto = uploadPhotoWhitFB($user_shop->photo, $username);
            $user_shop->photosite = $uploadPhoto;
        }

        $query = "UPDATE {$db->quoteName('#__users')} "
        . "SET `username` = '{$db->escape($username)}' "
        . "WHERE `id`= {$user_id}";
        $db->setQuery($query);
        $db->execute();

        $session = JFactory::getSession();
        $referrer = $session->get('referrer');
        if(isset($referrer) && $referrer != 0){
            $query = "INSERT "
                   . "INTO {$db->quoteName('#__friends_refer')} (`referrer`, `recipient`) "
                   . "VALUES ({$referrer}, {$user_id})";
            $db->setQuery($query);
            $db->execute();
        }

        $user_shop->register_activate = 1;
    endif;

    $user_postal_code = $modelUser->getDataUser($user_id, array('zip'));

    if ($user_postal_code->zip == $postal_code){
        $query = "UPDATE jproject_jshopping_users "
               . "SET `u_name` = '{$username}', `sex` = '{$sex}', `birthday` = '{$user_birthday}', `looking_for` = {$looking_for}, `age_look_from` = {$age_look_from}, `age_look_to` = {$age_look_to}, `distance` = '{$distance}', `photosite` = '{$user_shop->photosite}', `register_activate` = {$user_shop->register_activate} "
               . "WHERE `user_id`= {$user_id}";
    } else {
        $query = "UPDATE jproject_jshopping_users "
               . "SET `u_name` = '{$username}', `sex` = '{$sex}', `birthday` = '{$user_birthday}', `zip` = '{$postal_code}', `looking_for` = {$looking_for}, `age_look_from` = {$age_look_from}, `age_look_to` = {$age_look_to}, `distance` = '{$distance}', `city` = '{$city}', `state` = '{$state}', `longitude` = {$longitude}, `latitude` = {$latitude}, `photosite` = '{$user_shop->photosite}', `register_activate` = {$user_shop->register_activate} "
               . "WHERE `user_id`= {$user_id}";
    }

    $db->setQuery($query);

    if($db->execute()){
        return true;
    } else {
        return false;
    }

}

function checkUserName($username){
    $current_user = JSFactory::getUser()->user_id;

    $db = JFactory::getDBO();

    $query = "SELECT COUNT(user_id) "
           . "FROM {$db->quoteName('#__jshopping_users')} "
           . "WHERE u_name = '{$db->escape($username)}' and user_id != {$current_user}";

    $db->setQuery($query);
    $result = $db->loadResult();

    return $result == 0 ? false : true;
}

$success = false;
if (isset($_POST['username']) && ($_POST['username'] != "" && strlen($_POST['username']) <= 15)) {

    if (!checkUserName($_POST['username'])){
        $username = $_POST['username'];
    } else {
        echo 'error username';
        exit;
    }
} else {
    echo 'error username';
    exit;
}

if (is_numeric($_POST['sex']) && in_array($_POST['sex'], array(1, 2))) { $sex = (int)$_POST['sex']; } else { echo 'error sex'; exit; }

if (is_numeric($_POST['birthday']) && $_POST['birthday'] >= 18) { $birthday = (int)$_POST['birthday']; } else { echo 'error birthday'; exit; }

if (is_numeric($_POST['zip']) && (strlen($_POST['zip']) == 5)) {
    $postal_code = $_POST['zip'];
    $zip_json = file_get_contents('http://api.zippopotam.us/us/' . $postal_code);
    $zip_array = json_decode($zip_json, true);
    if (count($zip_array) != 0){
        $city = $zip_array['places'][0]['place name'];
        $state = $zip_array['places'][0]['state abbreviation'];
        $longitude = $zip_array['places'][0]['longitude'];
        $latitude = $zip_array['places'][0]['latitude'];

        $session = JFactory::getSession();
        $session->set('latitude', $latitude);
        $session->set('longitude', $longitude);
    } else {
        echo 'error zip';
        exit;
    }
} else {
    echo 'error zip';
    exit;
}

if (is_numeric($_POST['looking_for']) && in_array($_POST['looking_for'], array(1, 2, 3))){
    $looking_for = (int)$_POST['looking_for'];
} else {
    echo 'error looking for';
    exit;
}

if (is_numeric($_POST['age_look_from']) && ($_POST['age_look_from'] >= 18 && $_POST['age_look_from'] <= 99)){
    $age_look_from = (int)$_POST['age_look_from'];
} else {
    echo 'error age from';
    exit;
}

if (is_numeric($_POST['age_look_to']) && ($_POST['age_look_to'] >= 18 && $_POST['age_look_to'] <= 99)) {
    $age_look_to = (int)$_POST['age_look_to'];
} else {
    echo 'error age to';
    exit;
}

$available_distance = array();
foreach ($jshopConfig->user_distance as $key => $value) { array_push($available_distance, $key); }
if (is_numeric($_POST['distance']) && in_array($_POST['distance'], $available_distance)){
    $distance = (int)$_POST['distance'];
} else {
    echo 'error distance';
    exit;
}

if(isset($_POST['relationship_type']) && $_POST['relationship_type'] != ""){
    $modelUser = JSFactory::getModel('user', 'jshop');
    $modelUser->setMembershipType($current_user, $_POST['relationship_type']);
}

$success = setSettings($username, $sex, $birthday, $postal_code, $looking_for, $age_look_from, $age_look_to, $distance, $city, $state, $longitude, $latitude, $current_user);

answer();