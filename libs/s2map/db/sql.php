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

class SQL
{
	var $host;
	var $user;
	var $pass;
	var $db;
	var $last_query;
	
	function SQL( $host, $user, $pass, $db )
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db   = $db;
		
		$this->last_query = "";
	}
	function get_error()
	{
		return "";
	}
	function get_queries()
	{
		return 0;
	}
	function print_error($critical = 0)
	{
		$err = $this->get_error();
		if(!$err)
			return;
		
		$error = '
		<b style="color:red">
		Fehler: '.$err.'
		</b>
		<br>
		Letzter Query war:
		<pre>
		'.htmlentities($this->last_query).'
		</pre>
		';
		
		if($critical == 1)
			die($error);
		else
			echo $error;
	}
	function connect()
	{
		return 0;
	}
	function disconnect()
	{
		return 0;
	}
	function query_array()
	{
		return 0;
	}
	function query_count()
	{
		return 0;
	}
	function query_multiple()
	{
		return 0;
	}
	function query_one()
	{
		return 0;
	}
	function query_exec()
	{
		return 0;
	}
	function escape($escape)
	{
		return $escape;
	}
}
