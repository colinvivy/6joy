<?php
define('PATH_ROOT', dirname(__FILE__));
define('PATH_API', './api');
require_once PATH_API."/config.php";
require_once PATH_API."/plot.class.php";
include_once( PATH_API.'/sinasdk/saetv2.ex.class.php' );

set_include_path(PATH_API.'/qqsdk/lib/');
require_once 'OpenSDK/Tencent/Weibo.php';

$uri = $_SERVER['REQUEST_URI'];
$qmark = strpos($uri, '?');
if (false !== $qmark) {
    $uri = substr($uri, 0, $qmark);
}
$pathinfo = trim(preg_replace("/[^a-z\/]/", "", $uri), "/");
$paths = explode("/", $pathinfo);

$mod = $paths[0] ? $paths[0] : 'index';
$task = isset($paths[1]) ? $paths[1] : 'index';
$act = isset($paths[2]) ? $paths[2] : 'act';

$modfile = PATH_ROOT."/mod/".$mod.".mod.php";
$tplfile = PATH_ROOT."/tpl/".$mod.".tpl.html";
$acttpl = PATH_ROOT."/tpl/".$mod.'_'.$act.".tpl.html";

if (!is_file($modfile) && !is_file($tplfile)) {
    $tplfile = PATH_ROOT."/tpl/404.tpl.html";
}

// Logis
if (is_file($modfile)) {
    include $modfile;
}

// Templates
if (is_file($acttpl)) {
	include $acttpl;
}
else if (is_file($tplfile)) {
    include $tplfile;
}


