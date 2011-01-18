<?php

// $Id$
//
// Copyright (c) 2005 - 2010 Settlers Freaks (sf-team at siedler25.org)
//
// This file is part of Return To The Roots.
//
// Return To The Roots is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.
//
// Return To The Roots is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Return To The Roots. If not, see <http://www.gnu.org/licenses/>.

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
