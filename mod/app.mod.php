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
    if ($type == 's') {
    } else {
        /*
            [tencent_oauth_token] => 544b044180a54a65839185a4f450efcc
            [tencent_oauth_token_secret] => c841d56723145c40f7f2b39db98e2ec4
            [back] => cboauthtokendfbeeacbfeboauthverifieropenidopenkey
            [tencent_access_token] => df69db5fbac84e26aaed37f90dd50f1c
            [tencent_oauth_name] => astrologs
            [tencent_open_id] => CC3E549EC3DD0C5D991B93F4FAB79557
            [tencent_open_key] => 9B7626FC701304DB788D8D4129FAEA59
        */
        
        $data = include $appfile;
        $plot = new Plot();
        $plot->init($data, array('nickname'=>'DEMO'));

        $imgdata = $plot->output('return');

        OpenSDK_Tencent_Weibo::init($appkey, $appsecret);
        $api_name = 't/add_pic';
        $call_result = OpenSDK_Tencent_Weibo::call($api_name, array(
							'content' => '测试，发表一条带图片的微博',
							'clientip' => '123.119.32.253',
							), 'POST', array(
								'pic' => array(
									'type' => 'image/png',
									'name' => '0.png',
									'data' => $imgdata,
								)),false);
        var_dump($call_result);
    }
    exit;
}
if (is_file($appfile)) {
    $app = include $appfile;
} else {
    die('Invalid app');
}
