<?php

// OPEN FILE TO POPULATE LIST
function openFile($filename = '../data/list.txt') {
    $handle = fopen($filename, 'r');

    if (filesize($filename) > 0) {
    	$contents = trim(fread($handle, filesize($filename)));
    	$list = explode("\n", $contents);
    	fclose($handle);
    	return $list;
	}

	else 
		$list = [];
		return array_unique($list);
}

function addItem($item, $list) {
	$list[] = $item;
	$GLOBALS['item_added'] = "Added item.";
	return array_unique($list);
}

function removeItem($item, $list) {
	unset($list[$item]);
	array_values($list);
	$GLOBALS['item_removed'] = "Removed item.";
	return array_unique($list);
}

function uploadFile() {
	$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	$filename = basename($_FILES['upload_file']['name']);
	$saved_filename = $upload_dir . $filename;
	move_uploaded_file($_FILES['upload_file']['tmp_name'], $saved_filename);

	return openFile($saved_filename);

	// if (isset($saved_filename)) {
	//     $GLOBALS['file_uploaded']  = "<p>You can download your file <a href='/uploads/{$filename}'>here</a>.</p>";
	// }
}

// Overwrites Existing Save File With Current List
function saveToFile($list, $filename = '../data/list.txt') {
	$handle = fopen($filename, 'w');
	$string = '';

	foreach ($list as $key => $value) {
		$string .= "$value\n";
	}

	fwrite($handle, $string);
	fclose($handle);
}

function checkMIME() {
	if ($_FILES['upload_file']['type'] != 'text/plain') {
		$GLOBALS['error_message'] = "Error on file upload: MIME type isn't text/plain.";
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
		$GLOBALS['error_message'] = "Error on file upload: Unknown error.";
		return true;
}

function sanitizeInput($string) {
	return htmlspecialchars(strip_tags($string));
}

?>

<html>
<head>
	<title>To Do List</title>
	<link rel="stylesheet" type="text/css" href="http://todo.dev/css/style.css">
	<link href="//fonts.googleapis.com/css?family=Special+Elite:400" rel="stylesheet" type="text/css">
<body>


	<div id="header"></div>
	<div id="date"><?= date('l ') . " - " . date('F j, Y'); ?></div> 
	
	<div id="add-form">
	 	<!-- Add Item Form															-->
			<form method="POST" action="">
				<label for="add_item">Add Item: </label>
				<input id="add_item" name="add_item" type="text" placeholder="Item Here">
				<button type="submit">SUBMIT</button>
			</form>

			
		<?php  // If user feedback messages exist, output them.
			if (isset($GLOBALS['item_added'])) {
				echo "{$GLOBALS['item_added']}";
			}

			elseif (isset($GLOBALS['item_removed'])) {
				echo "{$GLOBALS['item_removed']}";
			} 
		?>
	</div>

	<div id="upload-form">
		<!-- Upload File Form														-->
			<form method="POST" enctype="multipart/form-data" action="">
				<label for="upload_file">Upload File:</label>
				<input id="upload_file" name="upload_file" type="file" placeholder="Choose file">
				<button type="submit" value="Upload">UPLOAD</button>
			</form>
	</div>
			
		<?php  // If file upload error messages exist, output them.
			if (isset($GLOBALS['error_message'])) { 
				echo "{$GLOBALS['error_message']}"; 
			} 
			elseif (isset($GLOBALS['file_uploaded'])) {
				echo "{$GLOBALS['file_uploaded']}";
			} 
		?>


 	<?php

 	$list = openFile();

 		if (!empty($_POST)) {
 			$item = sanitizeInput($_POST['add_item']);
 			$list = addItem($item, $list);
 			saveToFile($list);
 		}

 		if (isset($_GET['removeIndex'])) {
 		 	$list = removeItem($_GET['removeIndex'], $list);
 		 	saveToFile($list);
 		 	header('Location: http://todo.dev/');
 		}
 		
 		if (checkFileCount() == true) {
 			if (checkUploadError() == false && checkMIME() == true) {
 				$new_items = uploadFile();
 				foreach ($new_items as $item) {
 					$list[] = $item;
 					saveToFile($list);
 				}
 			}
 		}

 	?>
	<div id="list">
		<ul>
		<? foreach ($list as $key => $item) : ?>
			<li><?= "{$item} - <a href=\"?removeIndex={$key} \">Remove</a><br>" ?></li>
			<img id="underline" src="http://todo.dev/img/underline.png">
		<? endforeach ?>
		</ul>
 	</div>

</body>
</html>