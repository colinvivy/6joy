<?php
session_start();

require_once PATH_API."/db.class.php";

$task = $task == 't' ? 't' : 's';
$act = $act ? 'cb' : '';

$db = new DB($dbconfig);

//$arr = $db->fetch("SELECT * FROM t_connect");
$arr = $db->pdo->exec("INSERT INTO t_connect (Fuid, Fopenid) VALUES (11,'123123')");

if (!$arr) {
	print_r($db->pdo->errorInfo());
	die('');
}

