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

class s2pal
{
	var $colors = array();
	var $transparent = 254;

	function s2pal($file)
	{
		for($i = 0; $i < 256; $i++)
			$this->colors[$i] = array("r" => 0, "g" => 0, "b" => 0);
		
		if(is_file($file))
		{
			$f = pathinfo($file);
			switch(strtolower($f['extension']))
			{
			case 'bbm':	$this->_loadBBM($file); break;
			case 'pal':
			case 'act':
			case 'aco':	$this->_loadACT($file); break;
			}
		}
	}

	private function _loadBBM($file)
	{
		$bbm =  fopen($file, "rb");
		if(!$bbm)
			return false;

		fseek($bbm, 48, SEEK_SET);
		$this->colors = $this->read_256rgb($bbm);
		fclose($bbm);

		return true;
	}

	private function _loadACT($file)
	{
		$act =  fopen($file, "rb");
		if(!$act)
			return false;

		$this->colors = $this->read_256rgb($act);
		fclose($act);

		return true;
	}

	static function read_256rgb($file)
	{
		$colors = array();
		for($i = 0; $i < 256; $i++)
		{
			$r = file_helpers::freadchar($file);
			$g = file_helpers::freadchar($file);
			$b = file_helpers::freadchar($file);
			$colors[$i] = array("r" => $r, "g" => $g, "b" => $b);
		}
		return $colors;
	}
	
	function set_256rgb($colors)
	{
		if(count($colors) != 256)
			return false;
		for($i = 0; $i < 256; $i++)
		{
			if(!array_key_exists("r", $colors[$i]) || !array_key_exists("g", $colors[$i]) || !array_key_exists("b", $colors[$i]))
				return false;
		}
		
		$this->colors = $colors;
		return true;
	}
	
	function set_rgb($nr, $r, $g, $b)
	{
		if($r < 0 || $r >= 256 || $g < 0 || $g >= 256 || $b < 0 || $b >= 256 || $nr < 0 || $nr >= 256)
			return false;
		
		$this->colors[$nr] = array("r" => $color['r'], "g" => $color['g'], "b" => $color['b']);
		return true;
	}

	function rgb($color)
	{
		if(!array_key_exists($color, $this->colors))
		{
			trigger_error("color index out of bounds: ".$color);
			return $this->transparent();
		}

		return $this->colors[$color];
	}

	function transparent($index = false)
	{
		if($index)
			return $this->transparent;
			
		return $this->rgb($this->transparent);
	}

	function apply($img, $color)
	{
		$c = $color;
		if(!is_array($color))
			$c = $this->rgb($color);
		return imagecolorallocate($img, $c['r'], $c['g'], $c['b']);
	}
}
