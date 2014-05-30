<?php

/* -------------------------------------- */

// OPEN FILE TO POPULATE LIST

function open_file($filename = './data/list.txt') {

    $handle = fopen($filename, 'r');
    $contents = trim(fread($handle, filesize($filename)));
    $list = explode("\n", $contents);
    fclose($handle);

    return $list;
}

/* -------------------------------------- */

// OUTPUTS LIST FROM ARRAY AS HTML

function output_list($list) {

	$string = '';

	foreach ($list as $key => $item) {
		$removeLink = "<a href=\"todo_list.php?removeIndex={$key} \">Remove</a>";
		echo "<li>{$item} - {$removeLink}</li>";
	}
}

/* -------------------------------------- */

// Overwrites Existing Save File With Current List

function save_to_file($list, $filename = './data/list.txt') {

	$handle = fopen($filename, 'w');
	$string = implode("\n", $list);
	fwrite($handle, $string);
	fclose($handle);

	return "Succesfully saved list to file.";
}

// add function dedupe list
// add function sort list alphabetically
// add function set priority of items ... sort by priority

/* -------------------------------------- */

/*  BEGIN MAIN LOGIC  */

// Populate list array from data file
$list = open_file();

// Check for post data, continue if not empty
if (!empty($_POST)) {

	// Assign new_item variable to value posted from form.
	$new_item = $_POST['add_item'];

	// Add new item to existing array
	$list[] = $new_item;

	save_to_file($list);
}

// If get request exists to remove item, do so.
if (isset($_GET['removeIndex'])) {
 	unset($list[$_GET['removeIndex']]);
 	array_values($list);
 	// save new array to file
 	save_to_file($list);
 }

?>

<html>
<head>
	<title>Todo List Web App</title>
</head>
<body>

<!-- Add Item Form																-->
	<form method="POST" action="">

		<label for="add_item">Add Item: </label>
		<input id="add_item" name="add_item" type="text" placeholder="Item Here">

		<button type="submit">SUBMIT</button>

	</form>
<!-- 																			-->


<!-- Remove Item Form															
	<form method="GET" action="">

		<label for="remove_item">Remove Item: </label>
		<input id="remove_item" name="remove_item" type="text" placeholder="Item #">

		<button type="submit">SUBMIT</button>

	</form>
	 																			-->

	<ol>
		<?php
			// Output List to Browser
			echo output_list($list);
		?>
	</ol>

</body>
</html>

