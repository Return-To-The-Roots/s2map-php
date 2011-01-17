<?php

// Daten setzen
$config = array();
$config['sql'] = array();

$config['sql']['backend'] = "mysql";
$config['sql']['host']    = "localhost";
$config['sql']['user']    = "";
$config['sql']['pass']    = "";
$config['sql']['db']      = "";

srand ((double)microtime()*1000000);

$path = explode('/', __FILE__);
unset($path[count($path)-1]);
$path = implode('/', $path). "/";

if(file_exists($path."config_local.php"))
	require_once($path."config_local.php");

unset($path);
