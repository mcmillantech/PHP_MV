<?php
// Test model view
	require_once "view.php";

	$ar = array					// This holds the list of data to be displayed
	(
		"title" => "Demo of MV scheme",
		"test1" => "this is insert 1",
		"test2" => "and this is 2",
		"test9" => "This is inserted after the list of items"
	);
	
	$items = array();			// This sets up a list of items, rivers in this case
	$line = array();			// This will hold data for one item - a river
	
								// Typically, this next code would be in a while loop
								
	$line["country"] = "France";	// Set up data for a river
	$line["river"] = "Seine";
	$line["id"] = "rv1";
	array_push($items, $line);		// ... and add to the list of items
	$line["country"] = "England";
	$line["river"] = "Mersey";
	$line["id"] = "rv2";
	array_push($items, $line);
	$line["country"] = "USA";
	$line["river"] = "Hudson";
	$line["id"] = "rv3";
	array_push($items, $line);
								// This is the end of the loop
	$ar["itemlist"] = $items;	// Push the list of items into the main array

//	print_r($ar);				// You may like to see the multi dimension array

	showView("test.html", $ar);	// Finally, build and show the page
?>

