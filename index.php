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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="de" lang="de">
<head>
	<title>Settlers II Maps</title>
	<script type="text/javascript" src="libs/jquery/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="libs/jquery/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="libs/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="libs/jquery/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

</head>
<body>

<script type="text/javascript">
	$(document).ready(function() {

		$("a[rel=lightbox]").fancybox(
			{
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'titlePosition' 	: 'outside',
				'type'                  : 'image',
				'hideOnContentClick'	: true,
				'cyclic'		: true,
				'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
					return '<span id="fancybox-title-over">' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
				}

			}
		);

	});
</script>

<?php

$base = "maps/";

function scan($dir = "")
{
	global $base;

	$files = scandir($dir);
	foreach($files as $f)
	{
		#echo $f."<br>\n";
		if(is_dir($dir.$f) && $f != ".svn" && $f != "." && $f != "..")
		{
			echo '<div style="clear:both"></div>';
			echo '<div style="float:left"><p><hr></p><p>'.$dir.$f.'</p></div>';
			echo '<div style="clear:both"></div>';
			scan($dir.$f."/");
		}
		else
		{
			$ff = pathinfo($dir.$f);
			if(strtolower($ff['extension']) == "swd" || strtolower($ff['extension']) == "wld")
			{
				$link = 'map.php?map='.substr($dir, strlen($base)).$f;
				$title = substr($dir, strlen($base)).$f;
				echo '<div style="float:left; margin: 20px; width: 200px; height: 200px"><a href="'.$link.'" rel="lightbox" target="lightbox" title="'.$title.'"><img style="border:0px; max-width: 200px; max-height: 200px" src="'.$link.'"></a></div>';
			}
		}
	}
}

scan($base);

?>
</body>
</html>
