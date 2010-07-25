<?php

// Function for formating error messages nicely

function showniceerror ($errormsg) {
	die('
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
 
<!-- Holding page design borrowed from www.sandaru1.com --> 
 
<head> 
	<title>Error!</title> 
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" /> 
	<link href="http://fonts.googleapis.com/css?family=Molengo" rel="stylesheet" type="text/css" /> 
	<link  href="http://fonts.googleapis.com/css?family=IM+Fell+English:regular,italic" rel="stylesheet" type="text/css" /> 
	<link href="http://fonts.googleapis.com/css?family=Tangerine" rel="stylesheet" type="text/css" /> 
	<style type="text/css"> 
	  body {
	    background-color:#000;
	    color:#fff;
	    background-repeat:no-repeat;
	    background-position:center center;
	  }
	  
	  #wrap {
	    width:500px;
	    margin-left:auto;
	    margin-right:auto;
	    text-align:center;
	    margin-top:150px;
	    padding: 20px;
	    font-size:15px;
	    border:1px solid #222;
	  }
	  
    h1 { 
	  font-family: "IM Fell English", serif;
	  font-size: 28px;
	  font-style: normal;
	  font-weight: 400;
	  text-decoration: none;
	  text-transform: lowercase;
	  letter-spacing: 0.3em;
	  word-spacing: -0.2em;
	  line-height: 1em;
    }
    
    p {
	  font-family: "Molengo", arial, serif; 
	  text-align:center;
    }
    
    h2 { 
	  font-family: "Tangerine", arial, serif; 
	  font-size: 50px;
	  margin-top:0px;
    }
    
	</style> 
</head> 
<body> 
  <div id="wrap"> 
  	<h1>blog.coopersphotos.com</h1> 
  	<h2>Sorry!</h2>
	<p>'.$errormsg.'</p> 
	</div> 
</body> 
</html> 
	');

}

?>