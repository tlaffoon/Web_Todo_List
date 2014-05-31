<?php

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

function sortListAlpha($list) {
	return sort($list);
}

function addItem($item, $list) {
	// Add new item to existing array
	$list[] = $item;
	$GLOBALS['item_added'] = "<h5>Added item.</h5>";
	return $list;
}

function removeItem($item, $list) {
	// If get request exists to remove item, do so.
	unset($list[$item]);
	array_values($list);
	$GLOBALS['item_removed'] = "<h5>Removed item.</h5>";
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
	    return "<p>You can download your file <a href='/uploads/{$filename}'>here</a>.</p>";
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

function checkMIME() {
	if ($_FILES['upload_file']['type'] != 'text/plain') {
		$GLOBALS['error_message'] = "<h5>Error on file upload: MIME type isn't text/plain.</h5>";
		return false;
	}

	else 
		return true;
}

function checkFileCount() {
	if (count($_FILES) == 1) {
		return true;
	}

	else
		return false;
}

function checkUploadError() {
	if ($_FILES['upload_file']['error'] == 0) {
		return false;
	}

	else
		$GLOBALS['error_message'] = "<h5>Error on file upload: Unknown error.</h5>";
		return true;

}

function sanitizeInput($string) {
	return htmlspecialchars(strip_tags($string));
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
			$item = sanitizeInput($_POST['add_item']);
			$list = addItem($item, $list);
			saveToFile($list);
		}

		if (isset($_GET['removeIndex'])) {
		 	$list = removeItem($_GET['removeIndex'], $list);
		 	saveToFile($list);
		}
		
		if (checkFileCount() == true) {
			if ( checkUploadError() == false && checkMIME() == true) {
				uploadFile();
			}
		}

		echo outputList($list);
	?>
<hr>
	<!-- Add Item Form															-->
	<h3>Add Item Form:</h3>
	<p>
		<form method="POST" action="">
			<label for="add_item">Add Item: </label>
			<input id="add_item" name="add_item" type="text" placeholder="Item Here">
			<button type="submit">SUBMIT</button>
		</form>
		<!-- if user feedback messages exist, output them. -->
		<?php if (isset($GLOBALS['item_added'])) {
			echo $GLOBALS['item_added'];
		}

		elseif (isset($GLOBALS['item_removed'])) {
			echo $GLOBALS['item_removed'];
		} 
		?>
	</p>
<hr>
	<!-- Upload File Form														-->
	<h3>Upload File Form:</h3>
	<p>
		<form method="POST" enctype="multipart/form-data" action="">
			<label for="upload_file">Upload File:</label>
			<input id="upload_file" name="upload_file" type="file" placeholder="Choose file">
			<button type="submit" value="Upload">UPLOAD</button>
		</form>
		<!-- if error messages exist, output them. -->
		<?php if (isset($GLOBALS['error_message'])) { echo $GLOBALS['error_message']; } ?>
	</p>
<hr>
</body>
</html>