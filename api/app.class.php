<?php
require_once "config.php";

class App {
	var $data;
	
    function __construct($appname) {
    	$appfile = PATH_DATA."/appdata/".$appname.".cfg.php";
		$this->data = include $appfile;
    }
}

