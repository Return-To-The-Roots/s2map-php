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

class s2pal
{
	var $colors = array();
	var $transparent = 254;

	function s2pal($file)
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

	private function _loadBBM($file)
	{
		$bbm =  fopen($file, "rb");
		if(!$bbm)
			return false;

		fseek($bbm, 48, SEEK_SET);
		$this->_load($bbm);
		fclose($bbm);

		return true;
	}

	private function _loadACT($file)
	{
		$act =  fopen($file, "rb");
		if(!$act)
			return false;

		$this->_load($act);
		fclose($act);

		return true;
	}

	private function _load($f)
	{
		for($i = 0; $i < 256; $i++)
		{
			$r = file_helpers::freadchar($f);
			$g = file_helpers::freadchar($f);
			$b = file_helpers::freadchar($f);
			$this->colors[$i] = array("r" => $r, "g" => $g, "b" => $b);
		}
	}

	function rgb($color)
	{
		return $this->colors[$color];
	}

	function transparent()
	{
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
