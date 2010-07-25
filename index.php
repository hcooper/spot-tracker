<?php

////////////////////////////////////////////////////////////////
//
// index.php
// The main plotting file for spot-tracker
//
////////////////////////////////////////////////////////////////

// Start with the most important stuff - import all the code that does stuff!
include('includes/includes.php');

// Check what tag is being requested. If there is no tag defined, or it's blank show everything
if (isset($_GET['tag']) && $_GET['tag'] != "" && $_GET['tag'] != "All") {
	$tag = $_GET['tag'];
        $plotline = "1";
} else {
	$tag = "%";
	$plotline = "0";
}

// Query to return all points for a give tag (in ascending order)
//$result = mysql_query ("SELECT * FROM `".$unitname."` WHERE tag LIKE \"".$tag."\" order BY time ASC");
$result = cachedSQL("SELECT * FROM `".$unitname."` WHERE tag LIKE \"".$tag."\" order BY time ASC");

// Get the total number of points - used to various things
$num_rows = mysql_num_rows($result);

// Bail out if there is nothing to plot! (This shouldn't happen now as the default is "All").
if (!mysql_num_rows($result)) {
        showniceerror("<b>Error:</b> No data returned from the database!");
}

// Colour gradient settings. (start_hex_colour, finish_hex_colour, number_of_points)
$gradient=gradient($gradstart,$gradend,$num_rows);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" style="height:100%">
      
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="style/map.css" type="text/css" media="screen" />

	<title><?php echo $sitetitle; ?></title>
    
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $gmapsapi; ?>" type="text/javascript"></script>

	<script src="javascript/mapiconmaker.js" type="text/javascript"></script>

	<script type="text/javascript">

    //<![CDATA[

function load() {
  if (GBrowserIsCompatible()) {

    var mmap = new GMap2(document.getElementById("map"));
    mmap.setCenter(new GLatLng(0,0),2);
    var bounds = new GLatLngBounds();
    mmap.setMapType(G_PHYSICAL_MAP);
    mmap.addControl(new GScaleControl());
    mmap.addControl(new GLargeMapControl());
    mmap.addControl(new GMenuMapTypeControl());
    mmap.addMapType(G_PHYSICAL_MAP) ;
    mmap.enableScrollWheelZoom();


<?php

// Start a counter for each point
$count=0;

// Start the MySQL fetch
while($row = mysql_fetch_array($result)) {

	// Image Points
	if ($row['img']!="") {
		echo "\nvar newIcon".$count." = MapIconMaker.createMarkerIcon(".
		"{width: ".$iconsize.", height: ".$iconsize.", primaryColor: \"#".$photocolour."\"});\n";	

	// Last Point
	} elseif ($count==$num_rows-1) {
		echo "\nvar newIcon".$count." = MapIconMaker.createMarkerIcon(".
		"{width: ".$iconsize.", height: ".$iconsize.", primaryColor: \"#".$gradient[$count]."\"});\n";

	// First Point
	} elseif ($count=="0") {
		echo "\nvar newIcon".$count." = MapIconMaker.createMarkerIcon(".
		"{width: ".$iconsize.", height: ".$iconsize.", primaryColor: \"#".$gradient[$count]."\"});\n";

	// Other Points
	} else {
		echo "\nvar newIcon".$count." = MapIconMaker.createMarkerIcon(".
		"{width: ".$iconsize.", height: ".$iconsize.", primaryColor: \"#".$gradient[$count]."\"});\n";
 	}

	 echo "var position".$count." = new GLatLng(".$row['lat'].", ".$row['lng'].");\n";
	 echo "var marker".$count." = new GMarker(position".$count.", {icon: newIcon".$count."});\n";
	 echo "mmap.addOverlay(marker".$count.");\n";
	 echo "bounds.extend(position".$count.");\n\n";
	 echo "marker".$count.".bindInfoWindowHtml('<div class=\"pointbox\">";


// Check if there's an image to display for that point
	if ($row['img']!="") {
		 echo "<img src=\"".$row['img']."\"><br>";
 	}

// Print the message, location details and time
	echo "<center><b>Msg: </b>".
		$row['type'].
		" <b>Time: </b>"
		.date("jS F Y, g:i a T", $row['time']).
		"<br><b>Lat:</b> ".
		round($row['lat'],3).
		" <b>Long:</b> ".
		round($row['lng'],3).
		"<br><b>Tag:</b> ".	
		$row['tag'];
		if ($row['notes'] != "") {
			echo "<br><b>Notes:</b> ".
			$row['notes'];
		}
	echo"</div></center>');
		";

// Increment count for the next point to plot
	$count=$count+1;

// End MySQL fetch while loop
}


/////////////////////////////////////////
//
//  Start of the connected-line plotting
//
////////////////////////////////////////

// Query to fetch all points for a given tag (in descending order)
// FIX ME -- We shouldn't need to query the DB a second time!
//           We should just inverse the results of the previous query

// If plotline is enabled, connect the points
if ($plotline == 1) {
	$results_for_line = cachedSQL("SELECT * FROM `".$unitname."` WHERE tag LIKE \"".$tag."\" order BY time DESC");
	echo "\n\nvar polyline = new GPolyline([\n";
	while($row = mysql_fetch_array($results_for_line))
		{
			echo "new GLatLng(".$row['lat'].", ".$row['lng']."),\n";
	        }
	echo "], \"#FF0000\", 3);\n";
	echo "mmap.addOverlay(polyline);\n\n";
}

//DISABLED WHILST OTHER STUFF IS DEVELOPED
//      $newest = $_GET["newest"];
//      if (isset($newest)) {
//		echo "marker0.bindInfoWindowHtml('<strong>asdasd</strong>');";
//	}


// Determine the zoom level from the bounds
echo "\nmmap.setZoom(mmap.getBoundsZoomLevel(bounds));\n";

// Determine the centre from the bounds
echo "mmap.setCenter(bounds.getCenter());\n";

?>

  }
}
</script>
</head>

	<?php

