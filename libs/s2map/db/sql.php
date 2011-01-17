<?php

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
