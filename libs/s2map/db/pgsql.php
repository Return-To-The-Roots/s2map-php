<?php

require_once("sql.php");

class SQL_pgsql extends SQL
{
	var $connection;
	var $queries;
	
	function SQL_pqsql($host, $user, $pass, $db)
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
			return (pg_connection_status($this->connection) == PGSQL_CONNECTION_OK);
		
		$this->connection = pg_connect ("host={$this->host} port=5432 dbname={$this->db} user={$this->user} password={$this->pass}");
		if($this->connection == 0)
			return 0;
		 
		$this->queries = 0;
		
		if($this->connection != 0)
			return (pg_connection_status($this->connection) == PGSQL_CONNECTION_OK);
		return 0;
	}
	
	function escape($escape)
	{
		return pg_escape_string($escape);
	}
	
	// Closes connection to Database, returns 1 for success
	function disconnect()
	{
		if(pg_close($this->connection))
			return 1;
		return 0;
	}
	
	function get_error()
	{
		return pg_last_error();
	}
	
	function get_queries()
	{
		return $this->queries;
	}
	
	// Executes an Query and returns one (!) Array of the Result
	function query_array($query)
	{
		$result = pg_exec($this->connection, $query);
		if (!$result)
			return 0;
		
		$this->queries++;
		
		$return = pg_fetch_array($result,0);
		
		pg_free_result($result);
		if(!$return)
			return 0;
		return $return;
	}
	
	// Executes an Query and returns all (!) Array's of the Results
	function query_multiple($query)
	{
		$result = pg_exec($this->connection, $query);
		if (!$result)
			return 0;
		
		$return = array();
		
		while ($row = pg_fetch_array($result))
		{
			$this->queries++;
			$return[] = $row;
		}
		pg_free_result($result);
		return $return;
	}
	
	// Executes an Query and returns 1 or 0 for successful / failed query
	function query_exec($query)
	{
		$this->queries++;
		
		$result = pg_exec($this->connection, $query);
		if (!$result)
			return 0;
		return 1;
	}
}
