<?php
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../' ));
require_once ( JPATH_BASE . '/configuration.php' );
require_once( __DIR__ . '/lib/facebook.php' );

session_start();
$conf = new JConfig();

$facebook = new Facebook(array(
    'appId'  => $conf->fb_app_id,
    'secret' => $conf->fb_app_secret,
    'cookie' => true
));

if(isset($_GET['fb'])) {
    if (!isset($_SESSION['token'])) {

        $token_url = "https://graph.facebook.com/oauth/access_token?"
            . "client_id=" . $conf->fb_app_id . "&redirect_uri=" . urlencode($conf->fb_login_callback_url)
            . "&client_secret=" . $conf->fb_app_secret . "&code=" . $_GET['code'];

        print $token_url;
        $response = file_get_contents($token_url);
        $params = null;
        parse_str($response, $params);
        $_SESSION['token'] = $params['access_token'];
        $graph_url = "https://graph.facebook.com/me?access_token=" . $params['access_token'];
    } else {
        $graph_url = "https://graph.facebook.com/me?access_token=" . $_SESSION['token'];
    }


    $s = file_get_contents($graph_url);
    $data = json_decode($s, true);
    var_dump($data);
} else {
        print '<a href="https://www.facebook.com/dialog/oauth?client_id='.$conf->fb_app_id.'&redirect_uri='.$conf->fb_login_callback_url.'&scope=email,read_stream">login</a>';
}