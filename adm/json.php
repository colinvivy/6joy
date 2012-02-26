<?php
define('PATH_ROOT', '..');
define('PATH_API', '../api');
include PATH_API.'/config.php';

$uid = "seaprince";
if (!in_array($uid, (array)$admins)) exit('Access Denied');
$mod = preg_replace('/[^a-z_]/', '', @$_REQUEST['mod']);
$act = preg_replace('/[^a-z_]/', '', @$_REQUEST['act']);
$callback = preg_replace('/[^a-zA-Z_\.]/', '', @$_REQUEST['callback']);
$domain = preg_replace('/[^a-zA-Z_\.]/', '', @$_REQUEST['domain']);


if (!$mod) $mod = 'index';
if (!$act) $act = 'void';
if (!$callback) $callback = 'void';

$mod_file = "./mod/$mod.mod.php";
if (is_file($mod_file)) {
    include $mod_file;
    $func = $mod."_".$act;
    if (function_exists($func)) {
        $result = $func();
        $domain_set = $domain ? "document.domain = ".json_encode($domain).";" : "";
        $jsonp = $domain_set.$callback."(".json_encode($result).");";
        output($jsonp);
    }
}

function output($resp) {
    $try = isset($_REQUEST['try']) ? intval($_REQUEST['try']) : 1;
    
    if ($try) {
        $resp = "try{".$resp."}catch(e){}";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $resp = "<script>$resp</script>";
    }

    if ($_SERVER['HTTP_REFERER']) {
        header("Content-Type: application/javascript");
    }

    echo $resp;
}
