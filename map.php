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
	if(strtolower($ff['extension']) == "swd" || strtolower($ff['extension']) == "wld")
		die("no S2-Map");
	$map = $mapmap['basename'];
}


$map = new s2map("maps/".$map);

header("Expires: Mon, 01 Jul 1990 00:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT"); 
header("Pragma: no-cache"); 
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header("Content-Type: image/png", true);

echo $map->preview();

//require("filehelper.php");
//
//if(!isset($_GET['map']))
//	$map = "s2map.swd";
//else
//{
//	$map = $_GET['map'];
//	$mapmap = pathinfo($map);
//	if(strtolower($ff['extension']) == "swd" || strtolower($ff['extension']) == "wld")
//		die("no S2-Map");
//	$map = $mapmap['basename'];
//}
//
//$file = fopen("/www/ra-doersch.de/nightly/maps/".$map, "rb");
//
//if(!freadid($file))               // WORLD_V1.0
//	die("no S2-Map");
//	
//$name = trim(fread($file, 20));   // "foo"
//$width = freadshort($file);       // w
//$height = freadshort($file);      // h
//$type = freadchar($file);         // green-/waste-/winterland
//$players = freadchar($file);      // nr players
//$author = trim(fread($file, 20)); // "bar"
//
//$type_array = array(
//	0 => array("PAL5.BBM", "GOU5.DAT"),
//	1 => array("PAL5.BBM", "GOU6.DAT"),
//	2 => array("PAL5.BBM", "GOU7.DAT")
//);
//
//// jump to first subheader
//fseek($file, 2342, SEEK_SET);
//
//if(!freadshid($file, 0x11))       // 11 27 0 0 0 0
//	die("invalid S2-Map");
//	
//$width = freadshort($file);       // w (again)
//$height = freadshort($file);      // h (again)
//
//$blocks = array(
//	"altitude" => b"",
//	"terrain1" => b"",
//	"terrain2" => b"",
//	"roads"    => b"",
//	"landscape"  => b"",
//	"objects"  => b"",
//	"animals"  => b"",
//	"unknown8" => b"",
//	"buildingoptions" => b"",
//	"unknown10" => b"",
//	"unknown11" => b"",
//	"resources" => b"",
//	"shadow"   => b"",
//	"unknown14" => b""
//);
//$block_keys = array_keys($blocks);
//
//for($i = 0; $i < 14; $i++)
//{
//	if(!freadshid($file, 0x10))       // 10 27 0 0 0 0
//		die("invalid S2-Subheader");
//
//	$width = freadshort($file);       // sub w
//	$height = freadshort($file);      // sub h
//	if(freadshort($file) != 1)        // 01 00
//		die("invalid S2-Sub-Id");
//	$length = freadint($file);        // length of block (w*h)
//	$blocks[$block_keys[$i]] = freadchars($file, $length);
//}
//
//fclose($file);
//
//
//
//$pal = array();
//$file = fopen($type_array[$type][0], "rb");
//fseek($file, 48, SEEK_SET);
//for($i = 0; $i < 256; $i++)
//{
//	$r = freadchar($file);
//	$g = freadchar($file);
//	$b = freadchar($file);
//	$pal[$i] = array("r" => $r, "g" => $g, "b" => $b);
//}
//fclose($file);
//
//$gou = array();
//$file = fopen($type_array[$type][1], "rb");
//for($i = 0; $i < 256; $i++)
//{
//	for($j = 0; $j < 256; $j++)
//	{
//		$gou[$i][$j] = freadchar($file);
//	}
//}
//fclose($file);
//
//function apply_shadow($index, $shadow)
//{
//	global $gou, $pal;
//	
//	$color = array();
//	$color['r'] = $pal[$gou[$shadow][$index]]['r'];
//	$color['g'] = $pal[$gou[$shadow][$index]]['g'];
//	$color['b'] = $pal[$gou[$shadow][$index]]['b'];
//	
//	return $color;
//}
//
////for($cccc = 205; $cccc < 256; $cccc++)
////{
//	set_time_limit(30);
//	
//	$size = 6;
//	$img = imagecreatetruecolor( $width /** 2*/ * $size, $height * $size );
//	
//	imagecolortransparent($img, imagecolorallocate($img, $pal[254]['r'], $pal[254]['g'], $pal[254]['b']));
//	
//	$terrain_colors = array(
//		0x00 => 0xE7,  // steppe meadow
//		0x40 => 0,     //
//		0x01 => 0xC3,  // mining 1
//		0x02 => 0x9D,  // snow
//		0x03 => 0xE8,  // swamp
//		0x04 => 0xC4,  // steppe
//		0x05 => 0x3D,  // water
//		0x06 => 0x9D,  //
//		0x07 => 0xC6,  //
//		0x08 => 0xE4,  // meadow1
//		0x48 => 0,     //
//		0x09 => 0xE6,  // meadow2
//		0x49 => 0,     //
//		0x0A => 0xE4,  // meadow3
//		0x4A => 0,     //
//		0x0B => 0xC4,  // mining 2
//		0x0C => 0xC6,  // mining 3
//		0x0D => 0xC0,  // mining 4
//		0x0E => 0xE9,  // 
//		0x4E => 0,     //
//		0x0F => 0xE7,  // flower
//		0x4F => 0,     //
//		0x10 => 0x39,  // lava
//		0x12 => 0xC2,  // mining meadow
//		0x52 => 0,     //
//	);
//	
//	for($x = 0; $x < $width; $x++)
//	{
//		for($y = 0; $y < $height; $y++)
//		{
//			$color = 0xFE;
//			$landscape_obj = $blocks['objects'][$y * $width + $x];
//			$l = false;
//			
//			// wald?
//			if( $landscape_obj >= 0xC4 && $landscape_obj <= 0xC6)
//			{
//				$color = $landscape_obj - 0x9B;
//			}
//			
//			/*if($landscape_obj != 0)
//				echo dechex($landscape_obj)."<br>";*/
//	
//			else
//			{
//				$terrain2 = $blocks['terrain2'][$y * $width + $x];
//				if($terrain2 > 0x40)
//					$terrain2 -= 0x40;
//				$color = $terrain_colors[$terrain2];
//				if($color == 0)
//					echo $terrain2;
//			}
//			
//			$shadow = 0x40;
//			if($terrain2 != 0x05 && $terrain2 != 0x10)
//				$shadow = $blocks['shadow'][$y * $width + $x];
//			
//			$color = apply_shadow($color, $shadow);
//			$color2 = imagecolorallocate($img, $color['r'], $color['g'], $color['b']);
//			
//			imagefilledrectangle($img, $x/* * 2*/ * $size, $y * $size, $x/* * 2*/ * $size + $size, $y * $size + $size, $color2);
//			//imagefilledrectangle($img, $x * 2 * $size + $size, $y * $size, $x * 2 * $size + $size * 2, $y * $size + $size, $color2);
//		}
//	}
//	
//	$text = $map." (".$players.")\n".$name." von ".$author;
//	
//	$size = 12;
//	$box = imagettfbbox($size, 0, "s2map.ttf", $text);
//	
//	imagefilledrectangle($img, 2, 2, 8 + $box[2], 8 + $size*1.45 *2, imagecolorallocate($img, 0xFF, 0xFF, 0xFF));
//	imagettftext($img, $size, 0, 4, 20, imagecolorallocate($img, 0x00, 0x00, 0x00), "s2map.ttf", $text);
//	
//	//print_r($box);
//	
//	$output = 1;
//	// deactivate Cache
//	if($output)
//	{
//		header("Expires: Mon, 01 Jul 1990 00:00:00 GMT"); 
//		header("Last-Modified: " . gmdate("D, d M Y H:i:s") ." GMT"); 
//		header("Pragma: no-cache"); 
//		header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
//		header("Content-Type: image/png", true);
//		
//		imagepng($img/*, $cccc.".png"*/);
//		imagedestroy($img);
//	}
////}
