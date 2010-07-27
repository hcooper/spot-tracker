<?php

// Force no caching. Stops lame problems with old images appearing
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

//////////////////////////////////////////////////////////
//
// _admin.php
//
// This is the admin interface for spot-tracker
// Here you can customize all the points and their details
//
//////////////////////////////////////////////////////////


////////////////////////////////////////////////////
//
// The Magic Includes which make everything happen!
//
////////////////////////////////////////////////////
include('includes/includes.php');

// Read in the tag from any POST data avaliable
if (!isset($_GET['tag'])) {
	$tag = "";
} else {
	$tag = $_GET['tag'];
}

$result = cachedSQL("SELECT * FROM `".$unitname."` WHERE tag LIKE \"".$tag."\" order BY time ASC");

// Get the total number of points - used for gradient plotting
$num_rows = mysql_num_rows($result);
$gradient=gradient($gradstart,$gradend,$num_rows);

$count=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" style="height:100%">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<link href='style/admin.css' rel='stylesheet' type='text/css'>
    		<title>Live Map || Admin</title>
    		<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAKgG0R3M9OxpxGiBKm4inbhTJR35ebiLeByqbVSzBszWxVTR5ZxR93MDkF8UAWN222OFV6DTaoe3zHw" type="text/javascript"></script>
		<script src="javascript/mapiconmaker.js" type="text/javascript"></script>

    <script type="text/javascript">
    //<![CDATA[

function load() {
  if (GBrowserIsCompatible()) {

<?php
$num_rows = mysql_num_rows($result);
$count=0;
$map_javascript = "";
while($row = mysql_fetch_array($result)) {
	$map_javascript .= "var map".$count." = new GMap2(document.getElementById(\"map".$count."\"));\n";
	$map_javascript .= "map".$count.".setCenter(new GLatLng(0,0),2);\n";
	$map_javascript .= "var bounds = new GLatLngBounds();\n";
	$map_javascript .= "map".$count.".setMapType(G_PHYSICAL_MAP);\n";
	$map_javascript .= "map".$count.".enableScrollWheelZoom();\n";
	$map_javascript .= "var newIcon".$count." = MapIconMaker.createMarkerIcon({width: 32, height: 32, primaryColor: \"#".$gradient[$count]."\"});\n";
	$map_javascript .= "var position".$count." = new GLatLng(".$row['lat'].", ".$row['lng'].");\n";
	$map_javascript .= "var marker".$count." = new GMarker(position".$count.", {icon: newIcon".$count."});\n";
	$map_javascript .= "map".$count.".addOverlay(marker".$count.");\n";
	$map_javascript .= "bounds.extend(position".$count.");\n\n";
	$map_javascript .= "map".$count.".setZoom(map".$count.".getBoundsZoomLevel(bounds));\n";
	$map_javascript .= "map".$count.".setCenter(bounds.getCenter());\n\n";
	$count=$count+1;
}
echo $map_javascript;
?>
  } // end of GCompat
} // end of onLoad()
</script>

	<script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script> 
	<script type="text/javascript" src="javascript/jquery.form.js"></script> 
	<script type="text/javascript"> 
        // wait for the DOM to be loaded 
        $(document).ready(function() {
         
<?php
$count=0;
while($count < $num_rows) {
?>


		// Reload images & change row color on text field change
<?php
echo "		$('#imgurl".$count."').change(function() {\n";
echo "			image".$count.".src = document.form".$count.".imgurl".$count.".value;\n";
echo "			document.getElementById('row".$count."').style.backgroundColor='#CAFF70';\n";
echo "		});\n";
?>
       		

		// Reset colour and trigger popup when submit clicked
<?php
echo "		$('#form".$count."').ajaxForm(function() {\n";
echo "			document.getElementById('row".$count."').style.backgroundColor='#ffffff';\n";
echo "			$(\"div.infobox:hidden\").slideDown(\"slow\");\n";
echo "				window.setTimeout(function() {\n";
echo "				$(\"div.infobox:visible\").slideUp(\"slow\");\n";
echo "			}, 1500);\n";
echo "		});\n";
?>

                // Reset colour and trigger popup when submit clicked
<?php
echo "          $('#tag".$count."').change(function() {\n";
echo "                  document.getElementById('row".$count."').style.backgroundColor='#CAFF70';\n";
echo "          });\n";

echo "          $('#note".$count."').change(function() {\n";
echo "                  document.getElementById('row".$count."').style.backgroundColor='#CAFF70';\n";
echo "          });\n";

$count=$count+1;
}
?>     		 

        }); 
    </script> 

