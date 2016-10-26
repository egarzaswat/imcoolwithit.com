<?php
session_start();
unset($_SESSION['token']);

function setNextPage ($page=false,$slashes=true,$CurServer=true,$protocol='http://') {
    $Server = ($CurServer) ? $_SERVER['HTTP_HOST'].'/' : false;
    $Link = $protocol.$Server.$page;
    $Link.=($slashes&&$page!=false)?'/':false;
    header("location: ".$Link);
}

if(isset($_REQUEST['code'])) {
    $FacebookLogin = new FacebookLogin($_REQUEST['code']);
    setNextPage();
} else {
    if(isset($_SESSION['token']) && $_SESSION['token']) {
        $FacebookLogin = new FacebookLogin();
        var_dump($FacebookLogin->getInfo());
    } else {
        $FacebookLogin = new FacebookLogin();
        $Link = $FacebookLogin->genLink();
        echo 'enter by <a href="'.$Link.'">Facebook Login</a>';
    }
}


class FacebookLogin {
    protected $AppId      = '839017856152710';
    protected $ApiVersion = '2.3';
    protected $AppSecret  = '839b538a8dfad249a8ad7a0f3d15ed85';

    protected $token;

    protected $oauthUrl     = 'https://www.facebook.com/dialog/oauth';
    protected $tokenUrl     = 'https://graph.facebook.com/oauth/access_token';
    protected $userInfoUrl  = 'https://graph.facebook.com/me';

    public $RedirectTo = 'http://tmp.local/';

    public function __construct($Code=false){
        if($Code) {$this->getToken($Code);}
        elseif(isset($_SESSION['token']) && $_SESSION['token']) {$this->token = $_SESSION['token'];}
    }

    public function getInfo() {
        $Return = false;
        if($this->token) {
            $Return = $this->unJsonUrl($this->userInfoUrl.'?'.$this->genUrlParams(array('access_token'=>$this->token)));
        }
        return $Return;
    }

    private function getToken($Code){
        $Url = $this->tokenUrl.'?'.$this->genUrlParams(array(
                'client_id'=>$this->AppId,
                'client_secret'=>$this->AppSecret,
                'code'=>$Code,
                'redirect_uri'=>$this->RedirectTo,
            ));
        $TokenAnswer = $this->unJsonUrl($Url,false,'parse_str');
        if(is_array($TokenAnswer) && $TokenAnswer) {
            $this->token = (isset($TokenAnswer['access_token'])) ? $TokenAnswer['access_token'] : false;
            $_SESSION['token'] = $this->token;
        }
    }

    public function genLink(){
        return $this->oauthUrl.'?'.$this->genUrlParams(array(
            'client_id'=>$this->AppId,
            'redirect_uri'=>$this->RedirectTo,
            'response_type'=>'code',
            'scope'=> 'user_status,publish_stream,user_photos,user_photo_video_tags,user_birthday'
        ));
    }

    private function genUrlParams($params) {return urldecode(http_build_query($params));}
    private function unJsonUrl($url,$post=false,$type=false){
        $curl_handle=curl_init();

        curl_setopt($curl_handle, CURLOPT_URL,$url);
        if($post) {
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);

        $query = curl_exec($curl_handle);
        curl_close($curl_handle);

        if(!$type) {
            return json_decode($query,true);
        } else {
            $retAn = false;
            parse_str($query,$retAn);
            return $retAn;
        }
    }
}