// DISABLED WHILST OTHER STUFF IS DEVELOPED
// Jump to newest point on loading, if newest is passed in the url
// Otherwise just load normal map
//      $newest = $_GET["newest"];
//        if (isset($newest)) {
//              echo '
//              <body onLoad="javascript:GEvent.trigger(exml.gmarkers[0],\'click\')"  style="height:100%;margin:0"> ';
//      } else {
//                    echo '
//              <body style="height:100%;margin:0">';
//        }
       ?>

<body style="height:100%;margin:0" onload="load()">
        <div id="map" style="width: 100%; height: 100%;"></div>
        <div id="dropbox"></div>

  	<div id="title"><?php echo $sitetitle;?></div>

	<div id="box2"><?php echo $subtitle;?></div>

	<div id="legend">
		<img src="images/icon-red.png"><br>SPOT<br>
		<img src="images/icon-yellow.png"><br>SPOT with Photo 
	</div>

	<div id="box1">
		<form id="tagbox" action="index.php" method="get">
			Tag:
		<select name="tag" onChange="this.form.submit()">

<?php
	/////////////////////////////////////////////////////////////////
	//
	// Get the list of all the tags and display in the drop down box
	//
	/////////////////////////////////////////////////////////////////

	$tagresult = cachedSQL("SELECT DISTINCT tag FROM `".$unitname."`");

	while($tagrow = mysql_fetch_array($tagresult)) {
               if ($tagrow['tag']==$tag && $tag != "All") {
                echo "<option selected>".
                $tagrow['tag'].
                "</option>\n";
                } else {
                echo "<option>".
                $tagrow['tag'].
                "</option>\n";
                }
	}

	// If the tag is set to "All" or to nothing, make All the selection option
		if ($tag == "All" || $tag == "%") {
			echo "<option selected>All</option>";
		} else {
			echo "<option>All</option>";
		}

?>

	</select>
	</form>
        </div>

	<div id="lastloc">
		<?php
			// DISABLE FOR NOW AS NEEDS REDOING
			// Only show "Last known location" box,
			// if your not viewing an archived trip
			//if (isset($_GET["newest"])) {
			//echo "Last known location: ";
			//	printLastLoc("ESN:0-7371360","1");
			//}
		?>
	</div>

  </body>
</html>
