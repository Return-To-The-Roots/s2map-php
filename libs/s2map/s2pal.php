<?php

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
