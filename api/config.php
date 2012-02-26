<?php
define('PATH_ADMIN', PATH_ROOT.'/adm');
define('PATH_DATA', PATH_ROOT.'/../appsdata');

$admins = array('seaprince');

$host = $_SERVER['HTTP_HOST'];

// tencent
$appkey = '801099155';
$appsecret = '2d65fa8c5e7979ca986c25ba097fa94d';
$callback = "http://$host/login/t/cb/";

// sina
define( "WB_AKEY" , '176417687' );
define( "WB_SKEY" , '8faac06d05cabdd592a5315df7021db4' );
define( "WB_CALLBACK_URL" , "http://$host/login/s/cb/" );

