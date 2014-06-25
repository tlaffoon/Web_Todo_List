<?php

// New ToDo Application

// Establish DB Connection
$dbc = new PDO('mysql: host=127.0.0.1; dbname=todo', 'codeup', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";

function getItems($dbc) {
    return $dbc->query('SELECT * FROM items')->fetchAll(PDO::FETCH_ASSOC);
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

$todo = getItems($dbc);

if (!empty($_POST['item'])) {
	insertItem($dbc, $_POST['item']);
	header('Location: http://todo.dev/todo2.php');
}

if (!empty($_POST['remove'])) {
	var_dump($_POST);
	removeItem($dbc, $_POST['remove']);
	header('Location: http://todo.dev/todo2.php');
}

if (!empty($_GET)) {
	echo "get found.";
}

?>

<html>
<head>
	<title>To Do List</title>
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
	<script type="text/javascript" src="/js/jquery.js"></script>

	<body>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<p class="navbar-brand">Todo List</p>
				</div>
			</div>
		</nav>

		<div class="container col-md-6">

		<div id="add-form" class="form-group">
			<form role="form" method="POST" action="">
				<label for="add_item">Add an item: </label>
				<input id="item" name="item" class="form-control" type="text">
				<button id="btn1" class="btn btn-default" type="submit">Add</button>
			</form>
		</div>

		<table class="table table-striped">
			<tr>
				<? foreach ($todo as $entry) : ?>
					<? //foreach ($entry as $key => $value) : ?>
						<td><?= "{$entry['item']}"; ?></td>
						<td><button class="btn btn-danger btn-sm pull-right btn-remove" data-todo="<?= $entry['id']; ?>">Remove</button></td>
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

	 	$('.btn-remove').click(function () {
	 	    var todoID = $(this).data('todo');
	 	    // if (confirm('Are you sure you want to remove this item?')) {
	 	        $('#remove-id').val(todoID);
	 	        $('#remove-form').submit();
	 	    // }
	 	});
	});

	 	</script>

	</body>
	</html>