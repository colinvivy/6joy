<?php
define('PATH_ROOT', '..');
define('PATH_API', '../api');
include PATH_API.'/config.php';

$uid = "seaprince";
if (!in_array($uid, (array)$admins)) exit('Access Denied');
$mod = preg_replace('/[^a-z_]/', '', @$_GET['mod']);
if (!$mod) $mod = 'index';

$mod_file = PATH_ADMIN."/tpl/$mod.tpl.html";
if ($mod && is_file($mod_file)) {
    echo file_get_contents($mod_file);
}
