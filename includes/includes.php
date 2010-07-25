<?php

// includes.php
// Hereward Cooper

// This file is now split into sub-files each with specific functions

// The human customizable per-site bits
include_once('includes/site_config.php');

// Error handling functions
include_once('includes/errors.php');

// Database caching functions
include_once('includes/memcache.php');

// Database variables
if (is_file('database_config.php')) {
	include('database_config.php');
} else {
	showniceerror("No database settings file found. Please create a file called ".
		"<i>database_config.php</i> with the mysql connections strings.");
	exit;
}

// Database functions
include_once('includes/database_functions.php');

// Colour Gradient Functions
include_once('includes/grad.php');

// Currently unsed functions
//include_once('includes/unused.php');

?>
