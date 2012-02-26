<?php
require_once "config.php";

class Connect {
	private $type;
	private $instance;
	private $access_token;
	private $openid;
	private $callback;
	
	function __construct($type) {
		//q=qq,s=sina,z=qz,f=f8,t=twt,g=g+
		$this->type = $type;
		$this->callback = "http://{$_SERVER['HTTP_HOST']}/login/{$type}/cb/";
		$method = "{$this->type}_init";
		return $this->$method();
	}
	
	public function getURL() {
		$method = "{$this->type}_get_url";
		return $this->$method();
	}
	
	public function getInfo() {
		$method = "{$this->type}_get_info";
		return $this->$method();
	}
	
	public function postPic($text, $pic) {
		$method = "{$this->type}_post_pic";
		return $this->$method($text, $pic);
	}
	
	private function q_init() {
		OpenSDK_Tencent_Weibo::init( Q_AKEY, Q_SKEY );
	}
	private function q_get_url() {
		$mini = true;
        $request_token = OpenSDK_Tencent_Weibo::getRequestToken($this->callback);
        $url = OpenSDK_Tencent_Weibo::getAuthorizeURL($request_token);
		return $url;
	}
	private function q_get_info() {
		$api_name = 'user/info';
		$call_result = OpenSDK_Tencent_Weibo::call($api_name,array(),'get',false,false);
		
		$user = new ConnectUserInfo();
		$user->access_secret = $_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET];
		$user->access_token = $_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN];
		$user->openid = $_SESSION[OpenSDK_Tencent_Weibo::OPENID];
		$user->openkey = $_SESSION[OpenSDK_Tencent_Weibo::OPENKEY];
		$user->nickname = $_SESSION[OpenSDK_Tencent_Weibo::OAUTH_NAME];
		$user->avatar = $call_result['data']['head'];
		$user->expire = 0;
		$user->type = 'q';
		$user->json = json_encode($call_result['data']);
		
		return $user;
	}
	private function q_post_pic($text, $pic) {
		$api_name = 't/add_pic';
        $call_result = OpenSDK_Tencent_Weibo::call($api_name, array(
							'content' => $data['wbtext'],
							'clientip' => $_SERVER['REMOTE_ADDR'],
							), 'POST', array(
								'pic' => array(
									'type' => 'image/png',
									'name' => '0.png',
									'data' => $imgdata,
								)),false);
		return $call_result['ret'] == 0;
	}
	
	private function s_init() {
	}
	private function s_get_url() {
		$o = new SaeTOAuthV2( S_AKEY , S_SKEY );
		$code_url = $o->getAuthorizeURL( $this->callback );
		return str_replace('&amp;', '&', $code_url);
	}
	private function s_get_token() {
		$o = new SaeTOAuthV2( S_AKEY , S_SKEY );
		$keys = array();
        $keys['code'] = $_REQUEST['code'];
        $keys['redirect_uri'] = $this->callback;
		$token = $o->getAccessToken( 'code', $keys );
		
		if (!$token) return false;
		
		$this->access_token = $token['access_token'];
		$this->openid = $token['uid'];
		return array(
			'access_token' => $token['access_token'],
			'openid' => $token['uid'],
		);
	}
	private function s_get_info() {
		$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $this->access_token );
		$userinfo = $c->show_user_by_id($this->openid);
		
		$user = new ConnectUserInfo();
		$user->access_secret = '';
		$user->access_token = $this->access_token;
		$user->openid = $this->openid;
		$user->openkey = '';
		$user->nickname = $userinfo['screen_name'];
		$user->avatar = $userinfo['profile_image_url'];
		$user->expire = $_SESSION['expires_in'];
		$user->type = 's';
		$user->json = json_encode($userinfo);
		
		return $user;
	}
	private function s_post_pic($text, $pic) {
		$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $this->access_token );
		$ret = $c->upload($text, $pic);
		return !(isset($ret['error_code']) && $ret['error_code'] > 0);
	}
	
	private function z_init() {
	}
	private function z_get_url() {
	}
	private function z_get_info() {
	}
	private function z_post_pic($text, $pic) {
	}
	
	private function f_init() {
	}
	private function f_get_url() {
	}
	private function f_get_info() {
	}
	private function f_post_pic($text, $pic) {
	}
	
	private function t_init() {
	}
	private function t_get_url() {
	}
	private function t_get_info() {
	}
	private function t_post_pic($text, $pic) {
	}
	
	private function g_init() {
	}
	private function g_get_url() {
	}
	private function g_get_info() {
	}
	private function g_post_pic($text, $pic) {
	}
}

class ConnectUserInfo {
	public $access_token;
	public $access_secret;
	public $type;
	public $openid;
	public $openkey;
	public $expire;
	public $nickname;
	public $avatar;
	public $json;
}

