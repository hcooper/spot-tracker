<?php

// Function to calcudate difference between two given dates
function dateDiff ($date1,$date2)
        {

        $secs = $date1 - $date2;
        $days = $secs / 86400;
        $days = floor($days);
        $temp_remainder = $secs - ($days * 86400);
        $hours = floor($temp_remainder / 3600);
        $temp_remainder = $temp_remainder - ($hours * 3600);
        $minutes = round($temp_remainder / 60, 0);

        echo $days." days, ".$hours." hours, ".$minutes." minutes ago";

        }

// Print a nice box saying last known location and time. Needs the unitname passed to it.
// if passed the option "1" it will added a "show on map" link suitable for on the page use.
// if passed the option "newest" it will add a "show on map" link suitable for external pages.
function printLastLoc($unitname,$expand) {
	$result = cachedSQL("SELECT * FROM `".$unitname."` order BY time DESC LIMIT 1");

	if ($row = mysql_fetch_array($result)) {

	do {
        # CALCULATE TIME DIFFERENCE
        $today = mktime();
        $stamp = strtotime($row['time']);
        dateDiff($today,$stamp);

	if ($expand == "1") {
		echo ' (<a href="javascript:GEvent.trigger(exml.gmarkers[0],\'click\')">show on map</a>)';
	}

	if ($expand == "newest") {
		echo ' (<a href="livemap.php?newest">show on map</a>)';
	}

	echo "<br>";

	echo "<b>Message:</b> ".
	$row['type'].
	" <b>Time:</b> ".
	$row['time'].
	" <b>Position:</b> ".
	$row['lng'].
	", ".
	$row['lat'];


        } while($row = mysql_fetch_array($result));
        } else {print "Sorry, no records were found!";
        }
}

?>