<?php

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
