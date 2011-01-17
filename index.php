<?php

$files = scandir("/www/ra-doersch.de/nightly/maps/");
foreach($files as $f)
{
	$ff = pathinfo($f);
	if(strtolower($ff['extension']) == "swd" || strtolower($ff['extension']) == "wld")
	{
		echo '<p><img src="map.php?map='.$f.'"></p>';
	}
}