<?php

require_once("sql.php");

class SQL_mysql extends SQL
{
	var $connection;
	var $queries;
	
	function SQL_mysql($host, $user, $pass, $db)
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db	 = $db;
	}
	
	// Connects to Database, returns 1 for success
	function connect()
	{
		if($this->connection != 0)
			return mysql_ping($this->connection);
		
		$this->connection = mysql_connect($this->host,$this->user,$this->pass);
		if($this->connection == 0)
			return 0;
		
		if(!mysql_select_db($this->db))
			return 0;
		
		$this->queries = 0;
		
		if($this->connection != 0)
			return mysql_ping($this->connection);
		return 0;
	}
	
	// Closes connection to Database, returns 1 for success
	function disconnect()
	{
		if(mysql_close($this->connection))
			return 1;
		return 0;
	}
	
	function get_error()
	{
		return mysql_error();
	}
	function get_queries()
	{
		return $this->queries;
	}
	
	function escape($escape)
	{
	 	return mysql_real_escape_string($escape);
	}
	
	// Executes an Query and returns one (!) Array of the Result
	function query_array($query)
	{
		$this->last_query = $query;
		
		$result = mysql_query($query, $this->connection);
		if (!$result)
			return 0;
		
		$this->queries++;
		
		$return = mysql_fetch_array($result);
		
		mysql_free_result($result);
		if(!$return)
			return 0;
		return $return;
	}
	
	function query_count($query)
	{
		$val = $this->query_array($query);
		return $val[0];
	}
	
	function query_one($query)
	{
		return $this->query_count($query);
	}
	
	// Executes an Query and returns all (!) Array's of the Results
	function query_multiple($query)
	{
		$this->last_query = $query;
		
		$result = mysql_query ($query, $this->connection);
		if (!$result)
			return array();
		
		if(mysql_num_rows($result) == 0)
			return array();
		
		$return = array();
		
		while ($row = mysql_fetch_array($result))
		{
			$this->queries++;
			$return[] = $row;
		}
		mysql_free_result($result);
		return $return;
	}
	
	// Executes an Query and returns 1 or 0 for successful / failed query
	function query_exec($query)
	{
		$this->last_query = $query;
		
		$this->queries++;
		
		$result = mysql_query ($query, $this->connection);
		if (!$result)
			return 0;
		return 1;
	}
}
