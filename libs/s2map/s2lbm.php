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

require_once(__DIR__."/config/config.php");
require_once(__DIR__."/db/db.php");

class s2lbm
{
	var $sql         = NULL;
	var $error       = array();
	var $hash        = "";
	var $file        = "";

	var $image = array("header" => array(), "body" => array());
	var $palette = null;

	function s2lbm($file)
	{
		global $config;
		$this->sql = db_init($config['sql']);

		$this->palette = new s2pal("");
		
		if(is_file($file))
		{
			$this->hash = md5_file($file);
			$this->file = $file;

			$f = pathinfo($file);
			switch(strtolower($f['extension']))
			{
			case 'lbm':	$this->_loadLBM($file); break;
			}
		}
		echo $this->get_error();
			
	}

	private function _error($error)
	{
		$this->error[] = $error;
		return $error;
	}

	function get_error($n = "\n")
	{
		return implode($n, $this->error);
	}
		
	private function _loadLBM($file)
	{
		$lbm =  fopen($file, "rb");
		if(!$lbm)
			return false;

		fseek($lbm, 0, SEEK_END);
		$size = ftell($lbm);
		fseek($lbm, 0, SEEK_SET);
		
		$form = file_helpers::freadstring($lbm, 4, false);
		if($form != "FORM")
			return $this->_error("not a LBM file");
			
		$length = file_helpers::freadint($lbm);

		$pbm = file_helpers::freadstring($lbm, 4, false);
		if($pbm != "PBM ")
			return $this->_error("not a LBM file with PBM structure");
			
		while(!feof($lbm) && ftell($lbm) < $size)
		{
			$chunk = file_helpers::freadstring($lbm, 4, false);
			
			// Länge einlesen
			$length = file_helpers::freadbint($lbm);
			
			#echo $chunk." = ".$length."<br>\n";

			// Bei ungerader Zahl aufrunden
			if($length & 1)
				++$length;

			switch($chunk)
			{
			case "BMHD": // header
				{
					// Breite & Höhe einlesen
					$this->image['header']['width'] = file_helpers::freadbshort($lbm);
					$this->image['header']['height'] = file_helpers::freadbshort($lbm);

					// Unbekannte Daten ( 4 Byte ) berspringen
					fseek($lbm, 4, SEEK_CUR);

					// Farbtiefe einlesen
					$this->image['header']['depth'] = file_helpers::freadshort($lbm);

					// Nur 256 Farben und nicht mehr!
					if($this->image['header']['depth']  != 8)
					{
						$this->_error("Invalid File: LBM with 8bit colors are supported only");
						return false;
					}

					// Kompressionflag lesen
					$this->image['header']['compression'] = file_helpers::freadshort($lbm);
					if($this->image['header']['compression'] != 1)
					{
						$this->_error("Invalid File: LBM has unknown compression flag: ".$this->image['header']['compression']);
						return false;
					}

					$length -= 12;

					// Rest überspringen
					fseek($lbm, $length, SEEK_CUR);
					
				} break;
			case "BODY": // pixels
				{
					switch($this->image['header']['compression'])
					{
					case 0: // uncompressed
						return false;
					case 1: // compressed (RLE?)
						{
							$pos = 0;

							// Solange einlesen, bis Block zuende bzw. Datei zuende ist
							while($length >= 0 && !feof($lbm) && ftell($lbm) < $size)
							{
								// Typ lesen
								$ctype = file_helpers::freadchar($lbm);
								
								--$length;
								if($length == 0)
									continue;
	
								if($ctype < 128) // unkomprimierte Pixel
								{
									for($i = 0; $i <= $ctype; ++$i)
									{
										$this->image['body'][$pos] = file_helpers::freadchar($lbm);
										--$length;
										++$pos;
									}
								}
								else // komprimierte Pixel
								{
									$count = 0xFF - $ctype + 1;
									
									$color = file_helpers::freadchar($lbm);
									--$length;
	
									for($i = 0; $i <= $count; ++$i)
									{
										$this->image['body'][$pos] = $color;
										++$pos;
									}
								}
							}
							if($pos < $this->image['header']['width'] * $this->image['header']['height'])
							{
								$this->_error("ooops? $pos ". ($this->image['header']['width'] * $this->image['header']['height']));
								return false;
							}
						} break;
					}
				} break;
			case "CMAP": // color map
				{
					if($length != 256 * 3)
					{
						$this->_error("Invalid Chunk: $chunk with length $length does not have 256 rgb values");
						return false;
					}
					
					$this->palette->set_256rgb(s2pal::read_256rgb($lbm));
				} break;

			case "DPPS": // known but unused
			case "CRNG":
			case "TINY":
				fseek($lbm, $length, SEEK_CUR);
				break;
				
			default:  // unknown
				{
					$this->_error("Unknown Chunk: $chunk with length $length");

					// Rest überspringen
					fseek($lbm, $length, SEEK_CUR);
				} break;
			}
		}


		fclose($lbm);

		return true;
	}
	
	function color($x, $y)
	{
		if(!array_key_exists('width', $this->image['header']) || !array_key_exists('height', $this->image['header']))
			return $this->palette->transparent(true);
		
		if($x < 0 || $x >= $this->image['header']['width'] || $y < 0 || $y >= $this->image['header']['height'])
			return $this->palette->transparent(true);
		
		return $this->image['body'][$y * $this->image['header']['width'] + $x];
	}

	function rgb($x, $y)
	{
		return $this->palette->rgb($this->color($x, $y));
	}

	private function _generate()
	{
		$size = 1;
		$img = imagecreatetruecolor( $this->image['header']['width'] * $size, $this->image['header']['height'] * $size );

		imagecolortransparent($img, $this->palette->apply($img, $this->palette->transparent()));

		for($x = 0; $x < $this->image['header']['width']; $x++)
		{
			for($y = 0; $y < $this->image['header']['height']; $y++)
			{
				$color = $this->palette->apply($img, $this->image['body'][$y * $this->image['header']['width'] + $x]);

				imagefilledrectangle($img, $x * $size, $y * $size, $x * $size + $size, $y * $size + $size, $color);
			}
		}

		$temp_file = tempnam(sys_get_temp_dir(), 's2lbm');

		// create image file
		imagepng( $img, $temp_file );
		imagedestroy( $img );

		$img = fopen($temp_file, "rb");
		$img_data = fread($img, filesize($temp_file));
		fclose($img);

		unlink($temp_file);

		return $img_data;
	}
	
	function preview()
	{
		return $this->_generate();
	}
}
