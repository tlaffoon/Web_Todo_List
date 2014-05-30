<?php
/* -------------------------------------- */

// OPEN FILE TO POPULATE LIST
function openFile($filename) {

    $handle = fopen($filename, 'r');

    if (filesize($filename) == 0) {
    	$default_filesize = 1000;
    }

    else $default_filesize = filesize($filename);

    $contents = trim(fread($handle, $default_filesize));
    $list = explode("\n", $contents);
    fclose($handle);

    return $list;
}

// OUTPUTS LIST FROM ARRAY AS HTML
function outputList($list) {

	$string = '';
	$string .= "<ol>";
	foreach ($list as $key => $item) {
		$removeLink = "<a href=\"todo_list.php?removeIndex={$key} \">Remove</a>";
		$string .= "<li>{$item} - {$removeLink}</li>";
	}
	$string .= "</ol>";
	return $string;
}

function addItem($item, $list) {
	// Add new item to existing array
	$list[] = $item;
	return $list;
}

function removeItem($item, $list) {
	// If get request exists to remove item, do so.
	unset($list[$item]);
	array_values($list);
	return $list;
}

function uploadFile() {
	// Set the destination directory for uploads
	$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	// Grab the filename from the uploaded file by using basename
	$filename = basename($_FILES['upload_file']['name']);
	// Create the saved filename using the file's original name and our upload directory
	$saved_filename = $upload_dir . $filename;
	// Move the file from the temp location to our uploads directory
	move_uploaded_file($_FILES['upload_file']['tmp_name'], $saved_filename);

	if (isset($saved_filename)) {
	    // If we did, show a link to the uploaded file
	    echo "<p>You can download your file <a href='/uploads/{$filename}'>here</a>.</p>";
	}
}

// Overwrites Existing Save File With Current List
function saveToFile($list, $filename = './data/list.txt') {

	$handle = fopen($filename, 'w');
	
	$string = '';

	foreach ($list as $key => $value) {
		$string .= "$value\n";
	}

	fwrite($handle, $string);
	fclose($handle);

	return $list;
}

?>

<html>
<head>
	<title>To Do List</title>
</head>
<body>
<h2>To Do List:</h2>
	<?php

	$list = openFile('./data/list.txt');

		if (!empty($_POST)) {
			$list = addItem($_POST['add_item'], $list);
			echo "added item.";
			$list = saveToFile($list);
		}

		if (isset($_GET['removeIndex'])) {
		 	$list = removeItem($_GET['removeIndex'], $list);
		 	echo "removed item.";
		 	$list = saveToFile($list);
		}
		
		if (count($_FILES) > 0 && $_FILES['upload_file']['error'] == 0) {
			echo uploadFile();		
		}

		echo outputList($list);

	?>

	<!-- Add Item Form																-->
	<h3>Add Item Form:</h3>
	<p>
		<form method="POST" action="">

			<label for="add_item">Add Item: </label>
			<input id="add_item" name="add_item" type="text" placeholder="Item Here">

			<button type="submit">SUBMIT</button>

		</form>
	</p>
	<!-- Upload File Form																-->
	<h3>Upload File Form:</h3>
	<p>
		<form method="POST" enctype="multipart/form-data" action="">

			<label for="upload_file">Upload File:</label>
			<input id="upload_file" name="upload_file" type="file" placeholder="Choose file">

			<button type="submit" value="Upload">UPLOAD</button>

		</form>
	</p>
</body>
</html>