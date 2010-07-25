<?php

// Functions needed to connect to mysql

// Connect to the database, or fail
if (!($connection = @ mysql_connect($dbhost,$dbuser,$dbpass)))
	showniceerror("These was a problem reaching the database:<br>". mysql_errno(  ).' '.mysql_error(  ));

// Select the database, or fail
if (!(mysql_select_db($db, $connection)))
	showniceerror("These was a problem reading from the database:<br>". mysql_errno(  ).' '.mysql_error(  ));
?>