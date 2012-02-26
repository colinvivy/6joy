<script>
	var returnvalue,newwin;
	function go_oauth(){
		if(1==0){
			returnvalue=window.showModalDialog("?go_oauth","dialogWidth=300px;dialogHeight=200px");
			if(returnvalue==undefined){
				alert("没有返回值");
		 	//return;
		 	}else{
		 		alert(returnvalue);
		 	}
		}else{
		newwin=window.open("?go_oauth","","width=700,height=680,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,location=yes,resizable=no,status=no");
		}
	}
	
	function closeOpener(name,access_token,access_token_secret){
		//alert(window.opener);
		window.opener.returnValue(name,access_token,access_token_secret);
		window.close();
	}
	
	function returnValue(name,access_token,access_token_secret){
		document.write([name,access_token,access_token_secret].join(","));
		if(newwin){newwin.close();}
		location.reload();
	}	
</script>
<?php
/**
 * just a demo
 *
 * 仅仅是个demo，未有严格考虑，请不要使用这个简单逻辑到生产环境。
 *
 */
error_reporting('0');
//设置include_path 到 OpenSDK目录
set_include_path(dirname(dirname(__FILE__)) . '/lib/');
require_once 'OpenSDK/Tencent/Weibo.php';

include 'appkey.php';

OpenSDK_Tencent_Weibo::init($appkey, $appsecret);

//打开session
session_start();
header('Content-Type: text/html; charset=utf-8');
$exit = false;
if(isset($_GET['exit']))
{
	unset($_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN]);
	unset($_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN]);
	unset($_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET]);
	unset($_SESSION[OpenSDK_Tencent_Weibo::OPENID]);
	unset($_SESSION[OpenSDK_Tencent_Weibo::OPENKEY]);
	//echo '<a href="?go_oauth">点击去授权</a>';
	echo '<a href="javascript:go_oauth();">点击去授权</a>';
}
else if(	isset($_SESSION[OpenSDK_Tencent_Weibo::OPENID]) &&
		 	isset($_SESSION[OpenSDK_Tencent_Weibo::OPENKEY]) &&
		 	isset($_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN]) &&
		 	isset($_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET]))
{
	//已经取得全部授权数据
	echo '你已经获得授权。你的授权信息:<br />';
	echo 'Access token: ' , $_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN] , '<br />';
	echo 'oauth_token_secret: ' , $_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] , '<br />';
	echo 'openid: ' , $_SESSION[OpenSDK_Tencent_Weibo::OPENID] , '<br />';
	echo 'openkey: ' , $_SESSION[OpenSDK_Tencent_Weibo::OPENKEY] , '<br />';
	
	//三个例子，演示两种不同的调用方式：
	
	//使用 OAuth 方式调用接口
	$api_name = 'user/other_info';
	$params=array(
					'format'=>'json',
					'name'=>'api_weibo'
					);
	$call_result = OpenSDK_Tencent_Weibo::call($api_name,$params);
	echo '</br>OAuth call_result:</br><pre>';
	print_r($call_result);
	echo '</pre>';
	echo '</br>=====================================================</br>';
	
	//使用 openid&openkey 方式调用接口
	$api_name = 'user/info';
	$call_result = OpenSDK_Tencent_Weibo::call($api_name,array(),'get',false,false);
	echo '</br>openid&openkey call_result:</br><pre>';
	print_r($call_result);
	echo '</pre>';
	
	//使用 openid&openkey 方式调用接口,发送一个带图片的微博
	$api_name = 't/add_pic';
	$call_result = OpenSDK_Tencent_Weibo::call($api_name, array(
							'content' => '测试，发表一条带图片的微博',
							'clientip' => '123.119.32.253',
							), 'POST', array(
								'pic' => array(
									'type' => 'image/jpg',
									'name' => '0.jpg',
									'data' => file_get_contents('test.png'),
								)),false);
	
	echo '</br>openid&openkey call_result:</br><pre>';
	print_r($call_result);
	echo '</pre>';
	
}
else if( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) //第5，6步
{
	//从Callback返回时
	if(OpenSDK_Tencent_Weibo::getAccessToken($_GET['oauth_verifier']))
	{
		//此时已经可以正常调用CGI
		//$uinfo = OpenSDK_Tencent_Weibo::call('user/info');
		/*
		echo '从Opent返回并获得授权。你的微博帐号信息为：<br />';
		echo 'Access token: ' , $_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN] , '<br />';
		echo 'oauth_token_secret: ' , $_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] , '<br />';
		echo '你的微博帐号信息为:<br /><pre>';
		var_dump($uinfo);
		*/
		//echo '<script>closeOpener("'.$uinfo["data"]["name"].'","'.$_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN].'","'.$_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET].'");</script>';
		//echo '<textarea style="width:100%;height:600px;"><script>if(window.showModalDialog){window.returnValue="'.$uinfo["data"]["name"].','.$_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN].','.$_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET].','.$_SESSION[OpenSDK_Tencent_Weibo::OPENID].','.$_SESSION[OpenSDK_Tencent_Weibo::OPENKEY].'";window.close();}else{closeOpener("'.$uinfo["data"]["name"].'","'.$_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN].'","'.$_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET].'");}</script></textarea>';
		//echo '<script>closeOpener("'.$uinfo["data"]["name"].'","'.$_SESSION[OpenSDK_Tencent_Weibo::ACCESS_TOKEN].'","'.$_SESSION[OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET].'");</script>';
		echo '<script>closeOpener("'.''.'","'.''.'","'.''.'");</script>';
	
	}
	else
	{
		var_dump($_SESSION);
		echo '获得Access Tokn 失败';
	}
	$exit = true;
}
else if(isset($_GET['go_oauth'])) //第1，2，3，4步
{	$mini=true;
	$callback = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	$request_token = OpenSDK_Tencent_Weibo::getRequestToken($callback);
	$url = OpenSDK_Tencent_Weibo::getAuthorizeURL($request_token);
	header('Location: ' . $url);
}
else
{
	//echo '<a href="?go_oauth">点击去授权</a>';
	echo '</br><a href="javascript:go_oauth();">点击去授权</a></br>';
}

if($exit)
{
	echo '</br><a href="?exit">退出再来一次</a></br>';
}