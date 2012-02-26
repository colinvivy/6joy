<?php
session_start();

list($mod, $appname, $task, $type) = $paths;
$appname = preg_replace("[^a-z]", "", $appname);
$task = preg_replace("[^a-z]", "", $task);


$appfile = PATH_DATA."/appdata/".$appname.".cfg.php";


if ($task == 'preview') {
    // 处理app
    $data = include $appfile;
    $plot = new Plot();
    $plot->init($data, array('nickname'=>'DEMO'));
    $plot->output();
    exit;
}


if ($task == 'success') {
	$data = include $appfile;
    if ($type == 's') {
    	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		
		$status = $data['wbtext'];
		
		$pic_path = tempnam(PATH_DATA."/tmp", "sina");
		
        $plot = new Plot();
        $plot->init($data, array('nickname'=>$uid));
		$plot->output($pic_path);
		
		$ret = $c->upload($status, $pic_path);
		if (isset($ret['error_code']) && $ret['error_code'] > 0) {
        	header('Content-type: text/html; charset=utf-8');
        	die("微博发布失败！".$call_result['msg']);
        } else {
        	$wblink = "http://weibo.com/";
        }
    } else {
        /*
            [tencent_oauth_token] => 544b044180a54a65839185a4f450efcc
            [tencent_oauth_token_secret] => c841d56723145c40f7f2b39db98e2ec4
            [tencent_access_token] => df69db5fbac84e26aaed37f90dd50f1c
            [tencent_oauth_name] => astrologs
            [tencent_open_id] => CC3E549EC3DD0C5D991B93F4FAB79557
            [tencent_open_key] => 9B7626FC701304DB788D8D4129FAEA59
        */
        
        $plot = new Plot();
        $plot->init($data, array('nickname'=>$_SESSION['tencent_oauth_name']));

        $imgdata = $plot->output('return');

        OpenSDK_Tencent_Weibo::init($appkey, $appsecret);
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
        if ($call_result['ret'] == 0) {
        	$wblink = "http://t.qq.com/p/t/".$call_result['id'];
        } else {
        	header('Content-type: text/html; charset=utf-8');
        	die("微博发布失败！".$call_result['msg']);
        }
    }
}
if (is_file($appfile)) {
    $app = include $appfile;
} else {
    die('Invalid app');
}
