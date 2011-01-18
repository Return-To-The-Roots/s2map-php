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

class file_helpers
{
	static private function _oem2ansi($string)
	{
	  $ansi2oem_tab = array(
	  /*0000:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0010:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0020:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0030:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0040:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0050:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0060:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0070:*/ 0,0,0,0, 0,0,0,0, 0,0,0,0, 0,0,0,0,
	  /*0080:*/ 0x00,0x00,0x00,0x9F, 0x00,0x00,0x00,0xD8, 0x00,0x00,0x00,0x00, 0x00,0x00,0x00,0x00, 
	  /*0090:*/ 0x00,0x60,0x27,0x00, 0x00,0x00,0x00,0x00, 0x00,0x00,0x00,0x00, 0x00,0x00,0x00,0x00, 
	  /*00A0:*/ 0xFF,0xAD,0x9B,0x9C, 0x0F,0x9D,0x7C,0x15, 0x22,0x63,0xA6,0xAE, 0xAA,0x2D,0x52,0x00, 
	  /*00B0:*/ 0xF8,0xF1,0xFD,0x33, 0x27,0xE6,0x14,0xFA, 0x2C,0x31,0xA7,0xAF, 0xAC,0xAB,0x00,0xA8, 
	  /*00C0:*/ 0x41,0x41,0x41,0x41, 0x8E,0x8F,0x92,0x80, 0x45,0x90,0x45,0x45, 0x49,0x49,0x49,0x49, 
	  /*00D0:*/ 0x44,0xA5,0x4F,0x4F, 0x4F,0x4F,0x99,0x78, 0x4F,0x55,0x55,0x55, 0x9A,0x59,0x00,0xE1, 
	  /*00E0:*/ 0x85,0xA0,0x83,0x61, 0x84,0x86,0x91,0x87, 0x8A,0x82,0x88,0x89, 0x8D,0xA1,0x8C,0x8B, 
	  /*00F0:*/ 0x64,0xA4,0x95,0xA2, 0x93,0x6F,0x94,0xF6, 0x6F,0x97,0xA3,0x96, 0x81,0x79,0x00,0x98, 
	  );
	  
	  for($x = 0; $x < strlen($string); $x++)
	  {
	    if(ord($string[$x]) > 128)
	    {
	      foreach($ansi2oem_tab as $key => $value)
	      {
	        if(ord($string[$x]) == intval($value))
	        {
	          $string[$x] = chr($key);
	          break;
	        }
	      }
	    }
	  }
	  return $string;
	}
	
	static function freadchar($file)
	{
		$val = fread($file, 1);
		return ord($val{0});
	}

	static function freadchars($file, $length)
	{
		$chars = array();
		for($i = 0; $i < $length; $i++)
			$chars[$i] = file_helpers::freadchar($file);
		return $chars;
	}

	static function freadstring($file, $max)
	{
		$chars = file_helpers::freadchars($file, $max);
		$string = "";
		foreach($chars as $c)
		{
			if($c == 0)
				break;
			$string .= chr($c);
		}
		
		return file_helpers::_oem2ansi(trim($string));
	}
	
	static function freadshort($file)
	{
		$a = file_helpers::freadchar($file);
		$b = file_helpers::freadchar($file);
		return $b * 256 + $a;
	}
	
	static function freadint($file)
	{
		$a = file_helpers::freadshort($file);
		$b = file_helpers::freadshort($file);
		return $b * 256*256 + $a;
	}
	
	static function freadid($file)
	{
		$id = fread($file, 10);
		return ($id == "WORLD_V1.0");
	}
	
	static function freadshid($file, $first = 0x10)
	{
		$a = file_helpers::freadchar($file);
		$b = file_helpers::freadchar($file);
		$c = file_helpers::freadchar($file);
		$d = file_helpers::freadchar($file);
		$e = file_helpers::freadchar($file);
		$f = file_helpers::freadchar($file);
	
		return ($a == $first && $b == 0x27 && $c == 0 && $d == 0 && $e == 0 && $f == 0);
	}
}