</head>

<body onload="load()">
<div id="wrap">

<?php
echo $_POST['newtag'];
// Detect if this there is an update via POST, and do it if there is one
        if (isset($_POST['imgurl']) && isset($_POST['notes'])) {

                // Strip lines breaks out of input, as this breaks the javascript
                $chars = array("\n", "\r", "chr(13)", "\t", "\0", "\x0B");
                $stripped_notes = str_replace($chars,"<br>",$_POST['notes']);

                $updatequery =  "UPDATE `".$unitname.
                                "` SET img =\"".$_POST['imgurl'].
                                "\", notes = \"".$stripped_notes.
                                "\", tag = \"".$_POST['newtag'].
                                "\" WHERE id=\"".$_POST['id']."\"";

                if (mysql_query($updatequery)) {
// DISABLED AS USING jQuery now
//                        echo "<div class=\"infobox\">Details Updated!</div>";
                } else {
                         //showniceerror("Update Error!");
                        echo "<div class=\"error\">Updated Error!</div>";
                }

        }

?>

<div id="header">
	<h1>Livemap</h1>
	<h2>- Admin Interface -</h2>
</div>

<!-- Magic div which appears when submit is clicked -->
<div class="infobox">Details Saved!</div>

<br>
<div id="adminbox1">
	<form id="tagbox" action="<?php echo $_SELF;?>" method="get">Select Tag:
		<select name="tag" onChange="this.form.submit()">
		<option></option>
<?php
//Pull out a list of all the tags
	$tagresult = cachedSQL("SELECT DISTINCT tag FROM `".$unitname."`");
	while($tagrow = mysql_fetch_array($tagresult)) {
		if ($tagrow['tag']==$tag) {
echo "		<option selected>".
	        $tagrow['tag'].
		"</option>\n";
		} else {
echo "		<option>".
		$tagrow['tag'].
		"</option>\n";
		}
	}
?>
		</select>
		<br>
	</form>
</div>
<br>
<?php
	// If no tag is selected don't do print the table
	if ($num_rows != 0) {
?>
<table border="1" id="maintable" name="maintable">
<tr>
	<th width="100px">Map</th>
        <th width="100px">Time</th>
	<th>Lng</th>
	<th>Lat</th>
	<th>Image URL</th>
	<th>Notes</th>
</tr>

<?php
	// Loop for make maps
	$result = cachedSQL("SELECT * FROM `".$unitname."` WHERE tag LIKE \"".$tag."\" order BY time ASC");
	$count=0;
	while($row = mysql_fetch_array($result)) {
        	echo "<tr id=\"row".$count."\">\n";
		echo "	<td><div class=\"map\" id=\"map".$count."\" style=\"width: 100px; height: 100px;\"></div></td>\n";
                echo "	<td>".date("jS F Y, g:i a T",$row['time'])."</td>\n";
        	echo "	<td>".round($row['lng'],3)."</td>\n";
		echo "	<td>".round($row['lat'],3)."</td>\n";
		$loc = $row['lat'].$row['lng'];
		if ($row['img'] != "") {
			echo "	<td><img id=\"image".$count."\" class=\"my_image\" height=\"100\" src=\"".$row['img']."\">";
		} else {
			echo "	<td><img id=\"image".$count."\" class=\"my_image\" height=\"100\" src=\"images/no-image.gif\">";
		}
		echo "\n\n	<form id=\"form".$count."\" name=\"form".$count."\" class=\"updater\" method=\"post\" action=\"".$_SELF."\">
			<input type=\"hidden\" value=\"".$row['id']."\" name=\"id\">
			<input type=\"text\" value=\"".$row['img']."\" name=\"imgurl\" id=\"imgurl".$count."\">
	</td>\n";
		echo "	<td>
			<textarea id=\"notes".$count."\" name=\"notes\">".$row['notes']."</textarea>
			<br>
			<input type=\"submit\" value=\"Update\">
			<!-- To Be Implimented
			<input type=\"submit\" value=\"Delete\">
			-->
			<br>
			<input type=\"text\" name=\"newtag\" value=\"".$row['tag']."\" id=\"tag".$count."\">
		</form>
	</td>\n";
		echo "</tr>\n\n";
		$count=$count+1;
        }

  ?>

</table>
<?php
	}
?>
</div>
</body>
</html>
