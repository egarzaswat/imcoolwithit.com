<?php
	defined('_JEXEC') or die('Restricted access');
	jimport('joomla.user.helper');

    include_once "src/config.php";
    include_once "src/facebook.php";

    function isMobile(){
        $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
        $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
        $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
        $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $mobile = strpos($_SERVER['HTTP_USER_AGENT'],"Mobile");
        $symb = strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");
        $operam = strpos($_SERVER['HTTP_USER_AGENT'],"Opera M");
        $htc = strpos($_SERVER['HTTP_USER_AGENT'],"HTC_");
        $fennec = strpos($_SERVER['HTTP_USER_AGENT'],"Fennec/");
        $winphone = strpos($_SERVER['HTTP_USER_AGENT'],"WindowsPhone");
        $wp7 = strpos($_SERVER['HTTP_USER_AGENT'],"WP7");
        $wp8 = strpos($_SERVER['HTTP_USER_AGENT'],"WP8");
        if ($iphone || $android || $palmpre || $ipod || $berry || $mobile || $symb || $operam || $htc || $fennec || $winphone || $wp7 || $wp8 === true) {
            return true;
        } else {
            return false;
        }
    }


    $facebook = new Facebook(array(
        'appId' => $config['App_ID'],
        'secret' => $config['App_Secret'],
    ));

    $user = $facebook->getUser();
    if($user) {
        $user_profile = $facebook->api('/me');

        if(!isset($user_profile['email']) || $user_profile['email'] == ''){
            $session = JFactory::getSession();
            $session->destroy();

//            unset($_SESSION['__default']);
//            unset($_SESSION['fb_555433471263876_code']);
//            unset($_SESSION['fb_555433471263876_access_token']);
//            unset($_SESSION['fb_555433471263876_user_id']);
            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_LOGIN') . '?verify_email=0');
            die();
        }

        $user_profile['photo'] = 'http://graph.facebook.com/' . $user . '/picture';

        if (isset($user_profile['id'])) {
            $user_id = JUserHelper::getUserIdFb($user_profile['id']);
//
//            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_LOGIN') . '?verify_email=$user_id');
//            die();

            if (!$user_id) {
                $instance = JUser::getInstance();
                $instance->name = $user_profile['first_name'] . ' ' . $user_profile['last_name'];
                $instance->fb_id = $user_profile['id'];
                $instance->email = $user_profile['email'];
                if ($user_profile['gender'] == 'male') {
                    $instance->sex = 2;
                } elseif ($user_profile['gender'] == 'female') {
                    $instance->sex = 1;
                } else {
                    $instance->sex = 0;
                }

                $birthday_array = explode('/', $user_profile['birthday']);
                $instance->birthday = $birthday_array[2] . '-' . $birthday_array[0] . '-' . $birthday_array[1];

                $userDataSerialize = serialize($user_profile);
                $instance->setParam('params', $userDataSerialize);

                $db = JFactory::getDBO();
                $query = "SELECT `user_id` FROM `#__jshopping_users` WHERE `email` = '" . $instance->email . "'";
                $db->setQuery($query);
                $user_id_ex = $db->loadResult();

                if ($user_id_ex > 0) {
                    $query = "UPDATE `#__jshopping_users` SET `f_name` = '" . $user_profile['first_name'] . "', `l_name` = '" . $user_profile['last_name'] . "', `fb_id` = '" . $instance->fb_id . "', `register_date` = '" . date("Y-m-d H:i:s") . "', `sex` = $instance->sex, `birthday` = '" . $instance->birthday . "' WHERE `email`  = '" . $instance->email . "'";
                    $db->setQuery($query);
                    $db->query();

                    $query = "UPDATE `#__users` SET `name` = '" . $user_profile['first_name'] . " " . $user_profile['last_name'] . "', `fb_id` = '" . $instance->fb_id . "', `registerDate` = '" . date("Y-m-d H:i:s") . "', `sex` = $instance->sex, `birthday` = '" . $instance->birthday . "' WHERE `email` = '" . $instance->email . "'";
                    $db->setQuery($query);
                    $db->query();

                    $instance = JUser::getInstance($user_id_ex);
                    $session = JFactory::getSession();
                    $instance->guest = 0;
                    $instance->aid = 1;
                    $session->set('user', $instance);

                    $instance->setLastVisit();
                    header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_MY_ACCOUNT'));
                } else {
                    $query = "INSERT INTO `#__users` (`name`, `username`, `fb_id`, `email`, `birthday`, `registerDate`, `sex`) VALUES ('" . $instance->name . "', 'new_user', '" . $instance->fb_id . "', '" . $instance->email . "', '" . $instance->birthday . "', '" . date("Y-m-d H:i:s") . "', " . $instance->sex . ")";
                    $db->setQuery($query);
                    $db->query();
                    $id_last = $db->insertid();

                    $query = "INSERT INTO `#__jshopping_users` (`user_id`, `f_name`, `l_name`, `fb_id`, `photo`, `email`, `birthday`, `register_date`, `sex`) VALUES (" . $id_last . ", '" . $user_profile['first_name'] . "', '" . $user_profile['last_name'] . "', '" . $instance->fb_id . "', '" . $user_profile['photo'] . "', '" . $instance->email . "', '" . $instance->birthday . "', '" . date("Y-m-d H:i:s") . "', " . $instance->sex . ")";
                    $db->setQuery($query);
                    $db->query();

                    $query = "INSERT INTO `#__user_tokens` (`user_id`) VALUES (" . $id_last . ")";
                    $db->setQuery($query);
                    $db->query();

                    $userDataSerialize = serialize($_POST);
                    $instance->setParam('params', $instance);

                    $instance->guest = false;
                    $instance->id = $id_last;
                    $instance->save();

                    $instance = JUser::getInstance($id_last);
                    $session = JFactory::getSession();
                    $instance->guest = 0;
                    $instance->aid = 1;
                    $session->set('user', $instance);

                    $instance->setLastVisit();
                    if(isMobile()){
                        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST') . '?mobile=true');
                    } else {
                        header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST'));
                    }

                }
            } else {
                $instance = JUser::getInstance($user_id);
                $session = JFactory::getSession();
                $instance->guest = 0;
                $instance->aid = 1;
                $session->set('user', $instance);
                $instance->setLastVisit();
                if(isMobile()){
                    header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST') . '?mobile=true');
                } else {
                    header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_USERS_LIST'));
                }
            }
        }
    } else {
        // Get login URL
        $loginUrl = $facebook->getLoginUrl(array(
            'scope' => 'public_profile, email, user_birthday, user_photos',
        ));
        ?>
        <?php if(isset($_GET['verify_email']) && $_GET['verify_email'] == 0){ ?>
            <span class="verify_error">Please verify facebook email!</span>
        <?php } ?>
        <a id="fb_login" href="<?php print $loginUrl ?>"><i class="icon-fb"></i>
            <?php if($_SERVER['REQUEST_URI'] != '/join'){ ?>
                <span>Log in with Facebook</span>
            <?php } else { ?>
                <span>Join with Facebook</span>
            <?php }?>
        </a>
    <?php
    }