<?php

require_once(s2map::_dir("file_helpers.php"));
require_once(s2map::_dir("s2pal.php"));
require_once(s2map::_dir("s2gou.php"));

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
		require_once(s2map::_dir("config/config.php"));
		require_once(s2map::_dir("db/db.php"));
		
		$this->sql = db_init($config['sql']);
		
		$this->hash = md5_file($file);
		$this->file = $file;
	}

	static function _dir($file)
	{
		$path = explode('/', __FILE__);
		unset($path[count($path)-1]);
		return implode('/', $path). "/" . $file;
	}
	
	private function _error($error)
	{
		$this->error[] = $error;
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
			0 => array("PAL5.BBM", "GOU5.DAT"),
			1 => array("PAL6.BBM", "GOU6.DAT"),
			2 => array("PAL7.BBM", "GOU7.DAT")
		);

		if(!$this->_load())
			return $this->error("failed to load map");

		$s2pal = new s2pal(s2map::_dir("S2/GFX/PALETTE/".$files[$this->header['type']][0]));
		$s2gou = new s2gou(s2map::_dir("S2/DATA/TEXTURES/".$files[$this->header['type']][1]));
		
		$size = 6;
		$img = imagecreatetruecolor( $this->header['width'] * $size, $this->header['height'] * $size );
		
		imagecolortransparent($img, $s2pal->apply($img, $s2pal->transparent()));
		
		$terrain_colors = array(
			0x00 => 0xE7,  // steppe meadow
			0x40 => 0,     //
			0x01 => 0xC3,  // mining 1
			0x02 => 0x9D,  // snow
			0x03 => 0xE8,  // swamp
			0x04 => 0xC4,  // steppe
			0x05 => 0x3D,  // water
			0x06 => 0x9D,  //
			0x07 => 0xC6,  //
			0x08 => 0xE4,  // meadow1
			0x48 => 0,     //
			0x09 => 0xE6,  // meadow2
			0x49 => 0,     //
			0x0A => 0xE4,  // meadow3
			0x4A => 0,     //
			0x0B => 0xC4,  // mining 2
			0x0C => 0xC6,  // mining 3
			0x0D => 0xC0,  // mining 4
			0x0E => 0xE9,  // 
			0x4E => 0,     //
			0x0F => 0xE7,  // flower
			0x4F => 0,     //
			0x10 => 0x39,  // lava
			0x12 => 0xC2,  // mining meadow
			0x52 => 0,     //
		);
		
		switch($this->header['type'])
		{
		case 1: // waste
			{
				$terrain_colors[0x05] = 0x39;
			} break;
		case 2: // winter
			{
			} break;
		}
		
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
					$color = $terrain_colors[$terrain2];
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
		$box = imagettfbbox($size, 0, "s2map.ttf", $text);
		
		imagefilledrectangle($img, 2, 2, 6 + $box[2], 8 + $size*1.45 *2, imagecolorallocate($img, 0xFF, 0xFF, 0xFF));
		imagettftext($img, $size, 0, 4, 18, imagecolorallocate($img, 0x00, 0x00, 0x00), "s2map.ttf", $text);


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
			return $this->_generate();
		
		return $preview['preview'];
	}
}
