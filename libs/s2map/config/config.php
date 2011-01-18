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
