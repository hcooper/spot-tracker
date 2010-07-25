<?php

// All the little per-site configuraton options. Many to adjust the look of the site.

// Set a nice human site title
$sitetitle = "SPOT Tracker - Live Tracking Map";

// Set a subtitle
$subtitle = 'Version 1.2 - <b>Beta Testing</b><br>Global GPS updates by <a href="http://blog.coopersphotos.co.uk">Hereward Cooper</a>, using a SPOT Satellite Personal Tracker.';

// HACK: This bypassed unitname submission in the POST
// $unitname = $_GET['unitname'];
// $unitname = "ESN:0-7371360";
$unitname = "hcooper";

// Google Maps API Code
$gmapsapi = "ABQIAAAAKgG0R3M9OxpxGiBKm4inbhTJR35ebiLeByqbVSzBszWxVTR5ZxR93MDkF8UAWN222OFV6DTaoe3zHw";

// Set the colour range of the map icons
$gradstart="cc1122";
$gradend="441122";
$photocolour="ffbb00";

// Set the size of the icons
$iconsize="28";

?>