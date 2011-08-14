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

require_once("libs/s2map/s2map.php");

if(isset($_GET['map']))
{
	$map = $_GET['map'];
	$mapmap = pathinfo($map);
	if(!strtolower($mapmap['extension']) == "swd" && !strtolower($mapmap['extension']) == "wld")
		die("no S2-Map");
	//$map = $mapmap['basename'];
}

$map = new s2map("maps/".$map);

header("Expires: Mon, 01 Jul 1990 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT");
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header("Content-Type: image/png", true);

echo $map->preview();

