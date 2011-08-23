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

require_once(__DIR__."/error.php");
require_once(__DIR__."/file_helpers.php");

require_once(__DIR__."/s2pal.php");
require_once(__DIR__."/s2gou.php");
require_once(__DIR__."/s2lbm.php");

require_once(__DIR__."/config/config.php");
require_once(__DIR__."/db/db.php");

class s2map
{
	var $sql         = NULL;
	var $error       = array();
	var $hash        = "";
	var $file        = "";
	var $header      = array("name" => "", "author" => "", "width" => 0, "height" => 0, "type" => 0, "players" => 0);
	var $blocks      = array("altitude" => b"", "terrain1" => b"", "terrain2" => b"", "roads" => b"", "landscape" => b"", "objects" => b"", "animals" => b"", "unknown8" => b"", "buildingoptions" => b"", "unknown10" => b"", "unknown11" => b"", "resources" => b"", "shadow" => b"", "unknown14" => b"");

	function s2map($file)
	{
		global $config;
		$this->sql = db_init($config['sql']);

		$this->hash = md5_file($file);
		$this->file = $file;
	}

	private function _error($error)
	{
		$this->error[] = $error;
		trigger_error($error, E_USER_WARNING);
		return $error;
	}

	private function _load()
	{
		$wld = fopen($this->file, "rb");
		if(!$wld)
			return $this->_error("invalid file");

		if(!file_helpers::freadid($wld))                                 // WORLD_V1.0
			return $this->_error("no S2-Map");

		$this->header['name']    = file_helpers::freadstring($wld, 20);  // "foo"
		$this->header['width']   = file_helpers::freadshort($wld);       // w
		$this->header['height']  = file_helpers::freadshort($wld);       // h
		$this->header['type']    = file_helpers::freadchar($wld);        // green-/waste-/winterland
		$this->header['players'] = file_helpers::freadchar($wld);        // number of players
		$this->header['author']  = file_helpers::freadstring($wld, 20);  // "bar"

		// jump to first subheader
		fseek($wld, 2342, SEEK_SET);

		if(!file_helpers::freadshid($wld, 0x11))                    // 11 27 0 0 0 0
			return $this->_error("invalid S2-Map");

		$this->header['width']  = file_helpers::freadshort($wld);   // w (again)
		$this->header['height'] = file_helpers::freadshort($wld);   // h (again)

		$keys = array_keys($this->blocks);

		// read the blocks
		for($i = 0; $i < 14; $i++)
		{
			if(!file_helpers::freadshid($wld, 0x10))                // 10 27 0 0 0 0
				return $this->_error("invalid S2-Subheader");

			$width = file_helpers::freadshort($wld);                // sub w
			$height = file_helpers::freadshort($wld);               // sub h

			if(file_helpers::freadshort($wld) != 1)                 // 01 00
				return $this->_error("invalid S2-Sub-Id");

			$length = file_helpers::freadint($wld);                 // length of block (w*h)

			$this->blocks[$keys[$i]] = file_helpers::freadchars($wld, $length);
		}

		fclose($wld);

		return true;
	}

