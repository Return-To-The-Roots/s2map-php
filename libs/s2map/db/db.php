<?php

// Initialization
function db_init($config)
{
	$sql = NULL;
	
	switch($config['backend'])
	{
	default:
		{
			die("Databasebackend \"".$config['backend']."\" unsupported!");
		} break;
	case "mysql":
		{
			require_once("mysql.php");
			
			$sql = new SQL_mysql($config['host'],$config['user'],$config['pass'],$config['db']);
		} break;
	case "pgsql":
		{
			require_once("pgsql.php");
			
			$sql = new SQL_pgsql($config['host'],$config['user'],$config['pass'],$config['db']);
		} break;
	}
	
	register_shutdown_function("db_cleanup", &$sql);

	if(!$sql->connect())
		die("Database Error: Connect failed");
		
	return $sql;
}

// Exit-handler
function db_cleanup($sql)
{
	$sql->disconnect();
}
