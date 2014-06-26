<?php

// New ToDo Application

try {
	// Establish DB Connection
	$dbc = new PDO('mysql: host=127.0.0.1; dbname=todo', 'codeup', 'password');

	// Tell PDO to throw exceptions on error
	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
} 

catch (Exception $e) {
	$e->getMessage();	
}

function getItems($dbc, $itemsPerPage, $offset) {
    return $dbc->query("SELECT * FROM items LIMIT $itemsPerPage OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
}

function insertItem($dbc, $item) {
	$query = 'INSERT INTO items (item) VALUES (:item);';
	$stmt = $dbc->prepare($query);
	$stmt->bindValue(':item', $item, PDO::PARAM_STR);
	$stmt->execute();
	return "<p>Inserted ID: " . $dbc->lastInsertId() . "</p>";
}

function removeItem($dbc, $id) {
	$query = 'DELETE FROM items WHERE id = :id';
	$stmt = $dbc->prepare($query);
	$stmt->bindValue(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
}

function countItems($dbc) {
	return $dbc->query('SELECT COUNT(*) FROM items')->fetchColumn();
}

$itemsPerPage = 5;

if (!empty($_POST['item'])) {
	insertItem($dbc, $_POST['item']);
	header('Location: http://todo.dev/');
}

if (!empty($_POST['remove'])) {
	var_dump($_POST);
	removeItem($dbc, $_POST['remove']);
	header('Location: http://todo.dev/');
}

if (empty($_GET)) {
	$pageID = 1;
}

else {
	$pageID = $_GET['page'];
}

$maxPages = ceil(countItems($dbc) / $itemsPerPage);

$offset = ($pageID * $itemsPerPage) - $itemsPerPage;

$todo = getItems($dbc, $itemsPerPage, $offset);


?>

<html>
<head>
	<title>To Do List</title>
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
	<script type="text/javascript" src="/js/jquery.js"></script>
	<style type="text/css">
	#item {
		width: 80%;
	}
	.spaced {
		margin-bottom: 5px;
	}
	</style>
</head>

	<body>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<p class="navbar-brand">Todo List</p>
				</div>
			</div>
		</nav>

	<div class="container col-md-6 col-md-offset-2">
		<div id="add-form" class="form-group input-append">
			<form class="form-inline" role="form" method="POST" action="">
				<label for="add_item">Add an item: </label>
				<div class="form-horizontal">
				<input id="item" class="form-control" name="item" type="text">
				<button id="btn1" class="btn btn-default" type="submit">Add</button>
				</div>
			</form>
		</div>

		<? // Begin Pagination ?>
		<? if ($pageID > 1) : ?>
			<a class="btn btn-small btn-default pull-left spaced" href="?page=<?= ($pageID - 1) ?>"> Previous </a>
		<? endif ?>
		
		<? if ($pageID < $maxPages) : ?>
			<a class="btn btn-small btn-default pull-right spaced" href="?page=<?= ($pageID + 1) ?>"> Next </a>
		<? endif ?>
		<? // End Pagination ?>

		<table class="table table-striped">
			<tr>
				<? foreach ($todo as $entry) : ?>
					<? //foreach ($entry as $key => $value) : ?>
						<td><?= "{$entry['item']}"; ?></td>
						<td><button class="btn btn-danger btn-exsm pull-right btn-remove" data-todo="<?= $entry['id']; ?>">Remove</button></td>
					</tr>
					<? //endforeach ?>
				<? endforeach ?>
		</table>
	</div>

	 	<form id="remove-form" action="" method="post">
	 	    <input id="remove-id" type="hidden" name="remove" value="">
	 	</form>

	<script type="text/javascript">
	$('document').ready(function () {

		console.log('Document Loaded.');

		$('#btn1').click(function () {
		    $(this).addClass('btn-info');
		    $(this).fadeOut;
		});

	 	$('.btn-remove').click(function () {
	 	    var todoID = $(this).data('todo');
	 	    // if (confirm('Are you sure you want to remove this item?')) {
	 	        $('#remove-id').val(todoID);
	 	        $('#remove-form').submit();
	 	    // }
	 	});

	 	$('#btn-prev').click(function () {
	 	 	var pageID = $(this).data('pageID');
	 	 	console.log(pageID);
	 	 	//get pageID and increment/decrement value
	 	 	// pass get request with new value to browser
	 	});

	 	$('#btn-next').click(function () {
	 	 	var pageID = $(this).data('pageID');
	 	 	console.log(pageID);
	 	 	//get pageID and increment/decrement value
	 	 	// pass get request with new value to browser
	 	});
	});

	</script>

	</body>
	</html>