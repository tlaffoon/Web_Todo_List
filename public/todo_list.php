<?php

require('./includes/filestore.php');

function uploadFile() {
	$upload_dir = '/vagrant/sites/todo.dev/public/uploads/';
	$filename = basename($_FILES['upload_file']['name']);
	$saved_filename = $upload_dir . $filename;
	move_uploaded_file($_FILES['upload_file']['tmp_name'], $saved_filename);

	return $saved_filename;
}

function checkMIME() {
	if ($_FILES['upload_file']['type'] != 'text/plain') {
		$GLOBALS['error_message'] = "Error on file upload - must be a plain text file.";
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
	if (strlen($string) == 0 || strlen($string) > 240) {
		throw new Exception("Error on item add.  Please use between 1 and 240 characters.", 1);
	}
	return htmlspecialchars(strip_tags($string));
}

function addList($items_to_add, $list) { 
	foreach ($items_to_add as $item) {
		$list[] = $item;
	}
	return array_unique($list);
}

?>

<html>
<head>
	<title>To Do List</title>
	<link rel="stylesheet" type="text/css" href="http://todo.dev/css/style.css">
	<link href="//fonts.googleapis.com/css?family=Special+Elite:400" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="/js/jquery.js"></script>
<body>


	<div id="header"></div>
	<div id="date"><?= date('l ') . " - " . date('F j, Y'); ?></div> 
	
	<div id="add-form">
	 	<!-- Add Item Form															-->
			<form method="POST" action="">
				<label for="add_item">Add an item: </label>
				<input id="add_item" name="add_item" type="text" placeholder="Item Here">
				<button type="submit">SUBMIT</button>
			</form>
	</div>

	<div id="upload-form">
		<!-- Upload File Form														-->
			<form method="POST" enctype="multipart/form-data" action="">
				<label for="upload_file">Add items from file:</label>
				<input id="upload_file" name="upload_file" type="file" placeholder="Choose file">
				<button type="submit" value="Upload">UPLOAD</button>
			</form>
	</div>

 	<?php

 	$listObject = new Filestore('../data/list.txt');
 	$list = $listObject->read($listObject->filename);

 		if (!empty($_POST)) {
 			$item = sanitizeInput($_POST['add_item']);
 			$list[] = $item;
 			$list = array_unique($list);
 			$listObject->write(array_unique($list));
 		}

 		if (isset($_GET['removeIndex'])) {
 		 	unset($list[$_GET['removeIndex']]);
 		 	$list = array_unique(array_values($list));
 		 	$listObject->write($list);
 		 	header('Location: http://todo.dev/');
 		}

 		if (checkFileCount() == true && checkUploadError() == false && checkMIME() == true) {
 				$filename = uploadFile();
 				$listObject2 = new Filestore($filename);
 				$items_to_add = $listObject2->read($listObject2->filename);
 				$list = addList($items_to_add, $list);
 				$listObject->write($list);
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

 	<script type="text/javascript">

 	$('document').ready(function () {

 		console.log('Document Loaded.');

 		$('li').on('click', function () {
 			console.log($(this));
 			//$(this).addClass('checked-li');
 		});

 	});

 	</script>

</body>
</html>