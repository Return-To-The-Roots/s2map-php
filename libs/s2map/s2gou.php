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

require_once(s2map::_dir("file_helpers.php"));

class s2gou
{
	var $gouraud = array();
	
	function s2gou($file)
	{
		$this->load($file);
	}
	
	function load($file)
	{
		$gou = fopen($file, "rb");
		if(!$gou)
			return false;
			
		for($i = 0; $i < 256; $i++)
		{
			$this->gouraud[$i] = array();
			for($j = 0; $j < 256; $j++)
			{
				$this->gouraud[$i][$j] = file_helpers::freadchar($gou);
			}
		}
		fclose($gou);
		
		return true;
	}
	
	function apply($s2pal, $color, $shadow)
	{
		return $s2pal->rgb($this->gouraud[$shadow][$color]);
	}
}
