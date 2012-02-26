<?php
require_once PATH_API."/cfg2ini.php";
require_once PATH_API."/plot.class.php";

$dir = PATH_DATA."/appini/";
$cfg_dir = PATH_DATA."/appdata/";
$d = dir($dir);

$allApps = array();
$allIDs = array();
while( false !== ($f = $d->read()) ) {
    if (substr($f, -4) !== '.ini') continue;

    $ini_file = $dir."/".$f;
    $cfg_file = str_replace(".ini", ".cfg.php", $cfg_dir."/".$f);
    $ini = parse_ini_file($ini_file);

    if (!is_file($cfg_file)) {
        $ini['state'] = '未审核';
    } else {
        $cfg = include $cfg_file;
        $ini_raw = file_get_contents($dir."/".$f);
        if ($ini_raw != cfg2ini($cfg)) {
            $ini['state'] = '已审核，有修改未同步';
        } else {
            $ini['state'] = '已审核';
        }
    }
    $ini['ini_mtime'] = filemtime($ini_file);
    $ini['cfg_mtime'] = is_file($cfg_file) ? filemtime($cfg_file) : 0;
    unset($ini['plot']);

    $allApps[$ini['appid']] = $ini;
    $allIDs[] = $ini['appid'];
}

function apps_add() {
    global $allApps, $allIDs;
    $task = isset($_REQUEST['task']) ? intval($_REQUEST['task']) : '';
    $seeds = isset($_REQUEST['seeds']) ? $_REQUEST['seeds'] : '';
    $cfg = ini2cfg($seeds);
    $appid = 0;
    if (!$cfg) {
        $ret = -1;
        $msg = "配置错误，请检查。";
    } else {
        $cfg['appid'] = preg_replace("/[^a-z0-9]/", "", $cfg['appid']);
        if (!$cfg['appid']) {
            $ret = -2;
            $msg = "appid字段错误，ID只能包含a-z0-9";
        } else if (!$cfg['name']) {
            $ret = -3;
            $msg = "name字段错误";
        } else if (in_array($cfg['appid'], $allIDs) && $task != 'edit') {
            $ret = -4;
            $msg = "appid已经被占用，ID只能包含a-z0-9";
        } else if ($task == 'edit' && !in_array($cfg['appid'], $allIDs)) {
            $ret = -5;
            $msg = "请不要改动appid字段";
        } else if (!isset($cfg['plot']) || 1>count($cfg['plot'])) {
            $ret = -6;
            $msg = "绘图配置错误";
        } else {
            $ret = 0;
            $appid = $cfg['appid'];
            $written = file_put_contents(PATH_DATA."/appini/$appid.ini", cfg2ini($cfg));
            $msg = "$written bytes written";
        }
    }
    return array(
        'ret' => $ret,
        'msg' => $msg,
        'appid' => $appid,
    );
}

function apps_list() {
    global $allApps, $allIDs;
    return array(
        'ret' => 0,
        'data' => array_values($allApps)
    );
}


function apps_get() {
    global $allApps, $allIDs;
    $appid = isset($_REQUEST['appid']) ? preg_replace("/]^a-z0-9]/", "", $_REQUEST['appid']) : '';
    $data = file_get_contents(PATH_DATA."/appini/$appid.ini");
    return array(
        'ret'   => 0,
        'appid' => $appid,
        'data'  => $data
    );
}

function apps_preview() {
    global $allApps, $allIDs;
    $task = isset($_REQUEST['task']) ? intval($_REQUEST['task']) : '';
    $seeds = isset($_REQUEST['seeds']) ? $_REQUEST['seeds'] : '';
    $cfg = ini2cfg($seeds);
    $appid = 0;
    if (!$cfg) {
        $ret = -1;
        $msg = "配置错误，请检查。";
    } else {
        $cfg['appid'] = preg_replace("/[^a-z0-9]/", "", $cfg['appid']);
        if (!$cfg['appid']) {
            $ret = -2;
            $msg = "appid字段错误，ID只能包含a-z0-9";
        } else if (!$cfg['name']) {
            $ret = -3;
            $msg = "name字段错误";
        } else if (in_array($cfg['appid'], $allIDs) && $task != 'edit') {
            $ret = -4;
            $msg = "appid已经被占用，ID只能包含a-z0-9";
        } else if ($task == 'edit' && !in_array($cfg['appid'], $allIDs)) {
            $ret = -5;
            $msg = "请不要改动appid字段";
        } else if (!isset($cfg['plot']) || 1>count($cfg['plot'])) {
            $ret = -6;
            $msg = "绘图配置错误";
        } else {
            $ret = 0;
            $appid = $cfg['appid'];
            $msg = "/static/preview/$appid.png";
            $plot = new Plot();
            $plot->init($cfg, array('nickname'=>'PREVIEW'));
            $plot->output(PATH_ROOT.$msg);
        }
    }
    return array(
        'ret' => $ret,
        'msg' => $msg,
        'appid' => $appid,
    );
}
