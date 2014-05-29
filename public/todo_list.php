<html>
<head>
	<title>Todo List Web App</title>
</head>
<body>

	<form method="POST" action="">

		<label for="add_item">Add Item: </label>
		<input id="add_item" name="add_item" type="text" placeholder="Item Here">

		<button type="submit">SUBMIT</button>

	</form>

	<?php

	/* -------------------------------------- */

	// OPEN FILE TO POPULATE LIST

	function open_file($filename) {

	    // if (empty($file) ) {
	    //     $filename = './data/list.txt';
	    // }

	    $handle = fopen($filename, 'r');
	    $contents = trim(fread($handle, filesize($filename)));
	    $list = explode("\n", $contents);
	    fclose($handle);
	    
	    return $list;
	}

	/* -------------------------------------- */

	// SAVE LIST TO FILE

	function append_to_file($new_item, $filename = './data/list.txt') {

	    if (file_exists($filename)) {
	        $handle = fopen($filename, 'a');
	        fwrite($handle, $new_item . PHP_EOL);
	        fclose($handle);
	    }  // else nothing.
	    
	    return "Successfully saved item: {$new_item} to your list.";
	}

	/* -------------------------------------- */
	
	// Outputs List to Browser

	function output_list($list) {

		echo "<ol>";
		foreach ($list as $key => $item) {
			echo "<li>$item  <a href=''>Mark Completed</a> </li>";
		}
		echo "</ol>";

	}

	/* -------------------------------------- */

	// Create new_item variable from form $_POST data
	$new_item = implode("", $_POST);

	// Append new item to file data
	echo append_to_file($new_item);

	// Populate list array from data file
	$list_items = open_file('./data/list.txt');

	// Output List to Browser
	output_list($list_items);

?>

</body>
</html>