	private function _generate()
	{
		$files = array(
			0 => array("PAL5.BBM", "GOU5.DAT", "TEX5.LBM"),
			1 => array("PAL6.BBM", "GOU6.DAT", "TEX6.LBM"),
			2 => array("PAL7.BBM", "GOU7.DAT", "TEX7.LBM")
		);

		if(!$this->_load())
			return $this->error("failed to load map");

		$s2pal = new s2pal(__DIR__."/S2/GFX/PALETTE/".$files[$this->header['type']][0]);
		$s2gou = new s2gou(__DIR__."/S2/DATA/TEXTURES/".$files[$this->header['type']][1]);
		$s2lbm = new s2lbm(__DIR__."/S2/GFX/TEXTURES/".$files[$this->header['type']][2]);

		$size = 12; //6;
		$img = imagecreatetruecolor( $this->header['width'] * $size, $this->header['height'] * $size );

		imagecolortransparent($img, $s2pal->apply($img, $s2pal->transparent()));
		
		for($x = 0; $x < $this->header['width']; $x++)
		{
			for($y = 0; $y < $this->header['height']; $y++)
			{
				$color = 0xFE;
				$landscape_obj = $this->blocks['objects'][$y * $this->header['width'] + $x];

				// wald?
				if( $landscape_obj >= 0xC4 && $landscape_obj <= 0xC6)
				{
					$color = $landscape_obj - 0x9B;
				}

				else
				{
					$terrain2 = $this->blocks['terrain2'][$y * $this->header['width'] + $x];
					if($terrain2 > 0x40) // hafen?
						$terrain2 -= 0x40;

					$cx = -1; $cy = -1;
					$color = array();
					switch($terrain2)
					{
					// first row
					case  2: $cx = 16; $cy = 0; break;
					case  4: 
					case  7: $cx = 64; $cy = 0; break;
					case  3: $cx = 112; $cy = 0; break;
					case 15: $cx = 160; $cy = 0; break;

					// 2nd row
					case  1: $cx = 16; $cy = 48; break;
					case 11: $cx = 64; $cy = 48; break;
					case 12: $cx = 112; $cy = 48; break;
					case 13: $cx = 160; $cy = 48; break;

					// 3rd row
					case  0: $cx = 16; $cy = 96; break;
					case  8: $cx = 64; $cy = 96; break;
					case  9: $cx = 112; $cy = 96; break;
					case 10: $cx = 160; $cy = 96; break;

					// 4th row
					case 14: $color = array(236, 167, 233); break;
					case 18: $cx = 64; $cy = 144; break;
					case 16: $color = array(57, 248, 248); break;

					// water
					case  5: $color = array(61, 42, 240); break;
					case  6: $color = array(61, 42, 240); break;
					case 19: $color = array(61, 42, 240); break;


					// single pixel texture
					case 17: $cx = 0; $cy = 254; break;
					
					// unknown bottom 3
					case 20: $color = array(57, 248, 248); break;
					case 21: $color = array(57, 248, 248); break;
					case 22: $color = array(57, 248, 248); break;
					}
					
					if($cx != -1 && $cy != -1) {
						$color = $s2lbm->color($cx, $cy);
						//trigger_error("($cx, $cy) => $color");
					} else // hardcoded
						$color = $color[$this->header['type']];
				}

				$shadow = 0x40;
				if($terrain2 != 0x05 && $terrain2 != 0x10)
					$shadow = $this->blocks['shadow'][$y * $this->header['width'] + $x];

				$color = $s2pal->apply($img, $s2gou->apply($s2pal, $color, $shadow));

				imagefilledrectangle($img, $x * $size, $y * $size, $x * $size + $size, $y * $size + $size, $color);
			}
		}

		$text  = $this->header['name']." von ".$this->header['author'];
		$text .= "\n";
		$text .= "(".$this->header['width']."x".$this->header['height'].", ".$this->header['players']." players)";

		$size = 12;
		$box = imagettfbbox($size, 0, __DIR__."/s2map.ttf", $text);

		imagefilledrectangle($img, 2, 2, 6 + $box[2], 8 + $size*1.45 *2, imagecolorallocate($img, 0xFF, 0xFF, 0xFF));
		imagettftext($img, $size, 0, 4, 18, imagecolorallocate($img, 0x00, 0x00, 0x00), __DIR__."/s2map.ttf", $text);


		$temp_file = tempnam(sys_get_temp_dir(), 's2map');

		// create image file
		imagepng( $img, $temp_file );
		imagedestroy( $img );

		$img = fopen($temp_file, "rb");
		$img_data = fread($img, filesize($temp_file));
		fclose($img);

		unlink($temp_file);

		$map = fopen($this->file, "rb");
		$map_data = fread($map, filesize($this->file));
		fclose($map);

		$last_changed = filemtime($this->file);

		if(!$this->sql->query_exec("REPLACE INTO `s2map` (`hash`, `name`, `author`, `players`, `type`, `width`, `height`, `map`, `preview`, `last_changed`)
		                            VALUES (
		                                '".$this->sql->escape($this->hash)."',
		                                '".$this->sql->escape($this->header['name'])."',
		                                '".$this->sql->escape($this->header['author'])."',
		                                '".$this->sql->escape($this->header['players'])."',
		                                '".$this->sql->escape($this->header['type'])."',
		                                '".$this->sql->escape($this->header['width'])."',
		                                '".$this->sql->escape($this->header['height'])."',
		                                '".$this->sql->escape($map_data)."',
		                                '".$this->sql->escape($img_data)."',
		                                '".$this->sql->escape($last_changed)."')"))
			return 0;


		return $this->sql->query_one("SELECT preview FROM `s2map` WHERE `hash` = '".$this->sql->escape($this->hash)."'");
	}

	function get_error($n = "\n")
	{
		return implode($n, $this->error);
	}

	function preview()
	{
		$preview = $this->sql->query_array("SELECT preview,last_changed FROM `s2map` WHERE `hash` = '".$this->sql->escape($this->hash)."'");
		if($preview == 0 || $preview['last_changed'] != filemtime($this->file))
		{
			trigger_error("generate $this->file");
			return $this->_generate();
		}

		return $preview['preview'];
	}
}
