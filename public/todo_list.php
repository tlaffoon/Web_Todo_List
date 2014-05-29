<html>
<head>
	<title>Todo List Web App</title>
</head>
<body>

	<?php

	/* -------------------------------------- */

	// OPEN FILE TO POPULATE LIST

	function open_file($file) {

	    // echo "Please enter filename to open (default: ./data/list.txt): ";
	    // $filename = get_input();

	    if (empty($file) ) {
	        $filename = './data/list.txt';
	    }

	    $handle = fopen($filename, 'r');
	    $contents = trim(fread($handle, filesize($filename)));
	    $list = explode("\n", $contents);
	    fclose($handle);
	    return $list;
	}

	/* -------------------------------------- */

	$file = '';
	$list = open_file($file);

	//$list_items = ['walk dog', 'wash car', 'study code'];

	echo "<ul>";

	foreach ($list as $key => $value) {
		echo "<li>$value</li>";
	}

	echo "</ul>";

	?>

<form method="POST" action="./todo_list.php">

	<label for="add_item">Add Item: </label>
	<input id="add_item" type="text" placeholder="Item Here">

	<button type="submit">SUBMIT</button>

</form>



</body>
</html>

