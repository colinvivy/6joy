<?php
require_once "config.php";

class DB {
	var $pdo;
	
    function __construct($dbconfig) {
    	$cfg = parse_url($dbconfig);
		$cfg['path'] = trim($cfg['path'], ' /');
		$dsn = "mysql:host={$cfg['host']};dbname={$cfg['path']}";
		$this->pdo = new PDO($dsn, $cfg['user'], $cfg['pass']);
    }
	
	function fetch($sql) {
		if (!preg_match("/^select/i", $sql)) {
			return false;
		}
		if (!preg_match("/ limit [0-9]/i", $sql)) {
			$sql .= ' limit 1';
		}
		$arr = array();
		foreach ($this->pdo->query($sql) as $row) {
			$arr[] = $row;
		}
		return $arr;
	}
}

