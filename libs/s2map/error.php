<?php

// $Id$
//
// Copyright (c) 2005 - 2011 Settlers Freaks (sf-team at siedler25.org)
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

#error_reporting(error_reporting()&~E_NOTICE);

if(!function_exists("error_handler"))
{
	$error_id = uniqid();
	error_log("$error_id | ---- ".implode(', ', $_GET)."\n\n", 3, __DIR__."/error.log");
	
	function error_handler($errno, $errstr, $errfile, $errline)
	{
		global $error_id;
		
		error_log("$error_id | $errfile:$errline - $errstr\n", 3, __DIR__."/error.log");
		
		/*$trace = debug_backtrace();
		
		foreach($trace as $t)
		{
			$errfile = $t['file'];
			$errline = $t['errline'];
			$errstr = $t['function']."(".join(', '.$t['args']).")";
			error_log("$error_id | => $errfile:$errline - $errstr\n", 3, __DIR__."/error.log");
		}*/
	}
	set_error_handler("error_handler", E_ALL|E_NOTICE|E_USER_NOTICE);
}
