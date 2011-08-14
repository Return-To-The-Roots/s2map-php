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

$files = scandir("maps/");
foreach($files as $f)
$base = "maps/";

function scan($dir = "")
{
	global $base;

	$files = scandir($dir);
	foreach($files as $f)
	{
		$ff = pathinfo($dir.$f);
		if(strtolower($ff['extension']) == "swd" || strtolower($ff['extension']) == "wld")
		{
			$link = 'map.php?map='.substr($dir, strlen($base)).$f;
			echo '<p><a href="'.$link.'" onmouseover="javascript:document.getElementById(\''.$f.'\').style.height=null" onmouseout="javascript:document.getElementById(\''.$f.'\').style.height=\'200px\'"><img id="'.$f.'" style="border:0px; height: 200px" src="'.$link.'"></a></p>';
		}
		#echo $f."<br>\n";
		if(is_dir($dir.$f) && $f != ".svn" && $f != "." && $f != "..")
			scan($dir.$f."/");
	}
}

scan($base);
